<?php

namespace App\Services\Sms;
use Illuminate\Support\Facades\Http;

class SmsIrService
{
    /**
     * Create a new class instance.
     */
    public function __construct(public $apiKey = null, public $endpoint = null)
    {
        $this->apiKey = "FXhu-4eFW8I88e0zj4LWZSNm1bLZbPjRBwR9DgihUFg=";
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
        return $this->sendPattern('ya73yd76j7dnkr4',["code" => $code],$mob);
    }

    public function sendUnitCharge($mob,$name,$crypt)
    {
        if(env('APP_ENV') != 'local') {
            //$mob = '09158573224';
            return $this->sendPattern('0jmlr3314jksogw', ["name" => $name, "crypt" => $crypt], $mob);
        }
    }

    public function sendBill($mob,$name,$crypt)
    {
        if(env('APP_ENV') != 'local') {
            //$mob = '09158573224';
            return $this->sendPattern('0jmlr3314jksogw', ["name" => $name, "crypt" => $crypt], $mob);
        }
    }

}
