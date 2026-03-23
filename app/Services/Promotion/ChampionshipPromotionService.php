<?php

namespace App\Services\Promotion;

use App\Services\Title\TitleNormalizer;
use Illuminate\Support\Facades\DB;

class ChampionshipPromotionService
{
    protected TitleNormalizer $titleNormalizer;

    public function __construct(TitleNormalizer $titleNormalizer)
    {
        $this->titleNormalizer = $titleNormalizer;
    }

    public function promote(object $sandbox): int
    {
        return DB::transaction(function () use ($sandbox) {

            // 1) DOG ID FELOLDÁS
            $dogId = $sandbox->dog_id;

            if (!$dogId && $sandbox->dog_name) {
                $dogId = DB::table('pd_dogs')
                    ->where('name', $sandbox->dog_name)
                    ->value('id');
            }

            if (!$dogId) {
                throw new \Exception("Dog not found: {$sandbox->dog_name}");
            }

            // 2) EVENT ID FELOLDÁS
            $eventId = $sandbox->event_id;

            if (!$eventId && $sandbox->event_name) {
                $eventId = DB::table('pd_events')
                    ->where('name', $sandbox->event_name)
                    ->value('id');
            }

            // 3) TITLE DEFINITION FELOLDÁS
            $titleDefinitionId = $sandbox->title_definition_id;

            if (!$titleDefinitionId) {
                $titleDefinitionId = $this->titleNormalizer->normalize(
                    $sandbox->title_code ?? $sandbox->title_name ?? '',
                    $sandbox->country
                );
            }

            if (!$titleDefinitionId) {
                throw new \Exception("Title definition not found for: {$sandbox->title_code}");
            }

            // 4) COUNTRY ID FELOLDÁS
            $countryId = null;
            if ($sandbox->country) {
                $countryId = DB::table('countries')
                    ->where('code', strtoupper($sandbox->country))
                    ->value('id');
            }

            // 5) DUPLIKÁCIÓ ELLENŐRZÉS
            $existing = DB::table('pd_championships')
                ->where('dog_id', $dogId)
                ->where('title_definition_id', $titleDefinitionId)
                ->where('date', $sandbox->date)
                ->value('id');

            if ($existing) {

                DB::table('pd_championships')
                    ->where('id', $existing)
                    ->update([
                        'event_id'   => $eventId,
                        'country_id' => $countryId,
                        'source'     => $sandbox->source,
                        'external_id'=> $sandbox->external_id,
                        'updated_at' => now(),
                    ]);

                $finalId = $existing;

            } else {

                // 6) INSERT PD OLDALRA
                $finalId = DB::table('pd_championships')->insertGetId([
                    'dog_id'             => $dogId,
                    'event_id'           => $eventId,
                    'title_definition_id'=> $titleDefinitionId,
                    'country_id'         => $countryId,
                    'date'               => $sandbox->date,
                    'source'             => $sandbox->source,
                    'external_id'        => $sandbox->external_id,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }

            // 7) SANDBOX AUDIT UPDATE
            DB::table('pedroo_championships')
                ->where('id', $sandbox->id)
                ->update([
                    'status'     => 'promoted',
                    'updated_at' => now(),
                    'notes'      => "Promoted to pd_championships (ID: {$finalId})",
                ]);

            return $finalId;
        });
    }
}