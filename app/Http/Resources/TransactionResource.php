<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id ?? null,
            'user'          => $this->user ?? null,
            'payableType'   => $this->payable_type ?? null,
            'payableTitle'  => $this->payable_type ? (new $this->payable_type)->subject : null,
            'payableId'     => $this->payable_id ?? null,
            'amount'        => $this->amount ?? null,
            'method'        => $this->methodObject,
            'status'        => $this->statusObject,
            'transactionId' => $this->transaction_id,
            'referenceId'   => $this->reference_id,
            'description'   => $this->description,
            'cardNumber'    => $this->card_number,
            'createdAt'     => $this->createdAtObject,
        ];
    }
}
