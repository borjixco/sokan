<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id ?? null,
            'title'      => $this->title ?? null,
            'type'       => $this->media->mime_type ? getMimeTypeTitle($this->media->mime_type) : null,
            'size'       => $this->media->size ? formatSize($this->media->size) : null,
            'url'        => $this->media->file_path ? Storage::url($this->media->file_path) : null,
            'category'   => $this->categories ? $this->categories[0] : null,
            'createdAt'  => $this->createdAtObject ?? null,
        ];
    }
}
