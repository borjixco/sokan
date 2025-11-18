<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'superadmin',
                'label' => 'مدیرکل',
                'access_to' => 'admin',
            ],
            [
                'name' => 'admin',
                'label' => 'مدیر',
                'access_to' => 'admin',
            ],
            [
                'name' => 'operator',
                'label' => 'اپراتور',
                'access_to' => 'admin',
            ],
            [
                'name' => 'owner',
                'label' => 'مالک',
                'access_to' => 'app',
            ],
            [
                'name' => 'occupant',
                'label' => 'مستاجر',
                'access_to' => 'app',
            ],
            [
                'name'  => 'employee',
                'label' => 'پرسنل',
                'access_to' => 'app',
            ],
            [
                'name'  => 'supervisor',
                'label' => 'سرپرست',
                'access_to' => 'app',
            ],
        ];
        foreach ($data as $item){
            Role::create([
                'name' => $item['name'],
                'label' => $item['label'],
                'access_to' => $item['access_to'],
            ]);
        }

    }
}
