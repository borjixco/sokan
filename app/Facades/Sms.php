<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Sms extends Facade
{
    /**
     * @method static bool sendOtp(string $mobile, string $code)
     * @method static bool sendBill(string $mobile, string $name, string $crypt)
     * @method static bool sendUnitCharge(string $mobile, string $name, string $crypt)
     * @method static object sendPattern(string $patternCode, array $patternValues, string $recipient, string $sender = '')
     *
     * @see \App\Services\Sms\SmsServiceInterface
     */
    protected static function getFacadeAccessor(): string
    {
        return 'sms';
    }
}
