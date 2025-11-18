<?php

namespace App\Http\Resources;

use App\Enums\OccupantStatusEnum;
use App\Enums\UserGenderEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OccupantResource extends JsonResource
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
            'id'                => $this->id ?? null,
            'name'              => $this->name ?? null,
            'national_code'     => $this->national_code ?? null,
            'units'             => $this->units,
            'rental_start_date' => $transfer->rentalStartAtObject ?? null,
            'rental_end_date'   => $transfer->rentalEndDate ?? null,
            'doing_at'          => $transfer->doingAtObject ?? null,
            'mortgage_amount'   => $this->mortgage_amount ?? null,
            'rental_amount'     => $this->rental_amount ?? null,
            'status'            => $this->statusObject ?? null,
            'lawyer'            => $this->lawyer ?? null
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
                'birth_date'    => $this->birthDateObject,
                'gender'        => $this->user->genderObject,
                'address'       => $this->user->address,
                'tel'           => $this->user->tel,
                'job_types'     => jobTypesObject($this->user->jobTypes)
            ];
        }
        return $data;
    }
}
