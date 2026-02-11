<?php

use \App\Http\Controllers\CheckerController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\Api\CheckerAuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CustomerAuth\ForgotPasswordController;
use App\Http\Controllers\CustomerAuth\LoginController;
use App\Http\Controllers\CustomerAuth\RegisterController;
use App\Http\Controllers\CustomerAuth\ResetPasswordController;
use App\Http\Controllers\EmployeeTransferController;
use App\Http\Controllers\FerryBoatController;
use App\Http\Controllers\FerryScheduleController;
use App\Http\Controllers\GuestCategoryController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HouseboatAdminController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\ItemRateController;
use App\Http\Controllers\ItemsFromRatesController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SpecialChargeController;
use App\Http\Controllers\TicketEntryController;
use App\Http\Controllers\TicketReportController;
use App\Http\Controllers\TicketVerifyController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ============================================
// PUBLIC PAGES - Customer-Facing Website
// (Matching carferry.in design)
// ============================================



use App\Http\Controllers\HealthController;

// Homepage - carferry.in style
Route::get('/', [PublicController::class, 'home'])->name('public.home');

// ============================================
// HEALTH CHECK ENDPOINTS (No Auth Required)
// Used by load balancers and monitoring
// ============================================
Route::get('/health', [HealthController::class, 'check'])->name('health.check');
Route::get('/ping', [HealthController::class, 'ping'])->name('health.ping');

// Houseboat Booking
Route::get('/houseboat-booking', [\App\Http\Controllers\HouseboatController::class, 'index'])->name('houseboat.index');
Route::get('/houseboat-booking/checkout', [\App\Http\Controllers\HouseboatController::class, 'checkout'])->name('houseboat.checkout');
Route::post('/houseboat-booking/book', [\App\Http\Controllers\HouseboatController::class, 'store'])->name('houseboat.book');

// About Us page
Route::get('/about', [PublicController::class, 'about'])->name('public.about');

// Contact Us page
Route::get('/contact', [PublicController::class, 'contact'])->name('public.contact');
Route::post('/contact', [PublicController::class, 'submitContact'])->name('public.contact.submit');

// Ferry Route pages (e.g., /route/dabhol-dhopave)
Route::get('/route/{slug}', [PublicController::class, 'route'])->name('public.route');

// Convenience redirects for common URLs
Route::get('/book', fn() => redirect('/customer/login'))->name('book.redirect');
Route::get('/verify-ticket', fn() => redirect('/verify'))->name('verify-ticket.redirect');



Route::middleware('admin.guest')->group(function () {
    Auth::routes(); // admin login/register

    // Houseboat Admin Routes
    Route::prefix('houseboat')->name('admin.houseboat.')->group(function () {
        Route::get('/', [HouseboatAdminController::class, 'index'])->name('dashboard');
        Route::get('/rooms', [HouseboatAdminController::class, 'rooms'])->name('rooms');
        Route::put('/rooms/{id}', [HouseboatAdminController::class, 'updateRoom'])->name('rooms.update');
        Route::patch('/bookings/{id}/status', [HouseboatAdminController::class, 'updateBookingStatus'])->name('bookings.status');
    });
});

