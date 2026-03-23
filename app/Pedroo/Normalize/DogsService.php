<?php

namespace App\Pedroo\Normalize;

use Carbon\Carbon;

class DogsService
{
    /**
     * A DogsPipelineStep ezt hívja.
     * Visszaad:
     *  - 'dog' => normalizált pd_dogs rekord
     *  - 'timeline' => generált timeline események (breed + individual)
     */
    public function handle(array $row): array
    {
        $timeline = [];

        // --- ALAP NORMALIZÁLÁS ---

        $dog = [];

        $dog['name']       = $this->normalizeName($row['source_name'] ?? null);
        $dog['reg_no']     = $this->normalizeRegNo($row['source_reg_no'] ?? null);

        // névbontás
        $nameParts         = $this->splitName($dog['name']);
        $dog['prefix']     = $nameParts['prefix'];
        $dog['firstname']  = $nameParts['firstname'];
        $dog['lastname']   = $nameParts['lastname'];

        $dog['breed']      = $this->normalizeBreed($row['real_breed'] ?? null);
        $dog['dob']        = $this->normalizeDob($row['real_dob'] ?? null);
        $dog['sex']        = $this->normalizeSex($row['real_sex'] ?? null);

        $dog['color']          = $this->normalizeColor($row['raw_color'] ?? null);
        $dog['official_color'] = $this->normalizeColor($row['official_color'] ?? null);
        $dog['birth_color']    = $this->normalizeColor($row['birth_color'] ?? null);

        $dog['origin_country']   = $this->normalizeCountry($row['real_origin_country'] ?? null);
        $dog['standing_country'] = $this->normalizeCountry($row['real_standing_country'] ?? null);

        $dog['father_id']  = null;
        $dog['mother_id']  = null;
        $dog['breeder_id'] = null;
        $dog['owner_id']   = null;
        $dog['kennel_id']  = null;

        // --- TÖRTÉNETI BESOROLÁS ---

        $dog['history_classification'] = $this->classifyHistoryByRegNo(
            $dog['reg_no'],
            $dog['breed'],
            $dog['origin_country']
        );

        // timeline event: reg_no korszak
        if ($dog['history_classification']) {
            $timeline[] = $this->makeTimelineEvent(
                type: 'registry_period',
                date: $dog['dob'] ?? null,
                title: "Registry era: {$dog['history_classification']}",
                description: "The dog's registration number ({$dog['reg_no']}) belongs to the {$dog['history_classification']} era."
            );
        }

        // timeline event: születés
        if ($dog['dob']) {
            $timeline[] = $this->makeTimelineEvent(
                type: 'birth',
                date: $dog['dob'],
                title: "Birth",
                description: "The dog was born on {$dog['dob']}."
            );
        }

        // timeline event: fajta standard korszak (később finomítjuk)
        if ($dog['breed'] && $dog['dob']) {
            $timeline[] = $this->makeTimelineEvent(
                type: 'breed_standard_period',
                date: $dog['dob'],
                title: "Breed standard period",
                description: "The dog was born during a known breed standard period (placeholder)."
            );
        }

        return [
            'dog'      => $dog,
            'timeline' => $timeline,
        ];
    }

    // ---------------------------------------------------------------------
    // TIMELINE EVENT BUILDER
    // ---------------------------------------------------------------------

    private function makeTimelineEvent(string $type, ?string $date, string $title, string $description): array
    {
        return [
            'type'        => $type,
            'date'        => $date,
            'title'       => $title,
            'description' => $description,
        ];
    }

    // ---------------------------------------------------------------------
    // NÉV NORMALIZÁLÁS + BONTÁS
    // ---------------------------------------------------------------------

    private function normalizeName(?string $name): ?string
    {
        if (!$name) return null;
        $name = trim($name);
        return $name !== '' ? preg_replace('/\s+/', ' ', $name) : null;
    }

    private function splitName(?string $name): array
    {
        if (!$name) {
            return ['prefix' => null, 'firstname' => null, 'lastname' => null];
        }

        $parts = array_values(array_filter(explode(' ', $name)));

        if (count($parts) === 0) {
            return ['prefix' => null, 'firstname' => null, 'lastname' => null];
        }

        $firstname = array_shift($parts);
        $lastname  = count($parts) ? implode(' ', $parts) : null;

        return [
            'prefix'    => null, // később kennelnév felismerés
            'firstname' => $firstname,
            'lastname'  => $lastname,
        ];
    }

    // ---------------------------------------------------------------------
    // REG_NO NORMALIZÁLÁS
    // ---------------------------------------------------------------------

    private function normalizeRegNo(?string $regNo): ?string
    {
        if (!$regNo) return null;
        $regNo = trim($regNo);
        return $regNo !== '' ? preg_replace('/\s+/', ' ', $regNo) : null;
    }

    // ---------------------------------------------------------------------
    // FAJTA / DÁTUM / NEM
    // ---------------------------------------------------------------------

    private function normalizeBreed(?string $breed): ?string
    {
        if (!$breed) return null;
        return preg_replace('/\s+/', ' ', trim($breed));
    }

    private function normalizeDob(?string $dob): ?string
    {
        if (!$dob) return null;
        try {
            return Carbon::parse($dob)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeSex(?string $sex): ?string
    {
        if (!$sex) return 'U';
        $sex = strtoupper(trim($sex));
        return match (true) {
            in_array($sex, ['M', 'MALE', '♂']) => 'M',
            in_array($sex, ['F', 'FEMALE', '♀']) => 'F',
            default => 'U',
        };
    }

    // ---------------------------------------------------------------------
    // SZÍNEK
    // ---------------------------------------------------------------------

    private function normalizeColor(?string $color): ?string
    {
        if (!$color) return null;
        return preg_replace('/\s+/', ' ', trim($color));
    }

    // ---------------------------------------------------------------------
    // ORSZÁG
    // ---------------------------------------------------------------------

    private function normalizeCountry(?string $country): ?string
    {
        if (!$country) return null;
        return strtoupper(trim($country));
    }

    // ---------------------------------------------------------------------
    // TÖRTÉNETI BESOROLÁS – MAGYAR ALAP
    // ---------------------------------------------------------------------

    private function classifyHistoryByRegNo(?string $regNo, ?string $breed, ?string $originCountry): ?string
    {
        if (!$regNo) return null;

        $regNo = strtoupper(trim($regNo));

        if (str_starts_with($regNo, 'MET.')) return 'modern';
        if (str_starts_with($regNo, 'MEOE') || str_starts_with($regNo, 'MEOESZ')) return 'historical';
        if (str_starts_with($regNo, 'OMKT')) return 'legacy';

        return null;
    }
}
