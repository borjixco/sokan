<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $messages = null;
        if($this->messages){
            foreach ($this->messages as $message){
                $messages[] = [
                    'message'   => $message->message,
                    'type'      => $this->user_id === $message->user_id ? 'user' : 'support',
                    'user'      => $message->user,
                    'createdAt' => $message->createdAtObject,
                ];
            }
        }
        return [
            'id'         => $this->id           ?? null,
            'user'       => $this->user         ?? null,
            'subject'    => $this->subject      ?? null,
            'status'     => $this->statusObject ?? null,
            'category'   => $this->categories   ? $this->categories[0] : null,
            'messages'   => $messages,
            'createdAt'  => $this->createdAtObject ?? null,
        ];
    }
}
