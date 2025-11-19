<?php

namespace App\Helpers;

class FakePersianNameGenerator
{
    protected static array $maleNames = [
        'آرین','نیما','رادین','آرمین','آرش','شروین','کیان','سام','ماهان','مهران',
        'باراد','رایان','پرهام','امیررضا','سهند','کاوه','رهام','نوید','مانی','پارسا',
        'سامیار','مهدیار','پوریا','بهراد','سینا','آراد'
    ];

    protected static array $femaleNames = [
        'نازنین','پریا','ترانه','پرنیان','آیدا','نیلوفر','سارینا','شقایق','هانیه','مهسا',
        'یگانه','رعنا','تینا','نگار','بهاره','ریحانه','مائده','ملیسا','درسا','هلیا',
        'النا','الهام','شادی','نسترن','بهناز','ثنا','بهار'
    ];

    protected static array $lastNames = [
        'محمدی','احمدی','رضایی','حسینی','کریمی','صادقی','عباسی','نوری','مرادی','اکبری',
        'موسوی','حیدری','رستمی','قاسمی','جعفری','پاکدل','مقدم','زارع','فلاح','برزگر',
        'یزدانی','شفیعی','خسروی','صمدی','فرهمند','الهامی','نوروزی','طهماسبی','رهبری',
    ];

    /**
     * تولید یک نام فارسی طبیعی و یونیک
     */
    public static function generateUniqueCombination(int $index = 0): string
    {
        // جنسیت تصادفی
        $isMale = (bool) random_int(0, 1);
        $names = $isMale ? self::$maleNames : self::$femaleNames;

        // انتخاب دو اسم و یک نام خانوادگی
        shuffle($names);
        $first = $names[0];
        $second = $names[1];
        $last = self::$lastNames[array_rand(self::$lastNames)];

        // با احتمال 60٪ فقط یک اسم بده برای طبیعی بودن
        $useDouble = random_int(1, 100) <= 40;
        $fullName = $useDouble ? "{$first} {$second} {$last}" : "{$first} {$last}";

        // جلوگیری از تکرار از طریق اندیس در انتهای shuffle داخلی
        return $fullName;
    }

    /**
     * ساخت چند اسم یونیک بدون تکرار
     */
    public static function bulk(int $count = 1000): array
    {
        $unique = [];

        while (count($unique) < $count) {
            $name = self::generateUniqueCombination(count($unique));
            $unique[$name] = true; // تضمین عدم تکرار
        }

        return array_keys($unique);
    }
}
