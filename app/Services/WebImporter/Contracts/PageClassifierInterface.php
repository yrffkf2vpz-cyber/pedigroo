<?php

namespace App\Services\WebImporter\Contracts;

interface PageClassifierInterface
{
    public function classify(string $html): string;
}