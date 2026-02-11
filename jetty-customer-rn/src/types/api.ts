// API types following the migration guide
import { Customer, Booking } from './models';

export interface ApiResponse<T> {
    data?: T;
    message?: string;
    error?: string;
    errors?: Record<string, string[]>;
}

export interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export interface LoginResponse {
    token: string;
    customer: Customer;
}

export interface OTPResponse {
    success: boolean;
    message: string;
    exists?: boolean; // True if email already exists (409 response)
}

export interface RegisterRequest {
    firstName: string;
    lastName: string;
    email: string;
    mobile: string;
    password: string;
    passwordConfirmation: string;
}

export interface VerifyOTPRequest {
    email: string;
    otp: string;
}

export interface LoginRequest {
    email: string;
    password: string;
}

export interface GoogleSignInRequest {
    idToken: string;
    firstName: string;
    lastName: string;
    email: string;
    profileImage?: string;
}

export interface ResetPasswordRequest {
    email: string;
    password: string;
    passwordConfirmation: string;
}

export interface CreateBookingRequest {
    fromBranch: number;
    toBranch?: number;
    ferryBoatId: number;
    ferryTime: string;
    bookingDate: string; // YYYY-MM-DD format
    items: {
        itemRateId: number;  // Backend looks up rate from DB using this
        itemName: string;
        qty: number;
        rate: number;
        amount: number;
        vehicleNo?: string;
    }[];
    totalAmount: number;
    paymentId: string;
}

export interface RazorpayOrderResponse {
    orderId: string;
    amount: number;
    currency: string;
    keyId: string;
}

export interface RazorpayVerifyRequest {
    razorpayOrderId: string;
    razorpayPaymentId: string;
    razorpaySignature: string;
}

export interface UpdateProfileRequest {
    firstName: string;
    lastName: string;
    mobile: string;
}
