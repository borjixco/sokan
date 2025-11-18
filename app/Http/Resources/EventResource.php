<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id ?? null,
            'title'             => $this->title ?? null,
            'location'          => $this->location ?? null,
            'description'       => $this->description ?? null,
            'short_description' => $this->short_description ?? null,
            'createdAt'         => $this->createdAtObject ?? null,
            'eventDate'          => $this->eventDateObject ?? null
        ];
    }
}
