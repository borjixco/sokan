<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillResource;
use App\Jobs\SendSmsForBillJob;
use App\Models\Bill;
use App\Models\Charge;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $years = years(true);
        $months = months();
        $days = days();
        $rows = Bill::with('user');
        $search = $request->search;
        if($search){
            $rows = $rows->whereHas('user',function ($q) use($search){
                $q->where('name','LIKE', "%$search%")
                    ->orWhere('mobile','LIKE', "%$search%")
                    ->orWhere('national_code','LIKE', "%$search%");
            });
        }
        $rows = $rows->orderByDesc('id')->paginate(auth()->user()->perPage('admin',Bill::class));
        $rows = BillResource::collection($rows);
        $perPages = perPages();
        return inertia('Admin/Bills/Index', compact('rows','years', 'months', 'days', 'perPages'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        try {
            $request->validate([
                'title'  => 'required|min:3',
                'userId' => 'exists:users,id',
                'amount' => 'required|integer',
                'year'   => 'required|integer',
                'month'  => 'required|integer',
                'day'    => 'required|integer',
            ],
            [
                'title' => 'موضوع قبض را به درستی وارد نمایید',
                'userId' => 'پرداخت کننده را انتخاب نمایید',
                'amount' => 'مبلغ را به درستی وارد نمایید',
                'year' => 'سال را وارد نمایید',
                'month' => 'ماه را وارد نمایید',
                'day' => 'روز را وارد نمایید',
            ]);
            $dueDate = verta()->parse("{$request->year}-{$request->month}-{$request->day}")->toCarbon()->endOfDay();
            $bill = Bill::create([
                'user_id'  => $request->userId,
                'title'    => $request->title,
                'amount'   => $request->amount,
                'status'   => 'SENDING',
                'due_date' => $dueDate,
            ]);
            $description = 'افزایش موجودی از طریق درگاه';
            $transactionResult = (new TransactionService)->onlinePayment($request->userId,Bill::class,$bill->id,$request->amount,'GATEWAY', $description);
            SendSmsForBillJob::dispatch($bill,$transactionResult['path']);
            return redirectMessage('success','قبض بدهی صادر شد');
        }
        catch (ValidationException $e){
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }
    }

    public function userSearch(Request $request)
    {
        $query = User::query();
        $search = $request->q;
        if($search){
            $query = $query->where('name','LIKE',"%$search%")->orWhere('mobile','LIKE',"%$search%");
        }
        $query = $query->limit(20)->get();
        $users = [];
        foreach ($query as $user){
            $users[] = [
                'id'            => $user->id,
                'name'          => $user->name,
                'phone'         => $user->mobile,
                'national_code' => $user->national_code,
                'selected'      => false,
            ];
        }
        return $users;
    }
}
