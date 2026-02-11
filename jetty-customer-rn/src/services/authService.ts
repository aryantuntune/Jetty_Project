// Auth Service - Fixed to correctly map customer fields from API
import { api } from './api';
import { storageService } from './storageService';
import {
    Customer,
    LoginResponse,
    OTPResponse,
    RegisterRequest,
    VerifyOTPRequest,
    LoginRequest,
    GoogleSignInRequest,
    ResetPasswordRequest,
    UpdateProfileRequest,
    ApiResponse,
} from '@/types';

// API customer format (snake_case)
interface ApiCustomer {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    mobile: string;
    profile_image?: string;
    google_id?: string;
}

// Map API customer to our Customer type
const mapCustomer = (apiCustomer: ApiCustomer): Customer => ({
    id: apiCustomer.id,
    firstName: apiCustomer.first_name,
    lastName: apiCustomer.last_name,
    email: apiCustomer.email,
    mobile: apiCustomer.mobile,
    profileImage: apiCustomer.profile_image,
    googleId: apiCustomer.google_id,
});

// Wrapped response format from API
interface WrappedLoginResponse {
    success?: boolean;
    message?: string;
    data?: {
        token?: string;
        customer?: ApiCustomer;
    };
    token?: string;
    customer?: ApiCustomer;
}

const extractLoginResponse = (response: WrappedLoginResponse): LoginResponse => {
    // Handle wrapped response format (data.token, data.customer)
    if (response.data?.token && response.data?.customer) {
        return {
            token: response.data.token,
            customer: mapCustomer(response.data.customer),
        };
    }
    // Handle direct format (token, customer)
    if (response.token && response.customer) {
        return {
            token: response.token,
            customer: mapCustomer(response.customer),
        };
    }
    throw new Error('Invalid response format: missing token or customer data');
};

export const authService = {
    // Generate OTP for registration
    generateOTP: async (data: RegisterRequest): Promise<OTPResponse> => {
        const response = await api.post<OTPResponse>('/customer/generate-otp', {
            first_name: data.firstName,
            last_name: data.lastName,
            email: data.email,
            mobile: data.mobile,
            password: data.password,
            password_confirmation: data.passwordConfirmation,
        });
        return response;
    },

    // Verify OTP and complete registration
    verifyOTP: async (data: VerifyOTPRequest): Promise<LoginResponse> => {
        const rawResponse = await api.post<WrappedLoginResponse>('/customer/verify-otp', {
            email: data.email,
            otp: data.otp,
        });

        const response = extractLoginResponse(rawResponse);

        if (response.token) {
            await storageService.saveToken(response.token);
        }
        if (response.customer) {
            await storageService.saveCustomer(response.customer);
        }

        return response;
    },

    // Login
    login: async (data: LoginRequest): Promise<LoginResponse> => {
        const rawResponse = await api.post<WrappedLoginResponse>('/customer/login', {
            email: data.email,
            password: data.password,
        });

        const response = extractLoginResponse(rawResponse);

        if (response.token) {
            await storageService.saveToken(response.token);
        }
        if (response.customer) {
            await storageService.saveCustomer(response.customer);
        }

        return response;
    },

    // Google Sign-In
    googleSignIn: async (data: GoogleSignInRequest): Promise<LoginResponse> => {
        const rawResponse = await api.post<WrappedLoginResponse>('/customer/google-signin', {
            id_token: data.idToken,
            first_name: data.firstName,
            last_name: data.lastName,
            email: data.email,
            profile_image: data.profileImage,
        });

        const response = extractLoginResponse(rawResponse);

        if (response.token) {
            await storageService.saveToken(response.token);
        }
        if (response.customer) {
            await storageService.saveCustomer(response.customer);
        }

        return response;
    },

    // Logout
    logout: async (): Promise<void> => {
        try {
            await api.get('/customer/logout');
        } catch {
            // Ignore errors - we'll clear storage anyway
        }
        await storageService.clearAll();
    },

    // Request password reset OTP
    requestPasswordOTP: async (email: string): Promise<OTPResponse> => {
        return api.post<OTPResponse>('/customer/password-reset/request-otp', { email });
    },

    // Verify password reset OTP
    verifyPasswordOTP: async (data: VerifyOTPRequest): Promise<ApiResponse<{ verified: boolean }>> => {
        return api.post<ApiResponse<{ verified: boolean }>>('/customer/password-reset/verify-otp', {
            email: data.email,
            otp: data.otp,
        });
    },

    // Reset password
    resetPassword: async (data: ResetPasswordRequest): Promise<ApiResponse<void>> => {
        return api.post<ApiResponse<void>>('/customer/password-reset/reset', {
            email: data.email,
            password: data.password,
            password_confirmation: data.passwordConfirmation,
        });
    },

    // Get profile
    getProfile: async (): Promise<Customer> => {
        const response = await api.get<{ customer?: ApiCustomer; data?: { customer?: ApiCustomer } }>('/customer/profile');

        let apiCustomer: ApiCustomer | undefined;
        if (response.data?.customer) {
            apiCustomer = response.data.customer;
        } else if (response.customer) {
            apiCustomer = response.customer;
        }

        if (!apiCustomer) {
            throw new Error('Invalid profile response format');
        }

        return mapCustomer(apiCustomer);
    },

    // Update profile
    updateProfile: async (data: UpdateProfileRequest): Promise<Customer> => {
        const response = await api.put<{ customer?: ApiCustomer; data?: { customer?: ApiCustomer } }>('/customer/profile', {
            first_name: data.firstName,
            last_name: data.lastName,
            mobile: data.mobile,
        });

        let apiCustomer: ApiCustomer | undefined;
        if (response.data?.customer) {
            apiCustomer = response.data.customer;
        } else if (response.customer) {
            apiCustomer = response.customer;
        }

        if (!apiCustomer) {
            throw new Error('Invalid profile response format');
        }

        const customer = mapCustomer(apiCustomer);
        await storageService.saveCustomer(customer);

        return customer;
    },

    // Upload profile picture
    uploadProfilePicture: async (imageUri: string): Promise<Customer> => {
        const formData = new FormData();

        const uriParts = imageUri.split('.');
        const fileType = uriParts[uriParts.length - 1];

        formData.append('image', {
            uri: imageUri,
            name: `profile.${fileType}`,
            type: `image/${fileType}`,
        } as unknown as Blob);

        const response = await api.upload<{ customer?: ApiCustomer; data?: { customer?: ApiCustomer } }>('/customer/profile/upload-picture', formData);

        let apiCustomer: ApiCustomer | undefined;
        if (response.data?.customer) {
            apiCustomer = response.data.customer;
        } else if (response.customer) {
            apiCustomer = response.customer;
        }

        if (!apiCustomer) {
            throw new Error('Invalid profile response format');
        }

        const customer = mapCustomer(apiCustomer);
        await storageService.saveCustomer(customer);

        return customer;
    },
};

export default authService;
