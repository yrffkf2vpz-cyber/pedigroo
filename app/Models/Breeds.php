<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Breeds extends Model
{
    protected $table = 'pd_breeds';

    protected $fillable = [
        'name',
        'normalized_name',
        'fci_id',
        'is_active',
        'notes',
        'created_at',
        'updated_at',
        'group_id',
        'origin_country',
        'size_category',
        'coat_type',
        'usage_category',
        'fci_standard_url',
        'subgroup_id',
        'status',
    ];

    public function fci()
    {
        return $this->belongsTo(Fci::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function subgroup()
    {
        return $this->belongsTo(Subgroup::class);
    }

    public function breed_color_geneticss()
    {
        return $this->hasMany(Breed_color_genetics::class);
    }

    public function breed_colorss()
    {
        return $this->hasMany(Breed_colors::class);
    }

    public function breed_rules_healths()
    {
        return $this->hasMany(Breed_rules_health::class);
    }

}
