<?php

namespace App\Services\RuleEngine;

use App\Models\Dog;

class RuleEvaluator
{
    public function evaluateNode(array $node, Dog $dog): bool
    {
        $type = $node['type'] ?? 'rule';

        return match ($type) {
            'group' => $this->evaluateGroup($node, $dog),
            'rule'  => $this->evaluateRule($node, $dog),
            default => true,
        };
    }

    protected function evaluateGroup(array $group, Dog $dog): bool
    {
        $operator   = strtoupper($group['operator'] ?? 'AND');
        $conditions = $group['conditions'] ?? [];

        if ($operator === 'AND') {
            foreach ($conditions as $condition) {
                if (!$this->evaluateNode($condition, $dog)) {
                    return false;
                }
            }
            return true;
        }

        if ($operator === 'OR') {
            foreach ($conditions as $condition) {
                if ($this->evaluateNode($condition, $dog)) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }

    protected function evaluateRule(array $rule, Dog $dog): bool
    {
        $field    = $rule['field']    ?? null;
        $operator = $rule['operator'] ?? null;
        $value    = $rule['value']    ?? null;

        if (!$field || !$operator) {
            return true;
        }

        $dogValue = data_get($dog, $field);

        return match ($operator) {
            'equals'       => $dogValue == $value,
            'not_equals'   => $dogValue != $value,

            'in'           => in_array($dogValue, (array) $value),
            'not_in'       => !in_array($dogValue, (array) $value),

            'contains'     => str_contains((string) $dogValue, (string) $value),
            'not_contains' => !str_contains((string) $dogValue, (string) $value),

            'greater_than' => $dogValue > $value,
            'less_than'    => $dogValue < $value,

            'between'      => is_array($value)
                              && count($value) === 2
                              && $dogValue >= $value[0]
                              && $dogValue <= $value[1],

            'regex'        => is_string($value)
                              && preg_match($value, (string) $dogValue) === 1,

            'null'         => $dogValue === null,
            'not_null'     => $dogValue !== null,

            default        => true,
        };
    }
}