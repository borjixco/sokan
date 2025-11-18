<?php

namespace Database\Seeders;

use App\Enums\UserStatusEnum;
use App\Models\Floor;
use App\Models\JobType;
use App\Models\Role;
use App\Models\User;
use App\Services\CityService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class JobTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = ['روسری', 'دستگاههای سرمایشی و گرمایشی', 'پوشاک', 'بدلیجات', 'پوشاک زنانه', 'ساعت فروش بازار رضا', 'کتاب فروش'];
        foreach ($data as $label){
            JobType::create(['label' => $label]);
        }
    }
}
