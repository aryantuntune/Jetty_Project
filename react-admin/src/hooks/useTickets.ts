import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ticketService } from '@/services/ticketService';
import { CreateTicketRequest } from '@/types/ticket';
import { toast } from 'sonner';

interface TicketFilters {
    branch_id?: number;
    date_from?: string;
    date_to?: string;
    page?: number;
}

export function useTickets(filters?: TicketFilters) {
    const queryClient = useQueryClient();

    // Fetch tickets with pagination
    const ticketsQuery = useQuery({
        queryKey: ['tickets', filters],
        queryFn: () => ticketService.getTickets(filters),
        staleTime: 30 * 1000, // 30 seconds
    });

    // Create ticket mutation
    const createMutation = useMutation({
        mutationFn: (data: CreateTicketRequest) => ticketService.createTicket(data),
        onSuccess: (ticket) => {
            queryClient.invalidateQueries({ queryKey: ['tickets'] });
            toast.success(`Ticket #${ticket.ticket_no} created successfully!`);
            return ticket;
        },
        onError: (error: any) => {
            const message = error.response?.data?.message || 'Failed to create ticket';
            toast.error(message);
        },
    });

    return {
        tickets: ticketsQuery.data?.data || [],
        pagination: ticketsQuery.data ? {
            currentPage: ticketsQuery.data.current_page,
            lastPage: ticketsQuery.data.last_page,
            total: ticketsQuery.data.total,
        } : null,
        isLoading: ticketsQuery.isLoading,
        error: ticketsQuery.error,
        refetch: ticketsQuery.refetch,
        createTicket: createMutation.mutateAsync,
        isCreating: createMutation.isPending,
    };
}

export function useTicketDetail(id: number) {
    return useQuery({
        queryKey: ['ticket', id],
        queryFn: () => ticketService.getTicket(id),
        enabled: !!id,
    });
}

export function useItemRates(branchId?: number) {
    return useQuery({
        queryKey: ['item-rates', branchId],
        queryFn: () => ticketService.searchItemRates(branchId!),
        enabled: !!branchId,
        staleTime: 5 * 60 * 1000, // 5 minutes
    });
}

export function useNextFerryTime(branchId?: number) {
    return useQuery({
        queryKey: ['next-ferry-time', branchId],
        queryFn: () => ticketService.getNextFerryTime(branchId!),
        enabled: !!branchId,
        refetchInterval: 60 * 1000, // Refetch every minute
    });
}
