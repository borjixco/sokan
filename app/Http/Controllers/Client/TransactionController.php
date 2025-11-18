<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->search;
        $rows = $user->transactions()->with(['user']);
        if($search){
            $rows = $rows->where(function ($q) use($search){
                $q->whereHas('user',function ($q) use($search){
                    return $q->where('name','LIKE',"%$search%")->orWhere('mobile',"$search");
                })
                    ->orWhere('transaction_id','LIKE',"%$search%")
                    ->orWhere('reference_id','LIKE',"%$search%");
            });
        }
        if($request->payable_type && $request->payable_id){
            $rows = $rows->where('payable_type',classPath($request->payable_type))->where('payable_id',$request->payable_id);
        }
        $rows = $rows->orderByDesc('id')->paginate(10);
        $rows = TransactionResource::collection($rows);
        return inertia('Client/Transactions/Index',compact('rows'));
    }
}
