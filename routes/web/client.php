<?php

use App\Http\Controllers\Client\ChargeController;
use App\Http\Controllers\Client\BillController;
use App\Http\Controllers\Client\EventController;
use App\Http\Controllers\Client\ParkingController;
use App\Http\Controllers\Client\PaymentController;
use App\Http\Controllers\Client\TicketController;
use App\Http\Controllers\Client\TransactionController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Middleware\isApp;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class,'login'])->middleware('guest')->name('login');
Route::post('/auth/login', [AuthController::class,'loginProcess'])->middleware('guest')->name('auth.login');
Route::post('/auth/verify', [AuthController::class,'verify'])->middleware('guest')->name('auth.verify');
Route::post('/auth/resend', [AuthController::class,'resend'])->middleware('guest')->name('auth.resend');

Route::group(['middleware' => [isApp::class, 'throttle:client-limiter']],function () {
    Route::post('/logout', [AuthController::class,'logout'])->name('logout');

    Route::get('/', [DashboardController::class,'redirectToDashboard']);

    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    Route::get('/charges', [ChargeController::class,'index'])->name('charges');

    Route::get('/bills', [BillController::class,'index'])->name('bills');

    Route::get('/events', [EventController::class,'index'])->name('events');

    Route::get('/parking', [ParkingController::class,'index'])->name('parking');

    Route::get('/transactions', [TransactionController::class,'index'])->name('transactions');

    Route::get('/tickets', [TicketController::class,'index'])->name('tickets');
    Route::get('/tickets/create', [TicketController::class,'create'])->name('tickets.create');
    Route::post('/tickets/store', [TicketController::class,'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class,'edit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}', [TicketController::class,'update'])->name('tickets.update');

    Route::post('/payments/charge/{charge}', [PaymentController::class,'charge'])->name('payments.charge');
    Route::post('/payments/bill/{bill}', [PaymentController::class,'bill'])->name('payments.bill');

});
