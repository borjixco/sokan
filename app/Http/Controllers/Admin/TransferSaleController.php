<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TransferSaleExport;
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
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use function PHPUnit\Framework\throwException;

class TransferSaleController extends Controller
{

    public function index(Request $request)
    {
        $unit     = $request->unit;
        $contract = $request->contract;
        $rows     = Transfer::with(['owners', 'unit'])->where('type','SALE');
        $years = years(false,verta()->addYears(2)->year);
        $months = months();
        $days = days();
        if($unit){
            $rows = $rows->whereHas('unit',function ($q) use($unit){
                $q->where('unit_number','LIKE', "%$unit%");
            });
        }
        if($contract){
            $rows = $rows->where('contract_number','LIKE',"%$contract%");
        }
        $rows = $rows->orderByDesc('id')->paginate(auth()->user()->perPage('admin',Transfer::class));
        $rows = TransferSaleResource::collection($rows);
        $perPages = perPages();
        return inertia('Admin/Transfers/Sale/Index', compact('rows', 'perPages', 'years', 'months', 'days'));
    }

    public function create()
    {
        $general = Setting::get('admin.general');
        $years   = years();
        $months  = months();
        $days    = days();
        $jobs    = jobs();
        $genders = genders();
        return inertia('Admin/Transfers/Sale/Create', compact('general', 'years', 'months', 'days', 'jobs', 'genders'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'from_user_id'    => 'required|exists:users,id',
                'to_user_id'      => 'required|exists:users,id',
                'unit_id'         => 'required|exists:units,id',
                'contract_number' => 'required|string',
                'year'            => 'required|integer',
                'month'           => 'required|integer',
                'day'             => 'required|integer',
                'cost'            => 'required|numeric',
                'ownership'       => 'nullable|numeric',
                'goodwill_rental' => 'nullable|numeric',
                'terms'           => 'nullable|string',
                'first_witness'   => 'required|min:3',
                'second_witness'  => 'required|min:3',
                'lawyer'          => 'nullable',
                'reagent'         => 'nullable',
            ],
            [
                'from_user_id'    => 'طرف اول را انتخاب نکرده اید',
                'to_user_id'      => 'طرف دوم را انتخاب نکرده اید',
                'unit_id'         => 'واحد انتخاب نشده است',
                'contract_number' => 'شماره قرارداد را وارد نمایید',
                'year'            => 'سال قرارداد را وارد نمایید',
                'month'           => 'ماه قرارداد را وارد نمایید',
                'day'             => 'روز قرارداد را وارد نمایید',
                'cost'            => 'مبلغ را وارد نمایید',
                'ownership'       => 'حق مالکانه را عددی وارد نمایید',
                'goodwill_rental' => 'اجاره سرقفلی را عددی وارد نمایید',
                'first_witness'   => 'شاهد اول را وارد نمایید',
                'second_witness'  => 'شاهد دوم را وارد نمایید',
            ]);

            if($request->from_user_id){
                if(count($request->quotas_from) !== count($request->from_user_id)){
                    return redirectMessage('error','همه سهم های طرف اول باید مشخص شود');
                }
                elseif ((int)array_sum($request->quotas_from) !== 6){
                    return redirectMessage('error','مجموع سهم های طرف اول باید ۶ سهم باشد');
                }
            }
            if($request->to_user_id){
                if(count($request->quotas_to) !== count($request->to_user_id)){
                    return redirectMessage('error','همه سهم های طرف دوم باید مشخص شود');
                }
                elseif ((int)array_sum($request->quotas_to) !== 6){
                    return redirectMessage('error','جمع سهم ها باید ۶ سهم باشد');
                }
            }
            if(count(array_intersect($request->to_user_id,$request->from_user_id)) > 0){
                return redirectMessage('error','طرف اول و طرف دوم نباید مشابه باشند');
            }

            $data = DB::transaction(function () use($request){
                $doing_at = verta()->parse("$request->year-$request->month-$request->day")->toCarbon()->format('Y-m-d');
                $transfer = Transfer::create([
                    'unit_id'         => $request->unit_id,
                    'contract_number' => $request->contract_number,
                    'doing_at'        => $doing_at,
                    'cost'            => $request->cost,
                    'lawyer'          => $request->lawyer,
                    'reagent'         => $request->reagent,
                    'ownership'       => $request->ownership,
                    'goodwill_rental' => $request->goodwill_rental,
                    'terms'           => $request->terms,
                    'first_witness'   => $request->first_witness,
                    'second_witness'  => $request->second_witness,
                    'type'            => 'SALE',
                ]);
                $getOldOwner = Owner::where('unit_id', $request->unit_id)->where('status', 'CURRENT')->get();
                $oldOwners = [];
                if (!$getOldOwner->isEmpty()) {
                    // اگر مالک قبلی وجود داشت، همه را آرشیو کن
                    foreach ($getOldOwner as $oldOwner) {
                        $transfer->owners()->attach($oldOwner->id,['type' => 'OLD']);
                    }
                    Owner::where('unit_id', $request->unit_id)->where('status', 'CURRENT')->update(['status' => 'ARCHIVE']);
                } else {
                    // اگر مالک قبلی وجود نداشت، یک مالک جدید بساز
                    $i = 0;
                    foreach ($request->from_user_id as $user_id) {
                        $oldOwner = Owner::create([
                            'user_id' => $user_id,
                            'unit_id' => $request->unit_id,
                            'quota' => $request->quotas_from[$i++],
                            'status' => 'ARCHIVE',
                        ]);
                        $oldOwners[] = $oldOwner;
                        $transfer->owners()->attach($oldOwner->id,['type' => 'OLD']);
                    }
                }

                $i=0;
                $newOwners = [];
                foreach ($request->to_user_id as $user_id) {
                    $newOwner = Owner::create([
                        'user_id'         => $user_id,
                        'unit_id'         => $request->unit_id,
                        'status'          => 'CURRENT',
                        'quota'           => $request->quotas_to[$i++],
                    ]);
                    $newOwners[] = $newOwner;
                    $transfer->owners()->attach($newOwner->id,['type' => 'CURRENT']);
                }
                return (object)[
                    'transfer'  => $transfer,
                    'owners'    => $newOwners,
                    'oldOwners' => $oldOwners,
                ];
            });

            return redirectMessage('success','با موفقیت ثبت شد', null ,route('admin.transfers.sale.edit',$data->transfer->id));
        }
        catch (ValidationException $e){
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }
    }

    public function edit(Transfer $transfer, Request $request)
    {
        Gate::authorize('view',$transfer);
        $transferOrg = $transfer;
        $transfer = new TransferSaleResource(Transfer::where('id',$transfer->id)->with('unit')->first());
        $unit     = new UnitResource($transfer->unit);
        $years    = years();
        $years2    = years();
        $months   = months();
        $days     = days();
        $modelType = Transfer::class;
        $documents = DocumentResource::collection($transferOrg->documents()->with(['categories','media'])->orderByDesc('created_at')->paginate(20));
        $categories = categories(Transfer::class);
        return inertia('Admin/Transfers/Sale/Edit', compact('transfer','unit', 'years', 'years2', 'months', 'days', 'modelType','documents', 'categories'));
    }

    public function update(Transfer $transfer, Request $request)
    {
        Gate::authorize('view',$transfer);
        try {
            $request->validate([
                'contract_number' => 'required|string',
                'year'            => 'required|integer',
                'month'           => 'required|integer',
                'day'             => 'required|integer',
                'cost'            => 'required|numeric',
                'ownership'       => 'nullable|numeric',
                'goodwill_rental' => 'nullable|numeric',
                'terms'           => 'nullable|string',
                'first_witness'   => 'required|min:3',
                'second_witness'  => 'required|min:3',
                'lawyer'          => 'required|min:3',
                'reagent'         => 'required|min:3',
            ],
            [
                'contract_number' => 'شماره قرارداد را وارد نمایید',
                'year'            => 'سال قرارداد را وارد نمایید',
                'month'           => 'ماه قرارداد را وارد نمایید',
                'day'             => 'روز قرارداد را وارد نمایید',
                'cost'            => 'مبلغ را وارد نمایید',
                'ownership'       => 'حق مالکانه را عددی وارد نمایید',
                'goodwill_rental' => 'اجاره سرقفلی را عددی وارد نمایید',
                'first_witness'   => 'شاهد اول را وارد نمایید',
                'second_witness'  => 'شاهد دوم را وارد نمایید',
                'lawyer'          => 'وکیل را وارد نمایید',
                'reagent'         => 'معرف را وارد نمایید',
            ]);
            $doing_at = verta()->parse("$request->year-$request->month-$request->day")->toCarbon()->format('Y-m-d');
            $transfer->update([
                'contract_number' => $request->contract_number,
                'doing_at'        => $doing_at,
                'cost'            => $request->cost,
                'ownership'       => $request->ownership,
                'goodwill_rental' => $request->goodwill_rental,
                'terms'           => $request->terms,
                'first_witness'   => $request->first_witness,
                'second_witness'  => $request->second_witness,
                'lawyer'          => $request->lawyer,
                'reagent'         => $request->reagent,
            ]);
            return redirectMessage('success','با موفقیت ویرایش شد');
        }
        catch (ValidationException $e){
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }
    }

    public function print($transfer)
    {
        $transfer = Transfer::where('id', $transfer)->with(['currentOwners','oldOwners', 'unit', 'unit.position', 'unit.floor'])->first();
        $transfer = new TransferSaleResource($transfer);
        return inertia('Admin/Transfers/Sale/Print',compact('transfer'));
    }

    public function excel(Request $request)
    {

        try {
            $request->validate([
                'filters.year' => 'required|integer',
            ], [
                'filters.year' => 'سال را به درستی وارد نمایید',
            ]);

            $year  = $request->filters['year'];
            $month = $request->filters['month'] ?? null;
            $day   = $request->filters['day'] ?? null;

            if ($year && $month && $day) {
                $start = verta()->parse("{$year}-{$month}-{$day}")->startDay()->toCarbon()->format('Y-m-d H:i:s');
                $end   = verta()->parse("{$year}-{$month}-{$day}")->endDay()->toCarbon()->format('Y-m-d H:i:s');
            } elseif ($year && $month) {
                $start = verta()->parse("{$year}-{$month}-01")->startDay()->toCarbon()->format('Y-m-d H:i:s');
                $end   = verta()->parse("{$year}-{$month}-01")->endMonth()->endDay()->toCarbon()->format('Y-m-d H:i:s');
            } else {
                $start = verta()->parse("{$year}-01-01")->startDay()->toCarbon()->format('Y-m-d H:i:s');
                $end = verta()->parse("{$year}-12-01")->endMonth()->endDay()->toCarbon()->format('Y-m-d H:i:s');
            }

            $filters = [
                'start_date' => $start,
                'end_date'   => $end
            ];
            return Excel::download(new TransferSaleExport($filters), 'transfers-sale.xlsx');
        }
        catch (ValidationException $e){
            return responseJSon('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }

    }

}
