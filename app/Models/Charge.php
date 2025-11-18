<?php

namespace App\Models;

use App\Enums\ChargePaymentMethodEnum;
use App\Enums\ChargeStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Charge extends Model
{


    public $subject = 'شارژ';

    protected $fillable = [
        'unit_id',
        'user_id',
        'amount',
        'status',
        'payment_method',
        'reminder',
        'period',
        'due_date',
    ];

    protected $casts = [
        'amount' => 'decimal:0',
        'status' => 'string',
        'period' => 'integer',
        'due_date' => 'date',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'payable');
    }

    protected function statusObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $key = $attributes['status'];
                return ['value' => $key, 'label' => ChargeStatusEnum::fromKey($key)];
            }
        );
    }
    protected function paymentMethodObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $key = $attributes['payment_method'];
                return ['value' => $key, 'label' => ChargePaymentMethodEnum::fromKey($key)];
            }
        );
    }

    protected function periodObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $value = $attributes['period'];
                $label = verta()->instance($value)->format('Y/m/d');
                return ['value' => $value, 'label' => $label];
            }
        );
    }

    protected function dueDateObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $value = $attributes['due_date'];
                $label = verta()->instance($value)->format('Y/m/d H:i');
                return ['value' => $value, 'label' => $label];
            }
        );
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

}
