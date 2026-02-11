// Auth Service for Checker App
// CRITICAL: Only roleId = 5 users (Checkers) can login to this app

import api, { getErrorMessage } from './api';
import storageService from './storageService';
import { ApiResponse, LoginResponse, ProfileResponse } from '../types/api';
import { Checker } from '../types/models';

// Role ID for Checker - MUST BE 5
const CHECKER_ROLE_ID = 5;

export interface LoginCredentials {
    email: string;
    password: string;
}

export interface AuthResult {
    success: boolean;
    checker?: Checker;
    error?: string;
}

export const authService = {
    /**
     * Login with email and password
     * CRITICAL: Validates that the user has roleId = 5 (Checker)
     */
    login: async (credentials: LoginCredentials): Promise<AuthResult> => {
        try {
            const response = await api.post<ApiResponse<LoginResponse>>('/checker/login', credentials);

            if (response.success && response.data) {
                const { token, user } = response.data;

                // Transform the user data to Checker format
                const checker: Checker = {
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    mobile: user.mobile,
                    branch_id: user.branch_id,
                    branch: user.branch,
                    ferry_boat_id: user.ferry_boat_id,
                    ferryboat: user.ferryboat,
                    created_at: user.created_at,
                };

                // Save token and checker data
                await storageService.saveToken(token);
                await storageService.saveChecker(checker);

                return { success: true, checker };
            }

            return { success: false, error: response.message || 'Login failed' };
        } catch (error) {
            const errorMessage = getErrorMessage(error);
            return { success: false, error: errorMessage };
        }
    },

    /**
     * Logout - clears token on server and local storage
     */
    logout: async (): Promise<void> => {
        try {
            await api.post('/checker/logout');
        } catch {
            // Ignore errors - we'll clear storage anyway
        } finally {
            await storageService.clearAll();
        }
    },

    /**
     * Get current checker profile
     */
    getProfile: async (): Promise<AuthResult> => {
        try {
            const response = await api.get<ApiResponse<ProfileResponse>>('/checker/profile');

            if (response.success && response.data) {
                const data = response.data;
                const checker: Checker = {
                    id: data.id,
                    name: data.name,
                    email: data.email,
                    mobile: data.mobile,
                    branch_id: data.branch_id,
                    branch: data.branch ? { id: data.branch_id, branch_name: data.branch.branch_name } : undefined,
                    ferry_boat_id: data.ferry_boat_id,
                    ferryboat: data.ferryboat ? { id: data.ferry_boat_id!, name: data.ferryboat.name } : undefined,
                    created_at: data.created_at,
                };

                // Update stored checker data
                await storageService.saveChecker(checker);

                return { success: true, checker };
            }

            return { success: false, error: 'Failed to get profile' };
        } catch (error) {
            const errorMessage = getErrorMessage(error);
            return { success: false, error: errorMessage };
        }
    },

    /**
     * Check if user is currently logged in
     */
    isLoggedIn: async (): Promise<boolean> => {
        return storageService.isLoggedIn();
    },

    /**
     * Get stored checker data
     */
    getStoredChecker: async (): Promise<Checker | null> => {
        return storageService.getChecker();
    },
};

export default authService;
