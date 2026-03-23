<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdOwner extends Model
{
    protected $table = 'pd_owners';

    protected $fillable = [
        'name',             // raw név
        'normalized_name',  // tisztított név
        'country_id',       // ország ID (pd_countries)
        'kennel_id',        // ha kennelhez tartozik
        'is_person',        // bool
        'is_organization',  // bool
        'source',           // import / user / registry
        'fuzzy_key',        // fuzzy matching alap
    ];

    protected $casts = [
        'is_person'       => 'boolean',
        'is_organization' => 'boolean',
    ];
}