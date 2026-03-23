<?php

namespace App\Services\LearningQueue;

use App\Models\LearningQueue;
use App\Services\FuzzyMatchService;
use App\Services\AISuggestionService;
use App\Services\AIAliasGeneratorService;

class LearningQueueService
{
    public function __construct(
        protected FuzzyMatchService $fuzzy,
        protected AISuggestionService $ai,
        protected AIAliasGeneratorService $alias
    ) {}

    public function getItems(?string $domain, string $status, int $perPage)
    {
        $query = LearningQueue::query();

        if ($domain) {
            $query->where('domain', $domain);
        }

        $query->where('status', $status);

        $items = $query
            ->orderByDesc('count')
            ->orderBy('raw_input')
            ->paginate($perPage);

        // fuzzy + AI javaslatok (async-re is tehető később)
        foreach ($items as $item) {
            $item->suggested = $this->fuzzy->suggest($item->domain, $item->raw_input);

            if (!$item->ai_suggestion) {
                $item->ai_suggestion = $this->ai->suggest($item->domain, $item->raw_input);
                if ($item->ai_suggestion) {
                    $item->save();
                }
            }
        }

        return $items;
    }

    public function getDomains(): array
    {
        return LearningQueue::select('domain')
            ->distinct()
            ->pluck('domain')
            ->toArray();
    }

    public function updateItem(int $id, array $data): void
    {
        $item = LearningQueue::findOrFail($id);

        $item->normalized_input = $data['normalized_input'] ?? null;
        $item->status           = $data['status'];
        $item->last_seen_at     = now();
        $item->save();

        if ($data['status'] === 'CONFIRMED' && $item->normalized_input) {
            $this->alias->createAlias(
                $item->domain,
                $item->raw_input,
                $item->normalized_input
            );
        }
    }

    public function acceptAISuggestion(int $id): void
    {
        $item = LearningQueue::findOrFail($id);

        if (!$item->ai_suggestion) {
            return;
        }

        $item->normalized_input = $item->ai_suggestion;
        $item->status           = 'CONFIRMED';
        $item->last_seen_at     = now();
        $item->save();

        $this->alias->createAlias(
            $item->domain,
            $item->raw_input,
            $item->ai_suggestion
        );
    }
}
