<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerAuth\LoginController;
use App\Http\Controllers\CustomerAuth\RegisterController;
use App\Http\Controllers\CustomerAuth\ForgotPasswordController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\FerryBoatController;
use App\Http\Controllers\ItemRateController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RazorpayController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes (no authentication required)
Route::prefix('customer')->group(function () {
    // Registration
    Route::post('generate-otp', [RegisterController::class, 'sendOtp']);
    Route::post('verify-otp', [RegisterController::class, 'verifyOtp']);

    // Login
    Route::post('login', [LoginController::class, 'login']);
    Route::post('google-signin', [LoginController::class, 'googleSignIn']);

    // Password Reset
    Route::post('password-reset/request-otp', [ForgotPasswordController::class, 'requestOTP']);
    Route::post('password-reset/verify-otp', [ForgotPasswordController::class, 'verifyOTP']);
    Route::post('password-reset/reset', [ForgotPasswordController::class, 'resetPassword']);
});

// Protected routes (require authentication with customer token)
Route::middleware('customer.api')->group(function () {

    // Customer routes
    Route::prefix('customer')->group(function () {
        Route::get('logout', [LoginController::class, 'logout']);
        Route::get('profile', function (Request $request) {
            return response()->json([
                'success' => true,
                'message' => 'Profile retrieved successfully',
                'data' => $request->user()
            ]);
        });
        Route::get('branch', [BranchController::class, 'getBranches']);
    });

    // Branch routes
    Route::get('branches/{id}/to-branches', [BookingController::class, 'getToBranches']);
    
    // Ferry routes - Explicitly add middleware
    Route::get('ferryboats/branch/{id}', [FerryBoatController::class, 'getFerriesByBranch'])
        ->middleware('customer.api');
    
    // Item rates
    Route::get('item-rates/branch/{id}', [ItemRateController::class, 'getItemRatesByBranch']);

    // Razorpay payment routes
    Route::post('razorpay/order', [RazorpayController::class, 'createOrder']);
    Route::post('razorpay/verify', [RazorpayController::class, 'verifyPayment']);

    // Bookings
    Route::get('bookings/success', [BookingController::class, 'getSuccessfulBookings']);
    Route::get('bookings', [BookingController::class, 'index']);
    Route::post('bookings', [BookingController::class, 'store']);
    Route::get('bookings/{id}', [BookingController::class, 'show']);
    Route::post('bookings/{id}/cancel', [BookingController::class, 'cancel']);
});
