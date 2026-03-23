<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Children extends Model
{
    protected $table = 'pd_children';

    protected $fillable = [
        'parent_id',
        'child_id',
        'created_at',
        'updated_at',
    ];

    public function parent()
    {
        return $this->belongsTo(Parent::class);
    }

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

}
