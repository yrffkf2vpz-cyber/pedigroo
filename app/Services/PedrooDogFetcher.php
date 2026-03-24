<?php

namespace App\Services;

use App\Models\Dog;
use App\Models\PedrooDog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

// Pedroo modulok
use App\Services\Pedroo\EventService;
use App\Services\Pedroo\ShowService;
use App\Services\Pedroo\ShowResultService;
use App\Services\Pedroo\DogResultService;
use App\Services\Pedroo\DogActivationService;
use App\Services\Pedroo\ParentService;
use App\Services\Pedroo\KennelService;
use App\Services\Pedroo\OwnerService;
use App\Services\Pedroo\BreederService;

class PedrooDogFetcher
{
    protected EventService $eventService;
    protected ShowService $showService;
    protected ShowResultService $showResultService;
    protected DogResultService $dogResultService;
    protected DogActivationService $dogActivationService;
    protected ParentService $parentService;
    protected KennelService $kennelService;
    protected OwnerService $ownerService;
    protected BreederService $breederService;

    public function __construct()
    {
        $this->eventService        = new EventService();
        $this->showService         = new ShowService();
        $this->showResultService   = new ShowResultService();
        $this->dogResultService    = new DogResultService();
        $this->dogActivationService= new DogActivationService();
        $this->parentService       = new ParentService();
        $this->kennelService       = new KennelService();
        $this->ownerService        = new OwnerService();
        $this->breederService      = new BreederService();
    }

    /**
     * Egy kutya teljes feldolgoz?sa:
     * - HTML keres?s
     * - AI parse
     * - normaliz?l?s
     * - pedroo_dogs ment?s
     * - kapcsol?d? entit?sok ment?se (sz?lok, kennel, eredm?nyek, stb.)
     */
    public function processOneDog(): ?PedrooDog
    {
        $dog = $this->pickNextDog();

        if (! $dog) {
            return null;
        }

        $queries = $this->buildQueries($dog);

        $bestResult = null;

        foreach ($queries as $query) {
            $searchResults = $this->searchOnGoogle($query);

            foreach ($searchResults as $result) {
                $pageHtml = $this->fetchPage($result['url']);

                if (! $pageHtml) {
                    continue;
                }

                $parsed = $this->parseDogFromHtml($pageHtml);

                if (! $parsed || empty($parsed['dog'])) {
                    continue;
                }

                $normalizedDog = $this->normalizeDogData($parsed['dog']);

                $confidence = $this->calculateConfidence($dog, $normalizedDog);

                if (! $bestResult || $confidence > $bestResult['confidence']) {
                    $bestResult = [
                        'dog'        => $normalizedDog,
                        'raw'        => $parsed,
                        'url'        => $result['url'],
                        'confidence' => $confidence,
                        'notes'      => $parsed['notes'] ?? null,
                    ];
                }
            }
        }

        if (! $bestResult) {
            return PedrooDog::create([
                'source_dog_id'  => $dog->ID,
                'source_name'    => $dog->registered_name,
                'source_reg_no'  => $dog->reg_no,
                'source_fci_no'  => $dog->fci_no,
                'found_on'       => null,
                'confidence'     => 0,
                'checked_at'     => now(),
                'notes'          => 'No reliable result found',
            ]);
        }

        $d   = $bestResult['dog'];
        $raw = $bestResult['raw'];

        // 1) PedrooDog ment?se
        $pedrooDog = PedrooDog::create([
            'source_dog_id'  => $dog->ID,
            'source_name'    => $dog->registered_name,
            'source_reg_no'  => $dog->reg_no,
            'source_fci_no'  => $dog->fci_no,

            'real_name'      => $d['name'] ?? null,
            'real_prefix'    => $d['prefix'] ?? null,
            'real_firstname' => $d['firstname'] ?? null,
            'real_lastname'  => $d['lastname'] ?? null,

            'real_dob'       => $d['dob'] ?? null,
            'real_sex'       => $d['sex'] ?? null,
            'real_color'     => $d['color'] ?? null,
            'real_breed'     => $d['breed'] ?? null,
            'real_country'   => $d['country'] ?? null,

            'real_breeder'   => $d['breeder'] ?? null,
            'real_owner'     => $d['owner'] ?? null,
            'real_kennel'    => $d['kennel'] ?? null,

            'found_on'       => $bestResult['url'],
            'confidence'     => $bestResult['confidence'],
            'checked_at'     => now(),
            'notes'          => $bestResult['notes'],
        ]);

        // 2) Kapcsol?d? entit?sok feldolgoz?sa

        // 2/A Breeder + Owner + Kennel
        if (!empty($d['breeder'])) {
            $this->breederService->activateBreeder($d['breeder']);
        }

        if (!empty($d['owner'])) {
            $this->ownerService->activateOwner($d['owner']);
        }

        if (!empty($d['kennel'])) {
            // kutya m?g pedroo ?llapotban van, kennel csak elok?sz?l
            $this->kennelService->activateKennel($d['kennel'], null);
        }

        // 2/B Sz?lok, gyerekek, csal?dfa (csak pedroo_* t?bl?kba, ha ilyen parser adat van)
        $this->processFamilyData($pedrooDog, $raw);

        // 2/C Esem?nyek, show-k, eredm?nyek
        $this->processResultsData($pedrooDog, $raw);

        return $pedrooDog;
    }

