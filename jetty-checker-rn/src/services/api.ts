// API Service with Axios for Checker App
import axios, { AxiosInstance, AxiosRequestConfig, AxiosError } from 'axios';
import Constants from 'expo-constants';
import { storageService } from './storageService';

const API_BASE_URL = Constants.expoConfig?.extra?.apiBaseUrl || 'https://carferry.online/api';

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

// Token refresh state
let isRefreshing = false;
let failedQueue: Array<{ resolve: (token: string) => void; reject: (error: unknown) => void }> = [];

const processQueue = (error: unknown, token: string | null = null) => {
    failedQueue.forEach((prom) => {
        if (token) {
            prom.resolve(token);
        } else {
            prom.reject(error);
        }
    });
    failedQueue = [];
};

// Response interceptor with automatic token refresh
apiClient.interceptors.response.use(
    (response) => response,
    async (error: AxiosError) => {
        const originalRequest = error.config as AxiosRequestConfig & { _retry?: boolean };

        if (error.response?.status === 401 && !originalRequest._retry) {
            const currentToken = await storageService.getToken();
            if (!currentToken) {
                await storageService.clearAll();
                return Promise.reject(error);
            }

            if (isRefreshing) {
                return new Promise((resolve, reject) => {
                    failedQueue.push({
                        resolve: (token: string) => {
                            if (originalRequest.headers) {
                                originalRequest.headers.Authorization = `Bearer ${token}`;
                            }
                            resolve(apiClient(originalRequest));
                        },
                        reject,
                    });
                });
            }

            originalRequest._retry = true;
            isRefreshing = true;

            try {
                const response = await axios.post(
                    `${API_BASE_URL}/checker/refresh-token`,
                    {},
                    { headers: { Authorization: `Bearer ${currentToken}` } }
                );

                const newToken = response.data?.data?.token;
                if (newToken) {
                    await storageService.saveToken(newToken);
                    processQueue(null, newToken);

                    if (originalRequest.headers) {
                        originalRequest.headers.Authorization = `Bearer ${newToken}`;
                    }
                    return apiClient(originalRequest);
                }
            } catch (refreshError) {
                processQueue(refreshError, null);
                await storageService.clearAll();
                return Promise.reject(refreshError);
            } finally {
                isRefreshing = false;
            }
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
