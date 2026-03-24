<?php

namespace App\Domain\Dog\Models;

use Illuminate\Database\Eloquent\Model;

class PdDog extends Model
{
    protected $table = 'pd_dogs';

    protected $fillable = [
        'prefix',
        'firstname',
        'lastname',
        'owner_kennel',
        'name_order_id',
        'breed_id',
        'sex',
        'dob',
        'reg_no',
        'owner_id',
        'breeder_id',
    ];
}
