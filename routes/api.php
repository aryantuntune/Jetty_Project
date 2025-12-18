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
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\RazorpayController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes (no authentication required)
Route::prefix('customer')->group(function () {
    // Registration
    Route::post('generate-otp', [ApiController::class, 'sendOtp']);
    Route::post('verify-otp', [ApiController::class, 'verifyOtp']);

    // Login
    Route::post('login', [ApiController::class, 'customerlogin']);
    Route::post('google-signin', [ApiController::class, 'customergoogleSignIn']);

    // Password Reset
    Route::post('password-reset/request-otp', [ApiController::class, 'requestOTP']);
    Route::post('password-reset/verify-otp', [ApiController::class, 'verifyOTP']);
    Route::post('password-reset/reset', [ApiController::class, 'resetPassword']);
});

// Protected routes (require authentication with customer token)
Route::middleware('customer.api')->group(function () {

    // Customer routes
    Route::prefix('customer')->group(function () {
        Route::get('logout', [ApiController::class, 'customerlogout']);
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
    Route::get('branches/{id}/to-branches', [ApiController::class, 'getToBranches']);

    // Razorpay payment routes
    Route::post('razorpay/order', [RazorpayController::class, 'createOrder']);
    Route::post('razorpay/verify', [RazorpayController::class, 'verifyPayment']);

    // Bookings
    Route::get('bookings/success', [ApiController::class, 'getSuccessfulBookings']);
    Route::get('bookings', [ApiController::class, 'index']);
    Route::post('bookings', [ApiController::class, 'store']);
    Route::get('bookings/{id}', [ApiController::class, 'show']);
    Route::post('bookings/{id}/cancel', [ApiController::class, 'cancel']);
});
