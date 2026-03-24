<?php

namespace App\Services\Normalizers;

use App\DTO\Normalize\NormalizedDogDataDTO;

class NormalizeDogService
{
    public function mapToDatabaseArray(NormalizedDogDataDTO $dto): array
    {
        return [
            // Alapadatok
            'breed_code'        => $dto->breedCode,
            'name'              => $dto->nameNormalized,
            'sex'               => $dto->sex,
            'birth_date'        => $dto->birthDateNormalized,
            'death_date'        => $dto->deathDateNormalized,
            'country_code'      => $dto->countryCodeNormalized,
            'kennel_name'       => $dto->kennelNameNormalized,
            'regnum'            => $dto->regNumNormalized,
            'chip_number'       => $dto->chipNumberNormalized,

            // Színek
            'color_main'        => $dto->colorMainNormalized,
            'color_alt'         => $dto->colorAltNormalized,

            // Diagnózisok
            'diagnosis_hd'      => $dto->diagnosisHdNormalized,
            'diagnosis_ed'      => $dto->diagnosisEdNormalized,
            'diagnosis_other'   => $dto->diagnosisOtherNormalized,

            // Szülok
            'father_regnum'     => $dto->fatherRegNumNormalized,
            'mother_regnum'     => $dto->motherRegNumNormalized,

            // Import meta
            'source_url'        => $dto->rawData['source_url'] ?? null,
            'source_system'     => $dto->rawData['source_system'] ?? null,
            'imported_at'       => now(),

            // Normalizált JSON
            'normalized_data'   => json_encode(
                $dto->toArray(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            ),
        ];
    }
}