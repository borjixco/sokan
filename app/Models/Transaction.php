<?php

namespace App\Models;

use App\Enums\TransactionMethodEnum;
use App\Enums\TransactionStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'payable_id',
        'payable_type',
        'amount',
        'method',
        'status',
        'gateway',
        'uuid',
        'transaction_id',
        'reference_id',
        'trace_number',
        'description',
        'card_number',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:0',
        'status' => 'string',
        'method' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function statusObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $key = $attributes['status'];
                return ['value' => $key, 'label' => TransactionStatusEnum::fromKey($key)];
            }
        );
    }

    protected function methodObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $key = $attributes['method'];
                return ['value' => $key, 'label' => TransactionMethodEnum::fromKey($key)];
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
