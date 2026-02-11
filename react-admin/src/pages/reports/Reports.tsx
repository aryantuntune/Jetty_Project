import { useState, useMemo } from 'react';
import { useQuery } from '@tanstack/react-query';
import { Card, CardHeader, CardTitle, CardContent, Button, Select, Badge, Input } from '@/components/ui';
import { BarChart3, TrendingUp, Calendar, Download, RefreshCw, Filter, Printer, Search } from 'lucide-react';
import { formatCurrency } from '@/lib/utils';
import apiClient from '@/lib/axios';
import { toast } from 'sonner';

interface Branch {
    id: number;
    branch_name: string;
}

interface Ferry {
    id: number;
    name: string;
    number: string;
}

function extractData<T>(response: any): T {
    if (response.data?.data) return response.data.data;
    return response.data;
}

export function Reports() {
    // Filters state
    const [filterBranch, setFilterBranch] = useState('');
    const [filterPaymentMode, setFilterPaymentMode] = useState('');
    const [filterFerryType, setFilterFerryType] = useState('');
    const [filterFerry, setFilterFerry] = useState('');
    const [dateFrom, setDateFrom] = useState(() => new Date().toISOString().split('T')[0]);
    const [dateTo, setDateTo] = useState(() => new Date().toISOString().split('T')[0]);
    const [searchQuery, setSearchQuery] = useState('');

    // Fetch branches for filter
    const { data: branches = [] } = useQuery({
        queryKey: ['branches-filter'],
        queryFn: async () => {
            const res = await apiClient.get('/api/admin/branches');
            return extractData<Branch[]>(res);
        },
    });

    // Fetch ferries for filter
    const { data: ferries = [] } = useQuery({
        queryKey: ['ferries-filter', filterBranch],
        queryFn: async () => {
            const params = filterBranch ? `?branch_id=${filterBranch}` : '';
            const res = await apiClient.get(`/api/admin/ferries${params}`);
            return extractData<Ferry[]>(res);
        },
    });

    // Build query params
    const queryParams = useMemo(() => ({
        date_from: dateFrom,
        date_to: dateTo,
        branch_id: filterBranch || undefined,
        payment_mode: filterPaymentMode || undefined,
        ferry_type: filterFerryType || undefined,
        ferry_boat_id: filterFerry || undefined,
    }), [dateFrom, dateTo, filterBranch, filterPaymentMode, filterFerryType, filterFerry]);

    // Fetch tickets for reports
    const { data: tickets = [], isLoading, refetch } = useQuery({
        queryKey: ['tickets-report', queryParams],
        queryFn: async () => {
            const res = await apiClient.get('/api/tickets', { params: queryParams });
            return extractData<any[]>(res);
        },
    });

    // Filter by search
    const filteredTickets = useMemo(() => {
        if (!searchQuery.trim()) return tickets;
        return tickets.filter((t: any) =>
            (t.ticket_no || String(t.id)).toLowerCase().includes(searchQuery.toLowerCase()) ||
            (t.customer_name || '').toLowerCase().includes(searchQuery.toLowerCase())
        );
    }, [tickets, searchQuery]);

    // Calculate stats
    const totalTickets = filteredTickets.length;
    const totalRevenue = filteredTickets.reduce((sum: number, t: any) => sum + parseFloat(t.total_amount || 0), 0);
    const avgTicketValue = totalTickets > 0 ? totalRevenue / totalTickets : 0;

    // Count by payment mode
    const paymentModeCounts = filteredTickets.reduce((acc: any, t: any) => {
        const mode = t.payment_mode || 'Cash';
        acc[mode] = (acc[mode] || 0) + 1;
        return acc;
    }, {});

    // Count by branch
    const branchCounts = filteredTickets.reduce((acc: any, t: any) => {
        const branch = t.branch_name || 'Unknown';
        acc[branch] = (acc[branch] || 0) + 1;
        return acc;
    }, {});

    // Reset filters
    const resetFilters = () => {
        setFilterBranch('');
        setFilterPaymentMode('');
        setFilterFerryType('');
        setFilterFerry('');
        setDateFrom(new Date().toISOString().split('T')[0]);
        setDateTo(new Date().toISOString().split('T')[0]);
        setSearchQuery('');
    };

    // Export to CSV
    const exportCSV = () => {
        if (filteredTickets.length === 0) {
            toast.error('No data to export');
            return;
        }

        const headers = ['Date', 'Ticket No', 'Branch', 'Ferry', 'Ferry Type', 'Customer', 'Payment Mode', 'Amount'];
        const rows = filteredTickets.map((t: any) => [
            t.ticket_date || '',
            t.ticket_no || t.id,
            t.branch_name || '',
            t.ferry_boat_name || '',
            t.ferry_type || '',
            t.customer_name || '',
            t.payment_mode || 'Cash',
            t.total_amount || 0
        ]);

        const csvContent = [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `ticket_report_${dateFrom}_to_${dateTo}.csv`;
        a.click();
        URL.revokeObjectURL(url);
        toast.success('CSV exported successfully!');
    };

    // Print ticket
    const handlePrint = (ticketId: number, size: '58' | '80') => {
        window.open(`/tickets/print?id=${ticketId}&w=${size}`, '_blank');
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Ticket Details Report</h1>
                    <p className="text-gray-500">View and analyze ticket sales data</p>
                </div>
                <Button onClick={exportCSV} className="bg-green-600 hover:bg-green-700">
                    <Download className="w-5 h-5 mr-2" />
                    Export CSV
                </Button>
            </div>

            {/* Filters Card */}
            <Card>
                <CardHeader className="bg-gray-50 border-b py-3">
                    <CardTitle className="flex items-center gap-2 text-base font-semibold">
                        <Filter className="w-5 h-5 text-gray-400" />
                        Filters
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4">
                        {/* Branch */}
                        <div>
                            <label className="block text-xs font-medium text-gray-500 mb-1">Branch</label>
                            <Select value={filterBranch} onChange={(e) => setFilterBranch(e.target.value)}>
                                <option value="">All Branches</option>
                                {branches.map((b: Branch) => (
                                    <option key={b.id} value={b.id}>{b.branch_name}</option>
                                ))}
                            </Select>
                        </div>

                        {/* Payment Mode */}
                        <div>
                            <label className="block text-xs font-medium text-gray-500 mb-1">Payment Mode</label>
                            <Select value={filterPaymentMode} onChange={(e) => setFilterPaymentMode(e.target.value)}>
                                <option value="">All Modes</option>
                                <option value="Cash">Cash</option>
                                <option value="GPay">GPay</option>
                                <option value="UPI">UPI</option>
                                <option value="Card">Card</option>
                            </Select>
                        </div>

                        {/* Ferry Type */}
                        <div>
                            <label className="block text-xs font-medium text-gray-500 mb-1">Ferry Type</label>
                            <Select value={filterFerryType} onChange={(e) => setFilterFerryType(e.target.value)}>
                                <option value="">All Types</option>
                                <option value="Regular">Regular</option>
                                <option value="Express">Express</option>
                            </Select>
                        </div>

                        {/* Ferry Boat */}
                        <div>
                            <label className="block text-xs font-medium text-gray-500 mb-1">Ferry Boat</label>
                            <Select value={filterFerry} onChange={(e) => setFilterFerry(e.target.value)}>
                                <option value="">All Ferries</option>
                                {ferries.map((f: Ferry) => (
                                    <option key={f.id} value={f.id}>{f.name || f.number}</option>
                                ))}
                            </Select>
                        </div>

                        {/* Date From */}
                        <div>
                            <label className="block text-xs font-medium text-gray-500 mb-1">From Date</label>
                            <Input
                                type="date"
                                value={dateFrom}
                                onChange={(e) => setDateFrom(e.target.value)}
                            />
                        </div>

                        {/* Date To */}
                        <div>
                            <label className="block text-xs font-medium text-gray-500 mb-1">To Date</label>
                            <Input
                                type="date"
                                value={dateTo}
                                onChange={(e) => setDateTo(e.target.value)}
                            />
                        </div>

                        {/* Actions */}
                        <div className="flex items-end gap-2">
                            <Button onClick={() => refetch()} variant="outline" className="flex-1">
                                <Search className="w-4 h-4 mr-1" />
                                Filter
                            </Button>
                            <Button onClick={resetFilters} variant="outline">
                                Reset
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Stats Cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <Card className="bg-gradient-to-br from-blue-500 to-blue-600 text-white">
                    <CardContent className="p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-blue-100 text-sm font-medium">Total Tickets</p>
                                <p className="text-3xl font-bold mt-1">{totalTickets.toLocaleString()}</p>
                            </div>
                            <div className="p-3 bg-white/20 rounded-lg">
                                <BarChart3 className="w-6 h-6" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card className="bg-gradient-to-br from-green-500 to-green-600 text-white">
                    <CardContent className="p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-green-100 text-sm font-medium">Total Revenue</p>
                                <p className="text-3xl font-bold mt-1">{formatCurrency(totalRevenue)}</p>
                            </div>
                            <div className="p-3 bg-white/20 rounded-lg">
                                <TrendingUp className="w-6 h-6" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card className="bg-gradient-to-br from-purple-500 to-purple-600 text-white">
                    <CardContent className="p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-purple-100 text-sm font-medium">Avg Ticket Value</p>
                                <p className="text-3xl font-bold mt-1">{formatCurrency(avgTicketValue)}</p>
                            </div>
                            <div className="p-3 bg-white/20 rounded-lg">
                                <Calendar className="w-6 h-6" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card className="bg-gradient-to-br from-orange-500 to-orange-600 text-white">
                    <CardContent className="p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-orange-100 text-sm font-medium">Cash Payments</p>
                                <p className="text-3xl font-bold mt-1">{(paymentModeCounts['Cash'] || 0).toLocaleString()}</p>
                            </div>
                            <div className="p-3 bg-white/20 rounded-lg">
                                <BarChart3 className="w-6 h-6" />
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            {/* Charts Row */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Payment Mode Distribution */}
                <Card>
                    <CardHeader>
                        <CardTitle>Payment Mode Distribution</CardTitle>
                    </CardHeader>
                    <CardContent>
                        {isLoading ? (
                            <div className="h-48 flex items-center justify-center text-gray-500">Loading...</div>
                        ) : Object.keys(paymentModeCounts).length === 0 ? (
                            <div className="h-48 flex items-center justify-center text-gray-400">No data</div>
                        ) : (
                            <div className="space-y-4">
                                {Object.entries(paymentModeCounts).map(([mode, count]: [string, any]) => (
                                    <div key={mode} className="flex items-center gap-4">
                                        <Badge variant={mode === 'Cash' ? 'success' : mode === 'GPay' ? 'warning' : 'default'}>
                                            {mode}
                                        </Badge>
                                        <div className="flex-1 bg-gray-100 rounded-full h-4 overflow-hidden">
                                            <div
                                                className={`h-full ${mode === 'Cash' ? 'bg-green-500' : mode === 'GPay' ? 'bg-yellow-500' : 'bg-blue-500'}`}
                                                style={{ width: `${(count / totalTickets) * 100}%` }}
                                            />
                                        </div>
                                        <span className="font-semibold w-16 text-right">{count}</span>
                                    </div>
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Branch Distribution */}
                <Card>
                    <CardHeader>
                        <CardTitle>Tickets by Branch</CardTitle>
                    </CardHeader>
                    <CardContent>
                        {isLoading ? (
                            <div className="h-48 flex items-center justify-center text-gray-500">Loading...</div>
                        ) : Object.keys(branchCounts).length === 0 ? (
                            <div className="h-48 flex items-center justify-center text-gray-400">No data</div>
                        ) : (
                            <div className="space-y-4">
                                {Object.entries(branchCounts)
                                    .sort((a: any, b: any) => b[1] - a[1])
                                    .slice(0, 6)
                                    .map(([branch, count]: [string, any]) => (
                                        <div key={branch} className="flex items-center gap-4">
                                            <span className="w-32 truncate text-gray-700 text-sm">{branch}</span>
                                            <div className="flex-1 bg-gray-100 rounded-full h-4 overflow-hidden">
                                                <div
                                                    className="h-full bg-blue-500"
                                                    style={{ width: `${(count / Math.max(...Object.values(branchCounts) as number[])) * 100}%` }}
                                                />
                                            </div>
                                            <span className="font-semibold w-12 text-right">{count}</span>
                                        </div>
                                    ))}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>

            {/* Tickets Table */}
            <Card>
                <CardHeader className="flex flex-row items-center justify-between">
                    <CardTitle>Ticket Details ({filteredTickets.length})</CardTitle>
                    <div className="relative w-64">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <Input
                            placeholder="Search ticket #..."
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="pl-10"
                        />
                    </div>
                </CardHeader>
                <CardContent className="p-0">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50 border-b">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ticket #</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Branch</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ferry</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                                    <th className="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                                    <th className="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Print</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {isLoading ? (
                                    <tr><td colSpan={8} className="px-4 py-8 text-center text-gray-500">Loading...</td></tr>
                                ) : filteredTickets.length === 0 ? (
                                    <tr><td colSpan={8} className="px-4 py-8 text-center text-gray-500">No tickets found</td></tr>
                                ) : (
                                    filteredTickets.slice(0, 50).map((ticket: any) => (
                                        <tr key={ticket.id} className="hover:bg-gray-50">
                                            <td className="px-4 py-3 text-gray-600 text-sm">{ticket.date || ticket.created_at?.split(' ')[0] || '-'}</td>
                                            <td className="px-4 py-3">
                                                <span className="font-medium text-blue-600">#{ticket.ticket_no || ticket.id}</span>
                                            </td>
                                            <td className="px-4 py-3">
                                                <Badge variant="default">{ticket.branch_name || '-'}</Badge>
                                            </td>
                                            <td className="px-4 py-3 text-gray-600 text-sm">{ticket.ferry_name || '-'}</td>
                                            <td className="px-4 py-3 text-gray-600 text-sm">{ticket.ferry_type || 'Regular'}</td>
                                            <td className="px-4 py-3 text-gray-600 text-sm">{ticket.customer_name || 'Walk-in'}</td>
                                            <td className="px-4 py-3 text-right font-semibold text-green-600">
                                                {formatCurrency(parseFloat(ticket.total_amount || 0))}
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex justify-center gap-1">
                                                    <button
                                                        onClick={() => handlePrint(ticket.id, '58')}
                                                        className="p-1 text-gray-500 hover:text-blue-600"
                                                        title="Print 58mm"
                                                    >
                                                        <Printer className="w-4 h-4" />
                                                    </button>
                                                    <button
                                                        onClick={() => handlePrint(ticket.id, '80')}
                                                        className="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded hover:bg-blue-100"
                                                        title="Print 80mm"
                                                    >
                                                        80mm
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                            {filteredTickets.length > 0 && (
                                <tfoot className="bg-gray-50 border-t-2">
                                    <tr>
                                        <td colSpan={6} className="px-4 py-3 text-right font-semibold text-gray-700">Page Total:</td>
                                        <td className="px-4 py-3 text-right font-bold text-lg text-blue-600">
                                            {formatCurrency(filteredTickets.slice(0, 50).reduce((s: number, t: any) => s + parseFloat(t.total_amount || 0), 0))}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            )}
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    );
}
