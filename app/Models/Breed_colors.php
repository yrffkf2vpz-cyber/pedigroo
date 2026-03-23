<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Breed_colors extends Model
{
    protected $table = 'pd_breed_colors';

    protected $fillable = [
        'breed_id',
        'color_name',
        'fci_accepted',
        'notes',
        'created_at',
        'updated_at',
    ];

    public function breed()
    {
        return $this->belongsTo(Breed::class);
    }

}
