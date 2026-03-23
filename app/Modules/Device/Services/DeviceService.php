<?php

namespace App\Modules\Device\Services;

class DeviceService
{
    public function __construct(
        protected TrustScoreService $trust,
        protected SecurityFlagService $flags,
        protected RateLimitService $rate,
        protected AuditLogService $audit,
        protected AccessTimelineBuilder $timeline,
    ) {}

    /**
     * Új device észlelésekor hívjuk.
     */
    public function onNewDeviceDetected(int $userId): void
    {
        // Trust score -1
        $this->trust->deviceSuspicious($userId);

        // Ha sok a device verification request ? suspicious_user
        $this->checkTooManyDeviceVerifications($userId);
    }

    /**
     * Sikeres device verifikációkor hívjuk.
     */
    public function onDeviceVerified(int $userId): void
    {
        // Trust score +2
        $this->trust->deviceVerified($userId);

        // Rate limit reset
        $this->rate->reset('device_verification', $userId);

        // Ha volt suspicious_user flag, késobb policy dönthet a feloldásról
    }

    /**
     * Hibás verification kód esetén hívjuk.
     */
    public function onWrongVerificationCode(int $userId): void
    {
        $attempts = $this->rate->increment('device_verification', $userId);

        if ($attempts >= 3) {
            $this->rate->lock('device_verification', $userId, 10);

            if (!$this->flags->hasFlag($userId, 'suspicious_user')) {
                $this->flags->addFlag($userId, 'suspicious_user');
            }

            $this->trust->adjust($userId, -3, 'too_many_wrong_device_codes');

            $this->audit->log(
                userId: $userId,
                kennelId: null,
                dogId: null,
                action: 'security_lock_device_verification',
                reason: 'too_many_wrong_codes',
                meta: ['lock_minutes' => 10]
            );

            $this->timeline->log(
                userId: $userId,
                kennelId: null,
                dogId: null,
                event: 'security_lock_device_verification',
                meta: ['lock_minutes' => 10]
            );
        }
    }

    /**
     * Napi / óránkénti cronból hívható:
     * stabil user ? lassú, pozitív trust score emelés.
     */
    public function onStabilityTick(int $userId): void
    {
        // Itt lehetne komplexebb logika (pl. utolsó 7/30/90 nap viselkedése),
        // most egy egyszeru példa:
        if (!$this->flags->hasFlag($userId, 'suspicious_user')) {
            $this->trust->adjust($userId, +1, 'stability_bonus');
        }
    }

    /**
     * Policy: túl sok device verification request 24 órán belül.
     */
    protected function checkTooManyDeviceVerifications(int $userId): void
    {
        // Ezt a DeviceVerification modellel tudod lekérdezni,
        // itt csak a policy helye van – a konkrét count-ot
        // a DeviceService-ben vagy egy külön repositoryban számolod.
        // Példa hívásra: a DeviceService átadja az értéket.

        // Ez a metódus akkor hasznos igazán, ha így hívod:
        // $engine->onTooManyDeviceVerifications($userId, $count);

        // Most inkább külön, explicit policy-t írunk:
    }

    /**
     * Explicit policy: ha X felett van a verification count, lépjen életbe.
     */
    public function onTooManyDeviceVerifications(int $userId, int $count, int $threshold = 5): void
    {
        if ($count < $threshold) {
            return;
        }

        if (!$this->flags->hasFlag($userId, 'suspicious_user')) {
            $this->flags->addFlag($userId, 'suspicious_user');
        }

        $this->trust->adjust($userId, -5, 'too_many_device_verification_requests');

        $this->audit->log(
            userId: $userId,
            kennelId: null,
            dogId: null,
            action: 'security_flag_suspicious_user',
            reason: 'too_many_device_verification_requests',
            meta: ['count' => $count, 'threshold' => $threshold]
        );

        $this->timeline->log(
            userId: $userId,
            kennelId: null,
            dogId: null,
            event: 'security_flag_suspicious_user',
            meta: ['count' => $count, 'threshold' => $threshold]
        );
    }

    /**
     * Döntés: milyen "risk mode"-ban van a user?
     */
    public function getRiskLevel(int $userId, int $trustScore): string
    {
        if ($this->flags->hasFlag($userId, 'suspicious_user')) {
            return 'high';
        }

        if ($trustScore <= 0) {
            return 'high';
        }

        if ($trustScore <= 10) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Ezt hívhatja bármelyik modul, mielott érzékeny muveletet enged.
     * Pl. kennel adatmódosítás, új kennel, új dog, stb.
     */
    public function shouldRequireExtraVerification(int $userId, int $trustScore): bool
    {
        $risk = $this->getRiskLevel($userId, $trustScore);

        return in_array($risk, ['high', 'medium'], true);
    }
}
