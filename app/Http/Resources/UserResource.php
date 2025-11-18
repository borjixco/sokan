<?php

namespace App\Http\Resources;

use App\Enums\UserGenderEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $category = $this->categories ? $this->categories->first() : null;
        $supervisor = $this->supervisor ?? null;

        $data = [
            'id'            => $this->id ?? '',
            'name'          => $this->name ?? '',
            'father_name'   => $this->father_name ?? '',
            'national_code' => $this->national_code ?? '',
            'mobile'        => $this->mobile ?? '',
            'mobile2'       => $this->mobile2 ?? '',
            'email'         => $this->email ?? '',
            'avatar'        => $this->avatar ?? '',
            'gender' => [
                'value' => $this->gender ?? null,
                'label' => $this->gender ? UserGenderEnum::fromKey($this->gender)->value : null,
            ],
            'birth_date'    => $this->birthDateObject,
            'address'       => $this->address ?? '',
            'tel'           => $this->tel ?? '',
            'job_type'      => $this->jobTypes->isNotEmpty()
                ? $this->jobTypes->map(fn ($jt) => [
                    'value' => $jt->id,
                    'label' => $jt->label,
                ])->values()
                : [],
            'owners'        => $this->owners,
            'ownerUnits'    => $this->owners ? collect($this->owners)->pluck('unit')->map( fn ($item) => $item->unit_number) : null,
            'occupants'     => $this->occupants,
            'occupantUnits' => $this->occupants ? collect($this->occupants)->pluck('unit')->map( fn ($item) => $item->unit_number) : null,
            'category'      => ['id' => $category ? $category->id : null, 'name' => $category ? $category->name : null],
            'role'          => $this->roles->first() ?? null,
            'supervisor'    => ['id' => $supervisor ? $supervisor->id : null, 'name' => $supervisor ? $supervisor->name : null],
        ];

        return $data;
    }
}
