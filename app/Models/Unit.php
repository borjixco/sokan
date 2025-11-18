<?php

namespace App\Models;

use App\Enums\UnitBlockEnum;
use App\Enums\UnitOperationEnum;
use App\Enums\UnitRoofEnum;
use App\Enums\UnitStatusEnum;
use App\Enums\UnitTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Unit extends Model
{

    protected $fillable = ['unit_number', 'floor_id', 'meterage', 'unit_type', 'tel', 'postal_code', 'position_id', 'meter_serial_number', 'value_per_meter', 'total_value', 'maximum_full_mortgage', 'maximum_monthly_rent', 'owner_annual_goodwill_rent', 'sale_price_suggested_owner', 'owner_proposed_mortgage', 'rent_proposed_owner', 'charge_amount','roof','status', 'operation', 'block','computer_password', 'case'];

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function values()
    {
        return $this->hasMany(UnitValue::class);
    }

    public function owners()
    {
        return $this->hasMany(Owner::class);
    }

    public function occupants()
    {
        return $this->hasMany(Occupant::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'categorizable')->withTimestamps();
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }


    protected function typeObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $key = $attributes['unit_type'];
                return ['value' => $key, 'label' => $key ? UnitTypeEnum::fromKey($key) : null];
            }
        );
    }

    protected function statusObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $key = isset($attributes['status']) ? $attributes['status'] : '';
                return ['value' => $key, 'label' => $key ? UnitStatusEnum::fromKey($key) : null];
            }
        );
    }

    protected function operationObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $key = isset($attributes['operation']) ? $attributes['operation'] : '';
                return ['value' => $key, 'label' => $key ? UnitOperationEnum::fromKey($key) : $key];
            }
        );
    }

    protected function blockObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $key = isset($attributes['block']) ? $attributes['block'] : '';
                return ['value' => $key, 'label' => $key ? UnitBlockEnum::fromKey($key) : null];
            }
        );
    }
    protected function roofObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $key = isset($attributes['roof']) ? $attributes['roof'] : '';
                return ['value' => $key, 'label' => $key ? UnitRoofEnum::fromKey($key) : null];
            }
        );
    }
}
