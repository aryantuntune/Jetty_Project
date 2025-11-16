<?php

use \App\Http\Controllers\CheckerController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\EmployeeTransferController;
use App\Http\Controllers\FerryBoatController;
use App\Http\Controllers\FerryScheduleController;
use App\Http\Controllers\GuestCategoryController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\ItemRateController;
use App\Http\Controllers\ItemsFromRatesController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\SpecialChargeController;
use App\Http\Controllers\TicketEntryController;
use App\Http\Controllers\TicketReportController;
use App\Http\Controllers\TicketVerifyController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;






















Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth','blockRole5'])->group(function () {
    // Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');

    // List all categories
    Route::get('/item-categories', [ItemCategoryController::class, 'index'])->name('item_categories.index');

    // Create new category
    Route::get('/item-categories/create', [ItemCategoryController::class, 'create'])->name('item_categories.create')->middleware(['auth','role:1,2']);
    Route::post('/item-categories', [ItemCategoryController::class, 'store'])->name('item_categories.store')->middleware(['auth','role:1,2']);

    // Edit category
    Route::get('/item-categories/{itemCategory}/edit', [ItemCategoryController::class, 'edit'])->name('item_categories.edit')->middleware(['auth','role:1,2']);

    // Update category
    Route::put('/item-categories/{itemCategory}', [ItemCategoryController::class, 'update'])->name('item_categories.update')->middleware(['auth','role:1,2']);

    // Delete category
    Route::delete('/item-categories/{itemCategory}', [ItemCategoryController::class, 'destroy'])->name('item_categories.destroy');

    Route::get('/ferry-boats', [FerryBoatController::class, 'index'])->name('ferryboats.index');
    Route::get('/ferry-boats/create', [FerryBoatController::class, 'create'])->name('ferryboats.create');
    Route::post('/ferry-boats', [FerryBoatController::class, 'store'])->name('ferryboats.store');
    Route::get('/ferry-boats/{ferryboat}/edit', [FerryBoatController::class, 'edit'])->name('ferryboats.edit');
    Route::delete('/ferry-boats/{ferryboat}', [FerryBoatController::class, 'destroy'])->name('ferryboats.destroy');
    Route::put('/ferry-boats/{ferryboat}', [FerryBoatController::class, 'update'])->name('ferryboats.update');



    Route::resource('guest_categories', GuestCategoryController::class);

    Route::resource('branches', BranchController::class);
    Route::resource('guests', GuestController::class);
    Route::resource('ferry_schedules', FerryScheduleController::class);

    Route::resource('admin', AdministratorController::class);
    Route::resource('manager', ManagerController::class);
    Route::resource('operator', OperatorController::class);

    Route::get('/ajax/item-rate-lookup', [TicketEntryController::class, 'find'])
        ->name('ajax.item-rates.find');

    Route::resource('item-rates', ItemRateController::class);

    Route::get('/items-from-rates', [ItemsFromRatesController::class, 'index'])
        ->name('items.from_rates.index');

    Route::get('/ticket-entry', [TicketEntryController::class, 'create'])->name('ticket-entry.create');

    Route::post('/ticket-entry', [TicketEntryController::class, 'store'])->name('ticket-entry.store'); // optional

    Route::get('/item-rates/find', [TicketEntryController::class, 'find'])->name('api.item-rates.find');

    Route::get('ajax/next-ferry-time', [TicketEntryController::class, 'ajaxNextFerryTime'])->name('ajax.next-ferry-time');


    Route::get('/employees/transfer', [EmployeeTransferController::class, 'transferPage'])
        ->name('employees.transfer.index')->middleware(['auth','role:1,2']);

    // Show transfer form for specific employee
    Route::get('/employees/{id}/transfer', [EmployeeTransferController::class, 'showTransferForm'])
        ->name('employees.transfer.form')->middleware(['auth','role:1,2']);

    // Update employee branch
    Route::put('/employees/{id}/transfer', [EmployeeTransferController::class, 'transfer'])
        ->name('employees.transfer.update')->middleware(['auth','role:1,2']);


    //   Report 
    Route::get('/branches/{branch}/ferryboats', [App\Http\Controllers\TicketReportController::class, 'getFerryBoatsByBranch'])->name('branches.ferryboats');

    Route::prefix('reports')->group(function () {
        Route::get('/tickets', [TicketReportController::class, 'index'])
            ->name('reports.tickets');

        Route::get('/vehicle-tickets', [TicketReportController::class, 'vehicleWiseIndex'])
            ->name('reports.vehicle_tickets');
            // ⬇️ New export endpoints
        Route::get('/tickets/export', [TicketReportController::class, 'exportTicketsCsv'])->name('reports.tickets.export');
        Route::get('/vehicle-tickets/export', [TicketReportController::class, 'exportVehicleTicketsCsv'])->name('reports.vehicle_tickets.export');
    });

    Route::resource('special-charges', SpecialChargeController::class)->middleware(['auth','role:1,2']);

     Route::get('/tickets/{ticket}/print', [\App\Http\Controllers\TicketEntryController::class, 'print'])
        ->name('tickets.print');



// web.php
Route::get('/ajax/search-guest-by-id', [GuestController::class, 'searchById']);
Route::get('/ajax/search-guest-by-name', [GuestController::class, 'searchByName']);
Route::post('/ajax/add-guest', [GuestController::class, 'storebyticket']);

Route::get('/verify', [TicketVerifyController::class, 'index'])->name('verify.index')->middleware(['auth','role:1,2,5']);
Route::post('/verify', [TicketVerifyController::class, 'verify'])->name('verify.ticket')->middleware(['auth','role:1,2,5']);

Route::middleware(['auth', 'role:1,2'])->group(function () {
    Route::resource('checker', CheckerController::class);
});


});



Route::get('/clear-config', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return 'All caches cleared successfully.';
});



// Route::get('/manifest.json', function () {
//     return view('laravelpwa::manifest');
// })->name('laravelpwa.manifest');


// Route for showing booking form page
Route::get('/booking', [BookingController::class, 'show'])->name('booking.form');
Route::post('/booking', [BookingController::class, 'submit'])->name('booking.submit');
Route::get('/booking/to-branches/{branchId}', [BookingController::class, 'getToBranches']);