<?php

namespace App\Services\Pedroo;

class TemplateRegistry
{
    public static function resolveTemplate(string $module, string $task): ?string
    {
        // Normalize modul ? service template
        if ($module === 'normalize') {
            return 'service';
        }

        // Ingest modul ? UI template
        if ($module === 'ingest' && str_ends_with($task, '_ui')) {
            return 'livewire_component';
        }

        // Audit modul ? Blade view
        if ($module === 'audit') {
            return 'blade_view';
        }

        // Default
        return 'service';
    }
}
