<?php

namespace App\Providers;

use App\Models\BlackList;
use App\Services\Sms\SmsResolver;
use App\Services\Sms\SmsServiceInterface;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SmsServiceInterface::class, function () {
            return SmsResolver::resolve();
        });

        // یه alias هم برای راحتی
        $this->app->alias(SmsServiceInterface::class, 'sms');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('client-limiter', function (Request $request) {
            $userIdOrIp = auth()->check() ? $request->user()->id : $request->ip();

            if (RateLimiter::tooManyAttempts('client-limiter-' . $userIdOrIp, 120)) {
                // افزایش شمارنده در دیتابیس
                $this->blacklistSubmitter($request); // انجام نمیشه باید بررسی کرد
                return Limit::none(); // جلوگیری از ادامه درخواست
            }

            return auth()->check()
                ? Limit::perMinute(60)->by($userIdOrIp)
                : Limit::perMinute(20)->by($userIdOrIp);
        });

        Validator::extend('mobile', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^09[0-9]{9}$/', $value);
        }, 'فرمت شماره موبایل معتبر نیست.');
    }

    public function blacklistSubmitter($request)
    {
        if(auth()->check()) {
            if($blacklist = auth()->user()->blacklist){
                $blacklist->update([
                    'ip_address' => $request->ip(),
                    'count' => $blacklist->count+1,
                ]);
            }
            else{
                auth()->user()->blacklist()->create([
                    'ip_address' => $request->ip(),
                    'count' => 1,
                ]);
            }
        }
        else{
            if($blacklist = BlackList::where('ip_address',$request->ip())->first()){
                $blacklist->update([
                    'ip_address' => $request->ip(),
                    'count' => $blacklist->count+1,
                ]);
            }
            else{
                BlackList::create([
                    'ip_address' => $request->ip(),
                    'count' => 1,
                ]);
            }
        }
    }
}
