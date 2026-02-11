// Verification Slice - Manages ticket verification state
import { createSlice, createAsyncThunk, PayloadAction } from '@reduxjs/toolkit';
import { verificationService, VerificationResult } from '../../services/verificationService';
import { Ticket } from '../../types/models';

interface VerificationState {
    verifiedToday: number;
    lastVerifiedTicket: Ticket | null;
    lastVerificationResult: VerificationResult | null;
    recentVerifications: VerificationResult[];
    isVerifying: boolean;
    error: string | null;
}

const initialState: VerificationState = {
    verifiedToday: 0,
    lastVerifiedTicket: null,
    lastVerificationResult: null,
    recentVerifications: [],
    isVerifying: false,
    error: null,
};

// Async thunk to load today's verification count
export const loadVerificationCount = createAsyncThunk(
    'verification/loadCount',
    async () => {
        return await verificationService.getTodayCount();
    }
);

// Async thunk to verify a ticket
export const verifyTicket = createAsyncThunk(
    'verification/verifyTicket',
    async (ticketId: number | string, { rejectWithValue }) => {
        const result = await verificationService.verifyTicket(ticketId);
        return result;
    }
);

const verificationSlice = createSlice({
    name: 'verification',
    initialState,
    reducers: {
        incrementVerifiedCount: (state) => {
            state.verifiedToday += 1;
        },
        setLastVerifiedTicket: (state, action: PayloadAction<Ticket | null>) => {
            state.lastVerifiedTicket = action.payload;
        },
        resetDailyCount: (state) => {
            state.verifiedToday = 0;
        },
        clearVerificationResult: (state) => {
            state.lastVerificationResult = null;
        },
        clearError: (state) => {
            state.error = null;
        },
    },
    extraReducers: (builder) => {
        // Load verification count
        builder
            .addCase(loadVerificationCount.fulfilled, (state, action) => {
                state.verifiedToday = action.payload;
            });

        // Verify ticket
        builder
            .addCase(verifyTicket.pending, (state) => {
                state.isVerifying = true;
                state.error = null;
                state.lastVerificationResult = null;
            })
            .addCase(verifyTicket.fulfilled, (state, action) => {
                state.isVerifying = false;
                state.lastVerificationResult = action.payload;

                // Add to recent verifications (keep last 10)
                state.recentVerifications = [
                    action.payload,
                    ...state.recentVerifications.slice(0, 9)
                ];

                if (action.payload.success) {
                    // Successful verification
                    state.verifiedToday += 1;
                    if (action.payload.ticket) {
                        state.lastVerifiedTicket = action.payload.ticket;
                    }
                } else if (action.payload.alreadyVerified && action.payload.ticket) {
                    // Already verified - still show the ticket
                    state.lastVerifiedTicket = action.payload.ticket;
                }
            })
            .addCase(verifyTicket.rejected, (state, action) => {
                state.isVerifying = false;
                state.error = action.payload as string || 'Verification failed';
            });
    },
});

export const {
    incrementVerifiedCount,
    setLastVerifiedTicket,
    resetDailyCount,
    clearVerificationResult,
    clearError,
} = verificationSlice.actions;

export default verificationSlice.reducer;
