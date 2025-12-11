<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;



Route::post('customer/generate-otp', [ApiController::class, 'generateOtp']);
Route::post('customer/verify-otp', [ApiController::class, 'verifyOtpAndRegister']);

Route::post('customer/password-reset/request-otp', [ApiController::class, 'sendPasswordResetOtp']);
Route::post('customer/password-reset/verify-otp', [ApiController::class, 'verifyPasswordResetOtp']);
Route::post('customer/password-reset/reset', [ApiController::class, 'resetPassword']);

Route::post('customer/login', [ApiController::class, 'login']);
Route::post('customer/logout', [ApiController::class, 'logout'])->middleware('auth:sanctum');
Route::get('customer/profile', [ApiController::class, 'profile'])->middleware('auth:sanctum');
