<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Breed_color_genetics extends Model
{
    protected $table = 'pd_breed_color_genetics';

    protected $fillable = [
        'breed_id',
        'gene',
        'genotype',
        'description',
        'created_at',
        'updated_at',
    ];

    public function breed()
    {
        return $this->belongsTo(Breed::class);
    }

}
