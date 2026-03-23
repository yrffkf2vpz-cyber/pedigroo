<?php

namespace App\Services\Ingest;

use App\Services\Normalizers\NormalizeDogService;
use App\Models\Ingest\PdfImport;
use App\Jobs\ProcessPdfImportJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class IngestService
{
    protected NormalizeDogService $normalizer;

    public function __construct(NormalizeDogService $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * PDF felismerés + letöltés + ingest indítás
     */
    public function ingestUrl(string $url, string $source): void
    {
        // 1) PDF felismerés
        if ($this->isPdfLink($url)) {
            $path = $this->downloadPdf($url, $source);
            $type = $this->detectPdfType($url, $source);

            $this->createPdfImportRecord($path, $type, $source);
            return;
        }

        // 2) HTML → scraper (ha van)
        // Ha nincs scraper modulod, ezt később bővítjük.
    }

    private function isPdfLink(string $url): bool
    {
        return str_ends_with(strtolower($url), '.pdf');
    }

    private function downloadPdf(string $url, string $source): string
    {
        $contents = file_get_contents($url);

        $filename = basename(parse_url($url, PHP_URL_PATH));
        $path = "ingest/pdf/" . date('Y/m') . "/{$source}/{$filename}";

        Storage::put($path, $contents);

        return $path;
    }

    private function detectPdfType(string $url, string $source): string
    {
        $lower = strtolower($url);

        if (str_contains($lower, 'hd') || str_contains($lower, 'ed') || str_contains($lower, 'health')) {
            return 'health';
        }

        if (str_contains($lower, 'show') || str_contains($lower, 'result') || str_contains($lower, 'kiállítás')) {
            return 'event';
        }

        if (str_contains($lower, 'pedigree') || str_contains($lower, 'származás') || str_contains($lower, 'rodokmen')) {
            return 'pedigree';
        }

        return 'event';
    }

    private function createPdfImportRecord(string $path, string $type, string $source): void
    {
        $import = PdfImport::create([
            'user_id'   => null,
            'type'      => $type,
            'source'    => $source,
            'file_path' => $path,
            'status'    => 'pending',
            'stats'     => null,
            'log'       => [],
        ]);

        ProcessPdfImportJob::dispatch($import->id);
    }

    /**
     * A meglévő kutya-ingest változatlan marad
     */
    public function ingestDog(array $raw)
    {
        return DB::transaction(function () use ($raw) {

            // 1) Normalizálás
            $normalized = $this->normalizer->normalize($raw);

            // 2) Mentés a pedroo_dogs táblába
            $dogId = DB::table('pedroo_dogs')->insertGetId([
                'source_dog_id'   => $raw['source_id'] ?? null,
                'source_name'     => $raw['raw_name'] ?? null,
                'source_reg_no'   => $raw['raw_reg_no'] ?? null,
                'source_fci_no'   => $raw['raw_fci_no'] ?? null,

                'real_name'       => $normalized['real_name'],
                'real_prefix'     => $normalized['real_prefix'],
                'real_firstname'  => $normalized['real_firstname'],
                'real_lastname'   => $normalized['real_lastname'],
                'real_dob'        => $raw['raw_dob'] ?? null,
                'real_sex'        => $raw['raw_sex'] ?? null,
                'real_color'      => $normalized['real_color'],
                'real_breed'      => $raw['raw_breed'] ?? null,
                'real_origin_country'   => $normalized['origin_country'] ?? null,
                'real_standing_country' => $normalized['standing_country'] ?? null,
                'real_breeder'    => $raw['raw_breeder'] ?? null,
                'real_owner'      => $raw['raw_owner'] ?? null,
                'real_kennel'     => $raw['raw_kennel'] ?? null,

                'found_on'        => $raw['found_on'] ?? null,
                'confidence'      => $normalized['confidence'],
            ]);

            // 3) Szülők mentése
            if (!empty($raw['parents'])) {
                foreach ($raw['parents'] as $relation => $parentName) {
                    if (!$parentName) continue;

                    DB::table('pedroo_parents')->insert([
                        'child_name' => $normalized['real_name'],
                        'parent_name'=> $parentName,
                        'relation'   => $relation,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }

            // 4) Gyerekek mentése
            if (!empty($raw['children'])) {
                foreach ($raw['children'] as $childName) {
                    DB::table('pedroo_children')->insert([
                        'parent_name'=> $normalized['real_name'],
                        'child_name' => $childName,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }

            // 5) Eredmények mentése
            if (!empty($raw['results'])) {
                foreach ($raw['results'] as $result) {
                    DB::table('dog_event_results')->insert([
                        'dog_name'      => $normalized['real_name'],
                        'show_id'       => $result['show_id'] ?? null,
                        'show_result_id'=> $result['result_id'] ?? null,
                        'created_at'    => Carbon::now(),
                        'updated_at'    => Carbon::now(),
                    ]);
                }
            }

            return $dogId;
        });
    }
}
