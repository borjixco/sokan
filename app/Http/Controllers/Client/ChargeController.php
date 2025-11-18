<?php

namespace App\Http\Controllers\Client;

use App\Enums\ChargeStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChargeResource;
use App\Http\Resources\ClientChargeResource;
use App\Models\Charge;
use App\Models\ChargeSetting;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;

class ChargeController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->search;

        $rows = $user->charges()->with(['unit','user'])->whereIn('status',['UNPAID', 'PAID', 'OVERDUE', 'CANCELED']);
        if($search){
            $rows = $rows->whereHas('unit',function ($q) use($search){
                return $q->where('unit_number',"$search");
            });
        }
        $rows = $rows->orderByDesc('id')->paginate(10);

        $rows = ClientChargeResource::collection($rows);
        return inertia('Client/Charges/Index', compact('rows'));
    }
}
