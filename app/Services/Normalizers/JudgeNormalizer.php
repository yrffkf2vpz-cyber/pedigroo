<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;

class JudgeNormalizer
{
    /**
     * Normalize a list of judges from parsed JSON.
     */
    public static function normalizeList(array $judges): array
    {
        $out = [];

        foreach ($judges as $j) {
            $out[] = [
                "judge_id" => self::sandboxId($j["name"]),
                "role"     => $j["role"] ?? null
            ];
        }

        return $out;
    }

    /**
     * Insert or find judge in pedroo_judges sandbox table.
     */
    public static function sandboxId(string $name): int
    {
        $clean = self::cleanName($name);
        $hash  = sha1($clean);

        // Try to find existing sandbox judge
        $existing = DB::table("pedroo_judges")
            ->where("hash", $hash)
            ->value("id");

        if ($existing) {
            return $existing;
        }

        // Split name into parts
        [$prefix, $firstname, $lastname] = self::splitName($name);

        // Insert into sandbox
        return DB::table("pedroo_judges")->insertGetId([
            "source"         => "parser",
            "external_id"    => null,
            "source_name"    => $name,
            "real_prefix"    => $prefix,
            "real_firstname" => $firstname,
            "real_lastname"  => $lastname,
            "real_country"   => null, // k?sobb CountryNormalizer t?lti
            "specialization" => null,
            "raw"            => $name,
            "hash"           => $hash,
            "confidence"     => 80,
            "created_at"     => now(),
            "updated_at"     => now(),
        ]);
    }

    /**
     * Normalize judge name for matching.
     */
    protected static function cleanName(string $name): string
    {
        $name = trim($name);
        $name = mb_strtolower($name);
        $name = str_replace(".", "", $name);
        $name = preg_replace("/\s+/", " ", $name);

        return $name;
    }

    /**
     * Split judge name into prefix, firstname, lastname.
     */
    protected static function splitName(string $name): array
    {
        $name = trim($name);
        $parts = preg_split("/\s+/", $name);

        $prefix = null;
        $firstname = null;
        $lastname = null;

        // Detect prefix
        if (in_array(strtolower($parts[0]), ["dr", "mr", "mrs", "ms"])) {
            $prefix = array_shift($parts);
        }

        if (count($parts) === 1) {
            $lastname = $parts[0];
        } elseif (count($parts) >= 2) {
            $firstname = array_shift($parts);
            $lastname = implode(" ", $parts);
        }

        return [$prefix, $firstname, $lastname];
    }
}