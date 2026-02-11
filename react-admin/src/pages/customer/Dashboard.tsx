import { Link, useNavigate } from 'react-router-dom';
import { useQuery } from '@tanstack/react-query';
import { Card, CardHeader, CardTitle, CardContent, Button } from '@/components/ui';
import { useCustomerAuthStore } from '@/store/customerAuthStore';
import { Ship, Ticket, History, User, ArrowRight, Calendar, MapPin, Clock } from 'lucide-react';
import apiClient from '@/lib/axios';
import { formatCurrency } from '@/lib/utils';

export function CustomerDashboard() {
    const { customer } = useCustomerAuthStore();

    // Fetch recent bookings
    const { data: recentBookings = [] } = useQuery({
        queryKey: ['customer-bookings-recent'],
        queryFn: async () => {
            try {
                const response = await apiClient.get('/api/customer/bookings', {
                    params: { limit: 5 }
                });
                return response.data.data || [];
            } catch {
                return [];
            }
        },
    });

    const quickActions = [
        {
            icon: Ticket,
            title: 'Book New Ticket',
            description: 'Book a ferry ticket for your journey',
            path: '/customer/book',
            color: 'bg-blue-600 hover:bg-blue-700',
        },
        {
            icon: History,
            title: 'Booking History',
            description: 'View your past bookings',
            path: '/customer/history',
            color: 'bg-green-600 hover:bg-green-700',
        },
        {
            icon: User,
            title: 'My Profile',
            description: 'Update your profile details',
            path: '/customer/profile',
            color: 'bg-purple-600 hover:bg-purple-700',
        },
    ];

    return (
        <div className="space-y-6">
            {/* Welcome Banner */}
            <div className="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-6 md:p-8 text-white">
                <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 className="text-2xl md:text-3xl font-bold mb-2">
                            Welcome, {customer?.first_name || 'Guest'}!
                        </h1>
                        <p className="text-blue-100">
                            Ready to book your next ferry journey?
                        </p>
                    </div>
                    <Link to="/customer/book">
                        <Button size="lg" className="bg-white text-blue-700 hover:bg-gray-100">
                            <Ticket className="w-5 h-5 mr-2" />
                            Book Ticket
                        </Button>
                    </Link>
                </div>
            </div>

            {/* Quick Actions */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {quickActions.map((action) => {
                    const Icon = action.icon;
                    return (
                        <Link key={action.path} to={action.path}>
                            <Card className="p-6 hover:shadow-lg transition-shadow h-full">
                                <div className={`w-12 h-12 rounded-lg ${action.color} flex items-center justify-center mb-4`}>
                                    <Icon className="w-6 h-6 text-white" />
                                </div>
                                <h3 className="font-bold text-lg mb-1">{action.title}</h3>
                                <p className="text-gray-600 text-sm">{action.description}</p>
                            </Card>
                        </Link>
                    );
                })}
            </div>

            {/* Recent Bookings */}
            <Card>
                <CardHeader className="flex flex-row items-center justify-between">
                    <CardTitle className="flex items-center gap-2">
                        <History className="w-5 h-5" />
                        Recent Bookings
                    </CardTitle>
                    <Link to="/customer/history">
                        <Button variant="outline" size="sm">
                            View All
                            <ArrowRight className="w-4 h-4 ml-2" />
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    {recentBookings.length === 0 ? (
                        <div className="text-center py-12">
                            <Ship className="w-16 h-16 text-gray-300 mx-auto mb-4" />
                            <h3 className="text-lg font-semibold text-gray-600 mb-2">No bookings yet</h3>
                            <p className="text-gray-500 mb-4">Book your first ferry ticket now!</p>
                            <Link to="/customer/book">
                                <Button>Book Now</Button>
                            </Link>
                        </div>
                    ) : (
                        <div className="space-y-4">
                            {recentBookings.map((booking: any) => (
                                <div
                                    key={booking.id}
                                    className="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    <div className="flex items-center gap-4">
                                        <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <Ship className="w-6 h-6 text-blue-600" />
                                        </div>
                                        <div>
                                            <div className="font-semibold">
                                                {booking.from_branch_name} → {booking.to_branch_name}
                                            </div>
                                            <div className="text-sm text-gray-600 flex items-center gap-3">
                                                <span className="flex items-center gap-1">
                                                    <Calendar className="w-3 h-3" />
                                                    {booking.booking_date}
                                                </span>
                                                <span className="flex items-center gap-1">
                                                    <Clock className="w-3 h-3" />
                                                    {booking.departure_time}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <div className="font-bold text-green-600">
                                            {formatCurrency(booking.total_amount)}
                                        </div>
                                        <span className={`text-xs px-2 py-1 rounded-full ${booking.status === 'confirmed' ? 'bg-green-100 text-green-700' :
                                                booking.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                                                    'bg-gray-100 text-gray-700'
                                            }`}>
                                            {booking.status}
                                        </span>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </CardContent>
            </Card>
        </div>
    );
}
