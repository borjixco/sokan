<?php

use App\Http\Controllers\Web\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = auth()->user();
    if ($user) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('admin.login');
});

Route::get('/payment/gateway/{crypt}', [PaymentController::class, 'payment'])->name('payment');
Route::match(['get', 'post'],'/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');


Route::get('pay',function (){
    return inertia('Web/Payment/Index');
});

Route::get('callback',function (){
    return inertia('Web/Payment/Index');
});
