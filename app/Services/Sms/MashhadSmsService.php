<?php

namespace App\Services\Sms;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class MashhadSmsService implements SmsServiceInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(public $apiKey = null)
    {
        $smsSetting = Setting::get('admin.sms');
        $token = $smsSetting['mashhadSms']['token'];
        $this->apiKey = $token;
        $this->endpoint = 'https://api2.ippanel.com/api/v1';
    }

    public function sendPattern($patternCode, $patternValues, $recipient, $sender = '')
    {
        $sender = !$sender ? '3000505' : $sender;
        $response = Http::withHeaders([
            'apikey' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->endpoint.'/sms/pattern/normal/send', [
            'code'      => $patternCode,
            'sender'    => $sender,
            'recipient' => $recipient,
            'variable'  => $patternValues
        ]);
        return json_decode($response->body());
    }

    public function sendOtp($mob,$code)
    {
        return $this->sendPattern(config('sms.drivers.'.smsActiveDriver()["driver"].'.otpPattern'),["code" => $code],$mob);
    }
    public function sendUnitCharge($mob,$name,$crypt)
    {
        if(env('APP_ENV') != 'local') {
            //$mob = '09158573224';
            return $this->sendPattern(config('sms.drivers.'.smsActiveDriver()["driver"].'.chargePattern'), ["name" => $name, "crypt" => $crypt], $mob);
        }
    }

    public function sendBill($mob,$name,$crypt)
    {
        if(env('APP_ENV') != 'local') {
            //$mob = '09158573224';
            return $this->sendPattern(config('sms.drivers.'.smsActiveDriver()["driver"].'.billPattern'), ["name" => $name, "crypt" => $crypt], $mob);
        }
    }

}
