<?php

namespace App\Modules\SystemScanner;

class PipelineMapBuilder
{
    protected array $pipelines = [];

    public function build(array $fileMap, array $usageMap): array
    {
        $this->detectPipelines($fileMap);
        $this->summarizeTasks($fileMap);
        $this->calculateUsage($usageMap);
        $this->detectDependencies($fileMap);
        $this->categorizePipelines();

        return $this->pipelines;
    }

    /**
     * 1) Pipeline-ok felismerése.
     */
    protected function detectPipelines(array $fileMap): void
    {
        foreach ($fileMap as $path => $info) {
            if (str_contains($path, 'Pipeline')) {
                $pipeline = $this->extractPipelineName($path);

                if ($pipeline) {
                    $this->pipelines[$pipeline] ??= [
                        'name' => $pipeline,
                        'task_count' => 0,
                        'usage' => 0,
                        'dependency_count' => 0,
                        'status' => 'unknown',
                        'category' => 'unknown',
                    ];
                }
            }
        }
    }

    /**
     * 2) Taskok összegzése (nem listázás!).
     */
    protected function summarizeTasks(array $fileMap): void
    {
        foreach ($fileMap as $path => $info) {
            $pipeline = $this->extractPipelineName($path);

            if ($pipeline) {
                $this->pipelines[$pipeline]['task_count']++;
            }
        }
    }

    /**
     * 3) Pipeline aktivitás összegzése.
     */
    protected function calculateUsage(array $usageMap): void
    {
        foreach ($this->pipelines as $pipeline => &$data) {
            $hits = 0;

            foreach ($usageMap as $file => $count) {
                if (str_contains($file, $pipeline)) {
                    $hits += $count;
                }
            }

            $data['usage'] = $hits;

            if ($hits > 100) {
                $data['status'] = 'active';
            } elseif ($hits > 0) {
                $data['status'] = 'low_activity';
            } else {
                $data['status'] = 'possibly_unused';
            }
        }
    }

    /**
     * 4) Pipeline függoségek összegzése.
     */
    protected function detectDependencies(array $fileMap): void
    {
        foreach ($this->pipelines as $pipeline => &$data) {
            $count = 0;

            foreach ($fileMap as $path => $info) {
                if (!str_contains($path, $pipeline)) {
                    continue;
                }

                foreach ($info['uses'] as $use) {
                    if (str_contains($use, 'Pipeline') && !str_contains($use, $pipeline)) {
                        $count++;
                    }
                }
            }

            $data['dependency_count'] = $count;
        }
    }

    /**
     * 5) Pipeline kategorizálása (ingest / normalize / promote / stats / misc).
     */
    protected function categorizePipelines(): void
    {
        foreach ($this->pipelines as $pipeline => &$data) {
            $name = strtolower($pipeline);

            $data['category'] =
                str_contains($name, 'ingest') ? 'ingest' :
                (str_contains($name, 'normalize') ? 'normalize' :
                (str_contains($name, 'promote') ? 'promote' :
                (str_contains($name, 'stat') ? 'stats' : 'misc')));
        }
    }

    // -----------------------------------------------
    // Segédfüggvény
    // -----------------------------------------------

    protected function extractPipelineName(string $path): ?string
    {
        if (!str_contains($path, 'Pipeline')) {
            return null;
        }

        $parts = explode('/', $path);

        foreach ($parts as $part) {
            if (str_contains($part, 'Pipeline')) {
                return str_replace('.php', '', $part);
            }
        }

        return null;
    }
}
