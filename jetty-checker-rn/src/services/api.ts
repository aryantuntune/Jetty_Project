// API Service with Axios for Checker App
import axios, { AxiosInstance, AxiosRequestConfig, AxiosError } from 'axios';
import Constants from 'expo-constants';
import { storageService } from './storageService';

const API_BASE_URL = Constants.expoConfig?.extra?.apiBaseUrl || 'https://unfurling.ninja/api';

// Create Axios instance with 15s timeout (shorter for checker app)
const apiClient: AxiosInstance = axios.create({
    baseURL: API_BASE_URL,
    timeout: 15000,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// Request interceptor to inject Bearer token
apiClient.interceptors.request.use(
    async (config) => {
        const token = await storageService.getToken();
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor to handle 401 errors
apiClient.interceptors.response.use(
    (response) => response,
    async (error: AxiosError) => {
        if (error.response?.status === 401) {
            // Token expired or invalid - clear storage
            await storageService.clearAll();
            // The app will redirect to login via auth state change
        }
        return Promise.reject(error);
    }
);

// Helper function to extract error message
export const getErrorMessage = (error: unknown): string => {
    if (axios.isAxiosError(error)) {
        const axiosError = error as AxiosError<{ message?: string; error?: string; errors?: string[] | Record<string, string[]> }>;

        if (!axiosError.response) {
            return 'Network error. Please check your internet connection.';
        }

        const data = axiosError.response.data;

        if (data?.errors) {
            // Handle validation errors
            if (Array.isArray(data.errors)) {
                return data.errors[0];
            } else {
                const firstKey = Object.keys(data.errors)[0];
                return data.errors[firstKey][0];
            }
        }

        if (data?.message) {
            return data.message;
        }

        if (data?.error) {
            return data.error;
        }

        if (axiosError.response.status === 500) {
            return 'Server error. Please try again later.';
        }

        return 'Something went wrong. Please try again.';
    }

    if (error instanceof Error) {
        return error.message;
    }

    return 'An unexpected error occurred.';
};

// API methods (only get and post needed for checker app)
export const api = {
    get: <T>(url: string, config?: AxiosRequestConfig) =>
        apiClient.get<T>(url, config).then((res) => res.data),

    post: <T>(url: string, data?: unknown, config?: AxiosRequestConfig) =>
        apiClient.post<T>(url, data, config).then((res) => res.data),
};

export default api;
