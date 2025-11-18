<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    protected $fillable = ['login', 'code', 'expired_at'];

    protected $table = 'otp';

    public static function add($login, $code, $expired_at, $force = false)
    {
        if($force === false) {
            $otp = self::where('login', $login)->first();
            if (!$otp || $otp->expired_at <= now()) {
                if ($otp) {
                    $otp->delete();
                }
                return self::create([
                    'login' => $login,
                    'code' => $code,
                    'expired_at' => $expired_at
                ]);
            }
            return $otp;
        }
        else{
            $otp = self::where('login', $login)->first();
            if ($otp) {
                $otp->delete();
            }
            return self::create([
                'login' => $login,
                'code' => $code,
                'expired_at' => $expired_at
            ]);
        }
    }
}
