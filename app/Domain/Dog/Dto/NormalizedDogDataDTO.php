<?php

namespace App\Dto\Normalize;

class NormalizedDogDataDTO
{
    public function __construct(
        public readonly string $breedCode,
        public readonly ?string $nameNormalized,
        public readonly ?string $sex,
        public readonly ?string $birthDateNormalized,
        public readonly ?string $deathDateNormalized,
        public readonly ?string $countryCodeNormalized,
        public readonly ?string $kennelNameNormalized,
        public readonly ?string $regNumNormalized,
        public readonly ?string $chipNumberNormalized,

        public readonly ?string $colorMainNormalized,
        public readonly ?string $colorAltNormalized,

        public readonly ?string $diagnosisHdNormalized,
        public readonly ?string $diagnosisEdNormalized,
        public readonly ?string $diagnosisOtherNormalized,

        public readonly ?string $fatherRegNumNormalized,
        public readonly ?string $motherRegNumNormalized,

        public readonly array $rawData,
        public readonly array $fuzzyMatches = [],
    ) {}

    public function toArray(): array
    {
        return [
            'breedCode' => $this->breedCode,
            'nameNormalized' => $this->nameNormalized,
            'sex' => $this->sex,
            'birthDateNormalized' => $this->birthDateNormalized,
            'deathDateNormalized' => $this->deathDateNormalized,
            'countryCodeNormalized' => $this->countryCodeNormalized,
            'kennelNameNormalized' => $this->kennelNameNormalized,
            'regNumNormalized' => $this->regNumNormalized,
            'chipNumberNormalized' => $this->chipNumberNormalized,

            'colorMainNormalized' => $this->colorMainNormalized,
            'colorAltNormalized' => $this->colorAltNormalized,

            'diagnosisHdNormalized' => $this->diagnosisHdNormalized,
            'diagnosisEdNormalized' => $this->diagnosisEdNormalized,
            'diagnosisOtherNormalized' => $this->diagnosisOtherNormalized,

            'fatherRegNumNormalized' => $this->fatherRegNumNormalized,
            'motherRegNumNormalized' => $this->motherRegNumNormalized,

            'rawData' => $this->rawData,
            'fuzzyMatches' => $this->fuzzyMatches,
        ];
    }
}