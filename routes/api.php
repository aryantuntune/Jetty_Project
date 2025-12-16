<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerAuth\LoginController;
use App\Http\Controllers\CustomerAuth\RegisterController;
use App\Http\Controllers\CustomerAuth\ForgotPasswordController;
use App\Http\Controllers\CustomerAuth\CustomerProfileController;
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
// RATE LIMITING: Prevent brute force and abuse
Route::prefix('customer')->group(function () {
    // Strict rate limiting for OTP (prevent spam)
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('generate-otp', [RegisterController::class, 'sendOtp']);
        Route::post('password-reset/request-otp', [ForgotPasswordController::class, 'requestOTP']);
    });

    // Moderate rate limiting for authentication
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('verify-otp', [RegisterController::class, 'verifyOtp']);
        Route::post('login', [LoginController::class, 'login']);
        Route::post('google-signin', [LoginController::class, 'googleSignIn']);
        Route::post('password-reset/verify-otp', [ForgotPasswordController::class, 'verifyOTP']);
        Route::post('password-reset/reset', [ForgotPasswordController::class, 'resetPassword']);
    });
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
        Route::put('profile', [CustomerProfileController::class, 'updateProfile']);
        Route::post('profile/upload-picture', [CustomerProfileController::class, 'uploadProfilePicture']);
        
        Route::get('branch', [BranchController::class, 'getBranches']);
        
        // Ferry and rates routes
        Route::get('ferries/branch/{id}', [FerryBoatController::class, 'getFerriesByBranch']);
        Route::get('rates/branch/{id}', [ItemRateController::class, 'getItemRatesByBranch']);
    });

    // Branch routes
    Route::get('branches/{id}/to-branches', [BookingController::class, 'getToBranches']);

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
