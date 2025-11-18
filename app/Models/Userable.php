<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Userable extends Model
{

    protected $fillable = ['userable_id', 'userable_type', 'user_id'];

    // تعریف رابطه پلی‌مورفیک
    public function userable()
    {
        return $this->morphTo();
    }

    // رابطه با مدل User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
