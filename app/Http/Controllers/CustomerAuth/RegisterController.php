<?php

namespace App\Http\Controllers\CustomerAuth;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController
{
    public function showRegisterForm()
    {
        return view('customer.register');
    }

    // STEP 1: SEND OTP
   public function sendOtp(Request $request)
{
    $request->validate([
        'first_name' => 'required',
        'last_name'  => 'required',
        'mobile'     => 'required',
        'email'      => 'required|email',
        'password'   => 'required|min:6',
    ]);

    // --------------------------------------------
    // CHECK IF EMAIL EXISTS IN CUSTOMERS TABLE
    // --------------------------------------------
    if (\App\Models\Customer::where('email', $request->email)->exists()) {
        return response()->json([
            'success' => false,
            'exists' => true,
            'message' => "Email already exists. Please login or reset your password."
        ], 409);
    }

    // --------------------------------------------
    // GENERATE OTP
    // --------------------------------------------
    $otp = rand(100000, 999999);

    // Store OTP data in cache (15 minutes expiry) instead of session for API support
    $cacheKey = 'pending_registration_' . $request->email;
    \Cache::put($cacheKey, [
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
        'mobile'     => $request->mobile,
        'email'      => $request->email,
        'password'   => $request->password,
        'otp'        => $otp
    ], now()->addMinutes(15));

    // --------------------------------------------
    // SEND OTP EMAIL
    // --------------------------------------------
    Mail::raw("Your OTP is: $otp", function ($message) use ($request) {
        $message->to($request->email)
                ->subject('Your Email OTP Verification');
    });

    return response()->json([
        'success' => true,
        'message' => "OTP sent successfully!"
    ]);
}


    // STEP 2: VERIFY OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        // Retrieve registration data from cache
        $cacheKey = 'pending_registration_' . $request->email;
        $data = \Cache::get($cacheKey);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired or invalid. Please request a new one.'
            ], 400);
        }

        // Verify OTP
        if ($request->otp != $data['otp']) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        // OTP Correct → Create Customer
        $customer = Customer::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'mobile' => $data['mobile'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Create authentication token
        $token = $customer->createToken('mobile-app')->plainTextToken;

        // Clear cache data
        \Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'token' => $token,
                'customer' => $customer
            ]
        ]);
    }
}

