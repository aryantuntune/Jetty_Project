// Payment Service - Razorpay Integration
// Note: Requires EAS build to work (native module)
// In Expo Go, it will fallback to simulated payment

import Constants from 'expo-constants';
import api from './api';

// Cached Razorpay key (fetched from backend)
let cachedRazorpayKey: string | null = null;

/**
 * Fetch Razorpay key from backend API
 * This prevents hardcoding the key in the app bundle
 */
const getRazorpayKey = async (): Promise<string> => {
    // Return cached key if available
    if (cachedRazorpayKey) {
        return cachedRazorpayKey;
    }

    try {
        const response = await api.get('/config/razorpay-key');
        cachedRazorpayKey = response.data.key;

        if (!cachedRazorpayKey) {
            throw new Error('Razorpay key not configured on server');
        }

        return cachedRazorpayKey;
    } catch (error) {
        console.error('[PaymentService] Failed to fetch Razorpay key:', error);
        throw new Error('Payment service is unavailable. Please try again later.');
    }
};

// Check if Razorpay key is valid
const isRazorpayConfigured = async (): Promise<boolean> => {
    try {
        const key = await getRazorpayKey();
        return key && key.startsWith('rzp_');
    } catch {
        return false;
    }
};

export interface PaymentOptions {
    amount: number; // Amount in paise (INR * 100)
    currency?: string;
    orderId?: string;
    description: string;
    customerName: string;
    customerEmail: string;
    customerPhone: string;
}

export interface PaymentResult {
    success: boolean;
    paymentId?: string;
    orderId?: string;
    signature?: string;
    error?: string;
}

// Razorpay payment module type (will be available after EAS build)
interface RazorpayCheckout {
    open: (options: Record<string, unknown>) => Promise<{
        razorpay_payment_id: string;
        razorpay_order_id?: string;
        razorpay_signature?: string;
    }>;
}

// Try to get Razorpay module (only works after EAS build)
let RazorpayModule: RazorpayCheckout | null = null;
try {
    // This will only work in EAS build, not in Expo Go
    // eslint-disable-next-line @typescript-eslint/no-require-imports
    RazorpayModule = require('react-native-razorpay').default;
} catch {
    console.log('[PaymentService] Razorpay not available (Expo Go mode), using simulated payments');
}

/**
 * Initiate payment via Razorpay
 * Falls back to simulated payment in Expo Go
 */
export const initiatePayment = async (options: PaymentOptions): Promise<PaymentResult> => {
    console.log('[PaymentService] Initiating payment:', options);

    // Check if Razorpay module is available (only in EAS build)
    if (!RazorpayModule) {
        console.log('[PaymentService] Razorpay module not available (Expo Go mode), using simulated payment');
        return simulatePayment(options);
    }

    try {
        // Fetch Razorpay key from backend
        const razorpayKey = await getRazorpayKey();

        const razorpayOptions = {
            description: options.description,
            image: 'https://carferry.online/assets/logo.png', // Your app logo
            currency: options.currency || 'INR',
            key: razorpayKey, // Dynamic key from backend
            amount: options.amount, // Amount in paise
            name: 'Jetty Ferry Booking',
            order_id: options.orderId || '', // Only if using Razorpay Orders
            prefill: {
                email: options.customerEmail,
                contact: options.customerPhone,
                name: options.customerName,
            },
            theme: { color: '#006994' }, // Your brand color
        };

        console.log('[PaymentService] Opening Razorpay checkout...');
        const result = await RazorpayModule.open(razorpayOptions);

        console.log('[PaymentService] Payment successful:', result);
        return {
            success: true,
            paymentId: result.razorpay_payment_id,
            orderId: result.razorpay_order_id,
            signature: result.razorpay_signature,
        };
    } catch (error: unknown) {
        const errorMessage = error instanceof Error ? error.message : 'Payment failed';
        console.error('[PaymentService] Payment failed:', error);
        return {
            success: false,
            error: errorMessage,
        };
    }
};

/**
 * Simulate payment for Expo Go testing
 * Returns a fake payment ID after a short delay
 */
const simulatePayment = async (options: PaymentOptions): Promise<PaymentResult> => {
    console.log('[PaymentService] Simulating payment for ₹', options.amount / 100);

    // Simulate network delay
    await new Promise(resolve => setTimeout(resolve, 1500));

    // Generate a simulated payment ID
    const paymentId = `sim_${Date.now()}_${Math.random().toString(36).substring(7)}`;

    console.log('[PaymentService] Simulated payment ID:', paymentId);
    return {
        success: true,
        paymentId,
    };
};

/**
 * Verify payment with backend (REQUIRED for production)
 * Verifies Razorpay signature to prevent payment tampering
 */
export const verifyPayment = async (
    paymentId: string,
    orderId?: string,
    signature?: string
): Promise<boolean> => {
    // If simulated payment (development only), skip verification
    if (__DEV__ && paymentId.startsWith('sim_')) {
        console.log('[PaymentService] Skipping verification for simulated payment (DEV mode)');
        return true;
    }

    // Production mode or real payment - MUST verify signature
    if (!signature) {
        console.error('[PaymentService] No signature provided for verification');
        return false;
    }

    try {
        console.log('[PaymentService] Verifying payment signature with backend...');

        const response = await api.post('/payments/verify', {
            razorpay_payment_id: paymentId,
            razorpay_order_id: orderId,
            razorpay_signature: signature
        });

        if (response.data && response.data.verified === true) {
            console.log('[PaymentService] Payment signature verified successfully');
            return true;
        }

        console.error('[PaymentService] Payment verification failed: Invalid signature');
        return false;

    } catch (error) {
        console.error('[PaymentService] Payment verification error:', error);
        // In production, treat verification errors as payment failure
        return false;
    }
};

export const paymentService = {
    isRazorpayConfigured,
    initiatePayment,
    verifyPayment,
};

export default paymentService;
