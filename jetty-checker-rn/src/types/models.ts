// Type definitions for the Checker App
// Checker must have roleId = 5 to access this app

export interface Branch {
    id: number;
    branch_name: string;
}

export interface FerryBoat {
    id: number;
    name: string;
    number?: string;
}

export interface Checker {
    id: number;
    name: string;
    email: string;
    mobile?: string;
    branch_id: number;
    branch?: Branch;
    ferry_boat_id?: number;
    ferryboat?: FerryBoat;
    created_at?: string;
}

export interface TicketItem {
    id: number;
    item_name: string;
    qty: number;
    rate: number;
    lavy: number;
    amount: number;
    vehicle_name?: string;
    vehicle_no?: string;
}

export interface Ticket {
    id: number;
    ticket_number?: string;
    ticket_no?: string;
    branch_id: number;
    branch?: Branch;
    from_branch?: string;
    to_branch?: string;
    ferry_boat_id?: number;
    ferryBoat?: FerryBoat;
    ferry_boat?: string;
    ferry_time?: string;
    customer_name?: string;
    payment_mode?: string;
    total_amount: number;
    net_amount?: number;
    no_of_units?: number;
    verified_at?: string;
    verified_by?: string;
    checker_id?: number;
    lines?: TicketItem[];
    created_at?: string;
    updated_at?: string;
}

export interface VerificationCount {
    count: number;
    date: string; // ISO date string YYYY-MM-DD
}
