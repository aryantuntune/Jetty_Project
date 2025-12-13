<?php

namespace App\Http\Controllers\CustomerAuth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function showLinkForm()
    {
        return view('customer.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Send reset link using CUSTOMER broker
        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                ? back()->with('success', __($status))
                : back()->withErrors(['email' => __($status)]);
    }

    // API METHOD: Request Password Reset OTP
    public function requestOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if customer exists
        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this email address.'
            ], 404);
        }

        // Generate OTP
        $otp = rand(100000, 999999);

        // Store OTP in cache (15 minutes expiry)
        $cacheKey = 'password_reset_' . $request->email;
        \Cache::put($cacheKey, [
            'email' => $request->email,
            'otp' => $otp
        ], now()->addMinutes(15));

        // Send OTP email
        Mail::raw("Your password reset OTP is: $otp\n\nThis OTP will expire in 15 minutes.", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Password Reset OTP');
        });

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email successfully!'
        ]);
    }

    // API METHOD: Verify Password Reset OTP
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        // Retrieve OTP from cache
        $cacheKey = 'password_reset_' . $request->email;
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

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully'
        ]);
    }

    // API METHOD: Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|min:6',
        ]);

        // Verify OTP one more time
        $cacheKey = 'password_reset_' . $request->email;
        $data = \Cache::get($cacheKey);

        if (!$data || $request->otp != $data['otp']) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        // Find customer
        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        // Update password
        $customer->password = \Hash::make($request->password);
        $customer->save();

        // Clear OTP from cache
        \Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully!'
        ]);
    }
}
