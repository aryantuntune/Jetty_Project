<?php

namespace App\Http\Controllers\CustomerAuth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('customer.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find customer by email
        $customer = \App\Models\Customer::where('email', $request->email)->first();

        // Check if customer exists and password matches
        if (!$customer || !\Hash::check($request->password, $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // Create authentication token
        $token = $customer->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'customer' => $customer
            ]
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke current token for API
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Google Sign-In for API
     * Accepts Google ID token and creates/logs in the user
     */
    public function googleSignIn(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
        ]);

        // Check if customer exists
        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            // Create new customer from Google account
            $customer = Customer::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name ?? '',
                'email' => $request->email,
                'mobile' => '', // User can add later in profile
                'password' => Hash::make(uniqid()), // Random password for Google users
            ]);
        }

        // Create authentication token
        $token = $customer->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Google Sign-In successful',
            'data' => [
                'token' => $token,
                'customer' => $customer
            ]
        ]);
    }
}