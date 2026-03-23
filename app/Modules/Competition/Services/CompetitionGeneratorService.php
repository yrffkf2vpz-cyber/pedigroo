<?php

namespace App\Modules\Competition\Services;

use App\Modules\Competition\Models\Competition;
use App\Modules\Competition\Models\CompetitionCategory;
use App\Modules\Competition\Services\CompetitionService;
use Carbon\Carbon;

class CompetitionGeneratorService
{
    public function generateScheduledCompetitions()
    {
        $now = Carbon::now();

        // 1) Kategóriák lekérése, amelyekbol automatikusan generálunk
        $categories = CompetitionCategory::where('is_active', true)
            ->where('auto_generate', true)
            ->get();

        foreach ($categories as $category) {
            if ($this->shouldGenerate($category, $now)) {
                $this->createCompetitionFromCategory($category, $now);
            }
        }
    }

    private function shouldGenerate(CompetitionCategory $category, Carbon $now): bool
    {
        return match ($category->generate_frequency) {
            'daily'    => $now->isStartOfDay(),
            'weekly'   => $now->isMonday() && $now->hour === 0,
            'monthly'  => $now->isStartOfMonth(),
            'seasonal' => $this->isSeasonStart($now),
            'yearly'   => $now->isStartOfYear(),
            'trending' => $this->isTrendingCategory($category),
            default    => false,
        };
    }

    private function isSeasonStart(Carbon $now): bool
    {
        return in_array($now->format('m-d'), [
            '03-01', // tavasz
            '06-01', // nyár
            '09-01', // osz
            '12-01', // tél
        ]);
    }

    private function isTrendingCategory(CompetitionCategory $category): bool
    {
        // Itt késobb AI / statisztika / engagement alapú logika lesz
        return rand(0, 100) < $category->ai_weight;
    }

    private function createCompetitionFromCategory(CompetitionCategory $category, Carbon $now)
    {
        $service = app(CompetitionService::class);

        $title = $this->generateTitle($category, $now);

        $competition = $service->createCompetition([
            'category_id'       => $category->id,
            'title'             => $title,
            'description'       => $category->description,
            'starts_at'         => $now,
            'ends_at'           => $now->copy()->addDays(7),
            'status'            => 'active',
            'is_auto_generated' => true,
        ]);

        return $competition;
    }

    private function generateTitle(CompetitionCategory $category, Carbon $now): string
    {
        return match ($category->generate_frequency) {
            'daily'    => "{$category->name} – {$now->format('Y.m.d')}",
            'weekly'   => "{$category->name} – {$now->format('Y. W. hét')}",
            'monthly'  => "{$category->name} – {$now->format('Y. F')}",
            'seasonal' => "{$category->name} – {$this->seasonName($now)}",
            'yearly'   => "{$category->name} – {$now->year}",
            'trending' => "Trending: {$category->name}",
            default    => $category->name,
        };
    }

    private function seasonName(Carbon $now): string
    {
        return match (true) {
            $now->between(Carbon::parse('03-01'), Carbon::parse('05-31')) => 'Tavasz',
            $now->between(Carbon::parse('06-01'), Carbon::parse('08-31')) => 'Nyár',
            $now->between(Carbon::parse('09-01'), Carbon::parse('11-30')) => 'Osz',
            default => 'Tél',
        };
    }

    public function finishExpiredCompetitions()
    {
        $now = Carbon::now();

        $expired = Competition::where('status', 'active')
            ->where('ends_at', '<=', $now)
            ->get();

        $service = app(CompetitionService::class);

        foreach ($expired as $competition) {
            $service->finishCompetition($competition);
        }
    }
}
