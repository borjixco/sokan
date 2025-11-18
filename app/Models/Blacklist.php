<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blacklist extends Model
{
    protected $fillable = ['user_id', 'ip_address', 'count'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
