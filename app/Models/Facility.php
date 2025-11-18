<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{

    protected $fillable = [
        'user_id',
        'title',
        'description',
    ];

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

}
