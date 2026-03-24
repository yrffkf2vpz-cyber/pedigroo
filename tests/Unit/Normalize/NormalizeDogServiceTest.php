<?php

namespace Tests\Unit\Normalize;

use Tests\TestCase;
use App\Services\Normalize\NormalizeDogService;
use App\DTO\Normalize\NormalizedDogDataDTO;

class NormalizeDogServiceTest extends TestCase
{
    public function test_mapping_is_correct()
    {
        $dto = new NormalizedDogDataDTO(
            breedCode: 'kuvasz',
            nameNormalized: 'Csillag',
            sex: 'F',
            birthDateNormalized: '2020-01-01',
            deathDateNormalized: null,
            countryCodeNormalized: 'HU',
            kennelNameNormalized: 'vom Test',
            regNumNormalized: 'H12345',
            chipNumberNormalized: '123456789',

            colorMainNormalized: 'white',
            colorAltNormalized: null,

            diagnosisHdNormalized: 'HD-A',
            diagnosisEdNormalized: 'ED-0',
            diagnosisOtherNormalized: null,

            fatherRegNumNormalized: 'H54321',
            motherRegNumNormalized: 'H98765',

            rawData: [
                'source_url' => 'https://example.com',
                'source_system' => 'webimporter'
            ],
            fuzzyMatches: []
        );

        $service = new NormalizeDogService();
        $mapped = $service->mapToDatabaseArray($dto);

        $this->assertEquals('kuvasz', $mapped['breed_code']);
        $this->assertEquals('Csillag', $mapped['name']);
        $this->assertEquals('white', $mapped['color_main']);
        $this->assertEquals('H54321', $mapped['father_regnum']);
        $this->assertEquals('https://example.com', $mapped['source_url']);
        $this->assertNotEmpty($mapped['normalized_data']);
    }
}