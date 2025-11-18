<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\OTP;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login()
    {
        return inertia('Client/Auth/Login');
    }

    private function loginGenerate($request,$otpForce = false)
    {
        $validate = Validator::make($request->all(),[
            'mobile' => 'required|mobile|exists:users,mobile',
        ],
        [
            'mobile.exists' => 'شماره همراه وارد شده وجود ندارد'
        ]);
        if($validate->errors()->count()){
            return redirectMessage('error',$validate->errors()->first());
        }
        $user = User::where('mobile',$request->mobile)->first();
        if(!$user->roles()->pluck('access_to')->contains('app')){
            return redirectMessage('error','اجازه وجود به پنل را ندارید');
        }
        $code = 12345;
        OTP::add($request->mobile,$code,now()->addMinutes(5),$otpForce);
        return redirectMessage('success',null, ['step' => 2]);
    }

    public function loginProcess(Request $request)
    {

        return $this->loginGenerate($request);

    }

    public function resend(Request $request)
    {
        return $this->loginGenerate($request,true);
    }

    public function verify(Request $request)
    {

        try {
            $request->validate([
                'mobile' => 'required|mobile|exists:users,mobile',
                'code' => 'required|digits:5',
            ],
            [
                'mobile.exists' => 'شماره همراه وارد شده وجود ندارد'
            ]);

            $user = User::where('mobile',$request->mobile)->first();
            $otp = OTP::where('login', $request->mobile)->first();
            if (!$otp) {
                return redirectMessage('error', 'خطایی پیش آمده لطفا دوباره تلاش کنید');
            }
            elseif (Carbon::parse($otp->expired_at)->timestamp < now()->timestamp) {
                return redirectMessage('error', 'کد منقضی شده دوباره کد را دریافت کنید');
            }
            elseif ($otp->code != $request->code) {
                return redirectMessage('error', 'کد وارد شده صحیح نیست');
            }
            else {
                auth()->login($user, true);
                $otp->delete();
                return redirectMessage('success','ورود با موفقیت انجام شد',null,route('client.charges'));
            }

        }
        catch (ValidationException $e){
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirectMessage('success','خروج با موفقیت انجام شد',null,route('client.login'));
    }
}