    protected function pickNextDog(): ?Dog
    {
        $alreadyProcessedIds = PedrooDog::pluck('source_dog_id')->toArray();

        return Dog::whereNull('deleted_at')
            ->whereNotIn('ID', $alreadyProcessedIds)
            ->orderBy('ID')
            ->first();
    }

    protected function buildQueries(Dog $dog): array
    {
        $queries = [];

        if ($dog->registered_name) {
            $queries[] = $dog->registered_name;
            $queries[] = $dog->registered_name . ' dog';
            $queries[] = $dog->registered_name . ' pedigree';
            $queries[] = $dog->registered_name . ' kennel';
        }

        if ($dog->firstname && $dog->lastname) {
            $queries[] = $dog->firstname . ' ' . $dog->lastname;
            $queries[] = $dog->lastname . ' ' . $dog->firstname;
        }

        if ($dog->reg_no) {
            $queries[] = $dog->reg_no;
            $queries[] = $dog->reg_no . ' dog';
        }

        if ($dog->fci_no) {
            $queries[] = $dog->fci_no;
            $queries[] = $dog->fci_no . ' FCI';
        }

        return array_values(array_unique(array_filter($queries)));
    }

    protected function searchOnGoogle(string $query): array
    {
        $apiKey = config('services.google.key');
        $cx     = config('services.google.cx');

        $response = Http::get('https://www.googleapis.com/customsearch/v1', [
            'key' => $apiKey,
            'cx'  => $cx,
            'q'   => $query,
        ]);

        if (! $response->successful()) {
            return [];
        }

        return collect($response->json('items', []))
            ->map(fn($item) => [
                'title' => $item['title'] ?? null,
                'url'   => $item['link'] ?? null,
            ])
            ->toArray();
    }

