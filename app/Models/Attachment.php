<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    protected $fillable = [
        'user_id',
        'attachable_id',
        'attachable_type',
        'file_path',
        'file_name',
        'mime_type',
        'size'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
