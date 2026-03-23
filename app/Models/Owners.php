<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owners extends Model
{
    protected $table = 'pd_owners';

    protected $fillable = [
        'name',
        'country',
        'created_at',
        'updated_at',
    ];

    public function dogss()
    {
        return $this->hasMany(Dogs::class);
    }

    public function kennelss()
    {
        return $this->hasMany(Kennels::class);
    }

    public function breeders()
    {
        return $this->belongsToMany(Breeder::class);
    }

}
