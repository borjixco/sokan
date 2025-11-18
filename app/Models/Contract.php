<?php

namespace App\Models;

use App\Enums\ContractStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Contract extends Model
{
    protected $fillable = ['title', 'company', 'cost', 'terms', 'guarantee', 'tel', 'description', 'start_at', 'end_at', 'status'];

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    protected function startAtObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $value = $attributes['start_at'];
                $label = $attributes['start_at'] ? verta()->instance($value)->format('Y/m/d') :  null;
                return ['value' => $value, 'label' => $label];
            }
        );
    }

    protected function endAtObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $value = $attributes['end_at'];
                $label = $attributes['end_at'] ? verta()->instance($value)->format('Y/m/d') : null;
                return ['value' => $value, 'label' => $label];
            }
        );
    }
    protected function statusObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $key = $attributes['status'];
                return ['value' => $key, 'label' => ContractStatusEnum::fromKey($key)];
            }
        );
    }

}
