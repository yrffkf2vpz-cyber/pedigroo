<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AISuggestionService
{
    public function suggest(string $domain, string $raw): ?string
    {
        if (!$raw) {
            return null;
        }

        $url = config('services.ai.url');
        $key = config('services.ai.key');

        if (!$url || !$key) {
            return null;
        }

        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $key,
        ])->post($url, [
            'domain' => $domain,
            'raw'    => $raw,
        ]);

        if (!$response->ok()) {
            return null;
        }

        $data = $response->json();

        return $data['suggestion'] ?? null;
    }
}