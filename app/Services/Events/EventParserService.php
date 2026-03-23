<?php

namespace App\Services\Events;

use App\Services\Parsers\MkszHtmlParser;
use App\Services\Parsers\GenericPdfParser;
use App\Services\Parsers\CsvResultParser;

class EventParserService
{
    public function parse(string $type, string $path, string $source): array
    {
        switch ($type) {
            case 'mksz_html':
                $parser = new MkszHtmlParser();
                break;

            case 'generic_pdf':
                $parser = new GenericPdfParser();
                break;

            case 'csv':
                $parser = new CsvResultParser();
                break;

            default:
                throw new \InvalidArgumentException("Unknown parser type: {$type}");
        }

        $parsed = $parser->parseFile($path);

        $parsed['source'] = $source;

        return $parsed;
    }
}