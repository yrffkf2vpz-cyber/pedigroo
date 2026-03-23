<?php

namespace App\Services\Normalizers;

use App\Dto\RawDogData;
use App\Services\Normalizers\RegNo\RegNoService;
use App\Services\Ingest\DogRecordSaver;
use App\Events\RecordNeedsReview;

class NormalizePipelineService
{
    public function __construct(
        protected NormalizeDogService     $dog,
        protected NormalizeBreedService   $breed,
        protected NormalizeCountryService $country,
        protected NormalizeOwnerService   $owner,
        protected NormalizeParentService  $parent,
        protected NormalizeKennelService  $kennel,
        protected NormalizeColorService   $color,
        protected NormalizeHealthService  $health,
        protected RegNoService            $regno,
        protected ParentMatchingService   $parentMatcher,
        protected HistoryWriter           $historyWriter,
        protected DogRecordSaver          $saver,
    ) {}

    /**
     * TELJES 3.0‑ÁS NORMALIZÁLÓ PIPELINE
     */
    public function run(RawDogData $raw, bool $debug = false)
    {
        // ---------------------------------------------------------
        // 1) ALAP NORMALIZÁLÁSOK (3.0 API)
        // ---------------------------------------------------------
        $dogResult     = $this->dog->normalize($raw, $debug);
        $breedResult   = $this->breed->normalize($raw->raw_breed, $raw->raw_country, $debug);
        $countryResult = $this->country->normalize($raw->raw_country, $raw->raw_reg_no, $debug);
        $ownerResult   = $this->owner->normalize($raw->raw_owner, $raw->raw_country, $debug);
        $kennelResult  = $this->kennel->normalize($raw->raw_kennel, $raw->raw_country, $debug);
        $colorResult   = $this->color->normalize([
            'raw_color'   => $raw->raw_color,
            'raw_breed'   => $raw->raw_breed,
            'raw_country' => $raw->raw_country,
        ], $debug);
        $healthResult  = $this->health->normalize($raw->raw_health ?? [], $raw->raw_breed, $raw->raw_country, $debug);

        // ---------------------------------------------------------
        // 2) REGISZTRÁCIÓS SZÁM (3.0 API)
        // ---------------------------------------------------------
        $regnoResult = $this->regno->process($raw->raw_reg_no, $raw->raw_country, $debug);

        // ---------------------------------------------------------
        // 3) SZÜLŐK NORMALIZÁLÁSA + MATCHING (3.0 API)
        // ---------------------------------------------------------
        $parentInput   = $this->parent->normalize([
            'sire' => $raw->raw_sire,
            'dam'  => $raw->raw_dam,
        ], $debug);

        $matchedParents = [
            'sire' => $this->parentMatcher->match($parentInput['sire']),
            'dam'  => $this->parentMatcher->match($parentInput['dam']),
        ];

        // ---------------------------------------------------------
        // 4) CANONICAL NORMALIZED STRUCTURE
        // ---------------------------------------------------------
        $normalized = [
            'raw'     => $raw->toArray(),

            'dog'     => $dogResult,
            'breed'   => $breedResult,
            'country' => $countryResult,
            'owner'   => $ownerResult,
            'kennel'  => $kennelResult,
            'color'   => $colorResult,
            'health'  => $healthResult,
            'regno'   => $regnoResult,

            'parents' => [
                'input'   => $parentInput,
                'matched' => $matchedParents,
            ],

            'meta' => [
                'ai_used'    => true,
                'confidence' => $dogResult['confidence'] ?? 0,
                'flags'      => [],
                'warnings'   => [],
            ],
        ];

        // ---------------------------------------------------------
        // 5) DOG MAPPER (3.0 API)
        // ---------------------------------------------------------
        $mapped = $this->dog->mapToDogModel($normalized, $debug);

        // ---------------------------------------------------------
        // 6) MENTÉS (DogRecordSaver 3.0)
        // ---------------------------------------------------------
        $dog = $this->saver->save($mapped);

        // ---------------------------------------------------------
        // 7) HISTORY / REVIEW (3.0 API)
        // ---------------------------------------------------------
        $history = $this->historyWriter->write(
            dog:     $normalized['regno'],
            parents: $normalized['parents'],
            results: $normalized
        );

        if (!empty($history['needs_review'])) {
            event(new RecordNeedsReview(
                $dog,
                $normalized,
                $history['reason'] ?? 'unknown'
            ));
        }

        return $dog;
    }
}