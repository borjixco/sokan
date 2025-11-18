<?php

namespace App\Services;

use App\Jobs\SendSmsForChargeUnitJob;
use App\Models\Charge;
use App\Models\ChargeSetting;
use App\Models\Setting;
use Hekmatinasser\Verta\Verta;

class ChargeService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function generateCharge($unit,$chargeSetting,$existence,$dueDate)
    {
        if($unit->operation == 'EMPTY'){
            $amount = $chargeSetting->base_amount;
        }
        elseif($unit->charge_amount > 0){
            $amount =  $unit->charge_amount;
        }
        else{
            $amount = $chargeSetting->details()->where('from_area','<=',(int)$unit->meterage)->where('to_area','>=',(int)$unit->meterage)->first()->amount;
        }

        $charge = Charge::create([
            'unit_id'      => $unit->id,
            'user_id'      => $existence->user->id,
            'amount'       => $amount,
            'status'       => 'SENDING',
            'period'       => Verta::now()->startMonth()->toCarbon(),
            'due_date'     => $dueDate,
        ]);
        $description = 'افزایش موجودی از طریق درگاه';
        $transactionResult = (new TransactionService)->onlinePayment($existence->user->id,Charge::class,$charge->id,$amount,'GATEWAY', $description);

        $data = [
            'name'        => $existence->user->name,
            'mobile'      => $existence->user->mobile,
            'meterage'    => $unit->meterage,
            'amount'      => $amount,
            'paymentLink' => $transactionResult['path'],
        ];
        SendSmsForChargeUnitJob::dispatch($charge,$transactionResult['path']);
        return $data;
    }

    public function control($unit,$dueDate)
    {
        $result = [];
        $setting = Setting::get('admin.general');
        $chargeSetting = ChargeSetting::with('details')->whereDate('date',Verta::now()->startMonth()->toCarbon())->first();
        if($unit->occupants->count()){
            $result = $this->generateCharge($unit, $chargeSetting, $unit->occupants[0], $dueDate);
        }
        elseif($unit->owners->count()){
            $result = $this->generateCharge($unit,$chargeSetting,$unit->owners[0],$dueDate);
        }
        return $result;
    }

    public function createNewPayment($charge): void
    {
        $transactionResult = (new TransactionService)->onlinePayment($charge->user->id,Charge::class,$charge->id,$charge->amount,'GATEWAY', 'افزایش موجودی از طریق درگاه');
        SendSmsForChargeUnitJob::dispatch($charge,$transactionResult['path']);

    }

}
