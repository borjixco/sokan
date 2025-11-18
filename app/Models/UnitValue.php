<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitValue extends Model
{

    protected $fillable = ['unit_id', 'date', 'value_per_meter', 'total_value'];
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
