// API Types for the Checker App

import { Checker, Ticket } from './models';

// Generic API response wrapper
export interface ApiResponse<T> {
    success: boolean;
    message: string;
    data?: T;
    errors?: string[];
}

// Login response
export interface LoginResponse {
    token: string;
    user: Checker;
}

// Verify ticket response
export interface VerifyTicketResponse {
    ticket: Ticket;
    verified_at?: string;
    verified_by?: string;
}

// Profile response
export interface ProfileResponse {
    id: number;
    name: string;
    email: string;
    mobile?: string;
    branch_id: number;
    branch?: {
        branch_name: string;
    };
    ferry_boat_id?: number;
    ferryboat?: {
        name: string;
    };
    created_at: string;
}
