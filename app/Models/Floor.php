<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{

    public $timestamps = false;

    protected $fillable = ['id','label'];

    public function units()
    {
        return $this->hasOne(Unit::class);
    }
}
