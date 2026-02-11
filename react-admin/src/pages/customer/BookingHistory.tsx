import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { Card, Button, Input, Badge } from '@/components/ui';
import { Ship, Calendar, Clock, Search, Download, Eye, QrCode } from 'lucide-react';
import { formatCurrency } from '@/lib/utils';
import apiClient from '@/lib/axios';

export function BookingHistory() {
    const [searchQuery, setSearchQuery] = useState('');
    const [statusFilter, setStatusFilter] = useState('');

    const { data: bookings = [], isLoading } = useQuery({
        queryKey: ['customer-bookings'],
        queryFn: async () => {
            try {
                const res = await apiClient.get('/api/customer/bookings');
                return res.data.data || [];
            } catch {
                return [];
            }
        },
    });

    const filteredBookings = bookings.filter((booking: any) => {
        const matchesSearch = !searchQuery ||
            booking.booking_number?.toLowerCase().includes(searchQuery.toLowerCase()) ||
            booking.from_branch_name?.toLowerCase().includes(searchQuery.toLowerCase()) ||
            booking.to_branch_name?.toLowerCase().includes(searchQuery.toLowerCase());

        const matchesStatus = !statusFilter || booking.status === statusFilter;

        return matchesSearch && matchesStatus;
    });

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'confirmed': return 'bg-green-100 text-green-700';
            case 'pending': return 'bg-yellow-100 text-yellow-700';
            case 'cancelled': return 'bg-red-100 text-red-700';
            case 'completed': return 'bg-blue-100 text-blue-700';
            default: return 'bg-gray-100 text-gray-700';
        }
    };

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold">Booking History</h1>
                <p className="text-gray-600">View and manage your ferry bookings</p>
            </div>

            {/* Filters */}
            <Card className="p-4">
                <div className="flex flex-col md:flex-row gap-4">
                    <div className="flex-1 relative">
                        <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                        <Input
                            placeholder="Search by booking ID or route..."
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="pl-10"
                        />
                    </div>
                    <select
                        value={statusFilter}
                        onChange={(e) => setStatusFilter(e.target.value)}
                        className="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </Card>

            {/* Bookings List */}
            {isLoading ? (
                <div className="text-center py-12 text-gray-500">Loading bookings...</div>
            ) : filteredBookings.length === 0 ? (
                <Card className="p-12 text-center">
                    <Ship className="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h2 className="text-xl font-semibold text-gray-600 mb-2">No bookings found</h2>
                    <p className="text-gray-500">
                        {searchQuery || statusFilter
                            ? 'Try adjusting your filters'
                            : "You haven't made any bookings yet"}
                    </p>
                </Card>
            ) : (
                <div className="space-y-4">
                    {filteredBookings.map((booking: any) => (
                        <Card key={booking.id} className="overflow-hidden hover:shadow-lg transition-shadow">
                            <div className="flex flex-col md:flex-row">
                                {/* Left side - Route info */}
                                <div className="flex-1 p-6">
                                    <div className="flex items-start justify-between mb-4">
                                        <div>
                                            <div className="text-sm text-gray-500 mb-1">
                                                Booking #{booking.booking_number || booking.id}
                                            </div>
                                            <h3 className="text-xl font-bold">
                                                {booking.from_branch_name || 'N/A'} → {booking.to_branch_name || 'N/A'}
                                            </h3>
                                        </div>
                                        <Badge className={getStatusColor(booking.status)}>
                                            {booking.status}
                                        </Badge>
                                    </div>

                                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div className="flex items-center gap-2 text-gray-600">
                                            <Calendar className="w-4 h-4" />
                                            <span>{booking.booking_date}</span>
                                        </div>
                                        <div className="flex items-center gap-2 text-gray-600">
                                            <Clock className="w-4 h-4" />
                                            <span>{booking.departure_time}</span>
                                        </div>
                                        <div className="flex items-center gap-2 text-gray-600">
                                            <Ship className="w-4 h-4" />
                                            <span>{booking.ferry_name || 'Ferry'}</span>
                                        </div>
                                        <div className="font-bold text-green-600 text-lg">
                                            {formatCurrency(booking.total_amount)}
                                        </div>
                                    </div>
                                </div>

                                {/* Right side - Actions */}
                                <div className="flex md:flex-col items-center justify-center gap-2 p-4 bg-gray-50 border-t md:border-t-0 md:border-l">
                                    {booking.status === 'confirmed' && (
                                        <>
                                            <Button variant="outline" size="sm">
                                                <QrCode className="w-4 h-4 mr-1" />
                                                QR Code
                                            </Button>
                                            <Button variant="outline" size="sm">
                                                <Download className="w-4 h-4 mr-1" />
                                                Download
                                            </Button>
                                        </>
                                    )}
                                    <Button variant="outline" size="sm">
                                        <Eye className="w-4 h-4 mr-1" />
                                        Details
                                    </Button>
                                </div>
                            </div>
                        </Card>
                    ))}
                </div>
            )}
        </div>
    );
}
