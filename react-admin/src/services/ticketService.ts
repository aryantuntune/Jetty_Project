import apiClient from '@/lib/axios';
import { Ticket, CreateTicketRequest } from '@/types/ticket';
import { PaginatedResponse } from '@/types/api';

interface TicketFilters {
    branch_id?: number;
    date_from?: string;
    date_to?: string;
    page?: number;
    per_page?: number;
}

// Helper to extract data from Laravel API response wrapper
function extractData<T>(response: any): T {
    if (response.data && typeof response.data === 'object' && 'data' in response.data) {
        return response.data.data;
    }
    return response.data;
}

export const ticketService = {
    /**
     * Get paginated tickets with filters
     */
    async getTickets(filters?: TicketFilters): Promise<PaginatedResponse<Ticket>> {
        const response = await apiClient.get('/api/tickets', { params: filters });
        const data = extractData<Ticket[]>(response);
        const total = response.data.total || response.data.data?.length || 0;
        const perPage = filters?.per_page || 15;
        const currentPage = filters?.page || 1;
        return {
            data,
            total,
            per_page: perPage,
            current_page: currentPage,
            last_page: Math.ceil(total / perPage),
            from: (currentPage - 1) * perPage + 1,
            to: Math.min(currentPage * perPage, total),
        };
    },

    /**
     * Get single ticket by ID
     */
    async getTicket(id: number): Promise<Ticket> {
        const response = await apiClient.get(`/api/tickets/${id}`);
        return extractData<Ticket>(response);
    },

    /**
     * Create a new ticket
     */
    async createTicket(data: CreateTicketRequest): Promise<{
        id: number;
        ticket_number: string | number;
        total_amount: number;
    }> {
        const response = await apiClient.post('/api/tickets', data);
        return extractData(response);
    },

    /**
     * Search item rates for ticket entry
     */
    async searchItemRates(branchId: number, query?: string): Promise<any[]> {
        const response = await apiClient.get('/api/rates/branch/' + branchId, {
            params: { search: query }
        });
        return extractData<any[]>(response);
    },

    /**
     * Get next ferry times
     */
    async getNextFerryTime(branchId: number): Promise<any> {
        const response = await apiClient.get('/ajax/next-ferry-time', {
            params: { branch_id: branchId }
        });
        return response.data;
    },

    /**
     * Verify a ticket
     */
    async verifyTicket(ticketNo: string): Promise<any> {
        const response = await apiClient.post('/verify', { ticket_no: ticketNo });
        return response.data;
    },
};
