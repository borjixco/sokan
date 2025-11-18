<?php
namespace App\Services\Sms;

interface SmsServiceInterface
{
    public function sendOtp(string $mobile, string $code);
    public function sendUnitCharge(string $mobile, string $name, string $crypt);
    public function sendBill(string $mobile, string $name, string $crypt);
}
