<?php

declare(strict_types=1);

namespace App\Services\Normalizers;

class CountryDetector
{
    public function detect(array $dogData): ?string
    {
        if (!empty($dogData['reg_no'])) {
            $country = $this->detectFromRegNo($dogData['reg_no']);
            if ($country) {
                return $country;
            }
        }

        if (!empty($dogData['kennel_name'])) {
            $country = $this->detectFromKennel($dogData['kennel_name']);
            if ($country) {
                return $country;
            }
        }

        if (!empty($dogData['fields']) && is_array($dogData['fields'])) {
            $country = $this->detectFromFieldNames($dogData['fields']);
            if ($country) {
                return $country;
            }
        }

        return null;
    }

    private function detectFromRegNo(string $regNo): ?string
    {
        $rules = config('country_rules', []);

        foreach ($rules as $country => $info) {
            if (!empty($info['prefixes'])) {
                foreach ($info['prefixes'] as $prefix) {
                    if (stripos($regNo, $prefix) === 0) {
                        return $country;
                    }
                }
            }
        }

        $legacy = config('regno_rules', []);

        foreach ($legacy as $country => $data) {
            if (empty($data['patterns']) || !is_array($data['patterns'])) {
                continue;
            }

            foreach ($data['patterns'] as $pattern => $issuer) {
                if (preg_match($pattern, $regNo)) {
                    return $country;
                }
            }
        }

        return null;
    }

    private function detectFromKennel(string $kennel): ?string
    {
        $patterns = config('kennel_country_patterns', []);

        foreach ($patterns as $country => $regexes) {
            foreach ($regexes as $regex) {
                if (preg_match($regex, $kennel)) {
                    return $country;
                }
            }
        }

        return null;
    }

    private function detectFromFieldNames(array $fields): ?string
    {
        $patterns = config('fieldname_country_patterns', []);

        foreach ($fields as $key => $value) {
            foreach ($patterns as $country => $regexes) {
                foreach ($regexes as $regex) {
                    if (preg_match($regex, $key)) {
                        return $country;
                    }
                }
            }
        }

        return null;
    }
}