<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\TransferSaleResource;
use App\Http\Resources\UnitResource;
use App\Models\Occupant;
use App\Models\Owner;
use App\Models\Setting;
use App\Models\Transfer;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransferController extends Controller
{

    public function searchUnit(Request $request)
    {
        $unit = null;
        $owners = [];
        //$occupant = null;
        if($request->unit){
            $unit = Unit::where('unit_number',$request->unit)->first();
            if($unit){
                $unit = new UnitResource($unit);
                $owners = $unit->owners()->with('user')->where('status', 'CURRENT')->get();
                //$occupant = $unit->occupants()->with('user')->where('status', 'CURRENT')->first();
            }
        }

        return responseJSon('success','',compact('unit','owners'));
    }

    public function searchUser(Request $request)
    {
        if($request->role == 'owner') {
            $ownerQuery = User::whereHas('roles', function ($query) use ($request) {
                $query->where('name', 'owner');
            });
        }
        elseif ($request->role == 'occupant'){
            $ownerQuery = User::whereHas('roles',function ($query) use($request){
                $query->whereIn('name', ['owner','occupant']);
            });
        }

        $search = request()->q;
        if($search){
            $ownerQuery = $ownerQuery->where('name','LIKE',"%$search%")->orWhere('mobile','LIKE',"%$search%");
        }
        $ownerQuery = $ownerQuery->limit(50)->get();
        $owners = [];
        foreach ($ownerQuery as $item){
            $owners[] = [
                'id'            => $item->id,
                'name'          => $item->name,
                'phone'         => $item->mobile,
                'national_code' => $item->national_code,
                'selected'      => false,
            ];
        }
        return $owners;
    }
}
