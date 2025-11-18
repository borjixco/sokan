<?php

namespace App\Http\Resources;

use App\Models\Charge;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id             ?? null,
            'unit'            => $this->unit           ?? null,
            'user'            => $this->user           ?? null,
            'amount'          => $this->amount         ?? null,
            'status'          => $this->statusObject   ?? null,
            'paymentMethod'   => $this->payment_method ? $this->paymentMethodObject : ['value' => '', 'label' => ''],
            'period'          => $this->periodObject   ?? null,
            'dueDate'         => $this->dueDateObject  ?? null,
            'transactionLink' => route('admin.transactions',['payable_type' => class_basename(Charge::class), 'payable_id' => $this->id]),
            'createdAt'       => $this->createdAtObject ?? null,
        ];
    }
}
