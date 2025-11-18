<?php

namespace App\Models;

use App\Enums\OwnerStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $fillable = [
        'user_id',
        'unit_id',
        'quota',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function transfers()
    {
        return $this->morphToMany(Transfer::class, 'transferable');
    }
    protected function statusObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $key = $attributes['status'];
                return ['value' => $key, 'label' => OwnerStatusEnum::fromKey($key)];
            }
        );
    }

}
