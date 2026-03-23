<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dog_event_results extends Model
{
    protected $table = 'pd_dog_event_results';

    protected $fillable = [
        'dog_id',
        'show_id',
        'show_result_id',
        'created_at',
        'updated_at',
    ];

    public function dog()
    {
        return $this->belongsTo(Dog::class);
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function show_result()
    {
        return $this->belongsTo(Show_result::class);
    }

}
