<?php

namespace App\Http\Controllers;


use App\Http\Resources\UserResource;
use App\Models\Ticket;

abstract class Controller
{
    public function __construct()
    {
        $menuItems = collect($this->getMenu());
        $i = env('APP_ENV') === 'local' ? 2 : 1; // به دلیل اینکه در سرور بخش های مدیریت و اپ با ساب دامین پیاده شده و سمت لوکال به صورت route به خاطر همین باید این شرط رو بذاریم تا درست عمل کنه
        $currentSection = [];
        if ($thisMenu = $menuItems->where('name', request()->segment($i))->first()) {
            $currentSection = $thisMenu;
        } elseif ($thisMenu = $menuItems->where('name', request()->segment($i) . '.' . request()->segment($i + 1))->first()) {
            $currentSection = $thisMenu;
        }
        inertia()->share([
            'menuItems'      => $menuItems,
            'currentSection' => $currentSection,
            'currentUser'    => auth()->check() ?new UserResource(auth()->user()) : null,
            'settings'       => []
        ]);
    }

    private function getMenu()
    {
        if (auth()->check()) {
            if (auth()->user()->roles()->pluck('access_to')->contains('admin')) {
                return [
                    [
                        'title'           => 'داشبورد',
                        'show' => true,
                        'name'            => 'dashboard',
                        'icon'            => 'dashboard',
                        'pageTitle'       => 'داشبورد',
                        'pageDescription' => '',
                        'count'           => null,
                        'url'             => route('admin.dashboard')
                    ],
                    [
                        'title' => 'واحدهای تجاری',
                        'show' => true,
                        'name' => 'units',
                        'icon' => 'domain',
                        'pageTitle' => 'واحدهای تجاری',
                        'pageDescription' => 'لیست کلیه واحد‌های تجاری مرکز خرید 17 شهریور در این بخش قابل مشاهده است.',
                        'count' => null,
                        'url' => route('admin.units')
                    ],
                    [
                        'title' => 'مالکین',
                        'show' => true,
                        'name' => 'owners',
                        'icon' => 'group',
                        'pageTitle' => 'مالکین',
                        'pageDescription' => 'لیست کلیه مالکین مرکز خرید 17 شهریور در این بخش قابل مشاهده است.',
                        'count' => null,
                        'url' => route('admin.owners')
                    ],
                    [
                        'title' => 'مستاجرین',
                        'show' => true,
                        'name' => 'occupants',
                        'icon' => 'groups_2',
                        'pageTitle' => 'مستاجرین',
                        'pageDescription' => 'لیست کلیه مستاجرین مرکز خرید 17 شهریور در این بخش قابل مشاهده است.',
                        'count' => null,
                        'url' => route('admin.occupants')
                    ],
                    [
                        'title' => 'نقل و انتقال',
                        'show' => true,
                        'name' => 'transfers.sale',
                        'icon' => 'handshake',
                        'pageTitle' => 'نقل و انتقال',
                        'pageDescription' => 'لیست کلیه نقل و انتقالات مرکز خرید 17 شهریور در این بخش قابل مشاهده است.',
                        'count' => null,
                        'url' => route('admin.transfers.sale')
                    ],
                    [
                        'title' => 'اجاره نامه',
                        'show' => true,
                        'name' => 'transfers.rent',
                        'icon' => 'handshake',
                        'pageTitle' => 'اجاره نامه',
                        'pageDescription' => 'لیست کلیه اجاره نامه ها مرکز خرید 17 شهریور در این بخش قابل مشاهده است.',
                        'count' => null,
                        'url' => route('admin.transfers.rent')
                    ],
                    [
                        'title' => 'شارژ واحدها',
                        'show' => true,
                        'name' => 'charges',
                        'icon' => 'domain_add',
                        'pageTitle' => 'شارژ واحدها',
                        'pageDescription' => 'لیست کلیه شارژ واحدها مرکز خرید 17 شهریور در این بخش قابل مشاهده است.',
                        'count' => null,
                        'url' => route('admin.charges')
                    ],
                    [
                        'title' => 'قبض بدهی',
                        'show' => true,
                        'name' => 'bills',
                        'icon' => 'receipt_long',
                        'count' => null,
                        'pageTitle' => 'قبض بدهی',
                        'pageDescription' => 'لیست کلیه قبض های بدهی مرکز خرید 17 شهریور در این بخش قابل مشاهده است.',
                        'url' => route('admin.bills')
                    ],
                    /*
                     * [
                        'title' => 'گزارشات',
                        'show' => true,
                        'name' => 'reports',
                        'icon' => 'list_alt',
                        'count' => null,
                        'pageTitle' => 'گزارشات',
                        'pageDescription' => 'لیست کلیه گزارشات مرکز خرید 17 شهریور در این بخش قابل مشاهده است.',
                        'url' => route('admin.reports')
                    ],
                     * */
                    [
                        'title' => 'تاسیسات',
                        'show' => true,
                        'name' => 'facilities',
                        'icon' => 'manage_accounts',
                        'count' => null,
                        'pageTitle' => 'تاسیسات',
                        'pageDescription' => 'لیست کلیه تاسیسات مرکز خرید 17 شهریور در این بخش قابل مشاهده است.',
                        'url' => route('admin.facilities')
                    ],
                    [
                        'title' => 'قراردادها',
                        'show' => true,
                        'name' => 'contracts',
                        'icon' => 'handshake',
                        'count' => null,
                        'pageTitle' => 'قراردادها',
                        'pageDescription' => 'لیست کلیه قراردادها مرکز خرید 17 شهریور در این بخش قابل مشاهده است.',
                        'url' => route('admin.contracts')
                    ],
                    [
                        'title' => 'رویدادها',
                        'show' => true,
                        'name' => 'events',
                        'icon' => 'event',
                        'count' => null,
                        'pageTitle' => 'رویدادها',
                        'pageDescription' => 'لیست کلیه رویدادها مرکز خرید 17 شهریور در این بخش قابل مشاهده است.',
                        'url' => route('admin.events')
                    ],
                    [
                        'title' => 'پرسنل',
                        'show' => true,
                        'name' => 'employees',
                        'icon' => 'diversity_3',
                        'count' => null,
                        'pageTitle' => 'پرسنل',
                        'pageDescription' => 'لیست کلیه پرسنل مرکز خرید 17 شهریور در این بخش قابل مشاهده است.',
                        'url' => route('admin.employees')
                    ],
                    [
                        'title' => 'پارکینگ',
                        'show' => true,
                        'name' => 'parking',
                        'icon' => 'warehouse',
                        'count' => null,
                        'pageTitle' => 'پارکینگ',
                        'pageDescription' => 'پارکینگ مرکز خرید 17 شهریور در این بخش قابل مشاهده است.',
                        'url' => route('admin.parking')
                    ],
                    [
                        'title' => 'تراکنش ها',
                        'show' => true,
                        'name' => 'transactions',
                        'icon' => 'receipt_long',
                        'count' => null,
                        'pageTitle' => 'تراکنش ها',
                        'pageDescription' => 'لیست کلیه تراکنش های مرکز خرید 17 شهریور در این بخش قابل مشاهده است.',
                        'url' => route('admin.transactions')
                    ],
                    [
                        'title' => 'مرکز پیام',
                        'show' => false,
                        'name' => 'tickets',
                        'icon' => 'mail_outline',
                        'count' => Ticket::whereIn('status', ['PENDING', 'IN_PROGRESS'])->count(),
                        'pageTitle' => 'مرکز پیام',
                        'pageDescription' => 'لیست کلیه پیام های مرکز خرید 17 شهریور در این بخش قابل مشاهده است.',
                        'url' => route('admin.tickets')
                    ],
                    [
                        'title' => 'تنظیمات',
                        'show' => false,
                        'name' => 'settings.main',
                        'icon' => 'settings',
                        'count' => null,
                        'pageTitle' => 'تنظیمات',
                        'pageDescription' => 'تنظیمات مربوط به داشبورد در این بخش قابل مشاهده می باشد',
                        'url' => route('admin.settings.main')
                    ],
                    [
                        'title' => 'تنظیمات فنی',
                        'show' => true,
                        'name' => 'settings.dev',
                        'icon' => 'favorite',
                        'count' => null,
                        'pageTitle' => 'تنظیمات فنی',
                        'pageDescription' => 'تنظیمات فنی',
                        'url' => route('admin.settings.dev'),
                        'roleAccess' => 'superadmin'
                    ],
                ];
            } else {
                return [
                    [
                        'title' => 'شارژ واحدها',
                        'show' => true,
                        'name' => 'charges',
                        'icon' => 'domain_add',
                        'pageTitle' => 'شارژ واحدها',
                        'pageDescription' => 'لیست کلیه شارژ واحدها در این بخش قابل مشاهده است.',
                        'count' => null,
                        'url' => route('client.charges')
                    ],
                    [
                        'title' => 'قبض بدهی',
                        'show' => true,
                        'name' => 'bills',
                        'icon' => 'receipt_long',
                        'count' => null,
                        'pageTitle' => 'قبض بدهی',
                        'pageDescription' => 'لیست کلیه قبض های بدهی در این بخش قابل مشاهده است.',
                        'url' => route('client.bills')
                    ],
                    [
                        'title' => 'رویدادها',
                        'show' => true,
                        'name' => 'events',
                        'icon' => 'event',
                        'count' => null,
                        'pageTitle' => 'رویدادها',
                        'pageDescription' => 'لیست کلیه رویدادها در این بخش قابل مشاهده است.',
                        'url' => route('client.events')
                    ],
                    [
                        'title' => 'پارکینگ',
                        'show' => true,
                        'name' => 'parking',
                        'icon' => 'warehouse',
                        'count' => null,
                        'pageTitle' => 'پارکینگ',
                        'pageDescription' => 'پارکینگ در این بخش قابل مشاهده است.',
                        'url' => route('client.parking')
                    ],
                    [
                        'title' => 'تراکنش ها',
                        'show' => true,
                        'name' => 'transactions',
                        'icon' => 'receipt_long',
                        'count' => null,
                        'pageTitle' => 'تراکنش ها',
                        'pageDescription' => 'لیست کلیه تراکنش ها در این بخش قابل مشاهده است.',
                        'url' => route('client.transactions')
                    ],
                    [
                        'title' => 'مرکز پیام',
                        'show' => false,
                        'name' => 'tickets',
                        'icon' => 'mail_outline',
                        'count' => null,
                        'pageTitle' => 'مرکز پیام',
                        'pageDescription' => 'لیست کلیه پیام ها در این بخش قابل مشاهده است.',
                        'url' => route('client.tickets')
                    ],
                ];
            }
        }
    }
}
