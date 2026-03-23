<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Breeders extends Model
{
    protected $table = 'pd_breeders';

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

    public function owners()
    {
        return $this->belongsToMany(Owner::class);
    }

}
