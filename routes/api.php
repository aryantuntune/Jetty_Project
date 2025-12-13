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
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
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

// Protected routes (require authentication with Sanctum token)
Route::middleware('auth:sanctum')->group(function () {

    // Customer routes
    Route::prefix('customer')->group(function () {
        Route::get('logout', [LoginController::class, 'logout']); // Changed from POST to GET
        Route::get('profile', function (Request $request) {
            return response()->json([
                'success' => true,
                'message' => 'Profile retrieved successfully',
                'data' => $request->user()
            ]);
        });
    });

    // Branch routes - Updated to match Postman collection
    Route::get('customer/branch', [BranchController::class, 'getBranches']); // Changed from /branches
    Route::get('branches/{id}/to-branches', [BookingController::class, 'getToBranches']); // New route
    Route::get('ferryboats/branch/{id}', [FerryBoatController::class, 'getFerriesByBranch']); // Changed path
    Route::get('item-rates/branch/{id}', [ItemRateController::class, 'getItemRatesByBranch']); // Changed path

    // Razorpay payment routes
    Route::post('razorpay/order', [RazorpayController::class, 'createOrder']);
    Route::post('razorpay/verify', [RazorpayController::class, 'verifyPayment']);

    // Bookings
    Route::get('bookings/success', [BookingController::class, 'getSuccessfulBookings']); // New route
    Route::get('bookings', [BookingController::class, 'index']);
    Route::post('bookings', [BookingController::class, 'store']);
    Route::get('bookings/{id}', [BookingController::class, 'show']);
    Route::post('bookings/{id}/cancel', [BookingController::class, 'cancel']);
});
