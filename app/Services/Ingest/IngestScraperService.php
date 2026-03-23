<?php

namespace App\Services\Ingest;

class IngestScraperService
{
    protected DogRecordBuilder $builder;
    protected IngestService $ingest;

    public function __construct(DogRecordBuilder $builder, IngestService $ingest)
    {
        $this->builder = $builder;
        $this->ingest  = $ingest;
    }

    /**
     * @param array $scrapedDogs  Scraper által összegyűjtött kutya-adatok tömbje
     */
    public function import(array $scrapedDogs): void
    {
        foreach ($scrapedDogs as $data) {
            $rawDog = $this->builder->fromScraper($data);
            $this->ingest->ingestDog($rawDog);
        }
    }
}