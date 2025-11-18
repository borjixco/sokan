<?php

namespace App\Http\Resources;

use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id              ?? null,
            'title'           => $this->title           ?? null,
            'user'            => $this->user            ?? null,
            'amount'          => $this->amount          ?? null,
            'status'          => $this->statusObject    ?? null,
            'dueDate'         => $this->dueDateObject   ?? null,
            'transactionLink' => route('admin.transactions',['payable_type' => class_basename(Bill::class), 'payable_id' => $this->id]),
            'createdAt'       => $this->createdAtObject ?? null,
        ];
    }
}
