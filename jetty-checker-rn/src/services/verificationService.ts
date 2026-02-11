// Verification Service for Checker App
// Handles ticket verification via QR code scanning

import api, { getErrorMessage } from './api';
import storageService from './storageService';
import { ApiResponse, VerifyTicketResponse } from '../types/api';
import { Ticket } from '../types/models';

export interface VerificationResult {
    success: boolean;
    ticket?: Ticket;
    alreadyVerified: boolean;
    message: string;
    verifiedBy?: string;
    verifiedAt?: string;
}

export const verificationService = {
    /**
     * Verify a ticket by ID or QR hash
     * @param identifier - The ticket ID (number) or qr_hash (64-char string)
     */
    verifyTicket: async (identifier: number | string): Promise<VerificationResult> => {
        try {
            // Determine if this is a qr_hash (64-char hex string) or ticket_id
            const isQrHash = typeof identifier === 'string' &&
                identifier.length === 64 &&
                /^[a-f0-9]{64}$/i.test(identifier);

            const payload = isQrHash
                ? { qr_hash: identifier }
                : { ticket_id: identifier.toString() };

            const response = await api.post<ApiResponse<VerifyTicketResponse>>('/checker/verify-ticket', payload);

            if (response.success && response.data) {
                const { ticket } = response.data;

                // Increment local verification count
                await storageService.incrementVerificationCount();
                await storageService.setLastVerifiedAt(new Date().toISOString());

                return {
                    success: true,
                    ticket,
                    alreadyVerified: false,
                    message: response.message || 'Ticket verified successfully',
                };
            }

            // Check if already verified (success: false with ticket data)
            if (!response.success && response.data?.ticket) {
                const ticketData = response.data.ticket as Ticket & { verified_at?: string; verified_by?: string };
                return {
                    success: false,
                    ticket: ticketData,
                    alreadyVerified: true,
                    message: response.message || 'Ticket already verified',
                    verifiedBy: ticketData.verified_by,
                    verifiedAt: ticketData.verified_at,
                };
            }

            return {
                success: false,
                alreadyVerified: false,
                message: response.message || 'Verification failed',
            };
        } catch (error: unknown) {
            const errorMessage = getErrorMessage(error);

            // Check if error response contains already verified info
            if (typeof error === 'object' && error !== null && 'response' in error) {
                const axiosError = error as { response?: { data?: { data?: { ticket?: Ticket & { verified_at?: string; verified_by?: string } } } } };
                if (axiosError.response?.data?.data?.ticket) {
                    const ticketData = axiosError.response.data.data.ticket;
                    return {
                        success: false,
                        ticket: ticketData,
                        alreadyVerified: true,
                        message: errorMessage,
                        verifiedBy: ticketData.verified_by,
                        verifiedAt: ticketData.verified_at,
                    };
                }
            }

            return {
                success: false,
                alreadyVerified: false,
                message: errorMessage,
            };
        }
    },

    /**
     * Parse ticket ID from QR code data
     * QR code may contain plain ticket ID or formatted data
     * Supports: qr_hash (64-char hex), plain number, /verify?code=10, /tickets/10, JSON
     */
    parseTicketId: (qrData: string): string | number | null => {
        if (!qrData || typeof qrData !== 'string') {
            return null;
        }

        // Trim whitespace
        const trimmed = qrData.trim();

        // Check if this is a qr_hash (64-character hexadecimal string)
        // New secure QR codes contain only the hash
        if (trimmed.length === 64 && /^[a-f0-9]{64}$/i.test(trimmed)) {
            return trimmed; // Return hash as string
        }

        // Try to parse as plain number (legacy ticket_id)
        const plainNumber = parseInt(trimmed, 10);
        if (!isNaN(plainNumber) && plainNumber > 0 && trimmed === plainNumber.toString()) {
            return plainNumber;
        }

        // Try to extract from query param format (e.g., "/verify?code=123" or "?code=123")
        const codeMatch = trimmed.match(/[?&]code=(\d+)/i);
        if (codeMatch && codeMatch[1]) {
            return parseInt(codeMatch[1], 10);
        }

        // Try to extract from ticket_id query param (e.g., "?ticket_id=123")
        const ticketIdMatch = trimmed.match(/[?&]ticket_id=(\d+)/i);
        if (ticketIdMatch && ticketIdMatch[1]) {
            return parseInt(ticketIdMatch[1], 10);
        }

        // Try to extract number from URL path format (e.g., "/tickets/123" or "/verify/123")
        const urlMatch = trimmed.match(/\/(?:tickets?|verify)\/(\d+)/i);
        if (urlMatch && urlMatch[1]) {
            return parseInt(urlMatch[1], 10);
        }

        // Try to extract from JSON format
        try {
            const parsed = JSON.parse(trimmed);
            if (parsed.qr_hash && typeof parsed.qr_hash === 'string') return parsed.qr_hash;
            if (parsed.id) return parseInt(parsed.id, 10);
            if (parsed.ticket_id) return parseInt(parsed.ticket_id, 10);
            if (parsed.ticketId) return parseInt(parsed.ticketId, 10);
            if (parsed.code) return parseInt(parsed.code, 10);
        } catch {
            // Not JSON, continue
        }

        // Try to find any number in the string (last resort for legacy)
        const numberMatch = trimmed.match(/\d+/);
        if (numberMatch) {
            const num = parseInt(numberMatch[0], 10);
            if (num > 0) return num;
        }

        return null;
    },

    /**
     * Get today's verification count
     */
    getTodayCount: async (): Promise<number> => {
        return storageService.getVerificationCount();
    },

    /**
     * Reset daily count (called at midnight or new day)
     */
    resetDailyCount: async (): Promise<void> => {
        await storageService.resetVerificationCount();
    },
};

export default verificationService;
