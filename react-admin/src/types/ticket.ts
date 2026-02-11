export interface Ticket {
    id: number;
    ticket_no: string;
    ticket_date: string;
    branch_id: number;
    branch_name?: string;
    dest_branch_id: number;
    dest_branch_name?: string;
    ferry_boat_id: number;
    ferry_name?: string;
    ferry_time: string;
    payment_mode: 'Cash' | 'Card' | 'UPI' | 'Online';
    total_amount: number;
    customer_id?: number;
    verified_at?: string;
    checker_id?: number;
    checker_name?: string;
    ticket_lines: TicketLine[];
    created_at: string;
    updated_at: string;
}

export interface TicketLine {
    id: number;
    ticket_id: number;
    item_name: string;
    qty: number;
    rate: number;
    levy: number;
    amount: number;
    vehicle_name?: string;
    vehicle_no?: string;
}

export interface CreateTicketRequest {
    branch_id: number;
    dest_branch_id: number;
    ferry_boat_id: number;
    ferry_time: string;
    payment_mode: string;
    items: CreateTicketItem[];
    total_amount: number;
}

export interface CreateTicketItem {
    item_rate_id: number;
    item_name: string;
    qty: number;
    rate: number;
    levy: number;
    amount: number;
    vehicle_no?: string;
}
