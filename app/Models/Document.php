<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Document extends Model
{

    protected $fillable = ['title', 'model_type', 'media_id', 'documentable_id', 'documentable_type'];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'categorizable')->withTimestamps();
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    protected function createdAtObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $value = $attributes['created_at'];
                $label = verta()->instance($value)->format('Y/m/d H:i');
                return ['value' => $value, 'label' => $label];
            }
        );
    }

}
