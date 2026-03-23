<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBreedRuleRequest;
use App\Models\Breed;
use App\Models\BreedingRule;
use App\Services\Breeding\DefaultRuleGenerator;

class BreedRuleController extends Controller
{
    public function index(Breed $breed)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'breed' => $breed,
                'rules' => $breed->breedingRules()->orderBy('rule_key')->get(),
            ],
        ]);
    }

    public function store(StoreBreedRuleRequest $request, Breed $breed)
    {
        $data = $request->validated();

        $rule = BreedingRule::updateOrCreate(
            [
                'breed_id' => $breed->id,
                'rule_key' => $data['rule_key'],
            ],
            [
                'value' => $data['value'],
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $rule,
        ]);
    }

    public function destroy(Breed $breed, BreedingRule $rule)
    {
        if ($rule->breed_id !== $breed->id) {
            abort(403, 'Rule does not belong to this breed.');
        }

        $rule->delete();

        return response()->json(['success' => true]);
    }

    public function generateDefaults(Breed $breed, DefaultRuleGenerator $generator)
    {
        $generator->generateForBreed($breed);

        return response()->json([
            'success' => true,
            'message' => 'Default rules generated.',
            'data' => $breed->breedingRules()->get(),
        ]);
    }
}

