<?php

namespace Database\Seeders;

use App\Enums\UserStatusEnum;
use App\Models\Floor;
use App\Models\Role;
use App\Models\User;
use App\Services\CityService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FloorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = ['-1', 'همکف پایین', 'همکف بالا', 'حاشیه', '1+', '2+', '3+'];
        foreach ($data as $label){
            Floor::create(['label' => $label]);
        }
    }
}
