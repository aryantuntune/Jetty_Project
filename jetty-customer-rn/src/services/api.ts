// API Service with Axios following the migration guide
import axios, { AxiosInstance, AxiosRequestConfig, AxiosError } from 'axios';
import Constants from 'expo-constants';
import { storageService } from './storageService';

const API_BASE_URL = Constants.expoConfig?.extra?.apiBaseUrl || 'https://carferry.online/api';

// Create Axios instance
const apiClient: AxiosInstance = axios.create({
    baseURL: API_BASE_URL,
    timeout: 30000,
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

// Response interceptor to handle errors
apiClient.interceptors.response.use(
    (response) => response,
    async (error: AxiosError) => {
        if (error.response?.status === 401) {
            // Token expired or invalid - clear storage and logout
            await storageService.clearAll();
            // The app will redirect to login via auth state change
        }
        return Promise.reject(error);
    }
);

// Helper function to extract error message
export const getErrorMessage = (error: unknown): string => {
    if (axios.isAxiosError(error)) {
        const axiosError = error as AxiosError<{ message?: string; error?: string; errors?: Record<string, string[]> }>;

        if (!axiosError.response) {
            return 'Network error. Please check your internet connection.';
        }

        const data = axiosError.response.data;

        if (data?.errors) {
            // Return first validation error
            const firstKey = Object.keys(data.errors)[0];
            return data.errors[firstKey][0];
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

// API methods
export const api = {
    get: <T>(url: string, config?: AxiosRequestConfig) =>
        apiClient.get<T>(url, config).then((res) => res.data),

    post: <T>(url: string, data?: unknown, config?: AxiosRequestConfig) =>
        apiClient.post<T>(url, data, config).then((res) => res.data),

    put: <T>(url: string, data?: unknown, config?: AxiosRequestConfig) =>
        apiClient.put<T>(url, data, config).then((res) => res.data),

    delete: <T>(url: string, config?: AxiosRequestConfig) =>
        apiClient.delete<T>(url, config).then((res) => res.data),

    upload: <T>(url: string, formData: FormData) =>
        apiClient.post<T>(url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        }).then((res) => res.data),
};

export default api;
