<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferSaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        foreach ($this->currentOwners as $owner) {
            $currentOwners[] = [
                'user'   => $owner->user,
                'unit'   => $owner->unit,
                'quota'  => $owner->quota,
                'status' => $owner->statuObject,
            ];
        }
        foreach ($this->oldOwners as $owner) {
            $oldOwners[] = [
                'user'   => $owner->user,
                'unit'   => $owner->unit,
                'quota'  => $owner->quota,
                'status' => $owner->statuObject,
            ];
        }
        return [
            'id'              => $this->id                  ?? null,
            'unit'            => $this->unit                ?? null,
            'current_owners'  => $currentOwners             ?? [],
            'old_owners'      => $oldOwners                 ?? [],
            'lawyer'          => $this->lawyer              ?? null,
            'reagent'         => $this->reagent             ?? null,
            'contract_number' => $this->contract_number     ?? null,
            'ownership'       => $this->ownership           ?? null,
            'cost'            => $this->cost                ?? null,
            'goodwill_rental' => $this->goodwill_rental     ?? null,
            'first_witness'   => $this->first_witness       ?? null,
            'second_witness'  => $this->second_witness      ?? null,
            'terms'           => $this->terms               ?? null,
            'type'            => $this->typeObject          ?? null,
            'rental_start_at' => $this->rentalStartAtObject ?? null,
            'rental_end_at'   => $this->rentalEndAtObject   ?? null,
            'doing_at'        => $this->doingAtObject       ?? null,
        ];
    }
}
