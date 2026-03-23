<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Health_records extends Model
{
    protected $table = 'pd_health_records';

    protected $fillable = [
        'dog_id',
        'record_type_id',
        'result_code_id',
        'created_at',
        'updated_at',
    ];

    public function dog()
    {
        return $this->belongsTo(Dog::class);
    }

    public function record_type()
    {
        return $this->belongsTo(Record_type::class);
    }

    public function result_code()
    {
        return $this->belongsTo(Result_code::class);
    }

}
