<?php

namespace App\Helpers;

class IranianMobileGenerator
{
    /**
     * پیشوندهای معتبر اپراتورها
     */
    protected static array $prefixes = [
        // همراه اول
        '0910', '0911', '0912', '0913', '0914', '0915', '0916', '0917', '0918', '0919',
        '0990', '0991', '0992',

        // ایرانسل
        '0901', '0902', '0903', '0904', '0905',
        '0930', '0933', '0935', '0936', '0937', '0938', '0939',

        // رایتل
        '0920', '0921', '0922',

        // شاتل موبایل
        '0998',

        // تالیا
        '0932',
    ];

    /**
     * ساخت یک خط موبایل معتبر ایرانی
     */
    public static function generate(): string
    {
        $prefix = self::$prefixes[array_rand(self::$prefixes)];

        // 7 رقم بعدی
        $line = '';
        for ($i = 0; $i < 7; $i++) {
            $line .= random_int(0, 9);
        }

        return $prefix . $line;
    }

    /**
     * تولید تعداد زیادی شماره بدون تکرار
     */
    public static function bulk(int $count = 1000): array
    {
        $unique = [];

        while (count($unique) < $count) {
            $mobile = self::generate();
            $unique[$mobile] = true;
        }

        return array_keys($unique);
    }
}
