<?php

use App\Enums\UserGenderEnum;
use App\Models\Category;
use App\Models\JobType;
use App\Models\Owner;
use App\Models\Setting;
use Illuminate\Support\Str;

if (!function_exists('redirectMessage')) {
    function redirectMessage($status, $message = null, $data = null, $redirect = null)
    {
        if ($status == 'success') {
            return $redirect === null
                ? back()->with($status, ['message' => $message, 'data' => $data])
                : redirect($redirect)->with($status, $message);
        } else {
            return $redirect === null
                ? back()->withErrors(['status' => $status, 'message' => $message])
                : redirect($redirect)->withErrors(['status' => $status, 'message' => $message]);
        }

    }

}
if (!function_exists('responseJSon')) {
    function responseJSon($status, $message, $data = [], $state = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $state);

    }
}
if (!function_exists('convertDigits')) {
    function convertDigits($string)
    {
        if ($string === null)
            return null;
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        $num = range(0, 9);
        $convertedPersianNums = str_replace($persian, $num, $string);
        $englishNumbersOnly = str_replace($arabic, $num, $convertedPersianNums);

        return $englishNumbersOnly;
    }
}

if (!function_exists('isValidDate')) {
    function isValidDate($date)
    {
        // الگوی پذیرش تاریخ به دو فرمت: yyyy/mm/dd و yyyy-mm-dd
        $pattern = '/^(13|14)\d{2}[\/-](0[1-9]|1[0-2])[\/-](0[1-9]|[12][0-9]|3[01])$/';
        // بررسی تطابق با الگو
        if (preg_match($pattern, $date)) {
            // جداسازی اجزای تاریخ برای بررسی صحت آن (مانند ماه‌های 30 و 31 روزه)
            $separator = strpos($date, '/') !== false ? '/' : '-';
            list($year, $month, $day) = explode($separator, $date);

            // بررسی صحت مقادیر سال، ماه و روز
            return checkdate((int)$month, (int)$day, (int)$year);
        }

        return false; // اگر تطابق نداشته باشد
    }
}

if (!function_exists('months')) {
    function months()
    {
        return [
            [
                'label' => 'فروردین',
                'value' => 1,
            ],
            [
                'label' => 'اردیبهشت',
                'value' => 2,
            ],
            [
                'label' => 'خرداد',
                'value' => 3,
            ],
            [
                'label' => 'تیر',
                'value' => 4,
            ],
            [
                'label' => 'مرداد',
                'value' => 5,
            ],
            [
                'label' => 'شهریور',
                'value' => 6,
            ],
            [
                'label' => 'مهر',
                'value' => 7,
            ],
            [
                'label' => 'آبان',
                'value' => 8,
            ],
            [
                'label' => 'آذر',
                'value' => 9,
            ],
            [
                'label' => 'دی',
                'value' => 10,
            ],
            [
                'label' => 'بهمن',
                'value' => 11,
            ],
            [
                'label' => 'اسفند',
                'value' => 12,
            ],
        ];
    }
}

if (!function_exists('dayes')) {
    function days()
    {
        $days = [];
        for ($i = 1; $i <= 31; $i++) {
            $days[] = [
                'label' => $i,
                'value' => $i,
            ];
        }
        return $days;
    }
}

if (!function_exists('hours')) {
    function hours()
    {
        $hours = [];
        for ($i = 0; $i <= 23; $i++) {
            $hours[] = [
                'label' => $i,
                'value' => $i,
            ];
        }
        return $hours;
    }
}


if (!function_exists('minutes')) {
    function minutes()
    {
        $minutes = [];
        for ($i = 0; $i <= 59; $i++) {
            $minutes[] = [
                'label' => $i,
                'value' => $i,
            ];
        }
        return $minutes;
    }
}

if (!function_exists('enumNames')) {
    function enumNames($enumCase)
    {
        return collect($enumCase)->pluck('name')->toArray();
    }
}

if (!function_exists('enumFormated')) {
    function enumFormated($enumCase)
    {
        return collect($enumCase)->map(function ($item) {
            return [
                'label' => $item->value,
                'value' => $item->name,
            ];
        });
    }
}

if (!function_exists('base62Encode')) {
    function base62Encode($data)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base = strlen($characters);
        $result = '';
        $num = gmp_init(bin2hex($data), 16); // استفاده از gmp برای کار با اعداد بزرگ
        while (gmp_cmp($num, 0) > 0) {
            $remainder = gmp_mod($num, $base);
            $result = $characters[gmp_intval($remainder)] . $result;
            $num = gmp_div_q($num, $base);
        }
        return $result;
    }
}

if (!function_exists('base62Decode')) {
    function base62Decode($data)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base = strlen($characters);
        $num = gmp_init(0, 10); // استفاده از gmp برای مقداردهی اولیه عدد صفر

        // پردازش هر کاراکتر از رشته Base62
        for ($i = 0, $len = strlen($data); $i < $len; $i++) {
            $num = gmp_add(gmp_mul($num, $base), strpos($characters, $data[$i]));
        }
        // تبدیل عدد به باینری
        $hex = gmp_strval($num, 16); // تبدیل عدد به رشته هگزادسیمال
        if (strlen($hex) % 2 != 0) {
            $hex = '0' . $hex; // اضافه کردن صفر برای تبدیل صحیح به باینری
        }

        return hex2bin($hex);
    }
}