    protected function fetchPage(string $url): ?string
    {
        try {
            $response = Http::timeout(10)->get($url);

            if (! $response->successful()) {
                return null;
            }

            return $response->body();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * FONTOS: itt m?r azt k?rj?k az AI-t?l, hogy STRUKT?R?LT JSON-t adjon:
     * {
     *   "dog": {...},
     *   "parents": [...],
     *   "children": [...],
     *   "family": [...],
     *   "kennel": {...},
     *   "breeder": {...},
     *   "owner": {...},
     *   "show_events": [...],
     *   "show_results": [...],
     *   "sport_results": [...],
     *   "working_results": [...],
     *   "behavior_results": [...],
     *   "health_records": [...]
     * }
     */
    protected function parseDogFromHtml(string $html): ?array
    {
        $prompt = <<<PROMPT
You are a strict JSON API. Extract ALL structured dog-related data from this HTML.

Return EXACTLY ONE valid JSON object with this structure:

{
  "dog": {
    "name": "...",
    "prefix": "...",
    "firstname": "...",
    "lastname": "...",
    "dob": "...",
    "sex": "...",
    "color": "...",
    "breed": "...",
    "country": "...",
    "breeder": "...",
    "owner": "...",
    "kennel": "..."
  },
  "parents": [
    {"name": "...", "relation": "sire|dam"}
  ],
  "children": [
    {"name": "..."}
  ],
  "family": [
    {"related_name": "...", "relation_type": "sibling|half-sibling|other"}
  ],
  "show_events": [
    {
      "name": "...",
      "event_type": "...",
      "country": "...",
      "city": "...",
      "date": "YYYY-MM-DD"
    }
  ],
  "show_results": [
    {
      "event_name": "...",
      "date": "YYYY-MM-DD",
      "class_type": "...",
      "qualification": "...",
      "placement": 1,
      "titles": ["CAC", "BOB"],
      "judge": "...",
      "ring": "..."
    }
  ],
  "sport_results": [],
  "working_results": [],
  "behavior_results": [],
  "health_records": [],
  "notes": "..."
}

HTML:
$html
PROMPT;

        $response = Http::withToken(env('OPENAI_KEY'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]);

        $content = $response->json()['choices'][0]['message']['content'] ?? null;

        if (! $content) {
            return null;
        }

        $json = json_decode($content, true);

        return $json ?: null;
    }

    protected function normalizeDogData(array $parsed): array
    {
        $out = $parsed;

        // DOB
        if (! empty($parsed['dob'])) {
            try {
                $out['dob'] = Carbon::parse($parsed['dob'])->format('Y-m-d');
            } catch (\Throwable $e) {
                $out['dob'] = null;
            }
        } else {
            $out['dob'] = null;
        }

        // SEX
        if (! empty($parsed['sex'])) {
            $sex = Str::lower($parsed['sex']);
            if (in_array($sex, ['m', 'male', 'kan'])) {
                $out['sex'] = 'M';
            } elseif (in_array($sex, ['f', 'female', 'szuka'])) {
                $out['sex'] = 'F';
            } else {
                $out['sex'] = 'U';
            }
        } else {
            $out['sex'] = 'U';
        }

        // COUNTRY
        if (! empty($parsed['country'])) {
            $c = Str::lower($parsed['country']);
            if (Str::contains($c, ['hungary', 'magyar'])) {
                $out['country'] = 'HU';
            } elseif (Str::contains($c, ['germany', 'deutsch'])) {
                $out['country'] = 'DE';
            } else {
                $out['country'] = strtoupper(substr($parsed['country'], 0, 2));
            }
        } else {
            $out['country'] = null;
        }

        return $out;
    }

    protected function processFamilyData(PedrooDog $pd, array $raw): void
    {
        $dogName = $pd->real_name;

        // parents ? pedroo_parents
        foreach ($raw['parents'] ?? [] as $p) {
            if (empty($p['name']) || empty($p['relation'])) {
                continue;
            }

            DB::table('pedroo_parents')->updateOrInsert(
                [
                    'child_name'  => $dogName,
                    'parent_name' => $p['name'],
                    'relation'    => $p['relation'],
                ],
                [
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // children ? pedroo_children
        foreach ($raw['children'] ?? [] as $c) {
            if (empty($c['name'])) {
                continue;
            }

            DB::table('pedroo_children')->updateOrInsert(
                [
                    'parent_name' => $dogName,
                    'child_name'  => $c['name'],
                ],
                [
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // family ? pedroo_families
        foreach ($raw['family'] ?? [] as $f) {
            if (empty($f['related_name']) || empty($f['relation_type'])) {
                continue;
            }

            DB::table('pedroo_families')->updateOrInsert(
                [
                    'dog_name'        => $dogName,
                    'related_dog_name'=> $f['related_name'],
                    'relation_type'   => $f['relation_type'],
                ],
                [
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    protected function processResultsData(PedrooDog $pd, array $raw): void
    {
        $dogName = $pd->real_name;

        // 1) Show events + show results
        foreach ($raw['show_events'] ?? [] as $event) {
            if (empty($event['name']) || empty($event['country']) || empty($event['date'])) {
                continue;
            }

            // orsz?g ID-t itt most felt?telezz?k, hogy m?r tudod kezelni (pl. countries t?bl?b?l)
            $countryId = DB::table('countries')
                ->whereRaw('LOWER(name) = ?', [strtolower($event['country'])])
                ->value('id');

            if (! $countryId) {
                continue;
            }

            $eventId = $this->eventService->processEvent((object) [
                'name'          => $event['name'],
                'event_type_id' => 35, // pl. cacib_show ? ezt k?sobb finom?thatjuk
                'country_id'    => $countryId,
                'start_date'    => $event['date'],
                'city'          => $event['city'] ?? null,
                'source'        => 'pedroo',
            ]);

            $showId = $this->showService->processShow((object) [
                'name'        => $event['name'],
                'country_id'  => $countryId,
                'city'        => $event['city'] ?? '',
                'date'        => $event['date'],
                'source'      => 'pedroo',
                'judges'      => [], // majd a show_results-b?l j?nnek
            ]);

            // most a show_results-b?l k?tj?k hozz? a konkr?t eredm?nyeket
            foreach ($raw['show_results'] ?? [] as $res) {
                if (($res['event_name'] ?? null) !== $event['name']) {
                    continue;
                }

                $classTypeId     = null; // k?sobb: mapping t?bla
                $qualificationId = null; // k?sobb: mapping t?bla
                $titlesIds       = [];   // k?sobb: mapping t?bla

                $showResultId = $this->showResultService->processShowResult((object) [
                    'show_id'          => $showId,
                    'dog_name'         => $dogName,
                    'class_type_id'    => $classTypeId,
                    'qualification_id' => $qualificationId,
                    'placement'        => $res['placement'] ?? null,
                    'judge_name'       => $res['judge'] ?? null,
                    'ring'             => $res['ring'] ?? null,
                    'titles'           => $titlesIds,
                ]);

                $this->dogResultService->processDogShowResult((object) [
                    'dog_name'       => $dogName,
                    'show_id'        => $showId,
                    'show_result_id' => $showResultId,
                ]);
            }
        }

        // 2) Sport / working / behavior / health ? most csak v?z, k?sobb finom?thatjuk
        foreach ($raw['sport_results'] ?? [] as $sr) {
            // itt is kell majd event + mapping
        }

        foreach ($raw['working_results'] ?? [] as $wr) {
            // itt is
        }

        foreach ($raw['behavior_results'] ?? [] as $br) {
            // itt is
        }

        foreach ($raw['health_records'] ?? [] as $hr) {
            // itt is
        }
    }

    protected function calculateConfidence(Dog $dog, array $data): int
    {
        $score = 0;

        if ($dog->registered_name && ! empty($data['name'])) {
            if (Str::lower($dog->registered_name) === Str::lower($data['name'])) {
                $score += 40;
            } elseif ($dog->firstname && Str::contains(Str::lower($data['name']), Str::lower($dog->firstname))) {
                $score += 20;
            }
        }

        if ($dog->reg_no && ! empty($data['reg_no']) && $dog->reg_no === ($data['reg_no'] ?? null)) {
            $score += 30;
        }

        if ($dog->fci_no && ! empty($data['fci_no']) && $dog->fci_no === ($data['fci_no'] ?? null)) {
            $score += 30;
        }

        return max(0, min(100, $score));
    }
}