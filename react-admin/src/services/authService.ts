import apiClient from '@/lib/axios';
import { LoginRequest, LoginResponse, User } from '@/types/auth';

export const authService = {
    /**
     * Login with email and password
     */
    async login(credentials: LoginRequest): Promise<LoginResponse> {
        // First, get CSRF cookie for Sanctum
        await apiClient.get('/sanctum/csrf-cookie');

        // Then login
        const response = await apiClient.post('/api/admin/login', credentials);

        return {
            user: response.data.user || response.data,
            token: response.data.token,
            message: response.data.message,
        };
    },

    /**
     * Logout current user
     */
    async logout(): Promise<void> {
        await apiClient.post('/api/admin/logout');
    },

    /**
     * Get current authenticated user
     */
    async getCurrentUser(): Promise<User> {
        const response = await apiClient.get('/api/admin/user');
        return response.data;
    },

    /**
     * Check if user is authenticated
     */
    async checkAuth(): Promise<boolean> {
        try {
            await this.getCurrentUser();
            return true;
        } catch {
            return false;
        }
    },
};
