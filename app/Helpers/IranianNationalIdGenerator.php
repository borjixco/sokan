<?php

namespace App\Helpers;

class IranianNationalIdGenerator
{
    /**
     * ساخت یک کد ملی معتبر ایرانی
     */
    public static function generate(): string
    {
        // 9 رقم پایه
        $base = '';
        for ($i = 0; $i < 9; $i++) {
            $base .= random_int(0, 9);
        }

        // محاسبه رقم کنترل
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += intval($base[$i]) * (10 - $i);
        }

        $remainder = $sum % 11;

        $control = ($remainder < 2)
            ? $remainder
            : 11 - $remainder;

        return $base . $control;
    }

    /**
     * تولید چند کد ملی معتبر و بدون تکرار
     */
    public static function bulk(int $count = 1000): array
    {
        $unique = [];

        while (count($unique) < $count) {
            $id = self::generate();
            $unique[$id] = true;
        }

        return array_keys($unique);
    }
}
