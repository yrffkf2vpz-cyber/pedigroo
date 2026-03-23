<?php

namespace App\Services;

use GuzzleHttp\Client;

class DogWebScoutService
{
    public function run(string $query = 'pedigree dog')
    {
        $client = new Client();

        // 1) keresés a neten – több domain, több találat
        $res = $client->get(config('services.search.url'), [
            'headers' => [
                'Ocp-Apim-Subscription-Key' => config('services.search.key'),
            ],
            'query' => [
                'q' => $query,
                'count' => 20,
                'mkt' => 'en-US',
            ],
        ]);

        $data = json_decode($res->getBody(), true);

        $results = [];

        foreach ($data['webPages']['value'] ?? [] as $item) {
            $url = $item['url'];

            // 2) oldal letöltése
            try {
                $htmlRes = $client->get($url, ['timeout' => 10]);
                $html = (string) $htmlRes->getBody();
            } catch (\Throwable $e) {
                $results[] = [
                    'url' => $url,
                    'status' => 'fetch_failed',
                    'error' => $e->getMessage(),
                ];
                continue;
            }

            // 3) AI-nek odaadjuk: “szedj ki kutya adatokat”
            $dogData = $this->extractDogDataWithAI($html, $url);

            $results[] = [
                'url' => $url,
                'status' => $dogData ? 'ok' : 'no_dog_data',
                'dog' => $dogData,
            ];
        }

        return $results;
    }

    private function extractDogDataWithAI(string $html, string $url): ?array
    {
        $client = new Client();

        try {
            $res = $client->post(config('services.ai.url'), [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('services.ai.key'),
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'html' => $html,
                    'url'  => $url,
                    'instruction' => 'Extract dog-related structured data: name, breed, registration_number, date_of_birth, country, owner_name if present. Return JSON.',
                ],
            ]);

            $data = json_decode($res->getBody(), true);

            return $data['dog'] ?? $data ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}