<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description'];


    public function tickets()
    {
        return $this->morphedByMany(Ticket::class, 'categorizable')->withTimestamps();
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'categorizable')->withTimestamps();
    }

    public function documents()
    {
        return $this->morphedByMany(Document::class, 'categorizable')->withTimestamps();
    }
}