Route::middleware(['auth', 'blockRole5'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard'); // Alias for /home

    // Change Password routes
    Route::get('/password/change', [\App\Http\Controllers\Auth\ChangePasswordController::class, 'showChangeForm'])
        ->name('password.change');
    Route::post('/password/change', [\App\Http\Controllers\Auth\ChangePasswordController::class, 'update'])
        ->name('password.change.update');
});

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'blockRole5'])->group(function () {
    // Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');

    // Routes Management (Admin only)
    Route::resource('routes', \App\Http\Controllers\RouteController::class)
        ->middleware(['auth', 'role:1,2']);

    // List all categories
    Route::get('/item-categories', [ItemCategoryController::class, 'index'])->name('item_categories.index');

    // Create new category
    Route::get('/item-categories/create', [ItemCategoryController::class, 'create'])->name('item_categories.create')->middleware(['auth', 'role:1,2']);
    Route::post('/item-categories', [ItemCategoryController::class, 'store'])->name('item_categories.store')->middleware(['auth', 'role:1,2']);

    // Edit category
    Route::get('/item-categories/{itemCategory}/edit', [ItemCategoryController::class, 'edit'])->name('item_categories.edit')->middleware(['auth', 'role:1,2']);

    // Update category
    Route::put('/item-categories/{itemCategory}', [ItemCategoryController::class, 'update'])->name('item_categories.update')->middleware(['auth', 'role:1,2']);

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
    Route::get('/ajax/item-rates/list', [TicketEntryController::class, 'listItems'])->name('ajax.item-rates.list');

    Route::get('ajax/next-ferry-time', [TicketEntryController::class, 'ajaxNextFerryTime'])->name('ajax.next-ferry-time');


    Route::get('/employees/transfer', [EmployeeTransferController::class, 'transferPage'])
        ->name('employees.transfer.index')->middleware(['auth', 'role:1,2']);

    // Show transfer form for specific employee
    Route::get('/employees/{id}/transfer', [EmployeeTransferController::class, 'showTransferForm'])
        ->name('employees.transfer.form')->middleware(['auth', 'role:1,2']);

    // Update employee branch
    Route::put('/employees/{id}/transfer', [EmployeeTransferController::class, 'transfer'])
        ->name('employees.transfer.update')->middleware(['auth', 'role:1,2']);


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

    Route::resource('special-charges', SpecialChargeController::class)->middleware(['auth', 'role:1,2']);

    Route::get('/tickets/{ticket}/print', [\App\Http\Controllers\TicketEntryController::class, 'print'])
        ->name('tickets.print');

    // Secure print route using qr_hash (no ticket ID exposure)
    Route::get('/t/{hash}/print', [\App\Http\Controllers\TicketEntryController::class, 'printByHash'])
        ->name('tickets.print.secure')
        ->where('hash', '[a-f0-9]{64}');



    // web.php
    Route::get('/ajax/search-guest-by-id', [GuestController::class, 'searchById']);
    Route::get('/ajax/search-guest-by-name', [GuestController::class, 'searchByName']);
    Route::post('/ajax/add-guest', [GuestController::class, 'storebyticket']);

    Route::get('/verify', [TicketVerifyController::class, 'index'])->name('verify.index')->middleware(['auth', 'role:1,2,5']);
    Route::post('/verify', [TicketVerifyController::class, 'verify'])->name('verify.ticket')->middleware(['auth', 'role:1,2,5']);

    Route::middleware(['auth', 'role:1,2'])->group(function () {
        Route::resource('checker', CheckerController::class);
    });

    // Create Razorpay Order

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


// --------------------------------------------------------
// online customer
// Customer Register
// Customer Register
// Customer Authentication Routes
Route::middleware(['customer.guest'])->group(function () {

    Route::get('customer/register', [RegisterController::class, 'showRegisterForm'])
        ->name('customer.register');

    Route::post('customer/register', [RegisterController::class, 'register'])
        ->name('customer.register.submit');

    Route::get('customer/login', [LoginController::class, 'showLoginForm'])
        ->name('customer.login');

    Route::post('customer/login', [LoginController::class, 'login'])
        ->name('customer.login.submit');


    Route::get('/customer/forgot-password', [ForgotPasswordController::class, 'showLinkForm'])
        ->name('customer.password.request');

    Route::post('/customer/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
        ->name('customer.password.email');

    Route::get('/customer/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('customer.password.reset');

    Route::post('/customer/reset-password', [ResetPasswordController::class, 'resetPassword'])
        ->name('customer.password.update');


    Route::post('/customer/register/send-otp', [RegisterController::class, 'sendOtp'])->name('customer.register.sendOtp');
    Route::post('/customer/register/verify-otp', [RegisterController::class, 'verifyOtp'])->name('customer.register.verifyOtp');
});



// Customer Dashboard (Protected)
Route::middleware('auth:customer')->group(function () {

    // Dashboard should load branches → use controller
    Route::get('customer/dashboard', [BookingController::class, 'show'])
        ->name('customer.dashboard');

    Route::get('/booking', [BookingController::class, 'show'])->name('booking.form');
    Route::get('/booking/history', [BookingController::class, 'history'])->name('booking.history');

    Route::post('/booking', [BookingController::class, 'submit'])->name('booking.submit');

    Route::get('/booking/to-branches/{branchId}', [BookingController::class, 'getToBranches']);

    Route::post('customer/logout', [LoginController::class, 'logout'])->name('customer.logout');

    Route::get('/booking/items/{branchId}', [BookingController::class, 'getItems']);
    Route::get('/booking/item-rate/{itemRateId}', [BookingController::class, 'getItemRate']);
    Route::get('/booking/schedules/{branchId}', [BookingController::class, 'getSchedules']);

    Route::post('/payment/create-order', [BookingController::class, 'createOrder'])->name('payment.createOrder');

    // Payment Success Callback
    Route::post('/payment/verify1', [BookingController::class, 'verifyPayment1'])->name('payment.verify');

    // Customer-facing route to view ticket
    Route::get('/ticket/view/{ticket_id}', [BookingController::class, 'view'])->name('ticket.view');

    Route::get('/scan-ticket/{ticket_id}', [CheckerAuthController::class, 'scanTicket']);

});