<?php

namespace App\Services\Pedroo;

class RulesEngine
{
    private array $rules;

    public function __construct()
    {
        $this->rules = json_decode(
            file_get_contents(config_path('pedroo/rules.json')),
            true
        );
    }

    public function evaluateFile(string $pipeline, string $fileName, string $fileContent): array
    {
        $pipelineRules = $this->rules['pipelines'][$pipeline] ?? null;

        if (!$pipelineRules) {
            return [
                'status' => 'red',
                'reason' => 'Pipeline not found in rules.json'
            ];
        }

        // Kötelezo fájlok
        if (in_array($fileName, $pipelineRules['required'])) {
            return $this->evaluateRequiredFile($pipelineRules, $fileName, $fileContent);
        }

        // Tiltott fájlok
        if (in_array($fileName, $pipelineRules['forbidden'])) {
            return [
                'status' => 'red',
                'reason' => 'Forbidden file'
            ];
        }

        // Opcionális fájlok
        if (in_array($fileName, $pipelineRules['optional'])) {
            return $this->evaluateOptionalFile($pipelineRules, $fileName, $fileContent);
        }

        // Ha nem kötelezo, nem opcionális, nem tiltott ? felesleges
        return [
            'status' => 'red',
            'reason' => 'Unexpected file in pipeline'
        ];
    }

    private function evaluateRequiredFile(array $pipelineRules, string $fileName, string $content): array
    {
        // Globális szabályok
        if (!$this->passesGlobalRules($content)) {
            return [
                'status' => 'red',
                'reason' => 'Global rules failed'
            ];
        }

        // Pipeline specifikus szabályok
        $rules = $pipelineRules['rules'] ?? [];

        // mustContainMethods
        if (!empty($rules['mustContainMethods'])) {
            foreach ($rules['mustContainMethods'] as $method) {
                if (!str_contains($content, "function {$method}")) {
                    return [
                        'status' => 'yellow',
                        'reason' => "Missing method: {$method}"
                    ];
                }
            }
        }

        // mustExtend
        if (!empty($rules['mustExtend'])) {
            if (!str_contains($content, "extends {$rules['mustExtend']}")) {
                return [
                    'status' => 'yellow',
                    'reason' => "Must extend {$rules['mustExtend']}"
                ];
            }
        }

        // mustImplement
        if (!empty($rules['mustImplement'])) {
            foreach ($rules['mustImplement'] as $interface) {
                if (!str_contains($content, "implements {$interface}")) {
                    return [
                        'status' => 'yellow',
                        'reason' => "Must implement {$interface}"
                    ];
                }
            }
        }

        return [
            'status' => 'green',
            'reason' => 'OK'
        ];
    }

    private function evaluateOptionalFile(array $pipelineRules, string $fileName, string $content): array
    {
        // Opcionális fájl ? ha létezik, legyen érvényes
        if (!$this->passesGlobalRules($content)) {
            return [
                'status' => 'yellow',
                'reason' => 'Optional file exists but fails global rules'
            ];
        }

        return [
            'status' => 'green',
            'reason' => 'Optional file OK'
        ];
    }

    private function passesGlobalRules(string $content): bool
    {
        $global = $this->rules['globalRules'];

        // PHP kezdo tag
        if (!str_starts_with(trim($content), $global['phpFileMustStartWith'])) {
            return false;
        }

        // Tiltott debug függvények
        foreach ($global['noDebugFunctions'] as $debugFn) {
            if (str_contains($content, $debugFn . '(')) {
                return false;
            }
        }

        return true;
    }
}