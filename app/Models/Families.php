<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Families extends Model
{
    protected $table = 'pd_families';

    protected $fillable = [
        'dog_id',
        'related_dog_id',
        'relation_type',
        'created_at',
        'updated_at',
    ];

    public function dog()
    {
        return $this->belongsTo(Dog::class);
    }

    public function related_dog()
    {
        return $this->belongsTo(Related_dog::class);
    }

}
