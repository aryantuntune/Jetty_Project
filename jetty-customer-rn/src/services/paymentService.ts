// Payment Service - Razorpay Integration
// Note: Requires EAS build to work (native module)
// In Expo Go, it will fallback to simulated payment

import Constants from 'expo-constants';

// Get Razorpay key from app.json config
const RAZORPAY_KEY_ID = Constants.expoConfig?.extra?.razorpayKeyId || 'YOUR_RAZORPAY_KEY_ID';

// Check if we have a real Razorpay key configured
const isRazorpayConfigured = (): boolean => {
    return RAZORPAY_KEY_ID && RAZORPAY_KEY_ID !== 'YOUR_RAZORPAY_KEY_ID';
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

    // If Razorpay is not configured or not available, simulate payment
    if (!isRazorpayConfigured() || !RazorpayModule) {
        console.log('[PaymentService] Using simulated payment (Expo Go mode)');
        return simulatePayment(options);
    }

    try {
        const razorpayOptions = {
            description: options.description,
            image: 'https://carferry.online/assets/logo.png', // Your app logo
            currency: options.currency || 'INR',
            key: RAZORPAY_KEY_ID,
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
 * Verify payment with backend (optional - for extra security)
 */
export const verifyPayment = async (
    paymentId: string,
    orderId?: string,
    signature?: string
): Promise<boolean> => {
    // If simulated payment, skip verification
    if (paymentId.startsWith('sim_')) {
        console.log('[PaymentService] Skipping verification for simulated payment');
        return true;
    }

    try {
        // TODO: Call your backend API to verify the payment
        // const response = await api.post('/payments/verify', { paymentId, orderId, signature });
        // return response.verified;

        console.log('[PaymentService] Payment verification skipped (not implemented)');
        return true;
    } catch (error) {
        console.error('[PaymentService] Payment verification failed:', error);
        return false;
    }
};

export const paymentService = {
    isRazorpayConfigured,
    initiatePayment,
    verifyPayment,
};

export default paymentService;
