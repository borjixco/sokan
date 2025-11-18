<?php

namespace App\Models;

use App\Enums\ChargeStatusEnum;
use App\Enums\TicketStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Ticket extends Model
{
    protected $fillable = ['user_id', 'subject', 'description', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'categorizable')->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    protected function statusObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $key = $attributes['status'];
                return ['value' => $key, 'label' => TicketStatusEnum::fromKey($key)];
            }
        );
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
