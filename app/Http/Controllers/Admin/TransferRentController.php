<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TransferRentExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\TransferRentResource;
use App\Http\Resources\TransferSaleResource;
use App\Http\Resources\UnitResource;
use App\Models\Occupant;
use App\Models\Owner;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class TransferRentController extends Controller
{

    public function index(Request $request)
    {
        $unit     = $request->unit;
        $contract = $request->contract;
        $rows     = Transfer::query()->with(['owners', 'unit'])->where('type','RENT');
        if($unit){
            $rows = $rows->whereHas('unit',function ($q) use($unit){
                $q->where('unit_number','LIKE', "%$unit%");
            });
        }
        if($contract){
            $rows = $rows->where('contract_number','LIKE',"%$contract%");
        }
        $rows     = $rows->orderByDesc('id')->paginate(auth()->user()->perPage('admin',Owner::class));
        $rows     = TransferRentResource::collection($rows);
        $perPages = perPages();
        $years    = years(false,verta()->addYears(2)->year);
        $months   = months();
        $days     = days();
        return inertia('Admin/Transfers/Rent/Index', compact('rows','perPages', 'years', 'months', 'days'));
    }

    public function create()
    {
        $general = Setting::get('admin.general');
        $years   = years();
        $years2   = years(true);
        $months  = months();
        $days    = days();
        $jobs    = jobs();
        $genders = genders();
        return inertia('Admin/Transfers/Rent/Create', compact('general', 'years', 'months', 'years2', 'days', 'jobs', 'genders'));
    }

    public function store(Request $request)
    {

        try {
            $request->validate([
                'from_owner_id'   => 'required|exists:users,id',
                'to_user_id'      => 'required|exists:users,id',
                'unit_id'         => 'required|exists:units,id',
                'contract_number' => 'required|string',
                'year'            => 'required|integer',
                'month'           => 'required|integer',
                'day'             => 'required|integer',
                'rent_from_year'  => 'required|integer',
                'rent_from_month' => 'required|integer',
                'rent_from_day'   => 'required|integer',
                'rent_to_year'    => 'required|integer',
                'rent_to_month'   => 'required|integer',
                'rent_to_day'     => 'required|integer',
                'mortgage_amount' => 'required|numeric',
                'rental_amount'   => 'required|numeric',
                'duration'        => 'nullable|string',
                //'first_witness'   => 'nullable|min:3',
                //'second_witness'  => 'nullable|min:3',
                //'lawyer'          => 'nullable',
                'regulator'       => 'nullable',
                'check_number'    => 'nullable',
                'bank'            => 'nullable',
                'warranty_amount' => 'nullable',
                'current_account' => 'nullable',
                'card_number'     => 'nullable',
                'job'             => 'nullable|exists:job_types,id',
            ],
            [
                'from_owner_id'   => 'مالکی وجود ندارد ابتدا نقل و انتقال مالک را اضافه کنید',
                'to_user_id'      => 'مستاجر را انتخاب نکرده اید',
                'unit_id'         => 'واحد انتخاب نشده است',
                'contract_number' => 'شماره قرارداد را وارد نمایید',
                'year'            => 'سال قرارداد را وارد نمایید',
                'month'           => 'ماه قرارداد را وارد نمایید',
                'day'             => 'روز قرارداد را وارد نمایید',
                'rent_from_year'  => 'سال اجاره را وارد نمایید',
                'rent_from_month' => 'ماه اجاره را وارد نمایید',
                'rent_from_day'   => 'روز اجاره را وارد نمایید',
                'rent_to_year'    => 'سال اجاره را وارد نمایید',
                'rent_to_month'   => 'ماه اجاره را وارد نمایید',
                'rent_to_day'     => 'روز اجاره را وارد نمایید',
                'mortgage_amount' => 'رهن را وارد نمایید',
                'duration'        => 'مدت اجاره را به درستی نمایید',
                'rental_amount'   => 'اجاره را وارد نمایید',
                'first_witness'   => 'شاهد اول را به درستی وارد نمایید',
                'second_witness'  => 'شاهد دوم را به درستی وارد نمایید',
                'job.exists'      => 'یکی از نوع‌های شغل انتخاب‌شده نامعتبر است.',
            ]);

            $owners = Owner::whereIn('id',$request->from_owner_id)->get();
            if(count(array_intersect($owners->pluck('user_id')->toArray(),$request->to_user_id)) > 0){
                return redirectMessage('error','مالک و مستاجر نباید مشابه هم باشند');
            }

           $data = DB::transaction(function () use($request){
               $doing_at = verta()->parse("$request->year-$request->month-$request->day")->toCarbon()->format('Y-m-d');

               $rental_start_at = verta()->parse("$request->rent_from_year-$request->rent_from_month-$request->rent_from_day")->toCarbon()->format('Y-m-d');
               $rental_end_at = verta()->parse("$request->rent_to_year-$request->rent_to_month-$request->rent_to_day")->toCarbon()->format('Y-m-d');

               $transfer = Transfer::create([
                   'unit_id'         => $request->unit_id,
                   'contract_number' => $request->contract_number,
                   'doing_at'        => $doing_at,
                   'rental_start_at' => $rental_start_at,
                   'rental_end_at'   => $rental_end_at,
                   'mortgage_amount' => $request->mortgage_amount,
                   'rental_amount'   => $request->rental_amount,
                   'duration'        => $request->duration,
                   'terms'           => $request->terms,
                   //'first_witness'   => $request->first_witness,
                   //'second_witness'  => $request->second_witness,
                   //'lawyer'          => $request->lawyer,
                   'regulator'       => $request->regulator,
                   'check_number'    => $request->check_number,
                   'bank'            => $request->bank,
                   'warranty_amount' => $request->warranty_amount,
                   'current_account' => $request->current_account,
                   'card_number'     => $request->card_number,
                   'type'            => 'RENT',
               ]);

               $transfer->jobTypes()->attach([$request->job] ?? []);

               $oldOccupant = Occupant::where('unit_id',$request->unit_id)->where('status','CURRENT')->get();
               if(!$oldOccupant->isEmpty()){
                   // اگر مستاجر از قبل داشت همه رو آرشیو کن
                   Occupant::where('unit_id', $request->unit_id)->where('status', 'CURRENT')->update(['status' => 'ARCHIVE']);
               }

               $currentOwners = [];
               foreach ($request->from_owner_id as $owner_id) {
                   $currentOwners[] = $owner_id;
                   $transfer->owners()->attach($owner_id,['type' => 'CURRENT']);
               }
               $users_to = User::with('roles')->whereIn('id',(array)$request->to_user_id)->get();
               $role = Role::where('name', 'occupant')->first();
               foreach ($users_to as $user) {
                   $newOccupant = Occupant::create([
                       'user_id'         => $user->id,
                       'unit_id'         => $request->unit_id,
                       'status'          => 'CURRENT',
                   ]);
                   $newOccupants[] = $newOccupant;
                   $transfer->occupants()->attach($newOccupant->id,['type' => 'CURRENT']);
                   if($user->roles->where('name','occupant')->count() === 0){
                       $user->roles()->syncWithoutDetaching([$role->id]);
                   }
               }

               return (object)[
                   'transfer'      => $transfer,
                   'currentOwners' => $currentOwners,
                   'occupant'      => $newOccupants,
               ];
           });
            return redirectMessage('success','با موفقیت ثبت شد', null, route('admin.transfers.rent.edit',$data->transfer->id));
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
        $transfer = new TransferRentResource(Transfer::where('id',$transfer->id)->with('owners', 'unit')->first());
        $unit     = new UnitResource($transfer->unit);
        $years    = years();
        $years2   = years(true);
        $months   = months();
        $days     = days();
        $modelType = Transfer::class;
        $documents = DocumentResource::collection($transferOrg->documents()->with(['categories','media'])->orderByDesc('created_at')->paginate(20));
        $categories = categories(Transfer::class);
        $jobs    = jobs();
        return inertia('Admin/Transfers/Rent/Edit', compact('transfer','unit', 'years', 'years2', 'months', 'days', 'modelType','documents', 'categories','jobs'));
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
                'rent_from_year'  => 'required|integer',
                'rent_from_month' => 'required|integer',
                'rent_from_day'   => 'required|integer',
                'rent_to_year'    => 'required|integer',
                'rent_to_month'   => 'required|integer',
                'rent_to_day'     => 'required|integer',
                'mortgage_amount' => 'required|numeric',
                'rental_amount'   => 'required|numeric',
                'duration'        => 'nullable|string',
                'first_witness'   => 'nullable|min:3',
                'second_witness'  => 'nullable|min:3',
                'lawyer'          => 'nullable',
                'regulator'       => 'nullable',
                'check_number'    => 'nullable',
                'bank'            => 'nullable',
                'warranty_amount' => 'nullable',
                'current_account' => 'nullable',
                'card_number'     => 'nullable',
                'job'             => 'nullable|exists:job_types,id',
            ],
            [
                'contract_number' => 'شماره قرارداد را وارد نمایید',
                'year'            => 'سال قرارداد را وارد نمایید',
                'month'           => 'ماه قرارداد را وارد نمایید',
                'day'             => 'روز قرارداد را وارد نمایید',
                'rent_from_year'  => 'سال اجاره را وارد نمایید',
                'rent_from_month' => 'ماه اجاره را وارد نمایید',
                'rent_from_day'   => 'روز اجاره را وارد نمایید',
                'rent_to_year'    => 'سال اجاره را وارد نمایید',
                'rent_to_month'   => 'ماه اجاره را وارد نمایید',
                'rent_to_day'     => 'روز اجاره را وارد نمایید',
                'mortgage_amount' => 'رهن را وارد نمایید',
                'rental_amount'   => 'اجاره را وارد نمایید',
                'duration'        => 'مدت اجاره را به درستی وارد نمایید',
                'first_witness'   => 'شاهد اول را به درستی وارد نمایید',
                'second_witness'  => 'شاهد دوم را به درستی وارد نمایید',
                'job.exists'      => 'یکی از نوع‌های شغل انتخاب‌شده نامعتبر است.',
            ]);
            $transfer_at = verta()->parse("$request->year-$request->month-$request->day")->toCarbon()->format('Y-m-d');
            $rent_from = verta()->parse("$request->rent_from_year-$request->rent_from_month-$request->rent_from_day")->toCarbon()->format('Y-m-d');
            $rent_to = verta()->parse("$request->rent_to_year-$request->rent_to_month-$request->rent_to_day")->toCarbon()->format('Y-m-d');



            $transfer->update([
                'contract_number' => $request->contract_number,
                'transfer_at'     => $transfer_at,
                'rent_from'       => $rent_from,
                'rent_to'         => $rent_to,
                'mortgage_amount' => $request->mortgage_amount,
                'rental_amount'   => $request->rental_amount,
                'duration'        => $request->duration,
                'terms'           => $request->terms,
                'first_witness'   => $request->first_witness,
                'second_witness'  => $request->second_witness,
                'lawyer'          => $request->lawyer,
                'regulator'       => $request->regulator,
                'check_number'    => $request->check_number,
                'bank'            => $request->bank,
                'warranty_amount' => $request->warranty_amount,
                'current_account' => $request->current_account,
                'card_number'     => $request->card_number,
                'type'            => 'RENT',
            ]);
            $transfer->jobTypes()->sync($request->job ? [$request->job] : []);
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
        $transfer = Transfer::where('id', $transfer)->with(['currentOwners','occupants', 'unit', 'unit.position'])->first();

        $transfer = new TransferRentResource($transfer);
        return inertia('Admin/Transfers/Rent/Print',compact('transfer'));
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
            return Excel::download(new TransferRentExport($filters), 'transfers-rent.xlsx');
        }
        catch (ValidationException $e){
            return responseJSon('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }

    }
}
