<?php

namespace Database\Seeders;

use App\Enums\UserStatusEnum;
use App\Models\Floor;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use App\Services\CityService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = ['مانتو و روسری', 'پوشاک مردانه و زنانه', 'پوشاک مانتو زعفران', 'کیف و کفش لوازم ارایش بدلیجات و ....', 'سیسمونی کودک', 'فست فود - آبمیوه و بستنی', 'فست فود - سوپر مارکت', 'فست فود - ساندویچ سرد', 'فست فود - پیتزا', 'فست فود - غذای ایرانی و سنتی', 'فست فود - سیب زمینی و برگر', 'فست فود - ساندویچ گرم', 'فست فود - سوخاری', 'تاسیسات'];
        foreach ($data as $label){
            Position::create(['label' => $label]);
        }
    }
}
