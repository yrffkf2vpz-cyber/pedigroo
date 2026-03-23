<?php

namespace App\Services\WebImporter\Family;

use App\Models\Dog;
use App\Services\WebImporter\Contracts\DogImporterInterface;
use App\Services\WebImporter\DTO\DogDto;

class FamilyBuilder
{
    public function __construct(private DogImporterInterface $importer) {}

    public function build(DogDto $root): int
    {
        $rootId = $this->importer->import($root);

        if ($root->sire) {
            $sireId = $this->build($root->sire);
            Dog::whereKey($rootId)->update(['sire_id' => $sireId]);
        }

        if ($root->dam) {
            $damId = $this->build($root->dam);
            Dog::whereKey($rootId)->update(['dam_id' => $damId]);
        }

        return $rootId;
    }
}