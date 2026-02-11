// Booking Service - Fixed to match actual API response format
import { api } from './api';
import Constants from 'expo-constants';
import {
    Branch,
    Ferry,
    ItemRate,
    Booking,
    CreateBookingRequest,
    RazorpayOrderResponse,
    RazorpayVerifyRequest,
    PaginatedResponse,
    ApiResponse,
} from '@/types';

// The API returns: { success: true, data: [...] }
const extractDataArray = <T>(response: unknown): T[] => {
    if (!response) return [];

    if (typeof response === 'object' && response !== null) {
        const obj = response as Record<string, unknown>;

        // API format: { success: true, data: [...] }
        if (Array.isArray(obj.data)) {
            return obj.data as T[];
        }

        // If response itself is an array
        if (Array.isArray(response)) {
            return response as T[];
        }
    }

    return [];
};

const extractDataObject = <T>(response: unknown): T | null => {
    if (!response) return null;

    if (typeof response === 'object' && response !== null) {
        const obj = response as Record<string, unknown>;

        if (obj.data && typeof obj.data === 'object' && !Array.isArray(obj.data)) {
            return obj.data as T;
        }

        if (obj.booking) {
            return obj.booking as T;
        }
    }

    return null;
};

// Map API branch response to our Branch type
interface ApiBranch {
    id: number;
    name: string;
    dest_branch_id?: number;
    dest_branch_name?: string;
}

const mapBranch = (apiBranch: ApiBranch): Branch => ({
    id: apiBranch.id,
    branchName: apiBranch.name,
    destBranchId: apiBranch.dest_branch_id,
    destBranchName: apiBranch.dest_branch_name,
});

// Map API ferry response
interface ApiFerry {
    id: number;
    name: string;
    number: string;
    capacity?: number;
}

const mapFerry = (apiFerry: ApiFerry): Ferry => ({
    id: apiFerry.id,
    name: apiFerry.name,
    number: apiFerry.number,
    capacity: apiFerry.capacity,
});

// Map API rate response - API returns {id, item_name, price, description}
interface ApiRate {
    id: number;
    item_name: string;
    price: string | number;
    description?: string;
}

const mapRate = (apiRate: ApiRate): ItemRate => ({
    id: apiRate.id,
    itemName: apiRate.item_name,
    price: typeof apiRate.price === 'string' ? parseFloat(apiRate.price) : apiRate.price,
    description: apiRate.description,
});

// API returns booking in snake_case format
interface ApiBooking {
    id: number;
    customer_id: number;
    from_branch_id?: number;
    from_branch?: string | number;
    to_branch_id?: number;
    to_branch?: string | number;
    ferry_id?: number;
    ferry_boat?: string;
    departure_time?: string;
    ferry_time?: string;
    items?: unknown;
    total_amount: number | string;
    payment_id?: string;
    status: string;
    ticket_id?: number;
    qr_code?: string;
    verified_at?: string;
    created_at?: string;
    booking_date?: string;
}

// Map API booking to our Booking interface
const mapBooking = (api: ApiBooking): Booking => ({
    id: api.id,
    customerId: api.customer_id,
    fromBranch: String(api.from_branch ?? api.from_branch_id ?? ''),
    toBranch: api.to_branch ? String(api.to_branch) : (api.to_branch_id ? String(api.to_branch_id) : undefined),
    ferryBoat: api.ferry_boat ?? String(api.ferry_id ?? ''),
    ferryTime: api.departure_time ?? api.ferry_time ?? '',
    items: Array.isArray(api.items) ? api.items as Booking['items'] : [],
    totalAmount: typeof api.total_amount === 'string' ? parseFloat(api.total_amount) : api.total_amount,
    paymentId: api.payment_id,
    status: api.status as Booking['status'],
    ticketId: api.ticket_id,
    qrCode: api.qr_code,
    verifiedAt: api.verified_at,
    createdAt: api.created_at || new Date().toISOString(),
});

