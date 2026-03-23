<?php

namespace App\Services\Title;

use Illuminate\Support\Facades\DB;

class TitleImportService
{
    protected TitleDefinitionService $definitions;

    public function __construct(TitleDefinitionService $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * Import a list of title definitions (array, CSV, JSON, API).
     *
     * @param array $rows
     * @return array  Summary report
     */
    public function import(array $rows): array
    {
        $inserted = 0;
        $updated  = 0;
        $errors   = [];

        foreach ($rows as $index => $row) {

            try {
                $id = $this->definitions->upsert($row);

                if (isset($row['__existing']) && $row['__existing'] === true) {
                    $updated++;
                } else {
                    $inserted++;
                }

            } catch (\Throwable $e) {
                $errors[] = [
                    'row'    => $index,
                    'data'   => $row,
                    'error'  => $e->getMessage(),
                ];
            }
        }

        return [
            'inserted' => $inserted,
            'updated'  => $updated,
            'errors'   => $errors,
        ];
    }

    /**
     * Import from CSV file.
     */
    public function importCsv(string $path): array
    {
        $rows = [];

        if (!file_exists($path)) {
            throw new \Exception("CSV file not found: {$path}");
        }

        $handle = fopen($path, 'r');
        $header = null;

        while (($data = fgetcsv($handle, 2000, ',')) !== false) {
            if (!$header) {
                $header = $data;
                continue;
            }

            $rows[] = array_combine($header, $data);
        }

        fclose($handle);

        return $this->import($rows);
    }
}