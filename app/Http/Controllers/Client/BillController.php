<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientBillResource;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $rows = $user->bills()->with('user');
        $search = $request->search;
        if($search){
        }
        $rows = $rows->orderByDesc('id')->paginate(10);
        $rows = ClientBillResource::collection($rows);
        return inertia('Client/Bills/Index', compact('rows'));
    }
}
