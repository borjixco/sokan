<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserGenderEnum;
use App\Exports\OccupantExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\OccupantResource;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Models\JobType;
use App\Models\Lawyer;
use App\Models\Occupant;
use App\Models\Owner;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Userable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class OccupantController extends Controller
{
    public function index()
    {
        $search = request()->search;
        $rows = User::with(['jobTypes','occupants.unit','occupants' => function ($q) {
            return $q->where('status','CURRENT');
        }])
        ->whereHas('roles',function ($q){
            return $q->where('name','occupant');
        });
        if($search){
            $rows = $rows->where(function ($query) use($search){
                $query->where('name','LIKE',"%$search%")
                    ->orWhere('mobile','LIKE',"%$search%")
                    ->orWhere('national_code', 'LIKE',"%$search%");
            });
        }
        $rows = $rows->orderByDesc('id')->paginate(auth()->user()->perPage('admin',Occupant::class));
        $rows = UserResource::collection($rows);
        $perPages = perPages();
        return inertia('Admin/Occupants/Index',compact('rows', 'perPages'));
    }

    private function categories()
    {
        $categories = Category::where('model_type', Occupant::class)->get()->map(function ($category) {
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
        return inertia('Admin/Occupants/Create', compact('jobTypes', 'gender', 'years'));
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'name'                 => 'required|string|min:3',
                'national_code'        => 'required|digits_between:10,14',
                'father_name'          => 'nullable|string|min:3',
                'birth_day.value'      => 'nullable|numeric',
                'birth_month.value'    => 'nullable|numeric',
                'birth_year.value'     => 'nullable|numeric',
                'gender.value'         => 'nullable|in:'.collect(UserGenderEnum::cases())->pluck('name')->implode(','),
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
                $birthDate = isset($request->birth_day['value']) && isset($request->birth_month['value']) && isset($request->birth_year['value'])
                    ? verta()->parse("{$request->birth_day['value']}-{$request->birth_month['value']}-{$request->birth_year['value']}")->toCarbon()->format('Y-m-d')
                    : null;
                $user = User::create([
                    'name'              => $request->name,
                    'national_code'     => $request->national_code,
                    'father_name'       => $request->father_name,
                    'birth_date'        => $birthDate,
                    'gender'            => isset($request->gender['value']) ? $request->gender['value'] : null,
                    'address'           => $request->address,
                    'mobile'            => $request->mobile,
                    'mobile2'           => $request->mobile2,
                    'tel'               => $request->tel,
                ]);
                $role = Role::where('name', 'occupant')->first();
                $user->roles()->syncWithoutDetaching($role->id);
                if(isset($request->job_type)) {
                    $user->jobTypes()->attach($request->job_type);
                }
                return $user;
            });

            return redirectMessage('success','مستاجر با موفقیت ثبت شد',$user,isset($request->redirect) && $request->redirect == false ? null :  route('admin.occupants.edit',$user->id));


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
            $count = count($data);
            $occupantRole = Role::where('name', 'occupant')->first();
            foreach ($data as $row) {
                $unit = $units->where('unit_number', $row[0])->first();
                if($unit){
                    DB::transaction(function () use($row,$jobs,$unit,$occupantRole){
                        if(empty($row[8])){
                            $mob = '08'.generateRandomDigit(9);
                        }
                        else{
                            $mob = $row[8];
                        }
                        if(empty($row[2])){
                            $name = $mob;
                        }
                        else{
                            $name = $row[2];
                        }
                        $user = User::where('mobile', $mob)->first();
                        if (!$user) {
                            $gender = null;
                            if (in_array($row[6], ['آقا', 'خانم'])) {
                                $gender = UserGenderEnum::fromValue($row[6])->name;
                            }

                            $user = User::create([
                                'name'              => $name,
                                'national_code'     => $row[3],
                                'father_name'       => $row[4],
                                'birth_date'        => isValidDate($row[5]) ? verta()->parse($row[5])->toCarbon()->format('Y-m-d') : null,
                                'gender'            => $gender,
                                'address'           => $row[7],
                                'mobile'            => $mob,
                                'mobile2'           => $row[9],
                                'tel'               => $row[10],
                                //'computer_password' => $row[11],
                                //'case'              => $row[12],
                            ]);
                        }
                        if(!empty($row[13])) {
                            $job = $jobs->where('label', $row[13])->first();
                            if (!$job) {
                                $job = JobType::create(['label' => $row[13]]);
                                $newJob = new JobType(['id' => $job->id, 'label' => $row[13]]);
                                $jobs->push($newJob);
                            }
                            $user->jobTypes()->syncWithoutDetaching([$job->id]);
                        }
                        $user->roles()->syncWithoutDetaching($occupantRole->id);

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

                        $occupant = Occupant::create([
                            'user_id'           => $user->id,
                            'unit_id'           => $unit->id,
                            //'lawyer_id'         => $lawyerId,
                            'rental_start_date' => isValidDate($row[17]) ? verta()->parse($row[17])->toCarbon()->format('Y-m-d') : null,
                            'rental_end_date'   => isValidDate($row[18]) ? verta()->parse($row[18])->toCarbon()->format('Y-m-d') : null,
                            'drafting_date'     => isValidDate($row[19]) ? verta()->parse($row[19])->toCarbon()->format('Y-m-d') : null,
                            'regulator_id'      => null, //todo
                            'mortgage_amount'   => $row[23],
                            'rental_amount'     => $row[24],
                            'status'            => 'CURRENT',
                        ]);
                    });
                    $i++;
                }

            }
            return responseJSon('success',"$i رکورد از $count رکورد آپلود شد");
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
        $query = User::with(['jobTypes'])->find($user->id);
        $user = $query ? new UserResource($query) : [];
        $years = $this->years();
        $gender = $this->genders();
        $jobTypes = $this->jobTypes();
        $modelType = User::class;
        $documents = DocumentResource::collection($userOriginal->documents()->with(['categories','media'])->orderByDesc('created_at')->paginate(20));
        $categories = $this->categories();
        return inertia('Admin/Occupants/Edit', compact('user','years', 'gender', 'jobTypes', 'modelType' ,'documents', 'categories'));
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
                if(isset($request->job_type)){
                    $user->jobTypes()->sync($request->job_type);
                }
                return $user;
            });

            return redirectMessage('success','مستاجر با موفقیت ویرایش شد');
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
        return Excel::download(new OccupantExport, 'occupants.xlsx');
    }

}
