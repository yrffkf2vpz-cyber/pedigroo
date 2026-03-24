<?php

namespace App\Services\WebImporter\DTO;

class DogDto
{
    public ?string $regNo   = null;
    public ?string $name    = null;
    public ?string $breed   = null;
    public ?string $sex     = null;
    public ?string $dob     = null;

    public ?string $kennel  = null;
    public ?string $breeder = null;
    public ?string $owner   = null;
    public ?string $chip    = null;

    /** @var array<string,mixed> */
    public array $health = [];

    /** @var array<int,array<string,mixed>> */
    public array $shows = [];

    /** @var array<int,array<string,mixed>> */
    public array $litters = [];

    /** @var array<int,string> */
    public array $photos = [];

    public ?DogDto $sire = null;
    public ?DogDto $dam  = null;
}