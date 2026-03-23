<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;

class ClassNormalizer
{
    /**
     * Get or create class_id from class name.
     */
    public static function id(?string $className): ?int
    {
        if (!$className) {
            return null;
        }

        $clean = self::clean($className);

        // Try to find existing class
        $existing = DB::table("classes")
            ->where("normalized_name", $clean)
            ->value("id");

        if ($existing) {
            return $existing;
        }

        // Create new class
        return DB::table("classes")->insertGetId([
            "name"            => $className,
            "normalized_name" => $clean,
            "created_at"      => now(),
            "updated_at"      => now()
        ]);
    }

    /**
     * Normalize class names for matching.
     */
    protected static function clean(string $value): string
    {
        $value = trim($value);
        $value = mb_strtolower($value);

        // Replace accents
        $value = str_replace(
            ['á','é','í','ó','ö','ő','ú','ü','ű'],
            ['a','e','i','o','o','o','u','u','u'],
            $value
        );

        // Remove punctuation
        $value = str_replace([",", ".", "(", ")", "-"], " ", $value);

        // Collapse multiple spaces
        $value = preg_replace("/\s+/", " ", $value);

        return $value;
    }
}