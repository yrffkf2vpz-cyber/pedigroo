<?php

namespace App\Dto;

class RawDogData
{
    /**
     * A teljes 3.0-ás nyers adatstruktúra.
     *
     * Minden mezo explicit, típushelyes és auditálható.
     */
    public function __construct(
        // --- Alapadatok ---
        public string  $name,
        public ?string $sex,
        public ?string $dob,

        // --- Fajta / Szín / Ország ---
        public ?string $breed,
        public ?string $color,
        public ?string $country,

        // --- Regisztráció ---
        public ?string $reg_no,
        public ?string $reg_country = null,
        public ?string $reg_issuer  = null,

        // --- Kennel / Owner / Breeder ---
        public ?string $kennel  = null,
        public ?string $owner   = null,
        public ?string $breeder = null,

        // --- Szülok ---
        public ?array $parents = [
            'sire' => null,
            'dam'  => null,
        ],

        // --- Egészségügyi adatok ---
        public ?array $health = [],

        // --- Címek / Titulusok ---
        public ?array $titles = [],

        // --- Forrás metaadatok ---
        public ?string $source = null,
        public ?array  $meta   = []
    ) {}

    /**
     * Canonical raw ? array export (audit trailhez).
     */
    public function toArray(): array
    {
        return [
            'name'        => $this->name,
            'sex'         => $this->sex,
            'dob'         => $this->dob,

            'breed'       => $this->breed,
            'color'       => $this->color,
            'country'     => $this->country,

            'reg_no'      => $this->reg_no,
            'reg_country' => $this->reg_country,
            'reg_issuer'  => $this->reg_issuer,

            'kennel'      => $this->kennel,
            'owner'       => $this->owner,
            'breeder'     => $this->breeder,

            'parents'     => $this->parents,
            'health'      => $this->health,
            'titles'      => $this->titles,

            'source'      => $this->source,
            'meta'        => $this->meta,
        ];
    }
}