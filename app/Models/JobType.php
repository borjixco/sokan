<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobType extends Model
{
    protected $fillable = ['id','label', 'description'];

    public function users()
    {
        return $this->morphedByMany(User::class, 'job_typeable');
    }

    public function transfers()
    {
        return $this->morphedByMany(Transfer::class, 'job_typeable');
    }
}
