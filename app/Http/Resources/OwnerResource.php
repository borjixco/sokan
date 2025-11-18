<?php

namespace App\Http\Resources;

use App\Enums\OwnerStatusEnum;
use App\Enums\UserGenderEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OwnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $transfer = $this->transfers->first();
        $data = [
            'id'              => $this->id ?? null,
            'ownership'       => $transfer->ownership ?? null,
            'doing_at'        => $transfer->doingAtObject ?? null,
            'goodwill_rental' => $transfer->goodwill_rental ?? null,
            'status'          => $this->statusObject,
            'units'           => $this->units,
            'lawyer'          => $this->lawyer ?? null,
            'reagent'         => $this->reagent ?? null,
            'quota'           => $this->quota ?? null
        ];
        if($this->user){

            $data['user'] = [
                'id'            => $this->user->id,
                'name'          => $this->user->name,
                'father_name'   => $this->user->father_name,
                'national_code' => $this->user->national_code,
                'mobile'        => $this->user->mobile,
                'mobile2'       => $this->user->mobile2,
                'email'         => $this->user->email,
                'avatar'        => $this->user->avatar,
                'birth_date'    => $this->user->birthDateObject,
                'gender'        => $this->user->genderObject,
                'address'       => $this->user->address,
                'tel'           => $this->user->tel,
                'job_types'     => jobTypesObject($this->user->jobTypes)
            ];
        }
        return $data;
    }
}
