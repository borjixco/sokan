<?php

namespace App\Models;

use App\Enums\TransferTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Transfer extends Model
{
    protected $fillable = [
        'unit_id',
        'lawyer',
        'reagent',
        'regulator',
        'contract_number',
        'ownership',
        'cost',
        'goodwill_rental',
        'first_witness',
        'second_witness',
        'terms',
        'mortgage_amount',
        'rental_amount',
        'duration',
        'type',
        'rental_start_at',
        'rental_end_at',
        'doing_at',
        'check_number',
        'bank',
        'warranty_amount',
        'current_account',
        'card_number'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function transferables()
    {
        return $this->hasMany(Transferable::class);
    }

    public function owners()
    {
        return $this->morphedByMany(Owner::class, 'transferable')->withTimestamps();
    }

    public function currentOwners()
    {
        return $this->morphedByMany(Owner::class, 'transferable')->where('type', 'CURRENT');
    }

    public function oldOwners()
    {
        return $this->morphedByMany(Owner::class, 'transferable')->where('type', 'OLD');
    }


    public function occupants()
    {
        return $this->morphedByMany(Occupant::class, 'transferable')->withTimestamps();
    }

    public function jobTypes()
    {
        return $this->morphToMany(JobType::class, 'job_typeable')->withTimestamps();
    }

    protected function createdAtObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $value = $attributes['created_at'];
                $label = verta()->instance($value)->format('Y/m/d H:i');
                return ['value' => $value, 'label' => $label];
            }
        );
    }
    protected function typeObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $key = $attributes['type'];
                return ['value' => $key, 'label' => TransferTypeEnum::fromKey($key)];
            }
        );
    }

    protected function rentalStartAtObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $dateExp = explode('-',verta()->instance($attributes['rental_start_at'])->format('Y-m-d'));
                return [
                    'jalali' => $attributes['rental_start_at'] ? verta()->instance($attributes['rental_start_at'])->format('Y/m/d') : null,
                    'gregorian' => $attributes['rental_start_at'] ? Carbon::parse($attributes['rental_start_at'])->format('Y/m/d') : null,
                    'object' => [
                        'year'  => $attributes['rental_start_at'] ? (int)removeFirstZeros($dateExp[0]) : null,
                        'month' => $attributes['rental_start_at'] ? (int)removeFirstZeros($dateExp[1]) : null,
                        'day'   => $attributes['rental_start_at'] ? (int)removeFirstZeros($dateExp[2]) : null,
                    ]
                ];
            }
        );
    }

    protected function rentalEndAtObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $dateExp = explode('-',verta()->instance($attributes['rental_end_at'])->format('Y-m-d'));
                return [
                    'jalali' => $attributes['rental_end_at'] ? verta()->instance($attributes['rental_end_at'])->format('Y/m/d') : null,
                    'gregorian' => $attributes['rental_end_at'] ? Carbon::parse($attributes['rental_end_at'])->format('Y/m/d') : null,
                    'object' => [
                        'year'  => $attributes['rental_end_at'] ? (int)removeFirstZeros($dateExp[0]) : null,
                        'month' => $attributes['rental_end_at'] ? (int)removeFirstZeros($dateExp[1]) : null,
                        'day'   => $attributes['rental_end_at'] ? (int)removeFirstZeros($dateExp[2]) : null,
                    ]
                ];
            }
        );
    }

    protected function doingAtObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $dateExp = explode('-',verta()->instance($attributes['doing_at'])->format('Y-m-d'));
                return [
                    'jalali' => $attributes['doing_at'] ? verta()->instance($attributes['doing_at'])->format('Y/m/d') : null,
                    'gregorian' => $attributes['doing_at'] ? Carbon::parse($attributes['doing_at'])->format('Y/m/d') : null,
                    'object' => [
                        'year'  => $attributes['doing_at'] ? (int)removeFirstZeros($dateExp[0]) : null,
                        'month' => $attributes['doing_at'] ? (int)removeFirstZeros($dateExp[1]) : null,
                        'day'   => $attributes['doing_at'] ? (int)removeFirstZeros($dateExp[2]) : null,
                    ]
                ];
            }
        );
    }

    protected function jobTypeObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return $this->jobTypes->isNotEmpty()
                    ? $this->jobTypes->map(fn ($jt) => [
                        'value' => $jt->id,
                        'label' => $jt->label,
                    ])->values()
                    : [];
            }
        );
    }

}
