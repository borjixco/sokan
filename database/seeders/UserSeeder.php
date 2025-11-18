<?php

namespace Database\Seeders;

use App\Enums\UserStatusEnum;
use App\Models\Role;
use App\Models\User;
use App\Services\CityService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name'              => 'حسین هزاره',
                'mobile'            => '09158573224',
                'email'             => 'hosseinhezareh2@gmail.com',
                'email_verified_at' => now(),
                'password'          => bcrypt('123456'),
                'role'              => 'superadmin'
            ],
            [
                'name'              => 'علی مظلوم',
                'mobile'            => '09303736415',
                'email'             => 'ali.mzi210@gmail.com',
                'email_verified_at' => now(),
                'password'          => bcrypt('123456'),
                'role'              => 'superadmin'
            ],
            [
                'name'              => 'کاربر دمو',
                'mobile'            => '09123456789',
                'email'             => null,
                'email_verified_at' => now(),
                'password'          => bcrypt('123456'),
                'role'              => 'admin'
            ],
        ];

        foreach ($data as $item){
            $roleName = $item['role'];
            unset($item['role']);
            $user = User::create($item);
            $role = Role::where('name', $roleName)->first();
            $user->roles()->attach($role->id);
        }
    }
}
