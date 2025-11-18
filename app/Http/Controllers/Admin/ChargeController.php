<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ChargePaymentMethodEnum;
use App\Enums\ChargeStatusEnum;
use App\Exports\ChargeExport;
use App\Exports\UnitChargeExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChargeResource;
use App\Http\Resources\UnitResource;
use App\Jobs\SendSmsForChargeUnitJob;
use App\Models\Charge;
use App\Models\ChargeSetting;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Unit;
use App\Services\ChargeService;
use App\Services\TransactionService;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ChargeController extends Controller
{


    public function index(Request $request)
    {
        $search = $request->search;
        $years = years(true);
        $years2 = years();
        $months = months();
        $yearNow = verta()->year;
        $monthNow = verta()->month;
        $chargeSetting = ChargeSetting::with('details')->whereDate('date',Verta::now()->startMonth()->toCarbon()->format('Y-m-d'))->first();
        $status = enumFormated(ChargeStatusEnum::cases());
        $rows = Charge::with(['unit','user']);
        if($search){
            $rows = $rows->whereHas('user',function ($q) use($search){
                return $q->where('name','LIKE',"%$search%")->orWhere('mobile',"$search");
            })->orWhereHas('unit',function ($q) use($search){
                return $q->where('unit_number',"$search");
            });
        }
        $rows = $rows->orderByDesc('id')->paginate(auth()->user()->perPage('admin',Charge::class));

        $rows = ChargeResource::collection($rows);
        $perPages = perPages();
        return inertia('Admin/Charges/Index', compact('rows','years', 'years2', 'months', 'yearNow', 'monthNow', 'chargeSetting', 'status', 'perPages'));
    }

    public function searchUnitCharge(Request $request)
    {
        $date = "$request->year/$request->month/1";
        $date = Verta::parse($date)->startMonth()->toCarbon()->format('Y-m-d');
        $data = ChargeSetting::with('details')->whereDate('date',$date)->first();

        if($data) {
            return ['status' => 'success', 'data' => $data];
        }
        else{
            return ['status' => 'error', 'data' => $data];
        }
    }


    public function settingStore(Request $request){

        //dd($request->all());
        try {
            $request->validate([
                'year'                => 'required|integer',
                'month'               => 'required|integer',
                'base_amount'         => 'required|numeric',
                'details'             => 'required|array',
                'details.*.from_area' => 'required|numeric',
                'details.*.to_area'   => 'required|numeric',
                'details.*.amount'    => 'required|numeric',
            ],
            [
                'year'                => 'لطفا سال را انتخاب کنید',
                'month'               => 'لطفا ماه را انتخاب کنید',
                'base_amount'         => 'لطفا مبلغ شارژ واحدهای خالی را وارد نمایید',
                'details'             => 'شارژ براساس متراژ باید وارد شود',
                'details.*.from_area' => 'از متراژ را به درستی وارد نمایید',
                'details.*.to_area'   => 'تا متراژ را به درستی وارد نمایید',
                'details.*.amount'    => 'مبلغ ثابت را وارد نمایید',
            ]);


            DB::transaction(function () use($request){
                $date = verta()->parse("{$request->year}-{$request->month}-1")->toCarbon()->format('Y-m-d');
                $unitCharge = ChargeSetting::whereDate('date',$date)->first();
                if($unitCharge){
                    $unitCharge->update([
                        'date' => $date,
                        'base_amount' => $request->base_amount,
                    ]);
                    $unitCharge->details()->delete();
                }
                else{
                    $unitCharge = ChargeSetting::create([
                        'date' => $date,
                        'base_amount' => $request->base_amount,
                    ]);
                }
                foreach ($request->details as $detail) {
                    $unitCharge->details()->create($detail);
                }
            });

            return redirectMessage('success','تنظیمات با موفقیت ذخیره شد');
        }
        catch (ValidationException $e) {
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e) {
            return redirectMessage('error',$e->getMessage());
        }

    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'year' => 'required|integer',
                'month' => 'required|integer',
                'day' => 'required|integer',
            ],
            [
                'year' => 'لطفا سال را انتخاب کنید',
                'month' => 'لطفا ماه را انتخاب کنید',
                'day' => 'لطفا روز را انتخاب کنید',
            ]);

            $chargeService = new ChargeService;
            $dueDate = verta()->parse($request->year.'-'.$request->month.'-'.$request->day)->toCarbon();
            if($dueDate->timestamp <= now()->addDays(7)->timestamp){
                return redirectMessage('error','مهلت پرداخت باید بیشتر از یک هفته باشد');
            }
            $dueDate = $dueDate->format('Y-m-d 23:59:59');
            $units = Unit::with(['owners.user','occupants.user',
                'owners' => function ($q) {
                    $q->where('status','CURRENT');
                },
                'occupants' => function ($q) {
                    $q->where('status','CURRENT');
                }])->get();
            $result = [];
            foreach ($units as $unit){
                $result[] = $chargeService->control($unit,$dueDate);
            }
            return redirectMessage('success','پیامک ها در صف ارسال قرار گرفتند');
        }
        catch (ValidationException $e) {
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e) {
            return redirectMessage('error',$e->getMessage());
        }
    }

    public function updateStatus(Charge $charge,Request $request)
    {
        try {
            if($request->status === 'PAID'){
                DB::transaction(function () use($request,$charge){
                    $charge->update([
                        'status' => $request->status,
                        'payment_method' => $request->payment_method,
                    ]);
                    $description = 'پرداخت به صورت دستی از طریق '.ChargePaymentMethodEnum::fromKey($request->payment_method)->value;
                    (new TransactionService)->createPayment($charge->user_id,Charge::class,$charge->id,$charge->amount,$request->payment_method,'SUCCESSFUL',$request->referenceId,$description);
                });
                return redirectMessage('success', 'با موفقیت ذخیره شد');
            }
            else{
                return redirectMessage('error', 'فعلا این وضعیت تغییری نخواهد کرد');
            }

        }
        catch (ValidationException $e) {
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e) {
            return redirectMessage('error',$e->getMessage());
        }
    }

    public function units(Request $request)
    {
        $search = $request->search;
        $date = Verta::now()->startMonth()->toCarbon()->format('Y-m-d');
        $rows = Unit::query();
        if($search){
            $rows = $rows->where('unit_number','LIKE', "%$search%");
        }
        $rows = $rows->orderByDesc('id')->paginate(perPage(Charge::class));
        request()->merge(['settingDate' => $date]);
        $rows = UnitResource::collection($rows);
        $perPages = perPages();
        return inertia('Admin/Charges/Unit', compact('rows', 'perPages'));
    }


    public function excel(Request $request)
    {
        $filters = [];

        $filters['period'] = \verta()->parse($request->filters['year'].'-'.$request->filters['month'].'-'.'1')->toCarbon()->format('Y-m-d');
        return Excel::download(new ChargeExport($filters), 'charges.xlsx');
    }

    public function unitExcel(Request $request)
    {
        return Excel::download(new UnitChargeExport, 'unit-charges.xlsx');
    }
}
