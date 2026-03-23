<?php

namespace App\Services\Ingest;

use Maatwebsite\Excel\Facades\Excel;

class IngestExcelService
{
    protected DogRecordBuilder $builder;
    protected IngestService $ingest;

    public function __construct(DogRecordBuilder $builder, IngestService $ingest)
    {
        $this->builder = $builder;
        $this->ingest  = $ingest;
    }

    public function import(string $filePath): void
    {
        // ?? Itt n?zz?k meg az Excel elso 3 sor?t
        $sheets = Excel::toArray([], $filePath);

        dd(
            'ELSO 3 SOR:',
            $sheets[0][0] ?? 'nincs sor 0',
            $sheets[0][1] ?? 'nincs sor 1',
            $sheets[0][2] ?? 'nincs sor 2'
        );

        $rows = $sheets[0] ?? [];

        foreach ($rows as $row) {
            $rawDog = $this->builder->fromExcelRow($row);
            $this->ingest->ingestDog($rawDog);
        }
    }
}