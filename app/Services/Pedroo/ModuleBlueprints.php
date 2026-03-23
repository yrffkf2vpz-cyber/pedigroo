<?php

namespace App\Services\Pedroo;

class ModuleBlueprints
{
    public static function get(string $module): array
    {
        return match ($module) {

            'normalize' => [
                ['template' => 'service', 'suffix' => 'Service'],
                ['template' => 'pipeline_step', 'suffix' => 'PipelineStep'],
                ['template' => 'config', 'suffix' => 'Config'],
            ],

            'ingest' => [
                ['template' => 'livewire_component', 'suffix' => 'Component'],
                ['template' => 'blade_view', 'suffix' => 'View'],
                ['template' => 'service', 'suffix' => 'Service'],
                ['template' => 'pipeline_step', 'suffix' => 'PipelineStep'],
            ],

            'events' => [
                ['template' => 'downloader', 'suffix' => 'Downloader'],
                ['template' => 'parser', 'suffix' => 'Parser'],
                ['template' => 'service', 'suffix' => 'Service'],
                ['template' => 'pipeline_step', 'suffix' => 'PipelineStep'],
            ],

            'dogs' => [
                ['template' => 'normalizer', 'suffix' => 'Normalizer'],
                ['template' => 'service', 'suffix' => 'Service'],
                ['template' => 'pipeline_step', 'suffix' => 'PipelineStep'],
            ],

            default => [
                ['template' => 'service', 'suffix' => 'Service'],
            ],
        };
    }
}