if (!function_exists('paymentCrypt')) {
    function paymentCrypt($type, $id)
    {
        $rand = Str::random(4);
        return base62Encode($type . '-' . $rand . '-' . $id);
    }
}

if (!function_exists('classPath')) {
    function classPath($className)
    {
        $className = ucfirst($className);
        return 'App\\Models\\' . $className;
    }
}

if (!function_exists('years')) {
    function years($incremental = false, $startYear = null)
    {
        if ($startYear) {
            $now = $startYear;
        } else {
            $now = verta()->now()->format('Y');
        }
        $years = [];
        if ($incremental) {
            for ($i = $now; $i < $now + 100; $i++) {
                $years[] = [
                    'value' => $i,
                    'label' => $i,
                ];
            }
        } else {
            for ($i = $now; $i > $now - 100; $i--) {
                $years[] = [
                    'value' => $i,
                    'label' => $i,
                ];
            }
        }
        return $years;
    }
}

if (!function_exists('genders')) {
    function genders()
    {
        return collect(UserGenderEnum::cases())->map(function ($item) {
            return [
                'label' => $item->value,
                'value' => $item->name,
            ];
        });
    }
}
if (!function_exists('removeFirstZeros')) {
    function removeFirstZeros($number)
    {
        return ltrim($number, '0') ?: '0';
    }
}

if (!function_exists('getMimeTypeTitle')) {
    function getMimeTypeTitle($mimeType)
    {
        $types = [
            'image/jpeg' => 'تصویر',
            'image/png' => 'تصویر',
            'image/gif' => 'تصویر',
            'image/webp' => 'تصویر',
            'application/pdf' => 'PDF',
            'application/msword' => 'فایل Word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'فایل Word',
            'application/vnd.ms-excel' => 'فایل Excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'فایل Excel',
            'text/plain' => 'فایل متنی',
            'video/mp4' => 'ویدئو',
            'audio/mpeg' => 'فایل صوتی',
            'application/zip' => 'فایل فشرده',
            'application/x-rar-compressed' => 'فایل فشرده',
        ];

        return $types[$mimeType] ?? 'فرمت ناشناخته';
    }
}

if (!function_exists('formatSize')) {
    function formatSize($byte)
    {
        if ($byte < 1024) {
            return $byte . ' بایت';
        } elseif ($byte < 1048576) { // کمتر از یک مگابایت
            return round($byte / 1024, 2) . ' کیلوبایت';
        } elseif ($byte < 1073741824) { // کمتر از یک گیگابایت
            return round($byte / 1048576, 2) . ' مگابایت';
        } else { // بیشتر از یک گیگابایت
            return round($byte / 1073741824, 2) . ' گیگابایت';
        }
    }
}


if (!function_exists('jobs')) {
    function jobs()
    {
        $jobTypeModel = JobType::all();
        $jobTypes = [];
        foreach ($jobTypeModel as $item) {
            $jobTypes[] = [
                'value' => $item->id,
                'label' => $item->label,
            ];
        }
        return $jobTypes;
    }
}

if (!function_exists('categories')) {
    function categories($modelType)
    {
        $categories = Category::where('model_type', $modelType)->get()->map(function ($category) {
            return [
                'label' => $category->name,
                'value' => $category->id,
            ];
        });
        return $categories;

    }
}

if (!function_exists('jobTypesObject')) {
    function jobTypesObject($types)
    {
        $data = null;
        if ($types) {
            foreach ($types as $item) {
                $data[] = [
                    'value' => $item->id,
                    'label' => $item->label,
                ];
            }
        }
        return $data;
    }
}

if (!function_exists('perPages')) {
    function perPages()
    {
        return [20, 50, 100, 150, 300, 1000];
    }
}

if (!function_exists('generateRandomDigit')) {
    function generateRandomDigit($len = 10): string
    {
        return str_pad(strval(random_int(0, 9999999999)), $len, '0', STR_PAD_LEFT);
    }
}
if (!function_exists('generateTransactionId')) {
    function generateTransactionId()
    {
        return Str::random(3) . (now()->getTimestampMs() * mt_rand(100, 2000));
    }
}

if (!function_exists('smsDrivers')) {
    function smsDrivers()
    {
        return collect(config('sms.drivers'))
            ->map(fn($driver, $key) => [
                'driver' => $key,
                'title' => $driver['title'] ?? $key,
            ])
            ->values()
            ->toArray();
    }
}

if (!function_exists('smsActiveDriver')) {
    function smsActiveDriver()
    {
        $drivers = smsDrivers();
        $active = Setting::get('admin.sms')['active'] ?? null;
        return collect($drivers)->filter(fn($driver) => $driver['driver'] == $active)->first();
    }
}
