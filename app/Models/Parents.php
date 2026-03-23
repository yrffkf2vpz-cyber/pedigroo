<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parents extends Model
{
    protected $table = 'pd_parents';

    protected $fillable = [
        'dog_id',
        'parent_id',
        'relation',
        'created_at',
        'updated_at',
    ];

    public function dog()
    {
        return $this->belongsTo(Dog::class);
    }

    public function parent()
    {
        return $this->belongsTo(Parent::class);
    }

    public function childrens()
    {
        return $this->hasMany(Children::class);
    }

    public function parentss()
    {
        return $this->hasMany(Parents::class);
    }

    public function dogs()
    {
        return $this->belongsToMany(Dog::class);
    }

}
