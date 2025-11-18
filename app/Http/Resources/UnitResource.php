<?php

namespace App\Http\Resources;

use App\Models\ChargeSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentOwner = isset($this->owners) ? $this->owners()->with('user')->where('status','CURRENT')->first() : [];
        $currentOccupant = isset($this->occupants) ? $this->occupants()->with('user')->where('status','CURRENT')->first() : [];
        $nowValue = isset($this->values) ? $this->values()->latest()->first() : [];

        $formulaChargeAmount = 0;
        if(isset($request->settingDate) && !empty($request->settingDate)){
            $settingsCharge = ChargeSetting::with('details')->whereDate('date',$request->settingDate)->first();
            if(isset($settingsCharge->details)) {
                $settingsCharge = $settingsCharge->details->where('from_area', '<=', $this->meterage)->where('to_area', '>=', $this->meterage)->first();
                if($settingsCharge) {
                    $formulaChargeAmount = $settingsCharge->amount;
                }
            }
        }


        return [
            'id'                         => $this->id,
            'number'                     => $this->unit_number,
            'floor'                      => $this->floor,
            'meterage'                   => $this->meterage,
            'position'                   => $this->position,
            'tel'                        => $this->tel,
            'status'                     => $this->statusObject,
            'operation'                  => $this->operationObject,
            'block'                      => $this->blockObject,
            'type'                       => $this->typeObject,
            'postal_code'                => $this->postal_code,
            'meter_serial_number'        => $this->meter_serial_number,
            'roof'                       => $this->roofObject,
            'owner'                      => $currentOwner,
            'occupant'                   => $currentOccupant,
            'value_per_meter'            => $this->value_per_meter,
            'total_value'                => $this->total_value,
            'now_total_value'            => $nowValue?->total_value,
            'now_value_per_meter'        => $nowValue?->value_per_meter,
            'maximum_full_mortgage'      => $this->maximum_full_mortgage,
            'maximum_monthly_rent'       => $this->maximum_monthly_rent,
            'owner_annual_goodwill_rent' => $this->owner_annual_goodwill_rent,
            'sale_price_suggested_owner' => $this->sale_price_suggested_owner,
            'owner_proposed_mortgage'    => $this->owner_proposed_mortgage,
            'rent_proposed_owner'        => $this->rent_proposed_owner,
            'charge_amount'              => $this->charge_amount,
            'formula_charge_amount'      => $formulaChargeAmount,
            'computer_password'          => $this->computer_password,
            'case'                       => $this->case,
        ];
    }
}
