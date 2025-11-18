<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Contract;
use App\Models\Facility;
use App\Models\Occupant;
use App\Models\Owner;
use App\Models\Ticket;
use App\Models\Transfer;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name'       => 'عمومی',
                'model_type' => Ticket::class,
            ],
            [
                'name'       => 'مدیریت',
                'model_type' => User::class,
            ],
            [
                'name'       => 'فروش',
                'model_type' => User::class,
            ],
            [
                'name'       => 'تاسیسات مکانیک',
                'model_type' => User::class,
            ],
            [
                'name'       => 'تاسیسات برق',
                'model_type' => User::class,
            ],
            [
                'name'       => 'انتظامات',
                'model_type' => User::class,
            ],
            [
                'name'       => 'اتاق کنترل',
                'model_type' => User::class,
            ],
            [
                'name'       => 'ابنیه و تامین و نگهداری',
                'model_type' => User::class,
            ],
            [
                'name'       => 'عمومی',
                'model_type' => Unit::class,
            ],
            [
                'name'       => 'عمومی',
                'model_type' => Owner::class,
            ],
            [
                'name'       => 'عمومی',
                'model_type' => Occupant::class,
            ],
            [
                'name'       => 'عمومی',
                'model_type' => Contract::class,
            ],
            [
                'name'       => 'عمومی',
                'model_type' => Transfer::class,
            ],
            [
                'name'       => 'آسانسور',
                'model_type' => Facility::class,
            ],
            [
                'name'       => 'پله برقی',
                'model_type' => Facility::class,
            ],
            [
                'name'       => 'سرمایش/گرمایش/تهویه',
                'model_type' => Facility::class,
            ]
        ];
        foreach ($data as $category){
            Category::create($category);
        }
    }
}
