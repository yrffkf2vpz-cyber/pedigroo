<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kennels extends Model
{
    protected $table = 'pd_kennels';

    protected $fillable = [
        'name',
        'country',
        'needs_review',
        'breeder_id',
        'owner_id',
        'created_at',
        'updated_at',
    ];

    public function breeder()
    {
        return $this->belongsTo(Breeder::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function dogss()
    {
        return $this->hasMany(Dogs::class);
    }

}
