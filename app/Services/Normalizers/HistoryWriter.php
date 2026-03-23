<?php

namespace App\Services\Normalizers;

class HistoryWriter
{
    /**
     * Történeti naplózás + regisztrációs korszak beállítása.
     * A pd_dogs.history_classification mezőt tölti.
     */
    public function write(array $dog, array $parents, array $results): array
    {
        /**
         * A NormalizePipelineService-ben a RegNoService outputja így néz ki:
         *
         * $regno = [
         *     'raw'            => 'MET.Ku.123/2020',
         *     'normalized'     => 'MET-KUVASZ-123/2020',
         *     'year'           => 2020,
         *     'classification' => 'modern',
         *     ...
         * ];
         *
         * A HistoryWriter feladata: ezt a classification mezőt átadni a pd_dogs-nak.
         */

        $classification = $dog['regno']['classification'] ?? 'modern';

        return [
            // ezt írja majd be a NormalizePipelineService → pd_dogs.history_classification mezőbe
            'history_classification' => $classification,

            // későbbi audit trailhez
            'history' => [
                'regno_classification' => $classification,
                'regno_raw'            => $dog['regno']['raw']        ?? null,
                'regno_normalized'     => $dog['regno']['normalized'] ?? null,
                'regno_year'           => $dog['regno']['year']       ?? null,
            ],

            // debug
            'debug' => [
                'dog'     => $dog,
                'parents' => $parents,
                'results' => $results,
            ],
        ];
    }
}