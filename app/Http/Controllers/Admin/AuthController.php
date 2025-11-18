<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        return inertia('Admin/Auth/Login');
    }

    public function loginProcess(Request $request)
    {

        $validate = Validator::make($request->all(),[
            'mobile' => 'required|mobile|exists:users,mobile',
            'password' => 'required',
        ],
        [
            'mobile.required'   => 'شمراه همراه را وارد کنید',
            'password.required' => 'کلمه عبور را وارد نمایید',
            'mobile.exists'     => 'شماره همراه و یا کلمه عبور اشتباه است',
        ]);
        if($validate->errors()->count()){
            return redirectMessage('error',$validate->errors()->first());
        }
        $user = User::where('mobile',$request->mobile)->first();
        if(!$user->roles()->pluck('access_to')->contains('admin') || !Hash::check($request->password,$user->password)){
            return redirectMessage('error','شماره همراه و یا کلمه عبور اشتباه است');
        }
        auth()->login($user,true);
        return redirectMessage('success','ورود با موفقیت انجام شد',null,route('admin.dashboard'));

    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirectMessage('success','خروج با موفقیت انجام شد',null,route('admin.login'));
    }
}
