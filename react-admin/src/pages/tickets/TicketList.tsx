import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { Button, Input, Badge, Card, CardHeader, CardTitle, CardContent } from '@/components/ui';
import { ticketService } from '@/services';
import { Search, RefreshCw, Eye, Printer, Ticket as TicketIcon } from 'lucide-react';
import { formatCurrency } from '@/lib/utils';

export function TicketList() {
    const [searchQuery, setSearchQuery] = useState('');

    // Fetch tickets from API
    const { data: ticketsData, isLoading, refetch } = useQuery({
        queryKey: ['tickets'],
        queryFn: () => ticketService.getTickets(),
        staleTime: 60 * 1000, // 1 minute
    });

    const tickets = ticketsData?.data || [];

    // Filter tickets by search
    const filteredTickets = tickets.filter((ticket: any) => {
        if (!searchQuery) return true;
        const query = searchQuery.toLowerCase();
        return (
            ticket.ticket_number?.toString().includes(query) ||
            ticket.customer_name?.toLowerCase().includes(query) ||
            ticket.branch_name?.toLowerCase().includes(query)
        );
    });

    const getPaymentBadge = (mode: string) => {
        const variants: Record<string, 'success' | 'warning' | 'default'> = {
            'Cash': 'success',
            'GPay': 'warning',
            'Card': 'default',
            'Credit': 'warning',
        };
        return <Badge variant={variants[mode] || 'default'}>{mode}</Badge>;
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Tickets</h1>
                    <p className="text-gray-500">View and manage ferry tickets</p>
                </div>
                <div className="flex gap-2">
                    <Button variant="outline" onClick={() => refetch()}>
                        <RefreshCw className="w-4 h-4" />
                        Refresh
                    </Button>
                    <Button onClick={() => window.location.href = '/tickets/entry'}>
                        <TicketIcon className="w-4 h-4" />
                        New Ticket
                    </Button>
                </div>
            </div>

            {/* Search */}
            <Card>
                <CardContent className="p-4">
                    <div className="relative">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <Input
                            placeholder="Search by ticket number, customer name, or branch..."
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="pl-10"
                        />
                    </div>
                </CardContent>
            </Card>

            {/* Tickets Table */}
            <Card>
                <CardHeader>
                    <CardTitle>Recent Tickets ({filteredTickets.length})</CardTitle>
                </CardHeader>
                <CardContent className="p-0">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50 border-b">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ticket #</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Branch</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ferry</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Payment</th>
                                    <th className="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                                    <th className="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {isLoading ? (
                                    <tr>
                                        <td colSpan={9} className="px-4 py-8 text-center text-gray-500">
                                            Loading tickets...
                                        </td>
                                    </tr>
                                ) : filteredTickets.length === 0 ? (
                                    <tr>
                                        <td colSpan={9} className="px-4 py-8 text-center text-gray-500">
                                            No tickets found
                                        </td>
                                    </tr>
                                ) : (
                                    filteredTickets.map((ticket: any) => (
                                        <tr key={ticket.id} className="hover:bg-gray-50">
                                            <td className="px-4 py-3 font-medium text-blue-600">
                                                #{ticket.ticket_number}
                                            </td>
                                            <td className="px-4 py-3 text-gray-700">{ticket.branch_name}</td>
                                            <td className="px-4 py-3 text-gray-700">{ticket.ferry_name}</td>
                                            <td className="px-4 py-3 text-gray-700">{ticket.customer_name || '-'}</td>
                                            <td className="px-4 py-3">{getPaymentBadge(ticket.payment_mode)}</td>
                                            <td className="px-4 py-3 text-right font-semibold text-green-600">
                                                {formatCurrency(ticket.total_amount)}
                                            </td>
                                            <td className="px-4 py-3 text-gray-500 text-sm">{ticket.created_at}</td>
                                            <td className="px-4 py-3 text-gray-500 text-sm">{ticket.user_name}</td>
                                            <td className="px-4 py-3">
                                                <div className="flex justify-center gap-1">
                                                    <Button variant="ghost" size="icon" title="View Details">
                                                        <Eye className="w-4 h-4" />
                                                    </Button>
                                                    <Button variant="ghost" size="icon" title="Print">
                                                        <Printer className="w-4 h-4" />
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    );
}
