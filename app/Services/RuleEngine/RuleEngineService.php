<?php

namespace App\Services\RuleEngine;

use App\Models\Dog;
use App\Models\Rule;

class RuleEngineService
{
    public function __construct(
        protected RuleEvaluator $evaluator
    ) {}

    public function evaluateDog(Dog $dog): RuleEngineResult
    {
        $rules = Rule::query()
            ->whereIn('status', ['approved', 'applied'])
            ->where(function ($q) use ($dog) {
                $q->whereNull('breed_id')
                  ->orWhere('breed_id', $dog->breed_id);
            })
            ->get();

        $results = [];

        foreach ($rules as $rule) {
            $json = is_string($rule->rule_json)
                ? json_decode($rule->rule_json, true)
                : $rule->rule_json;

            if (!is_array($json)) {
                continue;
            }

            $passed = $this->evaluator->evaluateNode($json, $dog);

            $results[] = [
                'rule_id' => $rule->id,
                'passed'  => $passed,
                'rule'    => $json,
            ];
        }

        return new RuleEngineResult($results);
    }
}