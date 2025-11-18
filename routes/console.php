<?php

use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('17shahrivar:send-reminder-charge')->hourly()->when(fn () => now()->hour >= 9 && now()->hour <= 20);
Schedule::command('17shahrivar:clean-unconfirmed-files')->dailyAt('01:00');

