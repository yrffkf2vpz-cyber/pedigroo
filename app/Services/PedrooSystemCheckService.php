<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class PedrooSystemCheckService
{
    public function run(): array
    {
        return [
            'tasks_folder' => $this->checkTasksFolder(),
            'db_structure' => $this->checkDatabase(),
            'files_ok'     => $this->checkFiles(),
        ];
    }

    protected function checkTasksFolder()
    {
        $path = base_path('app/Tasks');
        return File::exists($path) ? 'OK' : 'HI?NYZIK';
    }

    protected function checkDatabase()
    {
        return Schema::hasTable('pipeline_tasks')
            ? 'OK'
            : 'HI?NYZIK';
    }

    protected function checkFiles()
    {
        return 'OK'; // ide j?het r?szletes ellenorz?s
    }
}
