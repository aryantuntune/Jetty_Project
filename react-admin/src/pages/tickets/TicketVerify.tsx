import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Card, CardContent, CardHeader, CardTitle, Button, Input, Badge } from '@/components/ui';
import { Search, CheckCircle, Clock, Printer, QrCode, AlertCircle } from 'lucide-react';
import { toast } from 'sonner';
import apiClient from '@/lib/axios';
import { formatCurrency } from '@/lib/utils';

interface TicketLine {
    id: number;
    item_name: string;
    qty: number;
    rate: number;
    levy: number;
    amount: number;
    vehicle_name?: string;
    vehicle_no?: string;
}

interface Ticket {
    id: number;
    ticket_no: string;
    ticket_date: string;
    ferry_time: string;
    total_amount: number;
    verified_at: string | null;
    branch_name: string;
    ferry_boat_name: string;
    user_name: string;
    lines: TicketLine[];
}

function extractData<T>(response: any): T {
    if (response.data?.data) return response.data.data;
    return response.data;
}

export function TicketVerify() {
    const queryClient = useQueryClient();
    const [searchQuery, setSearchQuery] = useState('');
    const [searchedTicket, setSearchedTicket] = useState<Ticket | null>(null);
    const [isSearching, setIsSearching] = useState(false);

    const handleSearch = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!searchQuery.trim()) return;

        setIsSearching(true);
        try {
            const res = await apiClient.get(`/api/tickets/${searchQuery}`);
            const ticket = res.data?.data || res.data;
            setSearchedTicket(ticket);
            if (!ticket) {
                toast.error('Ticket not found');
            }
        } catch (err: any) {
            toast.error('Ticket not found');
            setSearchedTicket(null);
        } finally {
            setIsSearching(false);
        }
    };

    const verifyMutation = useMutation({
        mutationFn: (ticketId: number) => apiClient.post(`/api/tickets/${ticketId}/verify`),
        onSuccess: () => {
            toast.success('Ticket verified successfully!');
            queryClient.invalidateQueries({ queryKey: ['dashboard-stats'] });
            if (searchedTicket) {
                setSearchedTicket({ ...searchedTicket, verified_at: new Date().toISOString() });
            }
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to verify'),
    });

    const handlePrint = (size: '58' | '80') => {
        if (searchedTicket) {
            window.open(`/api/tickets/${searchedTicket.id}/print?w=${size}`, '_blank');
        }
    };

    return (
        <div className="max-w-3xl mx-auto space-y-6">
            {/* Header */}
            <div>
                <h1 className="text-2xl font-bold text-gray-900">Ticket Verification</h1>
                <p className="text-gray-500 mt-1">Scan or search for tickets to verify</p>
            </div>

            {/* Search Card */}
            <Card>
                <CardHeader className="bg-gray-50 border-b">
                    <CardTitle className="flex items-center gap-2 text-base">
                        <QrCode className="w-5 h-5 text-gray-400" />
                        Search Ticket
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6">
                    <form onSubmit={handleSearch} className="space-y-4">
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Enter Ticket Number or Scan QR Code
                            </label>
                            <div className="relative">
                                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                <Input
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                    placeholder="Enter ticket number..."
                                    className="pl-10"
                                    autoFocus
                                />
                            </div>
                        </div>
                        <Button type="submit" loading={isSearching} className="w-full">
                            <Search className="w-5 h-5 mr-2" />
                            Search Ticket
                        </Button>
                    </form>
                </CardContent>
            </Card>

            {/* Ticket Details Card */}
            {searchedTicket && (
                <Card>
                    <CardHeader className="bg-gray-50 border-b">
                        <div className="flex items-center justify-between">
                            <CardTitle className="flex items-center gap-2 text-base">
                                <span>Ticket #{searchedTicket.ticket_no || searchedTicket.id}</span>
                            </CardTitle>
                            {searchedTicket.verified_at ? (
                                <span className="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">
                                    <CheckCircle className="w-4 h-4" />
                                    Verified
                                </span>
                            ) : (
                                <span className="inline-flex items-center gap-1 px-3 py-1 bg-amber-100 text-amber-700 text-sm font-medium rounded-full">
                                    <Clock className="w-4 h-4" />
                                    Pending
                                </span>
                            )}
                        </div>
                    </CardHeader>
                    <CardContent className="p-6 space-y-6">
                        {/* Ticket Info Grid */}
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div className="bg-gray-50 rounded-xl p-4">
                                <p className="text-xs text-gray-500 mb-1">Branch</p>
                                <p className="font-semibold text-gray-800">{searchedTicket.branch_name || '-'}</p>
                            </div>
                            <div className="bg-gray-50 rounded-xl p-4">
                                <p className="text-xs text-gray-500 mb-1">Date</p>
                                <p className="font-semibold text-gray-800">{searchedTicket.ticket_date || '-'}</p>
                            </div>
                            <div className="bg-gray-50 rounded-xl p-4">
                                <p className="text-xs text-gray-500 mb-1">Ferry Time</p>
                                <p className="font-semibold text-gray-800">{searchedTicket.ferry_time || '-'}</p>
                            </div>
                            <div className="bg-gray-50 rounded-xl p-4">
                                <p className="text-xs text-gray-500 mb-1">Total Amount</p>
                                <p className="font-semibold text-lg text-blue-600">{formatCurrency(searchedTicket.total_amount)}</p>
                            </div>
                        </div>

                        {/* Verification Status */}
                        {searchedTicket.verified_at && (
                            <div className="bg-green-50 border border-green-200 rounded-xl p-4">
                                <div className="flex items-center gap-3">
                                    <CheckCircle className="w-5 h-5 text-green-500" />
                                    <div>
                                        <p className="font-semibold text-green-800">Ticket Verified</p>
                                        <p className="text-sm text-green-600">
                                            {new Date(searchedTicket.verified_at).toLocaleString('en-IN')}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Items Table */}
                        {searchedTicket.lines && searchedTicket.lines.length > 0 && (
                            <div>
                                <h3 className="text-sm font-semibold text-gray-700 mb-3">Ticket Items</h3>
                                <div className="overflow-x-auto border border-gray-200 rounded-xl">
                                    <table className="w-full">
                                        <thead className="bg-gray-50 border-b">
                                            <tr>
                                                <th className="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Item</th>
                                                <th className="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                                                <th className="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Rate</th>
                                                <th className="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Levy</th>
                                                <th className="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y">
                                            {searchedTicket.lines.map((line) => (
                                                <tr key={line.id} className="hover:bg-gray-50">
                                                    <td className="px-4 py-3">
                                                        <p className="font-medium text-gray-800">{line.item_name}</p>
                                                        {(line.vehicle_name || line.vehicle_no) && (
                                                            <p className="text-xs text-gray-500">{line.vehicle_name} {line.vehicle_no}</p>
                                                        )}
                                                    </td>
                                                    <td className="px-4 py-3 text-center text-gray-600">{line.qty}</td>
                                                    <td className="px-4 py-3 text-right text-gray-600">{formatCurrency(line.rate)}</td>
                                                    <td className="px-4 py-3 text-right text-gray-600">{formatCurrency(line.levy)}</td>
                                                    <td className="px-4 py-3 text-right font-medium text-gray-800">{formatCurrency(line.amount)}</td>
                                                </tr>
                                            ))}
                                        </tbody>
                                        <tfoot className="bg-gray-50 border-t">
                                            <tr>
                                                <td colSpan={4} className="px-4 py-3 text-right font-semibold text-gray-700">Total Amount:</td>
                                                <td className="px-4 py-3 text-right font-bold text-lg text-blue-600">{formatCurrency(searchedTicket.total_amount)}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        )}

                        {/* Actions */}
                        <div className="flex flex-wrap gap-3 pt-4 border-t">
                            {!searchedTicket.verified_at && (
                                <Button
                                    onClick={() => verifyMutation.mutate(searchedTicket.id)}
                                    loading={verifyMutation.isPending}
                                    className="bg-green-600 hover:bg-green-700"
                                >
                                    <CheckCircle className="w-5 h-5 mr-2" />
                                    Mark as Verified
                                </Button>
                            )}
                            <Button variant="outline" onClick={() => handlePrint('58')}>
                                <Printer className="w-5 h-5 mr-2" />
                                Print 58mm
                            </Button>
                            <Button variant="outline" onClick={() => handlePrint('80')}>
                                <Printer className="w-5 h-5 mr-2" />
                                Print 80mm
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* Help Card */}
            <Card>
                <CardContent className="p-6">
                    <div className="flex items-start gap-4">
                        <div className="p-3 bg-blue-100 rounded-lg">
                            <AlertCircle className="w-6 h-6 text-blue-600" />
                        </div>
                        <div>
                            <h3 className="font-semibold text-gray-900">How to verify tickets?</h3>
                            <p className="text-gray-500 mt-1 text-sm">
                                Enter the ticket number from the printed receipt or scan the QR code using your device's camera.
                                Once found, click "Mark as Verified" to confirm the ticket has been checked.
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    );
}
