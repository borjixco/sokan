<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserGenderEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $role = $request->role;
        $employees = User::with('roles')->whereHas('roles',function ($q){
            $q->whereIn('name',['employee','supervisor']);
        });
        if($search){
            $employees = $employees->where(function ($q) use($search){
                $q->where('name','LIKE', "%$search%")
                ->orWhere('mobile','LIKE', "%$search%")
                ->orWhere('national_code','LIKE', "%$search%");
            });
        }
        if($role){
            $employees = $employees->whereHas('roles', function ($q) use($role){
               $q->where('id', $role);
            });
        }
        $employees = $employees->orderByDesc('id')->paginate(auth()->user()->perPage('admin',User::class));
        $rows = UserResource::collection($employees);
        $roles = $this->roles();
        $perPages = perPages();
        return inertia('Admin/Employees/Index', compact('rows','roles', 'perPages'));
    }

    private function categories()
    {
        $categories = Category::where('model_type', User::class)->get()->map(function ($category) {
            return [
                'label' => $category->name,
                'value' => $category->id,
            ];
        });
        return $categories;

    }

    private function roles()
    {
        $roles = Role::whereIn('name', ['employee', 'supervisor'])->get()->map(function ($role) {
            return [
                'label' => $role->label,
                'value' => $role->id,
            ];
        });
        return $roles;
    }

    private function supervisors()
    {
        $supervisors = User::with('categories')->whereHas('roles', function ($query) {
            $query->where('name', 'supervisor');
        })->get()->map(function ($role) {
            return [
                'label'    => $role->name,
                'value'    => $role->id,
                'category' => $role->categories->first()?->id,
            ];
        });
        return $supervisors;
    }

    public function create()
    {
        $gender = genders();
        $years = years();
        $months = months();
        $days = days();
        $categories = $this->categories();
        $roles = $this->roles();
        $supervisors = $this->supervisors();
        return inertia('Admin/Employees/Create', compact('gender','years', 'months', 'days', 'categories', 'roles', 'supervisors'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        try {
            $request->validate([
                'name'          => 'required|string|min:3',
                'national_code' => 'nullable|digits_between:10,14',
                'father_name'   => 'nullable|string|min:3',
                'birth_day'     => 'nullable|numeric',
                'birth_month'   => 'nullable|numeric',
                'birth_year'    => 'nullable|numeric',
                'gender'        => 'nullable|in:'.collect(UserGenderEnum::cases())->pluck('name')->implode(','),
                'address'       => 'nullable|min:5',
                'mobile'        => 'required|unique:users,mobile|digits:11',
                'mobile2'       => 'nullable|digits:11',
                'tel'           => 'nullable|numeric',
                'category'      => $request->role == 7 ? 'required|exists:categories,id' : 'nullable|exists:categories,id',
                'role'          => 'required|exists:roles,id',
                'supervisor'    => 'nullable|exists:users,id',
            ],
            [
                'name.required'     => 'نام و نام خانوادگی را وارد کنید',
                'national_code'     => 'کدملی را به درستی وارد نمایید',
                'father_name'       => 'نام پدر را به درستی وارد نمایید',
                'mobile.required'   => 'شماره موبایل را وارد کنید',
                'mobile.unique'     => 'شماره موبایل تکراری است',
                'mobile.digits'     => 'شماره موبایل باید 11 رقم باشد',
                'category'          => 'گروه کاری را به درستی انتخاب نمایید',
                'role.required'     => 'نقش را وارد کنید',
                'supervisor.exists' => 'سرپرست را وارد کنید',
            ]);

            $user = DB::transaction(function () use($request){
                $birthDate = isset($request->birth_day) && isset($request->birth_month) && isset($request->birth_year)
                    ? verta()->parse("{$request->birth_day}-{$request->birth_month}-{$request->birth_year}")->toCarbon()->format('Y-m-d')
                    : null;
                $user = User::create([
                    'name'          => $request->name,
                    'national_code' => $request->national_code,
                    'father_name'   => $request->father_name,
                    'birth_date'    => $birthDate,
                    'gender'        => $request->gender,
                    'address'       => $request->address,
                    'mobile'        => $request->mobile,
                    'mobile2'       => $request->mobile2,
                    'tel'           => $request->tel,
                    'supervisor_id' => $request->supervisor,
                ]);
                if($request->category) {
                    $user->categories()->attach([$request->category]);
                }
                $user->roles()->attach($request->role);
                return $user;
            });
            return redirectMessage('success','پرسنل با موفقیت ایجاد شد',null,route('admin.employees.edit',$user->id));
        }
        catch (ValidationException $e) {
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e) {
            return redirectMessage('error',$e->getMessage());
        }
    }

    public function edit(User $employee, Request $request)
    {
        $gender = genders();
        $years = years();
        $months = months();
        $days = days();
        $categories = $this->categories();
        $roles = $this->roles();
        $supervisors = $this->supervisors();
        $employee = new UserResource($employee);
        return inertia('Admin/Employees/Edit', compact('employee','gender','years', 'months', 'days', 'categories', 'roles', 'supervisors'));
    }

    public function update(User $employee, Request $request)
    {
        //dd($request->all());
        try {
            $request->validate([
                'name'          => 'required|string|min:3',
                'national_code' => 'nullable|digits_between:10,14',
                'father_name'   => 'nullable|string|min:3',
                'birth_day'     => 'nullable|numeric',
                'birth_month'   => 'nullable|numeric',
                'birth_year'    => 'nullable|numeric',
                'gender'        => 'nullable|in:'.collect(UserGenderEnum::cases())->pluck('name')->implode(','),
                'address'       => 'nullable|min:5',
                'mobile'        => 'required|unique:users,mobile,'.$employee->id.'|digits:11',
                'mobile2'       => 'nullable|digits:11',
                'tel'           => 'nullable|numeric',
                'category'      => 'nullable|exists:categories,id',
                'role'          => 'required|exists:roles,id',
                'supervisor'    => 'nullable|exists:users,id',
            ],
            [
                'name.required'     => 'نام و نام خانوادگی را وارد کنید',
                'national_code'     => 'کدملی را به درستی وارد نمایید',
                'father_name'       => 'نام پدر را به درستی وارد نمایید',
                'mobile.required'   => 'شماره موبایل را وارد کنید',
                'mobile.unique'     => 'شماره موبایل تکراری است',
                'mobile.digits'     => 'شماره موبایل باید 11 رقم باشد',
                'category'          => 'گروه کاری را به درستی انتخاب نمایید',
                'role.required'     => 'نقش را وارد کنید',
                'supervisor.exists' => 'سرپرست را وارد کنید',
            ]);

            $employeeUpdate = DB::transaction(function () use($employee,$request){
                $birthDate = isset($request->birth_day) && isset($request->birth_month) && isset($request->birth_year)
                    ? verta()->parse("{$request->birth_day}-{$request->birth_month}-{$request->birth_year}")->toCarbon()->format('Y-m-d')
                    : null;
                $employee->update([
                    'name'          => $request->name,
                    'national_code' => $request->national_code,
                    'father_name'   => $request->father_name,
                    'birth_date'    => $birthDate,
                    'gender'        => $request->gender,
                    'address'       => $request->address,
                    'mobile'        => $request->mobile,
                    'mobile2'       => $request->mobile2,
                    'tel'           => $request->tel,
                    'supervisor_id' => $request->supervisor,
                ]);
                $employee->categories()->detach();
                if($request->category) {
                    $employee->categories()->attach([$request->category]);
                }
                $role = Role::find((int)$request->role);
                if($role->name == 'supervisor'){
                    $employee->update(['supervisor_id' => null]);
                }
                $employee->roles()->sync($request->role);
                return $employee;
            });
            return redirectMessage('success','پرسنل با موفقیت ویرایش شد');
        }
        catch (ValidationException $e) {
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e) {
            return redirectMessage('error',$e->getMessage());
        }
    }
}
