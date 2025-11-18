<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transferable extends Model
{
    protected $table = 'transferables';

    protected $fillable = [
        'transfer_id',
        'transferable_id',
        'transferable_type',
        'type',
    ];

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

    public function transferable()
    {
        return $this->morphTo();
    }
}