export const bookingService = {
    // Get all branches
    getBranches: async (): Promise<Branch[]> => {
        try {
            console.log('[BookingService] Fetching branches');
            const response = await api.get<unknown>('/customer/branch');
            const apiBranches = extractDataArray<ApiBranch>(response);
            console.log('[BookingService] Got branches:', apiBranches.length);
            return apiBranches.map(mapBranch);
        } catch (error) {
            console.error('[BookingService] Error fetching branches:', error);
            return [];
        }
    },

    // Get destination branches for a given source branch
    getToBranches: async (fromBranchId: number): Promise<Branch[]> => {
        try {
            console.log('[BookingService] Fetching to-branches for', fromBranchId);
            const response = await api.get<unknown>(`/branches/${fromBranchId}/to-branches`);
            const apiBranches = extractDataArray<ApiBranch>(response);
            console.log('[BookingService] Got to-branches:', apiBranches.length);
            return apiBranches.map(mapBranch);
        } catch (error) {
            console.error('[BookingService] Error fetching to-branches:', error);
            return [];
        }
    },

    // Get ferries by branch
    getFerriesByBranch: async (branchId: number): Promise<Ferry[]> => {
        try {
            console.log('[BookingService] Fetching ferries for branch', branchId);
            const response = await api.get<unknown>(`/customer/ferries/branch/${branchId}`);
            const apiFerries = extractDataArray<ApiFerry>(response);
            console.log('[BookingService] Got ferries:', apiFerries.length);
            return apiFerries.map(mapFerry);
        } catch (error) {
            console.error('[BookingService] Error fetching ferries:', error);
            return [];
        }
    },

    // Get rates by branch
    getRatesByBranch: async (branchId: number): Promise<ItemRate[]> => {
        try {
            console.log('[BookingService] Fetching rates for branch', branchId);
            const response = await api.get<unknown>(`/customer/rates/branch/${branchId}`);
            const apiRates = extractDataArray<ApiRate>(response);
            console.log('[BookingService] Got rates:', apiRates.length);
            return apiRates.map(mapRate);
        } catch (error) {
            console.error('[BookingService] Error fetching rates:', error);
            return [];
        }
    },

    // Create Razorpay order
    createRazorpayOrder: async (amount: number): Promise<RazorpayOrderResponse> => {
        return api.post<RazorpayOrderResponse>('/razorpay/order', { amount });
    },

    // Verify Razorpay payment
    verifyRazorpayPayment: async (data: RazorpayVerifyRequest): Promise<ApiResponse<{ verified: boolean }>> => {
        return api.post<ApiResponse<{ verified: boolean }>>('/razorpay/verify', {
            razorpay_order_id: data.razorpayOrderId,
            razorpay_payment_id: data.razorpayPaymentId,
            razorpay_signature: data.razorpaySignature,
        });
    },

    // Create booking
    createBooking: async (data: CreateBookingRequest): Promise<Booking> => {
        console.log('[BookingService] Creating booking with data:', JSON.stringify(data));

        // Format items for backend - backend expects item_rate_id and quantity
        const formattedItems = data.items.map(item => ({
            item_rate_id: item.itemRateId,  // Backend looks up rate from DB
            quantity: item.qty,              // Backend expects "quantity" not "qty"
        }));

        const requestBody = {
            ferry_id: data.ferryBoatId,
            from_branch_id: data.fromBranch,
            to_branch_id: data.toBranch || data.fromBranch, // Use same branch if not specified
            booking_date: data.bookingDate, // Use selected date from request
            departure_time: data.ferryTime,
            items: formattedItems,
        };

        console.log('[BookingService] Request body:', JSON.stringify(requestBody));

        const response = await api.post<unknown>('/bookings', requestBody);

        console.log('[BookingService] Create booking response:', JSON.stringify(response));

        const booking = extractDataObject<Booking>(response);
        if (!booking) {
            // If no booking object returned, create a mock one for display
            console.log('[BookingService] No booking in response, creating local object');
            return {
                id: Date.now(),
                customerId: 1,
                fromBranch: String(data.fromBranch),
                toBranch: data.toBranch ? String(data.toBranch) : undefined,
                ferryBoat: String(data.ferryBoatId),
                ferryTime: data.ferryTime,
                items: data.items,
                totalAmount: data.totalAmount,
                paymentId: data.paymentId,
                status: 'confirmed',
                createdAt: new Date().toISOString(),
            };
        }
        return booking;
    },

    // Get bookings with pagination
    getBookings: async (page: number = 1): Promise<PaginatedResponse<Booking>> => {
        try {
            const response = await api.get<unknown>(`/bookings?page=${page}`);

            if (response && typeof response === 'object') {
                const obj = response as Record<string, unknown>;

                let pageData = obj;
                if (obj.data && typeof obj.data === 'object' && !Array.isArray(obj.data)) {
                    pageData = obj.data as Record<string, unknown>;
                }

                return {
                    data: Array.isArray(pageData.data)
                        ? (pageData.data as ApiBooking[]).map(mapBooking)
                        : [],
                    current_page: (pageData.current_page as number) || 1,
                    last_page: (pageData.last_page as number) || 1,
                    per_page: (pageData.per_page as number) || 10,
                    total: (pageData.total as number) || 0,
                };
            }

            return { data: [], current_page: 1, last_page: 1, per_page: 10, total: 0 };
        } catch (error) {
            console.error('[BookingService] Error fetching bookings:', error);
            return { data: [], current_page: 1, last_page: 1, per_page: 10, total: 0 };
        }
    },

    // Get booking detail
    getBookingDetail: async (bookingId: number): Promise<Booking> => {
        const response = await api.get<unknown>(`/bookings/${bookingId}`);
        const apiBooking = extractDataObject<ApiBooking>(response);
        if (!apiBooking) {
            throw new Error('Booking not found');
        }
        return mapBooking(apiBooking);
    },

    // Cancel booking
    cancelBooking: async (bookingId: number): Promise<Booking> => {
        const response = await api.post<unknown>(`/bookings/${bookingId}/cancel`);
        const apiBooking = extractDataObject<ApiBooking>(response);
        if (!apiBooking) {
            throw new Error('Failed to cancel booking');
        }
        return mapBooking(apiBooking);
    },

    // Download ticket PDF
    // Returns the PDF URL or downloads the PDF content
    downloadTicketPDF: async (bookingId: number): Promise<string> => {
        console.log('[BookingService] Downloading ticket PDF for booking:', bookingId);

        // Get base URL from API config (removes /api suffix for web routes)
        const baseUrl = (Constants.expoConfig?.extra?.apiBaseUrl || 'https://unfurling.ninja/api').replace('/api', '');
        const pdfUrl = `${baseUrl}/booking/${bookingId}/ticket`;

        console.log('[BookingService] PDF URL:', pdfUrl);
        return pdfUrl;
    },
};

export default bookingService;
