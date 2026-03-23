<?php

namespace App\Services\Ingest;

class IngestApiService
{
    protected DogRecordBuilder $builder;
    protected IngestService $ingest;

    public function __construct(DogRecordBuilder $builder, IngestService $ingest)
    {
        $this->builder = $builder;
        $this->ingest  = $ingest;
    }

    /**
     * @param array $payloads  Tömb API-kutyákkal (pl. egy API válaszból)
     */
    public function import(array $payloads): void
    {
        foreach ($payloads as $payload) {
            $rawDog = $this->builder->fromApiPayload($payload);
            $this->ingest->ingestDog($rawDog);
        }
    }
}