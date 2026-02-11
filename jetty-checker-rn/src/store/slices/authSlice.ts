// Auth Slice - Manages checker authentication state
import { createSlice, createAsyncThunk, PayloadAction } from '@reduxjs/toolkit';
import { authService } from '../../services/authService';
import { storageService } from '../../services/storageService';
import { Checker } from '../../types/models';

interface AuthState {
    checker: Checker | null;
    isAuthenticated: boolean;
    isLoading: boolean;
    isCheckingAuth: boolean;
    error: string | null;
}

const initialState: AuthState = {
    checker: null,
    isAuthenticated: false,
    isLoading: false,
    isCheckingAuth: true, // Start as true for initial auth check
    error: null,
};

// Async thunk to check authentication status on app start
// NOTE: This only checks local storage, does NOT make network calls
export const checkAuthStatus = createAsyncThunk(
    'auth/checkAuthStatus',
    async (_, { rejectWithValue }) => {
        try {
            const isLoggedIn = await authService.isLoggedIn();
            if (isLoggedIn) {
                const checker = await authService.getStoredChecker();
                if (checker) {
                    return checker;
                }
                // Token exists but no checker data - need to re-login
                // Don't make network call here, just show login screen
                await storageService.clearAll();
            }
            return null;
        } catch (error) {
            return rejectWithValue('Failed to check auth status');
        }
    }
);

// Async thunk for login
export const login = createAsyncThunk(
    'auth/login',
    async (credentials: { email: string; password: string }, { rejectWithValue }) => {
        const result = await authService.login(credentials);
        if (result.success && result.checker) {
            return result.checker;
        }
        return rejectWithValue(result.error || 'Login failed');
    }
);

// Async thunk for logout
export const logout = createAsyncThunk(
    'auth/logout',
    async (_, { rejectWithValue }) => {
        try {
            await authService.logout();
            return true;
        } catch (error) {
            return rejectWithValue('Logout failed');
        }
    }
);

// Async thunk to get/refresh profile
export const getProfile = createAsyncThunk(
    'auth/getProfile',
    async (_, { rejectWithValue }) => {
        const result = await authService.getProfile();
        if (result.success && result.checker) {
            return result.checker;
        }
        return rejectWithValue(result.error || 'Failed to get profile');
    }
);

const authSlice = createSlice({
    name: 'auth',
    initialState,
    reducers: {
        setChecker: (state, action: PayloadAction<Checker>) => {
            state.checker = action.payload;
            state.isAuthenticated = true;
        },
        clearError: (state) => {
            state.error = null;
        },
        resetAuth: (state) => {
            state.checker = null;
            state.isAuthenticated = false;
            state.isLoading = false;
            state.error = null;
        },
    },
    extraReducers: (builder) => {
        // Check auth status
        builder
            .addCase(checkAuthStatus.pending, (state) => {
                state.isCheckingAuth = true;
            })
            .addCase(checkAuthStatus.fulfilled, (state, action) => {
                state.isCheckingAuth = false;
                if (action.payload) {
                    state.checker = action.payload;
                    state.isAuthenticated = true;
                } else {
                    state.checker = null;
                    state.isAuthenticated = false;
                }
            })
            .addCase(checkAuthStatus.rejected, (state) => {
                state.isCheckingAuth = false;
                state.checker = null;
                state.isAuthenticated = false;
            });

        // Login
        builder
            .addCase(login.pending, (state) => {
                state.isLoading = true;
                state.error = null;
            })
            .addCase(login.fulfilled, (state, action) => {
                state.isLoading = false;
                state.checker = action.payload;
                state.isAuthenticated = true;
                state.error = null;
            })
            .addCase(login.rejected, (state, action) => {
                state.isLoading = false;
                state.error = action.payload as string;
            });

        // Logout
        builder
            .addCase(logout.pending, (state) => {
                state.isLoading = true;
            })
            .addCase(logout.fulfilled, (state) => {
                state.isLoading = false;
                state.checker = null;
                state.isAuthenticated = false;
                state.error = null;
            })
            .addCase(logout.rejected, (state) => {
                state.isLoading = false;
                // Even if logout fails on server, clear local state
                state.checker = null;
                state.isAuthenticated = false;
            });

        // Get profile
        builder
            .addCase(getProfile.pending, (state) => {
                state.isLoading = true;
            })
            .addCase(getProfile.fulfilled, (state, action) => {
                state.isLoading = false;
                state.checker = action.payload;
            })
            .addCase(getProfile.rejected, (state, action) => {
                state.isLoading = false;
                state.error = action.payload as string;
            });
    },
});

export const { setChecker, clearError, resetAuth } = authSlice.actions;
export default authSlice.reducer;
