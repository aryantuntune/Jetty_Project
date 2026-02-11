// Auth Slice following the migration guide
import { createSlice, createAsyncThunk, PayloadAction } from '@reduxjs/toolkit';
import { Customer, LoginRequest, RegisterRequest, VerifyOTPRequest, UpdateProfileRequest } from '@/types';
import { authService, storageService, getErrorMessage } from '@/services';

interface AuthState {
    customer: Customer | null;
    isAuthenticated: boolean;
    isLoading: boolean;
    error: string | null;
}

const initialState: AuthState = {
    customer: null,
    isAuthenticated: false,
    isLoading: false,
    error: null,
};

// Async Thunks

// Check auth status on app start
export const checkAuthStatus = createAsyncThunk(
    'auth/checkAuthStatus',
    async (_, { rejectWithValue }) => {
        try {
            const isLoggedIn = await storageService.isLoggedIn();
            if (isLoggedIn) {
                const customer = await storageService.getCustomer();
                return { customer, isAuthenticated: true };
            }
            return { customer: null, isAuthenticated: false };
        } catch (error) {
            return rejectWithValue(getErrorMessage(error));
        }
    }
);

// Login
export const login = createAsyncThunk(
    'auth/login',
    async (data: LoginRequest, { rejectWithValue }) => {
        try {
            const response = await authService.login(data);
            return response.customer;
        } catch (error) {
            return rejectWithValue(getErrorMessage(error));
        }
    }
);

// Register (triggers OTP send)
export const register = createAsyncThunk(
    'auth/register',
    async (data: RegisterRequest, { rejectWithValue }) => {
        try {
            await authService.generateOTP(data);
            return data.email;
        } catch (error) {
            return rejectWithValue(getErrorMessage(error));
        }
    }
);

// Verify OTP
export const verifyOTP = createAsyncThunk(
    'auth/verifyOTP',
    async (data: VerifyOTPRequest, { rejectWithValue }) => {
        try {
            const response = await authService.verifyOTP(data);
            return response.customer;
        } catch (error) {
            return rejectWithValue(getErrorMessage(error));
        }
    }
);

// Logout
export const logout = createAsyncThunk(
    'auth/logout',
    async (_, { rejectWithValue }) => {
        try {
            await authService.logout();
        } catch (error) {
            return rejectWithValue(getErrorMessage(error));
        }
    }
);

// Update profile
export const updateProfile = createAsyncThunk(
    'auth/updateProfile',
    async (data: UpdateProfileRequest, { rejectWithValue }) => {
        try {
            const customer = await authService.updateProfile(data);
            return customer;
        } catch (error) {
            return rejectWithValue(getErrorMessage(error));
        }
    }
);

// Upload profile picture
export const uploadProfilePicture = createAsyncThunk(
    'auth/uploadProfilePicture',
    async (imageUri: string, { rejectWithValue }) => {
        try {
            const customer = await authService.uploadProfilePicture(imageUri);
            return customer;
        } catch (error) {
            return rejectWithValue(getErrorMessage(error));
        }
    }
);

// Slice
const authSlice = createSlice({
    name: 'auth',
    initialState,
    reducers: {
        setCustomer: (state, action: PayloadAction<Customer | null>) => {
            state.customer = action.payload;
            state.isAuthenticated = !!action.payload;
        },
        clearError: (state) => {
            state.error = null;
        },
    },
    extraReducers: (builder) => {
        builder
            // Check auth status
            .addCase(checkAuthStatus.pending, (state) => {
                state.isLoading = true;
            })
            .addCase(checkAuthStatus.fulfilled, (state, action) => {
                state.isLoading = false;
                state.customer = action.payload.customer;
                state.isAuthenticated = action.payload.isAuthenticated;
            })
            .addCase(checkAuthStatus.rejected, (state, action) => {
                state.isLoading = false;
                state.error = action.payload as string;
            })
            // Login
            .addCase(login.pending, (state) => {
                state.isLoading = true;
                state.error = null;
            })
            .addCase(login.fulfilled, (state, action) => {
                state.isLoading = false;
                state.customer = action.payload;
                state.isAuthenticated = true;
            })
            .addCase(login.rejected, (state, action) => {
                state.isLoading = false;
                state.error = action.payload as string;
            })
            // Register
            .addCase(register.pending, (state) => {
                state.isLoading = true;
                state.error = null;
            })
            .addCase(register.fulfilled, (state) => {
                state.isLoading = false;
            })
            .addCase(register.rejected, (state, action) => {
                state.isLoading = false;
                state.error = action.payload as string;
            })
            // Verify OTP
            .addCase(verifyOTP.pending, (state) => {
                state.isLoading = true;
                state.error = null;
            })
            .addCase(verifyOTP.fulfilled, (state, action) => {
                state.isLoading = false;
                state.customer = action.payload;
                state.isAuthenticated = true;
            })
            .addCase(verifyOTP.rejected, (state, action) => {
                state.isLoading = false;
                state.error = action.payload as string;
            })
            // Logout
            .addCase(logout.fulfilled, (state) => {
                state.customer = null;
                state.isAuthenticated = false;
                state.error = null;
            })
            // Update profile
            .addCase(updateProfile.pending, (state) => {
                state.isLoading = true;
                state.error = null;
            })
            .addCase(updateProfile.fulfilled, (state, action) => {
                state.isLoading = false;
                state.customer = action.payload;
            })
            .addCase(updateProfile.rejected, (state, action) => {
                state.isLoading = false;
                state.error = action.payload as string;
            })
            // Upload profile picture
            .addCase(uploadProfilePicture.pending, (state) => {
                state.isLoading = true;
                state.error = null;
            })
            .addCase(uploadProfilePicture.fulfilled, (state, action) => {
                state.isLoading = false;
                state.customer = action.payload;
            })
            .addCase(uploadProfilePicture.rejected, (state, action) => {
                state.isLoading = false;
                state.error = action.payload as string;
            });
    },
});

export const { setCustomer, clearError } = authSlice.actions;
export default authSlice.reducer;
