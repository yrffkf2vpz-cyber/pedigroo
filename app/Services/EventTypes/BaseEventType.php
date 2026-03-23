<?php

namespace App\Services\EventTypes;

abstract class BaseEventType
{
    abstract public static function typeId(): string;
    abstract public static function name(): string;
    abstract public static function category(): string;

    public static function fields(): array
    {
        return static::$fields ?? [];
    }

    public static function normalize(array $raw): array
    {
        $normalized = [];
        foreach (static::fields() as $field => $def) {
            $value = $raw[$field] ?? null;
            if ($value === null) {
                $normalized[$field] = null;
                continue;
            }

            switch ($def['type'] ?? 'string') {
                case 'int':
                    $normalized[$field] = (int) $value;
                    break;
                case 'float':
                    $normalized[$field] = (float) str_replace(',', '.', (string) $value);
                    break;
                case 'bool':
                    $normalized[$field] = filter_var($value, FILTER_VALIDATE_BOOL);
                    break;
                case 'date':
                    $ts = strtotime((string) $value);
                    $normalized[$field] = $ts ? date('Y-m-d', $ts) : null;
                    break;
                case 'enum':
                case 'string':
                default:
                    $normalized[$field] = is_string($value) ? trim($value) : $value;
                    break;
            }
        }

        return $normalized;
    }

    public static function validate(array $raw): array
    {
        $errors = [];
        $fields = static::fields();

        foreach ($fields as $field => $def) {
            $required = $def['required'] ?? false;
            $type     = $def['type'] ?? 'string';

            if ($required && (!array_key_exists($field, $raw) || $raw[$field] === null || $raw[$field] === '')) {
                $errors[$field][] = 'required';
                continue;
            }

            if (!array_key_exists($field, $raw) || $raw[$field] === null || $raw[$field] === '') {
                continue;
            }

            $value = $raw[$field];

            switch ($type) {
                case 'int':
                    if (!is_numeric($value)) {
                        $errors[$field][] = 'integer';
                    }
                    break;
                case 'float':
                    if (!is_numeric(str_replace(',', '.', (string) $value))) {
                        $errors[$field][] = 'float';
                    }
                    break;
                case 'bool':
                    // bármit elfogadunk, amit bool-lá lehet castolni
                    break;
                case 'date':
                    if (strtotime((string) $value) === false) {
                        $errors[$field][] = 'date';
                    }
                    break;
                case 'enum':
                    $values = $def['values'] ?? [];
                    if (!in_array($value, $values, true)) {
                        $errors[$field][] = 'enum';
                    }
                    break;
                case 'string':
                default:
                    // nincs extra validáció
                    break;
            }
        }

        return [
            'valid'  => empty($errors),
            'errors' => $errors,
        ];
    }

    public static function toCanonical(array $normalized): array
    {
        // alap canonical: csak visszaadjuk, modulok felülírhatják
        return $normalized;
    }

    public static function fromCanonical(array $canonical): array
    {
        return $canonical;
    }

    public static function metadata(): array
    {
        return [
            'type'     => static::typeId(),
            'name'     => static::name(),
            'category' => static::category(),
            'fields'   => static::fields(),
        ];
    }
}
