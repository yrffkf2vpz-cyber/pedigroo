<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Championships extends Model
{
    protected $table = 'pd_championships';

    protected $fillable = [
        'dog_id',
        'event_id',
        'title_definition_id',
        'country_id',
        'date',
        'source',
        'external_id',
        'created_at',
        'updated_at',
    ];

    public function dog()
    {
        return $this->belongsTo(Dog::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function title_definition()
    {
        return $this->belongsTo(Title_definition::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function external()
    {
        return $this->belongsTo(External::class);
    }

}
