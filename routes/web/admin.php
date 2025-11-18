<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BillController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FacilitiesController;
use App\Http\Controllers\Admin\OccupantController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\ChargeController;
use App\Http\Controllers\Admin\ParkingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SettingDevController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\Admin\TransferRentController;
use App\Http\Controllers\Admin\TransferSaleController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\DevController;
use App\Http\Controllers\DocumentController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

Route::get('/login', [AuthController::class,'login'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class,'loginProcess'])->middleware('guest')->name('login.process');
Route::group(['middleware' => [IsAdmin::class, 'throttle:client-limiter']],function () {
    Route::post('/logout', [AuthController::class,'logout'])->name('logout');

    Route::get('/', [DashboardController::class,'redirectToDashboard']);
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
    Route::get('/dashboard/stat/units', [DashboardController::class,'statsUnits'])->name('dashboard.stats.units');
    Route::get('/dashboard/stat/owners', [DashboardController::class,'statsOwners'])->name('dashboard.stats.owners');
    Route::get('/dashboard/stat/occupants', [DashboardController::class,'statsOccupants'])->name('dashboard.stats.occupants');
    Route::get('/dashboard/stat/transfers', [DashboardController::class,'statsContracts'])->name('dashboard.stats.contracts');
    Route::get('/dashboard/chart/transfers', [DashboardController::class,'chartTransfers'])->name('dashboard.chart.transfers');
    Route::get('/dashboard/chart/charges', [DashboardController::class,'chartCharges'])->name('dashboard.chart.charges');
    Route::get('/dashboard/stats/bill', [DashboardController::class,'statsBill'])->name('dashboard.stats.bill');
    Route::get('/dashboard/reports/charges', [DashboardController::class,'reportsCharges'])->name('dashboard.reports.charges');

    Route::get('/units', [UnitController::class,'index'])->name('units');
    Route::get('/units/create', [UnitController::class,'create'])->name('units.create');
    Route::post('/units/store', [UnitController::class,'store'])->name('units.store');
    Route::post('/units/upload', [UnitController::class,'upload'])->name('units.upload');
    Route::get('/units/owners', [UnitController::class,'owners'])->name('units.owners');
    Route::get('/units/occupants', [UnitController::class,'occupants'])->name('units.occupants');
    Route::get('/units/{unit}', [UnitController::class,'edit'])->name('units.edit');
    Route::put('/units/{unit}', [UnitController::class,'update'])->name('units.update');
    Route::get('/units/{unit}/occupant', [UnitController::class,'occupant'])->name('units.edit.occupant');
    Route::post('/units/excel', [UnitController::class,'excel'])->name('units.excel');

    Route::get('/owners', [OwnerController::class,'index'])->name('owners');
    Route::get('/owners/create', [OwnerController::class,'create'])->name('owners.create');
    Route::post('/owners/store', [OwnerController::class,'store'])->name('owners.store');
    Route::get('/owners/{user}', [OwnerController::class,'edit'])->name('owners.edit');
    Route::put('/owners/{user}', [OwnerController::class,'update'])->name('owners.update');
    Route::post('/owners/upload', [OwnerController::class,'upload'])->name('owners.upload');
    Route::post('/owners/excel', [OwnerController::class,'excel'])->name('owners.excel');

    Route::get('/occupants', [OccupantController::class,'index'])->name('occupants');
    Route::get('/occupants/create', [OccupantController::class,'create'])->name('occupants.create');
    Route::post('/occupants/store', [OccupantController::class,'store'])->name('occupants.store');
    Route::get('/occupants/{user}', [OccupantController::class,'edit'])->name('occupants.edit');
    Route::put('/occupants/{user}', [OccupantController::class,'update'])->name('occupants.update');
    Route::post('/occupants/upload', [OccupantController::class,'upload'])->name('occupants.upload');
    Route::post('/occupants/excel', [OccupantController::class,'excel'])->name('occupants.excel');

    Route::get('/transfers/sale', [TransferSaleController::class,'index'])->name('transfers.sale');
    Route::get('/transfers/sale/create', [TransferSaleController::class,'create'])->name('transfers.sale.create');
    Route::post('/transfers/sale/store', [TransferSaleController::class,'store'])->name('transfers.sale.store');
    Route::get('/transfers/sale/{transfer}', [TransferSaleController::class,'edit'])->name('transfers.sale.edit');
    Route::put('/transfers/sale/{transfer}', [TransferSaleController::class,'update'])->name('transfers.sale.update');
    Route::get('/transfers/print/sale/{transfer}', [TransferSaleController::class,'print'])->name('transfers.sale.print');
    Route::post('/transfers/sale/excel', [TransferSaleController::class,'excel'])->name('transfers.sale.excel');

    Route::get('/transfers/rent', [TransferRentController::class,'index'])->name('transfers.rent');
    Route::get('/transfers/rent/create', [TransferRentController::class,'create'])->name('transfers.rent.create');
    Route::post('/transfers/rent/store', [TransferRentController::class,'store'])->name('transfers.rent.store');
    Route::get('/transfers/rent/{transfer}', [TransferRentController::class,'edit'])->name('transfers.rent.edit');
    Route::put('/transfers/rent/{transfer}', [TransferRentController::class,'update'])->name('transfers.rent.update');
    Route::get('/transfers/print/rent/{transfer}', [TransferRentController::class,'print'])->name('transfers.rent.print');
    Route::post('/transfers/rent/excel', [TransferRentController::class,'excel'])->name('transfers.rent.excel');

    Route::get('/transfers/search/unit', [TransferController::class,'searchUnit'])->name('transfers.search.unit');
    Route::get('/transfers/search/user', [TransferController::class,'searchUser'])->name('transfers.search.user');

    Route::get('/charges', [ChargeController::class,'index'])->name('charges');
    Route::get('/charges/setting/search', [ChargeController::class,'searchUnitCharge'])->name('charges.setting.search');
    Route::post('/charges/setting/store', [ChargeController::class,'settingStore'])->name('charges.setting.store');
    Route::post('/charges/store', [ChargeController::class,'store'])->name('charges.store');
    Route::post('/charges/update/{charge}/status', [ChargeController::class,'updateStatus'])->name('charges.update.status');
    Route::get('/charges/units', [ChargeController::class,'units'])->name('charges.units');
    Route::post('/charges/excel', [ChargeController::class,'excel'])->name('charges.excel');
    Route::post('/charges/units/excel', [ChargeController::class,'unitExcel'])->name('charges.units.excel');


    Route::get('/bills', [BillController::class,'index'])->name('bills');
    Route::post('/bills/store', [BillController::class,'store'])->name('bills.store');
    Route::get('/bills/users/search', [BillController::class,'userSearch'])->name('bills.users.search');

    Route::get('/reports', [ReportController::class,'index'])->name('reports');

    Route::get('/facilities', [FacilitiesController::class,'index'])->name('facilities');

    Route::get('/contracts', [ContractController::class,'index'])->name('contracts');
    Route::get('/contracts/create', [ContractController::class,'create'])->name('contracts.create');
    Route::post('/contracts/store', [ContractController::class,'store'])->name('contracts.store');
    Route::get('/contracts/{contract}', [ContractController::class,'edit'])->name('contracts.edit');
    Route::put('/contracts/{contract}', [ContractController::class,'update'])->name('contracts.update');

    Route::get('/events', [EventController::class,'index'])->name('events');
    Route::get('/events/create', [EventController::class,'create'])->name('events.create');
    Route::post('/events/store', [EventController::class,'store'])->name('events.store');
    Route::post('/events/review', [EventController::class,'review'])->name('events.review');

    Route::get('/employees', [EmployeeController::class,'index'])->name('employees');
    Route::get('/employees/create', [EmployeeController::class,'create'])->name('employees.create');
    Route::post('/employees/store', [EmployeeController::class,'store'])->name('employees.store');
    Route::get('/employees/{employee}', [EmployeeController::class,'edit'])->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class,'update'])->name('employees.update');

    Route::get('/parking', [ParkingController::class,'index'])->name('parking');

    Route::get('/transactions', [TransactionController::class,'index'])->name('transactions');

    Route::get('/tickets', [TicketController::class,'index'])->name('tickets');
    Route::get('/tickets/create', [TicketController::class,'create'])->name('tickets.create');
    Route::post('/tickets/store', [TicketController::class,'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class,'edit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}', [TicketController::class,'update'])->name('tickets.update');
    Route::put('/tickets/{ticket}/status', [TicketController::class,'updateStatus'])->name('tickets.update.status');

    Route::post('/units/document/store', [DocumentController::class,'store'])->name('documents.store');
    Route::post('/documents/upload', [DocumentController::class,'upload'])->name('documents.upload');
    Route::put('/documents/{document}/update', [DocumentController::class,'update'])->name('document.update');

    Route::get('/settings/main', [SettingController::class,'index'])->name('settings.main');
    Route::get('/settings/dev', [SettingDevController::class,'index'])->name('settings.dev');
    Route::get('/settings/dev/sms', [SettingDevController::class,'sms'])->name('settings.dev.sms');
    Route::put('/settings/dev/sms', [SettingDevController::class,'updateSms'])->name('settings.dev.sms');

    Route::get('/test', [DevController::class,'test'])->name('test');
    Route::post('/test/upload', [DevController::class,'upload'])->name('test.upload');

});

Route::get('/dev', [DevController::class,'index']);
