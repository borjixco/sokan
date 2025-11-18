<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UnitBlockEnum;
use App\Enums\UnitOperationEnum;
use App\Enums\UnitStatusEnum;
use App\Enums\UnitTypeEnum;
use App\Exports\UnitExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\OccupantResource;
use App\Http\Resources\OwnerResource;
use App\Http\Resources\UnitResource;
use App\Models\Category;
use App\Models\Document;
use App\Models\Floor;
use App\Models\Media;
use App\Models\Occupant;
use App\Models\Owner;
use App\Models\Position;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class UnitController extends Controller
{
    public function index()
    {

        $search = request()->search;
        $user = auth()->user();
        $units = Unit::with(['floor','position','owners.user']);
        if($search){
            $units = $units->where('unit_number',"$search");
        }
        $units = $units->orderByDesc('id')->paginate($user->perPage('admin',Unit::class));
        $rows = UnitResource::collection($units);
        $perPages = perPages();
        return inertia('Admin/Units/Index',compact('rows','perPages'));
    }


    protected function unitTypes(){
        $unitTypeEnum = UnitTypeEnum::cases();
        $unitTypes = [];
        foreach ($unitTypeEnum as $type){
            $unitTypes[] = [
                'value' => $type->name,
                'label' => $type->value,
            ];
        }
        return $unitTypes;
    }

    protected function positions(){
        $positionObject = Position::all();
        $positions = [];
        foreach ($positionObject as $position){
            $positions[] = [
                'value' => $position->id,
                'label' => $position->label,
            ];
        }
        return $positions;
    }

    protected function floor()
    {
        $floorObject = Floor::all();
        $floor = [];
        foreach ($floorObject as $item){
            $floor[] = [
                'value' => $item->id,
                'label' => $item->label,
            ];
        }
        return $floor;
    }

    public function roof()
    {
        $roof = [
            [
                'value' => 'WITH_ROOF',
                'label' => 'دارای سقف',
            ],
            [
                'value' => 'WITHOUT_ROOF',
                'label' => 'بدون سقف',
            ]
        ];
        return $roof;
    }

    public function create()
    {
        $unitTypes = $this->unitTypes();
        $positions = $this->positions();
        $floor = $this->floor();
        $roof = $this->roof();
        $owners = $this->ownerQuery();
        $occupants = $this->occupantQuery();
        $general = Setting::get('admin.general');
        return inertia('Admin/Units/Create',compact('unitTypes', 'positions', 'roof', 'floor','owners', 'occupants', 'general'));
    }

    private function categories()
    {
        $categories = Category::where('model_type', Unit::class)->get()->map(function ($category) {
            return [
                'label' => $category->name,
                'value' => $category->id,
            ];
        });
        return $categories;

    }

    protected function ownerQuery()
    {
        $ownerQuery = User::whereHas('roles',function ($query){
            $query->where('name', 'owner');
        });
        $search = request()->q;
        if($search){
            $ownerQuery = $ownerQuery->where('name','LIKE',"%$search%")->orWhere('mobile','LIKE',"%$search%");
        }
        $ownerQuery = $ownerQuery->limit(50)->get();
        $owners = [];
        foreach ($ownerQuery as $item){
            $owners[] = [
                'id'       => $item->id,
                'name'     => $item->name,
                'phone'    => $item->mobile,
                'selected' => false,
            ];
        }
        return $owners;
    }

    public function owners(){
        $owners = $this->ownerQuery();
        return $owners;
    }

    protected function occupantQuery($limit = 50)
    {
        $occupantQuery = User::whereHas('roles',function ($query){
            $query->whereIn('name', ['owner', 'occupant']);
        });
        $search = request()->q;
        if($search){
            $occupantQuery = $occupantQuery->where('name','LIKE',"%$search%")->orWhere('mobile','LIKE',"%$search%");
        }
        $occupantQuery = $occupantQuery->limit($limit)->get();
        $occupants = [];
        foreach ($occupantQuery as $item){
            $occupants[] = [
                'id'       => $item->id,
                'name'     => $item->name,
                'phone'    => $item->mobile,
                'selected' => false,
            ];
        }
        return $occupants;
    }
    public function occupants(){
        $occupants = $this->occupantQuery(10);
        return $occupants;
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xls,xlsx,csv|max:1024',
            ]);
            $file = $request->file('file');
            $data = Excel::toArray([], $file); // خواندن کل داده‌ها به صورت آرایه
            $data = $data[0];
            $PriceToday = verta()->parse(trim(str_replace('ارزش روز واحد','',$data[1][19])))->startMonth()->toCarbon()->format('Y-m-d');
            unset($data[0]);
            unset($data[1]);
            //dd($data);
            $floors = Floor::all();
            $positions = Position::all();
            foreach ($data as $row) {
                $transaction = DB::transaction(function () use($row,$floors, $positions, $PriceToday){

                    $unitNumberCell              = $row[0]; // شماره واحد
                    $floorCell                   = $row[1]; // طبقه
                    $meterageCell                = $row[2]; // متراژ
                    $unitTypeCell                = $row[3]; // نوع کاربری
                    $telCell                     = $row[4]; // تلفن
                    $postalCodeCell              = $row[5]; // کد پستی
                    $positionCell                = $row[6]; // موقعیت
                    $meterSerialNumberCell       = $row[7]; // شماره بدنه کنتور
                    $computerPasswordCell        = $row[8]; // رمز رایانه
                    $caseCell                    = $row[9]; // پرونده
                    $roofCell                    = $row[10]; //دارای سقف
                    $judicialBlockCell           = $row[11]; // پلمپ قضایی
                    $complexBlockCell            = $row[12]; // پلمپ مجتمع
                    $soldCell                    = $row[13]; // فروخته شده
                    $rentedCell                  = $row[14]; // اجاره شده
                    $emptyCell                   = $row[15]; // خالی
                    $valuePerMeterCell           = $row[16]; // ارزش هر متر از واحد تجاری
                    $totalValueCell              = $row[17]; // ارزش واحد های تجاری فروخته شده
                    $valuePerMeterTodayCell      = $row[18]; // ارزش هر متر به نرخ روز
                    $PriceTodayCell              = $PriceToday; //  ارزش روز واحد
                    $maximumFullMortgage         = $row[20]; // حداکثر رهن کامل (ریال)
                    $maximumMonthlyRentCell      = $row[21]; // حداکثر اجاره ماهیانه (ریال)
                    $ownerAnnualGoodwillRentCell = $row[22]; // اجاره سرقفلی سالانه مالک
                    $salePriceSuggestedOwnerCell = $row[23]; // فروشی (نرخ پیشنهادی مالک)
                    $ownerProposedMortgageCell   = $row[24]; // // اجاره‌ای (رهن پیشنهادی مالک)
                    $rentProposedOwnerCell       = $row[25]; //اجاره‌ای (اجاره پیشنهادی مالک)
                    $chargeAmountCell            = $row[26]; //مبلغ شارژ

                    $positionId = null;
                    if(!empty($positionCell)) { // موقعیت
                        $position = $positions->where('label', $positionCell)->first();
                        if (!$position) {
                            $position = Position::create(['label' => $positionCell]);
                            $newPosition = new Position(['id' => $position->id, 'label' => $positionCell]);
                            $positions->push($newPosition);
                        }
                        $positionId =  $position->id;
                    }
                    $floor = $floors->where('label',$floorCell)->first();
                    if(!$floor){
                        $floor = Floor::create(['label' => $floorCell]);
                        $newFloor = new Floor(['id' => $floor->id, 'label' => $floorCell]);
                        $floors->push($newFloor);
                    }
                    $data = [
                        'unit_number'                => $unitNumberCell, //شماره واحد
                        'floor_id'                   => $floor->id, // طبقه
                        'meterage'                   => $meterageCell, // متراژ
                        'unit_type'                  => (!empty($unitTypeCell) && UnitTypeEnum::fromValue($unitTypeCell)) ? UnitTypeEnum::fromValue($unitTypeCell)->name : null, // نوع کاربری
                        'tel'                        => $telCell, // تلفن
                        'postal_code'                => $postalCodeCell, // کد پستی
                        'position_id'                => $positionId, // موقعیت
                        'meter_serial_number'        => $meterSerialNumberCell, // شماره بدنه کنتور
                        'computer_password'          => $computerPasswordCell, // رمز رایانه
                        'case'                       => $caseCell, // پرونده
                        'value_per_meter'            => $valuePerMeterCell, // ارزش هرمتر واحد
                        'total_value'                => $totalValueCell, // ارزش واحد
                        'maximum_full_mortgage'      => $maximumFullMortgage, // حداکثر رهن کامل
                        'maximum_monthly_rent'       => $maximumMonthlyRentCell, // حداکثر اجاره ماهیانه
                        'owner_annual_goodwill_rent' => $ownerAnnualGoodwillRentCell, // اجاره سرقفلی سالانه مالک
                        'sale_price_suggested_owner' => $salePriceSuggestedOwnerCell, // فروشی (نرخ پیشنهادی مالک)
                        'owner_proposed_mortgage'    => $ownerProposedMortgageCell, // اجاره‌ای (رهن پیشنهادی مالک)
                        'rent_proposed_owner'        => $rentProposedOwnerCell, //اجاره‌ای (اجاره پیشنهادی مالک)
                        'charge_amount'              => $chargeAmountCell, //مبلغ شارژ
                        'roof'                       => $roofCell === 'بله' ? 'WITH_ROOF' : 'WITHOUT_ROOF',
                    ];
                    if($judicialBlockCell === 'بله'){
                        $data['block'] = 'JUDICIAL_BLOCK';
                    }
                    elseif($complexBlockCell === 'بله'){
                        $data['block'] = 'COMPLEX_BLOCK';
                    }

                    if($soldCell === 'بله'){
                        $data['status'] = 'SOLD';
                    }
                    else{
                        $data['status'] = 'NOT_SOLD';
                    }
                    if($rentedCell === 'بله'){
                        $data['status'] = 'RENTED';
                    }
                    if($emptyCell === 'بله'){
                        $data['status'] = 'EMPTY';
                    }

                    $unit = Unit::create($data);
                    if($valuePerMeterTodayCell) {
                        $unit->values()->create([
                            'date' => $PriceTodayCell,
                            'value_per_meter' => $valuePerMeterTodayCell,
                            'total_value' => $valuePerMeterTodayCell * $meterageCell,
                        ]);
                    }
                });
            }
            return responseJSon('success','با موفقیت آپلود شد');
        }
        catch (ValidationException $e){
            return responseJSon('error',$e->getMessage());
        }
        catch (\Exception $e){
            return responseJSon('error',$e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            if($request->owners){
                if(count($request->quotas) !== count($request->owners)){
                    return redirectMessage('error','همه سهم ها باید مشخص شود');
                }
                elseif ((int)array_sum($request->quotas) !== 6){
                    return redirectMessage('error','مجموع سهم ها باید ۶ سهم باشد');
                }
            }
            $request->validate([
                'unit_number'                => 'required|unique:units,unit_number',
                'floor'                      => 'required|exists:floors,id',
                'meterage'                   => 'required|min:1|max:10000',
                'unit_type'                  => 'required|in:'.collect(enumNames(UnitTypeEnum::cases()))->implode(','),
                'status'                     => 'required|in:'.collect(enumNames(UnitStatusEnum::cases()))->implode(','),
                'operation'                  => 'required|in:'.collect(enumNames(UnitOperationEnum::cases()))->implode(','),
                'block'                      => 'nullable|in:'.collect(enumNames(UnitBlockEnum::cases()))->implode(','),
                'tel'                        => 'nullable|numeric',
                'postal_code'                => 'nullable|numeric',
                'position'                   => 'nullable|exists:positions,id',
                'roof'                       => 'required|in:WITH_ROOF,WITHOUT_ROOF',
                'computer_password'          => 'nullable',
                'case'                       => 'nullable',
                'value_per_meter'            => 'nullable|numeric',
                'total_value'                => 'nullable|numeric',
                'total_now_value'            => 'nullable|numeric',
                'maximum_full_mortgage'      => 'nullable|numeric',
                'maximum_monthly_rent'       => 'nullable|numeric',
                'owner_annual_goodwill_rent' => 'nullable|numeric',
                'sale_price_suggested_owner' => 'nullable|numeric',
                'owner_proposed_mortgage'    => 'nullable|numeric',
                'rent_proposed_owner'        => 'nullable|numeric',
                'charge_amount'              => 'nullable|numeric',
            ],
            [
                'unit_number.required'                => 'شماره واحد را وارد نمایید',
                'unit_number.unique'                  => 'شماره واحد قبلا ثبت شده است',
                'floor.required'                      => 'طبقه را وارد نمایید',
                'floor.exists'                        => 'طقبه وارد شده در پایگاه داده یافت نشد',
                'meterage'                            => 'متراژ را به درستی وارد نمایید',
                'unit_type'                           => 'نوع کاربری را به درستی انتخاب کنید',
                'status'                              => 'وضعیت را به درستی انتخاب کنید',
                'operation'                           => 'نوع بهره برداری را به درستی انتخاب کنید',
                'block'                               => 'وضعیت پلمپ را به درستی انتخاب کنید',
                'tel.numeric'                         => 'تلفن به صورت صحیح وارد نشده است',
                'postal_code.numeric'                 => 'کدپستی به صورت صحیح وارد نشده است',
                'position.exists'                     => 'موقعیت وارد شده در پایگاه داده یافت نشد',
                'roof'                                => 'سقف را انتخاب نمایید',
                'value_per_meter.numeric'             => 'ارزش هر متر به صورت صحیح وارد نشده است',
                'total_value.numeric'                 => 'ارزش واحد تجاری به صورت صحیح وارد نشده است',
                'total_now_value.numeric'             => 'ارزش نهایی به نرخ روز به صورت صحیح وارد نشده است',
                'maximum_full_mortgage.numeric'       => 'حداکثر رهن کامل به صورت صحیح وارد نشده است',
                'maximum_monthly_rent.numeric'        => 'حداکثر اجاره ماهیانه به صورت صحیح وارد نشده است',
                'owner_annual_goodwill_rent.numeric'  => 'اجاره سرقفلی سالانه ملک به صورت صحیح وارد نشده است',
                'sale_price_suggested_owner.numeric'  => 'نرخ فروش پیشنهادی مالک به صورت صحیح وارد نشده است',
                'owner_proposed_mortgage.numeric'     => 'رهن پیشنهادی مالک به صورت صحیح وارد نشده است',
                'rent_proposed_owner.numeric'         => 'اجاره پیشنهادی مالک به صورت صحیح وارد نشده است',
                'charge_amount.numeric'               => 'مبلغ شارژ به صورت صحیح وارد نشده است',
            ]);



            $unit = DB::transaction(function () use($request){
                $totalValue = null;
                if($request->status == 'SOLD'){
                    $totalValue = $request->total_value;
                }
                $unit = Unit::create([
                    'unit_number'                => $request->unit_number,
                    'floor_id'                   => isset($request->floor) ? $request->floor : null,
                    'meterage'                   => $request->meterage,
                    'unit_type'                  => isset($request->unit_type) ? $request->unit_type : null,
                    'tel'                        => $request->tel,
                    'postal_code'                => $request->postal_code,
                    'position_id'                => isset($request->position) ? $request->position : null,
                    'meter_serial_number'        => $request->meter_serial_number,
                    'value_per_meter'            => $request->value_per_meter,
                    'total_value'                => $totalValue,
                    'maximum_full_mortgage'      => $request->maximum_full_mortgage,
                    'maximum_monthly_rent'       => $request->maximum_monthly_rent,
                    'owner_annual_goodwill_rent' => $request->owner_annual_goodwill_rent,
                    'sale_price_suggested_owner' => $request->sale_price_suggested_owner,
                    'owner_proposed_mortgage'    => $request->owner_proposed_mortgage,
                    'rent_proposed_owner'        => $request->rent_proposed_owner,
                    'charge_amount'              => $request->charge_amount,
                    'roof'                       => $request->roof,
                    'status'                     => $request->status,
                    'operation'                  => $request->operation,
                    'block'                      => $request->block,
                    'computer_password'          => $request->computer_password,
                    'case'                       => $request->case,
                ]);

                $unit->values()->create([
                    'date'            => Verta::now()->startMonth()->toCarbon()->format('Y/m/d'),
                    'value_per_meter' => $request->total_now_value/$request->meterage,
                    'total_value'     => $request->total_now_value,
                ]);

                if($request->owners){
                    $users = User::whereIn('mobile',collect($request->owners)->pluck('phone'))->get();
                    if($users){
                        $i=0;
                        foreach ($users as $user){
                            $owner = Owner::create([
                                'user_id' => $user->id,
                                'unit_id' => $unit->id,
                                'quota'   => $request->quotas[$i++],
                                'status'  => 'CURRENT',
                            ]);
                        }
                    }
                }
                if($request->occupants){
                    $users = User::whereIn('mobile',collect($request->occupants)->pluck('phone'))->get();
                    $role = Role::where('name', 'occupant')->first();
                    if($users){
                        foreach ($users as $user){
                            $occupant = Occupant::create([
                                'user_id' => $user->id,
                                'unit_id' => $unit->id,
                                'status' => 'CURRENT'
                            ]);
                            if($user->roles->where('name','occupant')->count() === 0){
                                $user->roles()->syncWithoutDetaching([$role->id]);
                            }
                        }
                    }
                }

                return $unit;
            });

            return redirectMessage('success','واحد با موفقیت ثبت شد',null,route('admin.units.edit',$unit->id));
        }
        catch (ValidationException $e){
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }
    }

    public function edit(Unit $unit)
    {
        $unitTypes    = $this->unitTypes();
        $positions    = $this->positions();
        $floor        = $this->floor();
        $roof         = $this->roof();
        $roofSelected = $unit->roof;
        $value        = $unit->values()->orderByDesc('date')->first();
        $nowDateValue = verta()->instance($value->date)->format('Y/m/d');

        $query = $unit->owners()->with(['user.jobTypes','transfers'])->where('status','CURRENT')->latest()->get();
        $owners = $query ? OwnerResource::collection($query) : [];
        $query = $unit->occupants()->with(['user.jobTypes','transfers'])->where('status','CURRENT')->latest()->get();
        $occupants = $query ? OccupantResource::collection($query) : [];

        $categories = $this->categories();

        $documents = DocumentResource::collection($unit->documents()->with(['categories','media'])->orderByDesc('created_at')->paginate(20));
        $modelType = Unit::class;
        $date = Verta::now()->startMonth()->toCarbon()->format('Y-m-d');
        request()->merge(['settingDate' => $date]);
        $unit = new UnitResource($unit);
        return inertia('Admin/Units/Edit', compact('unit', 'unitTypes', 'positions', 'floor', 'roof', 'roofSelected','value','nowDateValue', 'owners', 'occupants', 'categories', 'documents','modelType'));
    }

    public function update(Request $request, Unit $unit)
    {
        try {
            $request->validate([
                'unit_number'                => 'required|unique:units,unit_number,'.$unit->id,
                'floor'                      => 'required|exists:floors,id',
                'meterage'                   => 'required|min:1|max:10000',
                'tel'                        => 'nullable|numeric',
                'postal_code'                => 'nullable|numeric',
                'position'                   => 'nullable|exists:positions,id',
                'roof'                       => 'required|in:WITH_ROOF,WITHOUT_ROOF',
                'unit_type'                  => 'required|in:'.collect(enumNames(UnitTypeEnum::cases()))->implode(','),
                'status'                     => 'required|in:'.collect(enumNames(UnitStatusEnum::cases()))->implode(','),
                'operation'                  => 'required|in:'.collect(enumNames(UnitOperationEnum::cases()))->implode(','),
                'block'                      => 'nullable|in:'.collect(enumNames(UnitBlockEnum::cases()))->implode(','),
                'computer_password'          => 'nullable',
                'case'                       => 'nullable',
                'value_per_meter'            => 'nullable|numeric',
                'total_value'                => 'nullable|numeric',
                'total_now_value'            => 'nullable|numeric',
                'now_value_per_meter'        => 'nullable|numeric',
                'maximum_full_mortgage'      => 'nullable|numeric',
                'maximum_monthly_rent'       => 'nullable|numeric',
                'owner_annual_goodwill_rent' => 'nullable|numeric',
                'sale_price_suggested_owner' => 'nullable|numeric',
                'owner_proposed_mortgage'    => 'nullable|numeric',
                'rent_proposed_owner'        => 'nullable|numeric',
                'charge_amount'              => 'nullable|numeric',
            ],
            [
                'unit_number.required'                => 'شماره واحد را وارد نمایید',
                'unit_number.unique'                  => 'شماره واحد قبلا ثبت شده است',
                'floor.required'                      => 'طبقه را وارد نمایید',
                'floor.exists'                        => 'طقبه وارد شده در پایگاه داده یافت نشد',
                'meterage'                            => 'متراژ را به درستی وارد نمایید',
                'unit_type'                           => 'نوع کاربری را به درستی انتخاب کنید',
                'status'                              => 'وضعیت را به درستی انتخاب کنید',
                'operation'                           => 'نوع بهره برداری را به درستی انتخاب کنید',
                'block'                               => 'وضعیت پلمپ را به درستی انتخاب کنید',
                'tel.numeric'                         => 'تلفن به صورت صحیح وارد نشده است',
                'postal_code.numeric'                 => 'کدپستی به صورت صحیح وارد نشده است',
                'position.exists'                     => 'موقعیت وارد شده در پایگاه داده یافت نشد',
                'roof'                                => 'سقف را انتخاب نمایید',
                'value_per_meter.numeric'             => 'ارزش هر متر به صورت صحیح وارد نشده است',
                'total_value.numeric'                 => 'ارزش واحد تجاری به صورت صحیح وارد نشده است',
                'total_now_value.numeric'             => 'ارزش نهایی به نرخ روز به صورت صحیح وارد نشده است',
                'now_value_per_meter.numeric'         => 'ارزش هرمتر به نرخ روز به صورت صحیح وارد نشده است',
                'maximum_full_mortgage.numeric'       => 'حداکثر رهن کامل به صورت صحیح وارد نشده است',
                'maximum_monthly_rent.numeric'        => 'حداکثر اجاره ماهیانه به صورت صحیح وارد نشده است',
                'owner_annual_goodwill_rent.numeric'  => 'اجاره سرقفلی سالانه ملک به صورت صحیح وارد نشده است',
                'sale_price_suggested_owner.numeric'  => 'نرخ فروش پیشنهادی مالک به صورت صحیح وارد نشده است',
                'owner_proposed_mortgage.numeric'     => 'رهن پیشنهادی مالک به صورت صحیح وارد نشده است',
                'rent_proposed_owner.numeric'         => 'اجاره پیشنهادی مالک به صورت صحیح وارد نشده است',
                'charge_amount.numeric'               => 'مبلغ شارژ به صورت صحیح وارد نشده است',
                ]);
            $unitUpdate = DB::transaction(function () use($unit,$request){
                $totalValue = null;
                if($request->status == 'SOLD'){
                    $totalValue = $request->total_value;
                }
                $update = $unit->update([
                    'unit_number'                => $request->unit_number,
                    'floor_id'                   => $request->floor,
                    'meterage'                   => $request->meterage,
                    'unit_type'                  => $request->unit_type,
                    'tel'                        => $request->tel,
                    'postal_code'                => $request->postal_code,
                    'position_id'                => $request->position,
                    'meter_serial_number'        => $request->meter_serial_number,
                    'value_per_meter'            => $request->value_per_meter,
                    'total_value'                => $totalValue,
                    'maximum_full_mortgage'      => $request->maximum_full_mortgage,
                    'maximum_monthly_rent'       => $request->maximum_monthly_rent,
                    'owner_annual_goodwill_rent' => $request->owner_annual_goodwill_rent,
                    'sale_price_suggested_owner' => $request->sale_price_suggested_owner,
                    'owner_proposed_mortgage'    => $request->owner_proposed_mortgage,
                    'rent_proposed_owner'        => $request->rent_proposed_owner,
                    'charge_amount'              => $request->charge_amount,
                    'roof'                       => $request->roof,
                    'status'                     => $request->status,
                    'operation'                  => $request->operation,
                    'block'                      => $request->block,
                    'computer_password'          => $request->computer_password,
                    'case'                       => $request->case,
                ]);

                $values = $unit->values()->latest()->first();
                if($values->total_value != $request->total_now_value) {
                    if($value = $unit->values()->whereDate('date',Verta::now()->startMonth()->toCarbon()->format('Y/m/d'))->first()) {
                        $value->update([
                            'value_per_meter' => $request->total_now_value/$request->meterage,
                            'total_value'     => $request->total_now_value,
                        ]);
                    }
                    else{
                        $unit->values()->create([
                            'date' => Verta::now()->startMonth()->toCarbon()->format('Y/m/d'),
                            'value_per_meter' => $request->total_now_value/$request->meterage,
                            'total_value'     => $request->total_now_value,
                        ]);
                    }
                }
                return $unit;
            });

            return redirectMessage('success','واحد با موفقیت ویرایش شد',new UnitResource($unit));
        }
        catch (ValidationException $e){
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }
    }

    public function excel()
    {
        return Excel::download(new UnitExport(), 'units.xlsx');
    }

}
