<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Breed extends Model
{
    protected $table = 'pd_breeds';

    protected $fillable = [
        'name',
        'normalized_name',
        'fci_id',
        'is_active',
        'notes',
        'group_id',
        'origin_country',
        'size_category',
        'coat_type',
        'usage_category',
        'fci_standard_url',
        'subgroup_id',
        'status',
        'recognition_type',
        // ha designer fajtákat is akarsz:
        'parent1_id',
        'parent2_id',
    ];

    // 🔥 EZ HIÁNYZOTT — a DefaultRuleGenerator ezt használja
    public function breedingRules()
    {
        return $this->hasMany(BreedingRule::class, 'breed_id');
    }

    // 🔥 Designer fajtákhoz (opcionális, de ajánlott)
    public function parent1()
    {
        return $this->belongsTo(Breed::class, 'parent1_id');
    }

    public function parent2()
    {
        return $this->belongsTo(Breed::class, 'parent2_id');
    }
}

