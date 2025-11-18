<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['user_id', 'title', 'location', 'event_date', 'short_description', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function add($args)
    {
        return self::create($args);
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

    protected function eventDateObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $value = $attributes['event_date'];
                $label = verta()->instance($value)->format('Y/m/d H:i');
                return ['value' => $value, 'label' => $label];
            }
        );
    }

}
