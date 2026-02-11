// Booking Slice following the migration guide
import { createSlice, createAsyncThunk, PayloadAction } from '@reduxjs/toolkit';
import { Branch, Ferry, ItemRate, BookingItem, Booking, CreateBookingRequest } from '@/types';
import { bookingService, getErrorMessage } from '@/services';

interface BookingState {
    // Master data
    branches: Branch[];
    toBranches: Branch[];
    ferries: Ferry[];
    rates: ItemRate[];

    // Form state
    fromBranch: Branch | null;
    toBranch: Branch | null;
    selectedFerry: Ferry | null;
    selectedTime: string | null;
    selectedDate: string | null; // YYYY-MM-DD format
    items: BookingItem[];
    totalAmount: number;

    // Bookings data
    bookings: Booking[];
    currentBooking: Booking | null;
    currentPage: number;
    lastPage: number;

    // UI state
    isLoading: boolean;
    error: string | null;
}

const initialState: BookingState = {
    branches: [],
    toBranches: [],
    ferries: [],
    rates: [],
    fromBranch: null,
    toBranch: null,
    selectedFerry: null,
    selectedTime: null,
    selectedDate: null,
    items: [],
    totalAmount: 0,
    bookings: [],
    currentBooking: null,
    currentPage: 1,
    lastPage: 1,
    isLoading: false,
    error: null,
};

// Async Thunks

// Fetch all branches
export const fetchBranches = createAsyncThunk(
    'booking/fetchBranches',
    async (_, { rejectWithValue }) => {
        try {
            return await bookingService.getBranches();
        } catch (error) {
            return rejectWithValue(getErrorMessage(error));
        }
    }
);

// Fetch destination branches
export const fetchToBranches = createAsyncThunk(
    'booking/fetchToBranches',
    async (fromBranchId: number, { rejectWithValue }) => {
        try {
            return await bookingService.getToBranches(fromBranchId);
        } catch (error) {
            return rejectWithValue(getErrorMessage(error));
        }
    }
);

// Fetch ferries by branch
export const fetchFerriesByBranch = createAsyncThunk(
    'booking/fetchFerriesByBranch',
    async (branchId: number, { rejectWithValue }) => {
        try {
            return await bookingService.getFerriesByBranch(branchId);
        } catch (error) {
            return rejectWithValue(getErrorMessage(error));
        }
    }
);

// Fetch rates by branch
export const fetchRatesByBranch = createAsyncThunk(
    'booking/fetchRatesByBranch',
    async (branchId: number, { rejectWithValue }) => {
        try {
            return await bookingService.getRatesByBranch(branchId);
        } catch (error) {
            return rejectWithValue(getErrorMessage(error));
        }
    }
);

// Create booking
export const createBooking = createAsyncThunk(
    'booking/createBooking',
    async (data: CreateBookingRequest, { rejectWithValue }) => {
        try {
            return await bookingService.createBooking(data);
        } catch (error) {
            return rejectWithValue(getErrorMessage(error));
        }
    }
);

// Fetch bookings
export const fetchBookings = createAsyncThunk(
    'booking/fetchBookings',
    async (page: number = 1, { rejectWithValue }) => {
        try {
            return await bookingService.getBookings(page);
        } catch (error) {
            return rejectWithValue(getErrorMessage(error));
        }
    }
);

// Fetch booking detail
export const fetchBookingDetail = createAsyncThunk(
    'booking/fetchBookingDetail',
    async (bookingId: number, { rejectWithValue }) => {
        try {
            return await bookingService.getBookingDetail(bookingId);
        } catch (error) {
            return rejectWithValue(getErrorMessage(error));
        }
    }
);

// Cancel booking
export const cancelBooking = createAsyncThunk(
    'booking/cancelBooking',
    async (bookingId: number, { rejectWithValue }) => {
        try {
            return await bookingService.cancelBooking(bookingId);
        } catch (error) {
            return rejectWithValue(getErrorMessage(error));
        }
    }
);

// Helper function to calculate total
const calculateTotal = (items: BookingItem[]): number => {
    return items.reduce((sum, item) => sum + item.amount, 0);
};

