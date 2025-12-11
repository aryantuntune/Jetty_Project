<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    /**
     * Send OTP to the given email
     */


    public function generateOtp(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email',
            'mobile'     => 'required|string|max:20',
            'password'   => 'required|string|min:6',
        ]);

        $email = $request->email;

        // Check if email already exists
        if (Customer::where('email', $email)->exists()) {
            return response()->json(['message' => 'Email is already registered.'], 422);
        }

        // Generate 6 digit OTP
        $otp = random_int(100000, 999999);

        // Store OTP and user data temporarily in cache for 10 mins
        $cacheData = [
            'otp' => $otp,
            'user_data' => [
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'      => $email,
                'mobile'     => $request->mobile,
                'password'   => $request->password,
            ]
        ];
        Cache::put('signup_otp_for_' . $email, $cacheData, now()->addMinutes(10));

        // Send OTP to email (or mobile SMS, if you want)
        Mail::raw("Your signup OTP is: $otp", function ($message) use ($email) {
            $message->to($email)->subject('Signup OTP Verification');
        });

        return response()->json(['message' => 'OTP sent successfully.']);
    }


    /**
     * Register new customer after OTP verification
     */
    public function verifyOtpAndRegister(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:customers,email',
            'otp'   => 'required|digits:6',
        ]);

        $email = $request->email;
        $otp = $request->otp;

        $cacheData = Cache::get('signup_otp_for_' . $email);

        if (!$cacheData) {
            return response()->json(['message' => 'OTP expired or not found.'], 422);
        }

        if ($cacheData['otp'] != $otp) {
            return response()->json(['message' => 'Invalid OTP.'], 422);
        }

        // Create user
        $userData = $cacheData['user_data'];
        $customer = Customer::create([
            'first_name' => $userData['first_name'],
            'last_name'  => $userData['last_name'],
            'email'      => $userData['email'],
            'mobile'     => $userData['mobile'],
            'password'   => Hash::make($userData['password']),
        ]);

        // Delete cached data
        Cache::forget('signup_otp_for_' . $email);

        return response()->json([
            'message' => 'Signup successful.',
            'data'    => $customer
        ], 201);
    }
    // ---------------------------------------------------------------------------------

    // Send Password Reset OTP (forgot password request)
    public function sendPasswordResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
        ]);

        $email = $request->email;

        // Generate OTP
        $otp = random_int(100000, 999999);

        Cache::put('password_reset_otp_for_' . $email, $otp, now()->addMinutes(10));

        // Send OTP to email
        Mail::raw("Your password reset OTP is: $otp", function ($message) use ($email) {
            $message->to($email)->subject('Password Reset OTP Verification');
        });

        return response()->json(['message' => 'Password reset OTP sent successfully.']);
    }

    // Verify Password Reset OTP
    public function verifyPasswordResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
            'otp'   => 'required|digits:6',
        ]);

        $email = $request->email;
        $otp = $request->otp;

        $cachedOtp = Cache::get('password_reset_otp_for_' . $email);

        if (!$cachedOtp) {
            return response()->json(['message' => 'OTP expired or not found.'], 422);
        }

        if ($cachedOtp != $otp) {
            return response()->json(['message' => 'Invalid OTP.'], 422);
        }

        return response()->json(['message' => 'OTP verified successfully.']);
    }

    // Reset Password after OTP verified
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
            'password' => 'required',
        ]);

        $email = $request->email;

        // Optional: check if OTP is verified by looking up cached OTP or some flag

        $customer = Customer::where('email', $email)->first();

        $customer->password = Hash::make($request->password);
        $customer->save();

        // Clear OTP cache
        Cache::forget('password_reset_otp_for_' . $email);

        return response()->json(['message' => 'Password reset successful.']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        // Create Sanctum token
        $token = $customer->createToken('customer_api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'data'    => $customer
        ]);
    }

    /**
     * Logout (Revoke Token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Customer Profile API
     */
    public function profile(Request $request)
    {
        return response()->json([
            'message' => 'Profile fetched successfully',
            'data' => $request->user()
        ]);
    }
}
