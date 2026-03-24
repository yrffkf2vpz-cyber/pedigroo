<?php

namespace App\Domain\Dog\Models;

use Illuminate\Database\Eloquent\Model;

class Dog_behavior_results extends Model
{
    protected $table = 'pd_dog_behavior_results';

    protected $fillable = [
        'dog_id',
        'test_type_id',
        'result',
        'created_at',
        'updated_at',
    ];

    public function dog()
    {
        return $this->belongsTo(Dog::class);
    }

    public function test_type()
    {
        return $this->belongsTo(Test_type::class);
    }

}