// Slice
const bookingSlice = createSlice({
    name: 'booking',
    initialState,
    reducers: {
        setFromBranch: (state, action: PayloadAction<Branch | null>) => {
            state.fromBranch = action.payload;
            // Clear dependent selections
            state.toBranch = null;
            state.selectedFerry = null;
            state.selectedTime = null;
            state.toBranches = [];
            state.ferries = [];
            state.rates = [];
        },
        setToBranch: (state, action: PayloadAction<Branch | null>) => {
            state.toBranch = action.payload;
        },
        setSelectedFerry: (state, action: PayloadAction<Ferry | null>) => {
            state.selectedFerry = action.payload;
        },
        setSelectedTime: (state, action: PayloadAction<string | null>) => {
            state.selectedTime = action.payload;
        },
        setSelectedDate: (state, action: PayloadAction<string | null>) => {
            state.selectedDate = action.payload;
            // Reset time when date changes (user needs to reselect time)
            state.selectedTime = null;
        },
        addItem: (state, action: PayloadAction<BookingItem>) => {
            state.items.push(action.payload);
            state.totalAmount = calculateTotal(state.items);
        },
        removeItem: (state, action: PayloadAction<number>) => {
            state.items.splice(action.payload, 1);
            state.totalAmount = calculateTotal(state.items);
        },
        updateItem: (state, action: PayloadAction<{ index: number; item: BookingItem }>) => {
            state.items[action.payload.index] = action.payload.item;
            state.totalAmount = calculateTotal(state.items);
        },
        updateItemQty: (state, action: PayloadAction<{ itemRateId: number; itemName: string; qty: number; rate: number }>) => {
            const { itemRateId, itemName, qty, rate } = action.payload;
            const existingIndex = state.items.findIndex(item => item.itemRateId === itemRateId);

            if (qty <= 0) {
                // Remove item if quantity is 0 or less
                if (existingIndex !== -1) {
                    state.items.splice(existingIndex, 1);
                }
            } else if (existingIndex !== -1) {
                // Update existing item
                state.items[existingIndex] = {
                    itemRateId,
                    itemName,
                    qty,
                    rate,
                    amount: rate * qty,
                };
            } else {
                // Add new item
                state.items.push({
                    itemRateId,
                    itemName,
                    qty,
                    rate,
                    amount: rate * qty,
                });
            }
            state.totalAmount = calculateTotal(state.items);
        },
        clearBookingForm: (state) => {
            state.fromBranch = null;
            state.toBranch = null;
            state.selectedFerry = null;
            state.selectedTime = null;
            state.selectedDate = null;
            state.items = [];
            state.totalAmount = 0;
            state.toBranches = [];
            state.ferries = [];
            state.rates = [];
        },
        clearError: (state) => {
            state.error = null;
        },
        // Add a local booking (for simulated payments)
        addLocalBooking: (state, action: PayloadAction<Booking>) => {
            state.bookings.unshift(action.payload);
            state.currentBooking = action.payload;
        },
    },
    extraReducers: (builder) => {
        builder
            // Fetch branches
            .addCase(fetchBranches.pending, (state) => {
                state.isLoading = true;
            })
            .addCase(fetchBranches.fulfilled, (state, action) => {
                state.isLoading = false;
                state.branches = action.payload;
            })
            .addCase(fetchBranches.rejected, (state, action) => {
                state.isLoading = false;
                state.error = action.payload as string;
            })
            // Fetch to branches
            .addCase(fetchToBranches.fulfilled, (state, action) => {
                state.toBranches = action.payload;
            })
            // Fetch ferries
            .addCase(fetchFerriesByBranch.fulfilled, (state, action) => {
                state.ferries = action.payload;
            })
            .addCase(fetchFerriesByBranch.rejected, (state, action) => {
                state.error = action.payload as string;
            })
            // Fetch rates
            .addCase(fetchRatesByBranch.fulfilled, (state, action) => {
                state.rates = action.payload;
            })
            .addCase(fetchRatesByBranch.rejected, (state, action) => {
                state.error = action.payload as string;
            })
            // Create booking
            .addCase(createBooking.pending, (state) => {
                state.isLoading = true;
                state.error = null;
            })
            .addCase(createBooking.fulfilled, (state, action) => {
                state.isLoading = false;
                state.currentBooking = action.payload;
                // Add to bookings list at the beginning
                state.bookings.unshift(action.payload);
            })
            .addCase(createBooking.rejected, (state, action) => {
                state.isLoading = false;
                state.error = action.payload as string;
            })
            // Fetch bookings
            .addCase(fetchBookings.pending, (state) => {
                state.isLoading = true;
            })
            .addCase(fetchBookings.fulfilled, (state, action) => {
                state.isLoading = false;
                if (action.payload.current_page === 1) {
                    state.bookings = action.payload.data;
                } else {
                    state.bookings = [...state.bookings, ...action.payload.data];
                }
                state.currentPage = action.payload.current_page;
                state.lastPage = action.payload.last_page;
            })
            .addCase(fetchBookings.rejected, (state, action) => {
                state.isLoading = false;
                state.error = action.payload as string;
            })
            // Fetch booking detail
            .addCase(fetchBookingDetail.pending, (state) => {
                state.isLoading = true;
            })
            .addCase(fetchBookingDetail.fulfilled, (state, action) => {
                state.isLoading = false;
                state.currentBooking = action.payload;
            })
            .addCase(fetchBookingDetail.rejected, (state, action) => {
                state.isLoading = false;
                state.error = action.payload as string;
            })
            // Cancel booking
            .addCase(cancelBooking.pending, (state) => {
                state.isLoading = true;
            })
            .addCase(cancelBooking.fulfilled, (state, action) => {
                state.isLoading = false;
                state.currentBooking = action.payload;
                // Update in bookings list
                const index = state.bookings.findIndex(b => b.id === action.payload.id);
                if (index !== -1) {
                    state.bookings[index] = action.payload;
                }
            })
            .addCase(cancelBooking.rejected, (state, action) => {
                state.isLoading = false;
                state.error = action.payload as string;
            });
    },
});

export const {
    setFromBranch,
    setToBranch,
    setSelectedFerry,
    setSelectedTime,
    setSelectedDate,
    addItem,
    removeItem,
    updateItem,
    updateItemQty,
    clearBookingForm,
    clearError,
    addLocalBooking,
} = bookingSlice.actions;

export default bookingSlice.reducer;
