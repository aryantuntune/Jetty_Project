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
        Route::post('logout', [LoginController::class, 'logout']);
        Route::get('profile', function (Request $request) {
            return response()->json([
                'success' => true,
                'message' => 'Profile retrieved successfully',
                'data' => $request->user()
            ]);
        });
    });

    // Branch routes
    Route::get('branches', [BranchController::class, 'index']);
    Route::get('branches/{id}/ferries', [FerryBoatController::class, 'getFerriesByBranch']);
    Route::get('branches/{from}/to/{to}/routes', [BranchController::class, 'getRoutes']);

    // Item rates (pricing)
    Route::get('item-rates', [ItemRateController::class, 'getItemRates']);

    // Bookings
    Route::get('bookings', [BookingController::class, 'index']);
    Route::post('bookings', [BookingController::class, 'store']);
    Route::get('bookings/{id}', [BookingController::class, 'show']);
    Route::post('bookings/{id}/cancel', [BookingController::class, 'cancel']);
});
