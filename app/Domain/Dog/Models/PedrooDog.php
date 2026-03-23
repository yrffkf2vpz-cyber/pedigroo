<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedrooDog extends Model
{
    protected $table = 'pedroo_dogs';

    protected $fillable = [
        'real_name',
        'real_prefix',
        'real_firstname',
        'real_lastname',
        'owner_id',
        'breed_id',
        'sex',
        'dob',
        'reg_no',
        'source_country',
        'name_order_id',
        'owner_kennel',
        'needs_review',
    ];

    public function owner()
    {
        return $this->belongsTo(PedrooOwner::class, 'owner_id');
    }
}
