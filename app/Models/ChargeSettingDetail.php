<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChargeSettingDetail extends Model
{
    protected $fillable = ['charge_setting_id', 'from_area', 'to_area', 'amount'];

    public function chargeSetting()
    {
        return $this->belongsTo(ChargeSetting::class);
    }
}
