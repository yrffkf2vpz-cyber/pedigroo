<?php

namespace App\Services\Breeding;

use App\Models\Breed;
use App\Models\BreedingRule;
use Illuminate\Support\Arr;

class DefaultRuleGenerator
{
    public function generateForBreed(Breed $breed): void
    {
        $type = $breed->recognition_type ?? 'fci';

        $rules = match ($type) {
            'fci'        => $this->forFci($breed),
            'akc', 'ukc', 'kc' => $this->forKennelClub($breed),
            'designer'   => $this->forDesigner($breed),
            'landrace'   => $this->forLandrace($breed),
            'experimental' => $this->forExperimental($breed),
            default      => $this->forLandrace($breed),
        };

        $this->storeRules($breed, $rules);
    }

    protected function storeRules(Breed $breed, array $rules): void
    {
        foreach ($rules as $key => $value) {
            // ha már van ilyen rule_key, nem írjuk felül (admin override tiszteletben tartása)
            BreedingRule::firstOrCreate(
                [
                    'breed_id' => $breed->id,
                    'rule_key' => $key,
                ],
                [
                    'value' => $value,
                ]
            );
        }
    }

    protected function forFci(Breed $breed): array
    {
        // ide jöhet fajtaspecifikus FCI sablon, ha akarsz switch-elni fci_id-re
        return $this->baseTemplate();
    }

    protected function forKennelClub(Breed $breed): array
    {
        // ha később lesz AKC/UKC adat, itt tudsz specializálni
        return $this->landraceTemplate();
    }

    protected function forLandrace(Breed $breed): array
    {
        return $this->landraceTemplate();
    }

    protected function forExperimental(Breed $breed): array
    {
        return $this->emptyTemplate();
    }

    protected function forDesigner(Breed $breed): array
    {
        // feltételezzük, hogy van parent1_id, parent2_id a breeds táblában vagy pivotban
        $parent1 = $breed->parent1 ?? null;
        $parent2 = $breed->parent2 ?? null;

        if (!$parent1 || !$parent2) {
            // ha nincs szülő, essünk vissza landrace-re
            return $this->landraceTemplate();
        }

        $p1 = $this->getExistingRulesAsArray($parent1);
        $p2 = $this->getExistingRulesAsArray($parent2);

        $requiredTests = $this->mergeTests(
            Arr::get($p1, 'required_tests', ''),
            Arr::get($p2, 'required_tests', '')
        );

        $allowedColors = $this->intersectLists(
            Arr::get($p1, 'allowed_colors', 'any'),
            Arr::get($p2, 'allowed_colors', 'any')
        );

        return [
            'min_age_male'          => min(
                (int) Arr::get($p1, 'min_age_male', 18),
                (int) Arr::get($p2, 'min_age_male', 18)
            ),
            'min_age_female'        => min(
                (int) Arr::get($p1, 'min_age_female', 20),
                (int) Arr::get($p2, 'min_age_female', 20)
            ),
            'max_age_male'          => max(
                (int) Arr::get($p1, 'max_age_male', 96),
                (int) Arr::get($p2, 'max_age_male', 96)
            ),
            'max_age_female'        => max(
                (int) Arr::get($p1, 'max_age_female', 84),
                (int) Arr::get($p2, 'max_age_female', 84)
            ),
            'max_coi_percent'       => 3,
            'allowed_colors'        => $allowedColors,
            'disallowed_colors'     => 'merle_merle,albino,extreme_white',
            'required_tests'        => $requiredTests,
            'max_litters_female'    => 4,
            'rest_after_litter_months' => 12,
        ];
    }

    protected function baseTemplate(): array
    {
        return [
            'min_age_male'          => 18,
            'min_age_female'        => 20,
            'max_age_male'          => 96,
            'max_age_female'        => 84,
            'max_coi_percent'       => 6,
            'allowed_colors'        => 'any',
            'disallowed_colors'     => 'merle_merle,albino,extreme_white',
            'required_tests'        => '',
            'max_litters_female'    => 6,
            'rest_after_litter_months' => 10,
        ];
    }

    protected function landraceTemplate(): array
    {
        return [
            'min_age_male'          => 18,
            'min_age_female'        => 20,
            'max_age_male'          => 96,
            'max_age_female'        => 84,
            'max_coi_percent'       => 8,
            'allowed_colors'        => 'any',
            'disallowed_colors'     => 'merle_merle,albino',
            'required_tests'        => 'HD,ED,Eye',
            'max_litters_female'    => 6,
            'rest_after_litter_months' => 10,
        ];
    }

    protected function emptyTemplate(): array
    {
        return [
            'min_age_male'          => null,
            'min_age_female'        => null,
            'max_age_male'          => null,
            'max_age_female'        => null,
            'max_coi_percent'       => null,
            'allowed_colors'        => null,
            'disallowed_colors'     => null,
            'required_tests'        => null,
            'max_litters_female'    => null,
            'rest_after_litter_months' => null,
        ];
    }

    protected function getExistingRulesAsArray(Breed $breed): array
    {
        return $breed->breedingRules
            ->pluck('value', 'rule_key')
            ->toArray();
    }

    protected function mergeTests(string $tests1, string $tests2): string
    {
        $a = $tests1 ? array_map('trim', explode(',', $tests1)) : [];
        $b = $tests2 ? array_map('trim', explode(',', $tests2)) : [];

        $merged = array_values(array_unique(array_filter(array_merge($a, $b))));
        return implode(',', $merged);
    }

    protected function intersectLists(string $list1, string $list2): string
    {
        if ($list1 === 'any' && $list2 === 'any') {
            return 'any';
        }

        if ($list1 === 'any') {
            return $list2;
        }

        if ($list2 === 'any') {
            return $list1;
        }

        $a = array_map('trim', explode(',', $list1));
        $b = array_map('trim', explode(',', $list2));

        $intersect = array_values(array_intersect($a, $b));

        return empty($intersect) ? 'any' : implode(',', $intersect);
    }
}
