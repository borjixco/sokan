<?php

namespace App\Helpers;

class IranianAddressGenerator
{
    protected static array $locations = [
        'تهران' => ['تهران', 'ری', 'اسلامشهر', 'شمیرانات', 'شهریار', 'قدس', 'ملارد'],
        'البرز' => ['کرج', 'فردیس', 'نظرآباد', 'هشتگرد'],
        'اصفهان' => ['اصفهان', 'فولادشهر', 'کاشان', 'خمینی‌شهر', 'نجف‌آباد'],
        'فارس' => ['شیراز', 'مرودشت', 'کازرون', 'فسا', 'لار'],
        'خراسان رضوی' => ['مشهد', 'تربت', 'نیشابور', 'سبزوار'],
        'آذربایجان شرقی' => ['تبریز', 'مراغه', 'مرند', 'اهر'],
        'آذربایجان غربی' => ['ارومیه', 'ماکو', 'خوی'],
        'خوزستان' => ['اهواز', 'آبادان', 'خرمشهر', 'ماهشهر'],
        'گیلان' => ['رشت', 'آستارا', 'انزلی', 'تالش'],
        'مازندران' => ['ساری', 'آمل', 'بابلسر', 'چالوس'],
        'یزد' => ['یزد', 'اردکان', 'میبد'],
        'کرمان' => ['کرمان', 'سیرجان', 'رفسنجان'],
        'کردستان' => ['سنندج', 'سقز', 'مریوان'],
        'هرمزگان' => ['بندرعباس', 'قشم', 'کیش'],
        'بوشهر' => ['بوشهر', 'برازجان', 'گناوه'],
        'قزوین' => ['قزوین', 'البرز', 'آبیک'],
        'قم' => ['قم'],
    ];

    protected static array $streetNames = [
        'ولیعصر', 'مطهری', 'شریعتی', 'انقلاب', 'پیروزی', 'سعدی', 'جمهوری',
        'مفتح', 'سپهبد قرنی', 'کارگر', 'بهشتی', 'بلوار امام', 'بلوار مدرس',
        'هاشمی', 'حکیم', 'باهنر', 'کشاورز', 'شهید رجایی', 'آیت‌الله کاشانی',
    ];

    /**
     * ساخت یک آدرس ایرانی نچرال
     */
    public static function generate(): array
    {
        // انتخاب استان و شهر
        $province = array_rand(self::$locations);
        $city = self::$locations[$province][array_rand(self::$locations[$province])];

        // اجزای آدرس
        $street = self::$streetNames[array_rand(self::$streetNames)];
        $alley = 'کوچه ' . random_int(1, 200);
        $plaque = 'پلاک ' . random_int(1, 300);
        $unit = 'واحد ' . random_int(1, 20);
        $postalCode = self::generatePostalCode();

        return [
            'province'    => $province,
            'city'        => $city,
            'street'      => "خیابان {$street}",
            'alley'       => $alley,
            'plaque'      => $plaque,
            'unit'        => $unit,
            'postal_code' => $postalCode,
            'full'        => "استان {$province}، شهر {$city}، خیابان {$street}، {$alley}، {$plaque}، {$unit}",
        ];
    }

    /**
     * کدپستی معتبر (۱۰ رقمی، شروع منطقی برای شهرها)
     */
    protected static function generatePostalCode(): string
    {
        $first = random_int(1, 9); // شروع هیچ‌وقت صفر نیست
        $rest = '';

        for ($i = 0; $i < 9; $i++) {
            $rest .= random_int(0, 9);
        }

        return $first . $rest;
    }

    /**
     * تولید چند آدرس بدون تکرار
     */
    public static function bulk(int $count = 1000): array
    {
        $unique = [];

        while (count($unique) < $count) {
            $address = self::generate();
            $unique[md5($address['full'])] = $address;
        }

        return array_values($unique);
    }
}
