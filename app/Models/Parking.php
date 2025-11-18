<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Parking extends Model
{


    public $subject = 'پارکینگ';
    protected $fillable = [
        'unit_id',
        'user_id',
        'car_id',
        'start_date',
        'end_date',
        'amount',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
        'status' => 'string',
    ];


    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'payable');
    }
}
