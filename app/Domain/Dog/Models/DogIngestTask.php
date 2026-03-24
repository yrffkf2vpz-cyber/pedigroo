<?php

namespace App\Domain\Dog\Models;

use Illuminate\Database\Eloquent\Model;

class DogIngestTask extends Model
{
    protected $fillable = [
        'pedroo_dog_id',
        'status',
        'attempts',
        'last_error',
    ];

    public function dog()
    {
        return $this->belongsTo(PedrooDog::class, 'pedroo_dog_id');
    }
}
