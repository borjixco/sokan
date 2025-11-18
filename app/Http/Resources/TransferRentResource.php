<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferRentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $owners = [];
        $occupants = [];
        foreach ($this->currentOwners as $owner) {
            $owners[] = [
                'user'   => $owner->user,
                'unit'   => $owner->unit,
                'quota'  => $owner->quota,
                'status' => $owner->statuObject,
            ];
        }
        foreach ($this->occupants as $occupant) {
            $occupants[] = [
                'user'   => $occupant->user,
                'unit'   => $occupant->unit,
                'quota'  => $occupant->quota,
                'status' => $occupant->statuObject,
            ];
        }
        return [
            'id'              => $this->id                  ?? null,
            'unit'            => $this->unit                ?? null,
            'owners'          => $owners                    ?? [],
            'occupants'       => $occupants                 ?? [],
            'lawyer'          => $this->lawyer              ?? null,
            'regulator'       => $this->regulator             ?? null,
            'contract_number' => $this->contract_number     ?? null,
            'mortgage_amount' => $this->mortgage_amount     ?? null,
            'rental_amount'   => $this->rental_amount       ?? null,
            'first_witness'   => $this->first_witness       ?? null,
            'second_witness'  => $this->second_witness      ?? null,
            'terms'           => $this->terms               ?? null,
            'duration'        => $this->duration            ?? null,
            'check_number'    => $this->check_number        ?? null,
            'bank'            => $this->bank                ?? null,
            'warranty_amount' => $this->warranty_amount     ?? null,
            'current_account' => $this->current_account     ?? null,
            'card_number'     => $this->card_number         ?? null,
            'type'            => $this->typeObject          ?? null,
            'job_type'        => $this->jobTypeObject       ?? null,
            'rental_start_at' => $this->rentalStartAtObject ?? null,
            'rental_end_at'   => $this->rentalEndAtObject   ?? null,
            'doing_at'        => $this->doingAtObject       ?? null,
        ];
    }
}
