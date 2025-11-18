<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $startAt = explode('/',$this->startAtObject['label']);
        $endAt = explode('/',$this->startAtObject['label']);
        return [
            'id'          => $this->id ?? null,
            'title'       => $this->title ?? null,
            'company'     => $this->company ?? null,
            'cost'        => $this->cost ?? null,
            'terms'       => $this->terms ?? null,
            'guarantee'   => $this->guarantee ?? null,
            'tel'         => $this->tel ?? null,
            'description' => $this->description ?? null,
            'startAt'     => $this->startAtObject ?? null,
            'startYear'   => $startAt[0] ?? null,
            'startMonth'  => $startAt[1] ?? null,
            'startDay'    => $startAt[2] ?? null,
            'endYear'     => $endAt[0] ?? null,
            'endMonth'    => $endAt[1] ?? null,
            'endDay'      => $endAt[2] ?? null,
            'endAt'       => $this->endAtObject ?? null,
        ];
    }
}
