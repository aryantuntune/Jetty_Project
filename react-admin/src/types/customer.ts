export interface Customer {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    mobile: string;
    profile_image?: string;
    google_id?: string;
    created_at?: string;
}

export interface CustomerLoginRequest {
    email: string;
    password: string;
}

export interface CustomerRegisterRequest {
    first_name: string;
    last_name: string;
    email: string;
    mobile: string;
    password: string;
    password_confirmation: string;
}

export interface Booking {
    id: number;
    booking_number: string;
    customer_id: number;
    ferry_id: number;
    from_branch: number;
    to_branch: number;
    booking_date: string;
    departure_time: string;
    items: BookingItem[];
    total_amount: number;
    payment_id?: string;
    payment_mode?: string;
    qr_code?: string;
    status: 'pending' | 'confirmed' | 'cancelled' | 'completed';
    booking_source: 'web' | 'app' | 'admin';
    verified_at?: string;
    verified_by?: number;
    ticket_id?: number;
    created_at: string;
    // Relations
    from_branch_name?: string;
    to_branch_name?: string;
    ferry_name?: string;
}

export interface BookingItem {
    item_id: number;
    item_name: string;
    qty: number;
    rate: number;
    levy: number;
    amount: number;
    vehicle_name?: string;
    vehicle_no?: string;
}

export interface BookingRequest {
    from_branch: number;
    to_branch: number;
    booking_date: string;
    departure_time: string;
    items: BookingItem[];
    customer_name?: string;
    customer_email?: string;
    customer_phone?: string;
    payment_mode: string;
}
