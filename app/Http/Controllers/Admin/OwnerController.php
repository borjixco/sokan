<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UnitTypeEnum;
use App\Enums\UserGenderEnum;
use App\Exports\OwnerExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\OwnerResource;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Models\Floor;
use App\Models\JobType;
use App\Models\Lawyer;
use App\Models\Owner;
use App\Models\Position;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use App\Models\Userable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class OwnerController extends Controller
{
    public function index()
    {
        $search = request()->search;
        $rows = User::with(['jobTypes','owners.unit', 'owners' => function ($q) {
                return $q->where('status','CURRENT');
            }])
            ->whereHas('roles',function ($q){
                return $q->where('name','owner');
            });
        if($search){
            $rows = $rows->where(function ($query) use($search){
                $query->where('name','LIKE',"%$search%")
                    ->orWhere('mobile','LIKE',"%$search%")
                    ->orWhere('national_code', 'LIKE',"%$search%");
            });
        }
        $rows = $rows->orderByDesc('id')->paginate(auth()->user()->perPage('admin',Owner::class));
        $rows = UserResource::collection($rows);
        $perPages = perPages();
        return inertia('Admin/Owners/Index',compact('rows', 'perPages'));
    }

    private function categories()
    {
        $categories = Category::where('model_type', Owner::class)->get()->map(function ($category) {
            return [
                'label' => $category->name,
                'value' => $category->id,
            ];
        });
        return $categories;

    }

    protected function jobTypes(){
        $jobTypeModel = JobType::all();
        $jobTypes = [];
        foreach ($jobTypeModel as $item){
            $jobTypes[] = [
                'value' => $item->id,
                'label' => $item->label,
            ];
        }
        return $jobTypes;
    }

    protected function genders(){
        $genderEnum = UserGenderEnum::cases();
        $gender = [];
        foreach ($genderEnum as $item){
            $gender[] = [
                'value' => $item->name,
                'label' => $item->value,
            ];
        }
        return $gender;
    }

    private function years()
    {
        $now = verta()->now()->format('Y');
        $years= [];
        for ($i=$now;$i>$now-100;$i--){
            $years[] = [
                'value' => $i,
                'label' => $i,
            ];
        }
        return $years;
    }


    public function create()
    {
        $jobTypes = $this->jobTypes();
        $gender   = $this->genders();
        $years    = $this->years();
        return inertia('Admin/Owners/Create', compact( 'jobTypes', 'gender', 'years'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name'                 => 'required|string|min:3',
                'national_code'        => 'required|digits_between:10,14',
                'father_name'          => 'nullable|string|min:3',
                'birth_day'            => 'nullable|numeric',
                'birth_month'          => 'nullable|numeric',
                'birth_year'           => 'nullable|numeric',
                'gender'               => 'nullable|in:'.collect(UserGenderEnum::cases())->pluck('name')->implode(','),
                'address'              => 'nullable|min:5',
                'mobile'               => 'required|unique:users,mobile|digits:11',
                'mobile2'              => 'nullable|digits:11',
                'tel'                  => 'nullable|numeric',
                'job_type'             => 'nullable|array',
                'job_type.*'           => 'exists:job_types,id',
            ],
            [
                'national_code'        => 'کدملی را به درستی وارد کنید',
                'mobile.required'      => 'وارد کردن شماره همراه اجباری می باشد',
                'mobile.unique'        => 'شماره همراه وارد شده قبلا ثبت شده است',
                'mobile.digits'        => 'شماره همراه باید :digits رقم باشد',
                'mobile2.digits'       => 'شماره همراه دوم باید :digits رقم باشد',
                'address'              => 'آدرس را به درستی وارد نمایید',
                'tel'                  => 'تلفن ثابت را به درستی وارد نمایید',
                'job_type.array'       => 'فیلد نوع شغل باید یک آرایه باشد.',
                'job_type.*.exists'    => 'یکی از نوع‌های شغل انتخاب‌شده نامعتبر است.',
            ]);
            $user = DB::transaction(function () use($request){
                $birthDate = isset($request->birth_day) && isset($request->birth_month) && isset($request->birth_year)
                    ? verta()->parse("{$request->birth_day}-{$request->birth_month}-{$request->birth_year}")->toCarbon()->format('Y-m-d')
                    : null;
                $user = User::create([
                    'name'              => $request->name,
                    'national_code'     => $request->national_code,
                    'father_name'       => $request->father_name,
                    'birth_date'        => $birthDate,
                    'gender'            => isset($request->gender) && !empty($request->gender) ? $request->gender : null,
                    'address'           => $request->address,
                    'mobile'            => $request->mobile,
                    'mobile2'           => $request->mobile2,
                    'tel'               => $request->tel,
                ]);
                $role = Role::where('name', 'owner')->first();
                $user->roles()->syncWithoutDetaching($role->id);
                if(isset($request->job_type) && !empty($request->job_type)) {
                    $user->jobTypes()->attach($request->job_type);
                }
                return $user;
            });

            return redirectMessage('success','مالک با موفقیت ثبت شد',$user,isset($request->redirect) && $request->redirect == false ? null : route('admin.owners.edit',$user->id));
        }
        catch (ValidationException $e){
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }
    }

    public function upload(Request $request)
    {
        try {
            set_time_limit(300);
            $request->validate([
                'file' => 'required|file|mimes:xls,xlsx,csv|max:1024',
            ]);
            $file = $request->file('file');
            $data = Excel::toArray([], $file); // خواندن کل داده‌ها به صورت آرایه
            $data = $data[0];
            unset($data[0]);
            unset($data[1]);
            //dd($data);
            $units = Unit::all();
            $jobs = JobType::all();
            $i = 0;
            $count = 0;
            $userCount = 0;
            $ownerRole = Role::where('name', 'owner')->first();
            foreach ($data as $row) {
                $count++;
                $unitNumber = $row[0];
                $unit = $units->where('unit_number', $unitNumber)->first();
                if($unit){
                    $dbData = DB::transaction(function () use($row,$jobs,$unit,$ownerRole){
                        $quotaCell = $row[1];
                        $avatarCell = $row[2];
                        $nameCell = $row[3];
                        $nationalCodeCell = $row[4];
                        $fatherNameCell = $row[5];
                        $birthDateCell = $row[6];
                        $genderCell = $row[7];
                        $addressCell = $row[8];
                        $mobileCell = $row[9];
                        $mobile2Cell = $row[10];
                        $telCell = $row[11];
                        $jobCell = $row[12];
                        $lawyerNameCell = $row[13];
                        $lawyerNationalCodeCell = $row[14];
                        $lawyerMobileCell = $row[15];
                        $purchaseDateCell = $row[16];
                        $reagentNameCell = $row[17];
                        $reagentNationalCodeCell = $row[18];
                        $reagentMobileCell = $row[19];
                        $ownershipCell = $row[20];
                        $goodwillRentalCell = $row[21];
                        if(empty($mobileCell)){
                            $mob = '08'.generateRandomDigit(9);
                        }
                        else{
                            $mob = $mobileCell;
                        }
                        if(empty($nameCell)){
                            $name = $mob;
                        }
                        else{
                            $name = $nameCell;
                        }
                        $user = User::where('mobile', $mob)->first();
                        $userCreated = 0;
                        if (!$user) {
                            $userCreated = 1;
                            $gender = null;
                            if (in_array($genderCell, ['آقا', 'خانم'])) {
                                $gender = UserGenderEnum::fromValue($genderCell)->name;
                            }
                            if(!empty($jobCell)) {
                                $job = $jobs->where('label', $jobCell)->first();
                                if (!$job) {
                                    $job = JobType::create(['label' => $jobCell]);
                                    $newJob = new JobType(['id' => $job->id, 'label' => $jobCell]);
                                    $jobs->push($newJob);
                                }
                            }
                            $user = User::create([
                                'name'              => $name,
                                'national_code'     => $nationalCodeCell,
                                'father_name'       => $fatherNameCell,
                                'birth_date'        => isValidDate($birthDateCell) ? verta()->parse($birthDateCell)->toCarbon()->format('Y-m-d') : null,
                                'gender'            => $gender,
                                'address'           => $addressCell,
                                'mobile'            => $mob,
                                'mobile2'           => $mobile2Cell,
                                'tel'               => $telCell,
                            ]);

                        }
                        if(!empty($jobCell)) {
                            $user->jobTypes()->syncWithoutDetaching([$job->id]);
                        }
                        $user->roles()->syncWithoutDetaching($ownerRole->id);


                        /*$lawyerId = null;
                        if(!empty($row[14]) && !empty($row[16])){
                            $lawyer = Lawyer::where('mobile',$row[16])->first();
                            if(!$lawyer){
                                $lawyer = Lawyer::create([
                                    'name'          => $row[14],
                                    'national_code' => $row[15],
                                    'mobile'        => $row[16],
                                ]);

                            }
                            Userable::create([
                                'userable_id' => $lawyer->id,
                                'userable_type' => Lawyer::class,
                                'user_id' => $user->id
                            ]);
                            $lawyerId = $lawyer->id;
                        }*/

                        $owner = Owner::create([
                            'user_id'         => $user->id,
                            'unit_id'         => $unit->id,
                            //'lawyer_id'       => $lawyerId,
                            //'doing_at'     => isValidDate($row[17]) ? verta()->parse($row[17])->toCarbon()->format('Y-m-d') : null,
                            //'reagent_id'      => null,
                            //'ownership'       => $row[21],
                            //'goodwill_rental' => $row[22],
                            'status'          => 'CURRENT',
                            'quota'           => convertDigits($quotaCell),
                        ]);
                        return [
                            'userCreated' => $userCreated
                        ];
                    });
                    $userCount += $dbData['userCreated'];
                    $i++;
                }

            }
            return responseJSon('success',"$i رکورد از $count رکورد آپلود شد"."\n"."تعداد $userCount کاربر اضافه شد");
        }
        catch (ValidationException $e){
            return responseJSon('error',$e->getMessage());
        }
        catch (\Exception $e){
            return responseJSon('error',$e->getMessage());
        }
    }

    public function edit(User $user)
    {
        $userOriginal = $user;
        $query = User::with(['jobTypes'])->find($userOriginal->id);
        $user = $query ? new UserResource($query) : [];
        $years = $this->years();
        $gender = $this->genders();
        $jobTypes = $this->jobTypes();
        $modelType = User::class;
        $documents = DocumentResource::collection($userOriginal->documents()->with(['categories','media'])->orderByDesc('created_at')->paginate(20));
        $categories = $this->categories();
        return inertia('Admin/Owners/Edit', compact('user','years', 'gender', 'jobTypes','modelType','documents', 'categories'));
    }

    public function update(User $user, Request $request)
    {
        try {
            $request->validate([
                'name'                 => 'required|string|min:3',
                'national_code'        => 'required|digits_between:10,14',
                'father_name'          => 'nullable|string|min:3',
                'birth_day'            => 'nullable|numeric',
                'birth_month'          => 'nullable|numeric',
                'birth_year'           => 'nullable|numeric',
                'gender.value'         => 'nullable|in:'.collect(UserGenderEnum::cases())->pluck('name')->implode(','),
                'address'              => 'nullable|min:5',
                'mobile'               => 'required|digits:11|unique:users,mobile,'.$user->id,
                'mobile2'              => 'nullable|digits:11',
                'tel'                  => 'nullable|numeric',
                'job_type'             => 'nullable|array',
                'job_type.*'           => 'exists:job_types,id',
            ],
            [
                'national_code'        => 'کدملی را به درستی وارد کنید',
                'mobile.required'      => 'وارد کردن شماره همراه اجباری می باشد',
                'mobile.unique'        => 'شماره همراه وارد شده قبلا ثبت شده است',
                'mobile.digits'        => 'شماره همراه باید :digits رقم باشد',
                'mobile2.digits'       => 'شماره همراه دوم باید :digits رقم باشد',
                'address'              => 'آدرس را به درستی وارد نمایید',
                'tel'                  => 'تلفن ثابت را به درستی وارد نمایید',
                'job_type.array'       => 'فیلد نوع شغل باید یک آرایه باشد.',
                'job_type.*.exists'    => 'یکی از نوع‌های شغل انتخاب‌شده نامعتبر است.',
            ]);
            DB::transaction(function () use($user,$request){
                $birthDate = isset($request->birth_day) && isset($request->birth_month) && isset($request->birth_year)
                    ? verta()->parse("{$request->birth_day}-{$request->birth_month}-{$request->birth_year}")->toCarbon()->format('Y-m-d')
                    : null;
                $userUpdate = $user->update([
                    'name'              => $request->name,
                    'national_code'     => $request->national_code,
                    'father_name'       => $request->father_name,
                    'birth_date'        => $birthDate,
                    'gender'            => $request->gender,
                    'address'           => $request->address,
                    'mobile'            => $request->mobile,
                    'mobile2'           => $request->mobile2,
                    'tel'               => $request->tel,
                ]);

                $user->jobTypes()->sync($request->job_type ?? []);
                return $user;
            });

            return redirectMessage('success','مالک با موفقیت ویرایش شد');
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
        return Excel::download(new OwnerExport(), 'owners.xlsx');
    }

}
