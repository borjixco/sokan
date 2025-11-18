<?php

namespace App\Services\Sms;

use App\Models\Setting;

class SmsResolver
{
    public static function resolve(): SmsServiceInterface
    {
        // تنظیمات از دیتابیس
        $settings = Setting::get('admin.sms');
        $activeKey = $settings['active'] ?? null;

        // لیست درایورها از config
        $drivers = config('sms.drivers');

        if (!$activeKey || !isset($drivers[$activeKey])) {
            throw new \Exception("SMS driver [$activeKey] not found.");
        }

        $class = $drivers[$activeKey]['class'];
        $token = $settings[$activeKey]['token'] ?? null;

        return new $class($token);
    }
}
