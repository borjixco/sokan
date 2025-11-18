<?php
return [
    'drivers' => [
        'mashhadSms' => [
            'title' => 'مشهد پیامک',
            'class' => \App\Services\Sms\MashhadSmsService::class,
            'otpPattern' => env('MASHHAD_SMS_OTP_PATTERN', null),
            'chargePattern' => env('MASHHAD_SMS_CHARGE_PATTERN', null),
        ],
        'smsIr' => [
            'title' => 'sms.ir',
            'class' => \App\Services\Sms\SmsIrService::class,
        ],
    ],
];
