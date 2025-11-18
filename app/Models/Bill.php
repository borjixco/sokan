<?php

namespace App\Models;

use App\Enums\BillStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Bill extends Model
{

    public $subject = 'قبض بدهی';

    protected $fillable = [
        'user_id',
        'title',
        'amount',
        'status',
        'due_date',
    ];

    protected $casts = [
        'amount' => 'decimal:0',
        'status' => 'string',
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
                return ['value' => $key, 'label' => BillStatusEnum::fromKey($key)];
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
