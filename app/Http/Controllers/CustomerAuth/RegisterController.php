<?php

namespace App\Http\Controllers\CustomerAuth;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class RegisterController
{
    public function showRegisterForm()
    {
        return Inertia::render('Customer/Register');
    }

    // STEP 1: SEND OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
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

        // Save form data + OTP in session
        session([
            'pending_user' => [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'password' => $request->password
            ],
            'otp' => $otp
        ]);

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
        if ($request->otp != session('otp')) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP']);
        }

        // OTP Correct → Create Customer
        $data = session('pending_user');

        $customer = Customer::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'mobile' => $data['mobile'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Clear session data
        session()->forget(['pending_user', 'otp']);

        return response()->json(['success' => true]);
    }
}
