<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Car extends Model
{
    protected $fillable = [
        'user_id',
        'model',
        'color',
        'plate_number',
    ];

    protected $casts = [
        'model' => 'string',
        'color' => 'string',
        'plate_number' => 'string',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
