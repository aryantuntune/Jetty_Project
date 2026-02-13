<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     * Get Razorpay public key for payment processing
     *
     * Note: Only returns the public key (key_id), never the secret
     * This endpoint can be called without authentication for initial app setup
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRazorpayKey(Request $request)
    {
        $razorpayKey = config('services.razorpay.key');

        if (empty($razorpayKey)) {
            return response()->json([
                'error' => 'Payment service is not configured'
            ], 503);
        }

        return response()->json([
            'key' => $razorpayKey,
            'environment' => config('app.env') === 'production' ? 'live' : 'test'
        ]);
    }

    /**
     * Server identity verification endpoint
     * Used by mobile apps to verify they're talking to the real server
     */
    public function getServerIdentity(Request $request)
    {
        return response()->json([
            'server_id' => 'carferry-jetty-prod-v1',
            'domain' => 'carferry.online',
            'timestamp' => now()->timestamp,
        ]);
    }

    /**
     * Get app configuration
     * Returns non-sensitive configuration data for the mobile apps
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAppConfig(Request $request)
    {
        return response()->json([
            'app_name' => config('app.name'),
            'api_version' => '1.0.0',
            'min_app_version' => '1.0.0',
            'features' => [
                'google_signin_enabled' => !empty(config('services.google.client_id')),
                'razorpay_enabled' => !empty(config('services.razorpay.key')),
            ],
            'contact' => [
                'support_email' => config('mail.from.address'),
                'support_phone' => '+91-1234567890', // Update with actual support number
            ],
            'policies' => [
                'privacy_policy_url' => config('app.url') . '/privacy-policy',
                'terms_url' => config('app.url') . '/terms',
            ]
        ]);
    }
}
