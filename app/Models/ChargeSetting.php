<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChargeSetting extends Model
{
    protected $fillable = ['date', 'base_amount'];

    public function details()
    {
        return $this->hasMany(ChargeSettingDetail::class);
    }
}
