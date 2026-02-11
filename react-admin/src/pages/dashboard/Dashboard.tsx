import { useState, useMemo } from 'react';
import { useQuery } from '@tanstack/react-query';
import { Card, CardContent, CardHeader, CardTitle, Button } from '@/components/ui';
import { useAuthStore } from '@/store';
import { Ticket, DollarSign, Users, Ship, TrendingUp, Clock, ChevronLeft, ChevronRight, CheckCircle } from 'lucide-react';
import { Link } from 'react-router-dom';
import apiClient from '@/lib/axios';

type ViewMode = 'day' | 'month';

function extractData<T>(response: any): T {
    if (response.data?.data) return response.data.data;
    return response.data;
}

export function Dashboard() {
    const { user } = useAuthStore();
    const [viewMode, setViewMode] = useState<ViewMode>('day');
    const [selectedDate, setSelectedDate] = useState(() => new Date().toISOString().split('T')[0]);
    const [selectedMonth, setSelectedMonth] = useState(() => new Date().toISOString().slice(0, 7));

    // Compute date range for API
    const dateParams = useMemo(() => {
        if (viewMode === 'day') {
            return { date_from: selectedDate, date_to: selectedDate };
        } else {
            const year = parseInt(selectedMonth.split('-')[0]);
            const month = parseInt(selectedMonth.split('-')[1]);
            const lastDay = new Date(year, month, 0).getDate();
            return {
                date_from: `${selectedMonth}-01`,
                date_to: `${selectedMonth}-${lastDay.toString().padStart(2, '0')}`
            };
        }
    }, [viewMode, selectedDate, selectedMonth]);

    // Fetch dashboard stats
    const { data: stats, isLoading } = useQuery({
        queryKey: ['dashboard-stats', dateParams],
        queryFn: async () => {
            try {
                const res = await apiClient.get('/api/tickets', {
                    params: dateParams
                });
                const tickets = extractData<any[]>(res) || [];
                const totalRevenue = tickets.reduce((sum, t) => sum + parseFloat(t.total_amount || 0), 0);
                return {
                    ticketsCount: tickets.length,
                    totalRevenue,
                    pendingVerifications: tickets.filter((t: any) => !t.verified_at).length,
                    recentTickets: tickets.slice(0, 5)
                };
            } catch {
                return { ticketsCount: 0, totalRevenue: 0, pendingVerifications: 0, recentTickets: [] };
            }
        },
    });

    // Fetch ferries count
    const { data: ferries = [] } = useQuery({
        queryKey: ['ferries-count'],
        queryFn: async () => {
            const res = await apiClient.get('/api/admin/ferries');
            return extractData<any[]>(res) || [];
        },
    });

    const periodLabel = viewMode === 'day'
        ? new Date(selectedDate).toLocaleDateString('en-IN', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
        : new Date(selectedMonth + '-01').toLocaleDateString('en-IN', { year: 'numeric', month: 'long' });

    const isToday = selectedDate === new Date().toISOString().split('T')[0];
    const isThisMonth = selectedMonth === new Date().toISOString().slice(0, 7);

    const navigateDate = (direction: 'prev' | 'next') => {
        if (viewMode === 'day') {
            const d = new Date(selectedDate);
            d.setDate(d.getDate() + (direction === 'next' ? 1 : -1));
            setSelectedDate(d.toISOString().split('T')[0]);
        } else {
            const [year, month] = selectedMonth.split('-').map(Number);
            const d = new Date(year, month - 1 + (direction === 'next' ? 1 : -1), 1);
            setSelectedMonth(d.toISOString().slice(0, 7));
        }
    };

    return (
        <div className="space-y-6">
            {/* Welcome Banner with Date Filter */}
            <div className="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-6 md:p-8 text-white shadow-lg">
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <h1 className="text-2xl md:text-3xl font-bold mb-2">Welcome back, {user?.name || 'Admin'}!</h1>
                        <p className="text-blue-100">Viewing metrics for <span className="font-semibold">{periodLabel}</span></p>
                    </div>

                    {/* Date Filter Controls */}
                    <div className="flex flex-col sm:flex-row gap-3">
                        {/* View Mode Toggle */}
                        <div className="flex bg-white/20 rounded-lg p-1">
                            <button
                                onClick={() => setViewMode('day')}
                                className={`px-4 py-2 rounded-md text-sm font-medium transition-all ${viewMode === 'day' ? 'bg-white text-blue-700' : 'text-white hover:bg-white/10'}`}
                            >
                                Daily
                            </button>
                            <button
                                onClick={() => setViewMode('month')}
                                className={`px-4 py-2 rounded-md text-sm font-medium transition-all ${viewMode === 'month' ? 'bg-white text-blue-700' : 'text-white hover:bg-white/10'}`}
                            >
                                Monthly
                            </button>
                        </div>

                        {/* Date/Month Picker */}
                        <div className="flex items-center gap-2">
                            <button
                                onClick={() => navigateDate('prev')}
                                className="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors"
                            >
                                <ChevronLeft className="w-5 h-5" />
                            </button>
                            {viewMode === 'day' ? (
                                <input
                                    type="date"
                                    value={selectedDate}
                                    onChange={(e) => setSelectedDate(e.target.value)}
                                    max={new Date().toISOString().split('T')[0]}
                                    className="bg-white/20 border-0 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-white/50 [color-scheme:dark]"
                                />
                            ) : (
                                <input
                                    type="month"
                                    value={selectedMonth}
                                    onChange={(e) => setSelectedMonth(e.target.value)}
                                    max={new Date().toISOString().slice(0, 7)}
                                    className="bg-white/20 border-0 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-white/50 [color-scheme:dark]"
                                />
                            )}
                            <button
                                onClick={() => navigateDate('next')}
                                disabled={viewMode === 'day' ? isToday : isThisMonth}
                                className={`p-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors ${(viewMode === 'day' ? isToday : isThisMonth) ? 'opacity-50 cursor-not-allowed' : ''}`}
                            >
                                <ChevronRight className="w-5 h-5" />
                            </button>
                        </div>

                        {/* Today/This Month Button */}
                        {(viewMode === 'day' ? !isToday : !isThisMonth) && (
                            <button
                                onClick={() => viewMode === 'day' ? setSelectedDate(new Date().toISOString().split('T')[0]) : setSelectedMonth(new Date().toISOString().slice(0, 7))}
                                className="px-4 py-2 bg-white text-blue-700 rounded-lg font-medium hover:bg-blue-50 transition-colors"
                            >
                                {viewMode === 'day' ? 'Today' : 'This Month'}
                            </button>
                        )}
                    </div>
                </div>
            </div>

            {/* Stats Grid */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <StatsCard
                    icon={Ticket}
                    title="Tickets Issued"
                    value={isLoading ? '...' : (stats?.ticketsCount || 0).toLocaleString()}
                    trend={viewMode === 'day' ? 'Today' : 'This Month'}
                    color="blue"
                />
                <StatsCard
                    icon={DollarSign}
                    title="Revenue"
                    value={isLoading ? '...' : `₹${(stats?.totalRevenue || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`}
                    trend={viewMode === 'day' ? 'Today' : 'This Month'}
                    color="green"
                />
                <StatsCard
                    icon={Ship}
                    title="Ferry Boats"
                    value={ferries.length}
                    trend="Active"
                    color="purple"
                />
                <StatsCard
                    icon={Clock}
                    title="Pending Verifications"
                    value={isLoading ? '...' : (stats?.pendingVerifications || 0)}
                    trend={stats?.pendingVerifications === 0 ? 'All Done' : 'Pending'}
                    color="orange"
                />
            </div>

            {/* Quick Actions */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <TrendingUp className="w-5 h-5" />
                        Quick Actions
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <QuickActionButton icon={Ticket} label="New Ticket" href="/tickets/entry" />
                        <QuickActionButton icon={CheckCircle} label="Verify Tickets" href="/tickets/verify" />
                        <QuickActionButton icon={Users} label="Manage Guests" href="/guests" />
                        <QuickActionButton icon={DollarSign} label="View Reports" href="/reports" />
                    </div>
                </CardContent>
            </Card>

            {/* Recent Tickets & Revenue */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center justify-between">
                            <span>Recent Tickets</span>
                            <Link to="/reports" className="text-sm text-blue-600 hover:text-blue-700 font-medium">View All</Link>
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-0">
                        {isLoading ? (
                            <p className="text-gray-500 text-center py-8">Loading...</p>
                        ) : stats?.recentTickets?.length ? (
                            <div className="divide-y">
                                {stats.recentTickets.map((ticket: any) => (
                                    <div key={ticket.id} className="flex items-center gap-4 p-4 hover:bg-gray-50">
                                        <div className={`w-10 h-10 rounded-full flex items-center justify-center ${ticket.verified_at ? 'bg-green-100' : 'bg-blue-100'}`}>
                                            {ticket.verified_at ? (
                                                <CheckCircle className="w-5 h-5 text-green-600" />
                                            ) : (
                                                <Ticket className="w-5 h-5 text-blue-600" />
                                            )}
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <p className="text-sm font-medium text-gray-900 truncate">
                                                Ticket #{ticket.ticket_no || ticket.id} - ₹{parseFloat(ticket.total_amount || 0).toFixed(2)}
                                            </p>
                                            <p className="text-xs text-gray-500">
                                                {ticket.ferry_boat_name || 'N/A'} | {ticket.user_name || 'Unknown'}
                                            </p>
                                        </div>
                                        <span className="text-xs text-gray-400">
                                            {ticket.ticket_date || ticket.created_at?.split('T')[0]}
                                        </span>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-8 text-gray-400">
                                <Ticket className="w-12 h-12 mx-auto mb-3 opacity-50" />
                                <p>No tickets found for this period</p>
                            </div>
                        )}
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>System Status</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-4">
                            <div className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div className="flex items-center gap-3">
                                    <Ship className="w-5 h-5 text-gray-600" />
                                    <span className="text-sm text-gray-700">Ferry Boats</span>
                                </div>
                                <span className="text-sm font-medium text-green-600">{ferries.length} Active</span>
                            </div>
                            <div className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div className="flex items-center gap-3">
                                    <Users className="w-5 h-5 text-gray-600" />
                                    <span className="text-sm text-gray-700">Role</span>
                                </div>
                                <span className="text-sm font-medium text-gray-600">{user?.role_name || 'Admin'}</span>
                            </div>
                            <div className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div className="flex items-center gap-3">
                                    <CheckCircle className="w-5 h-5 text-green-600" />
                                    <span className="text-sm text-gray-700">System</span>
                                </div>
                                <span className="text-sm font-medium text-green-600">All Operational</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    );
}

// Stats Card Component
interface StatsCardProps {
    icon: React.ElementType;
    title: string;
    value: string | number;
    trend: string;
    color: 'blue' | 'green' | 'purple' | 'orange';
}

function StatsCard({ icon: Icon, title, value, trend, color }: StatsCardProps) {
    const colorClasses = {
        blue: 'bg-blue-100 text-blue-600',
        green: 'bg-green-100 text-green-600',
        purple: 'bg-purple-100 text-purple-600',
        orange: 'bg-orange-100 text-orange-600',
    };

    return (
        <Card className="hover:shadow-md transition-shadow">
            <CardContent className="p-6">
                <div className="flex items-center justify-between mb-4">
                    <div className={`p-3 rounded-xl ${colorClasses[color]}`}>
                        <Icon className="w-6 h-6" />
                    </div>
                    <span className={`text-xs font-medium px-2 py-1 rounded-full ${color === 'orange' && trend === 'Pending' ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-600'}`}>
                        {trend}
                    </span>
                </div>
                <h3 className="text-2xl font-bold text-gray-800">{value}</h3>
                <p className="text-sm text-gray-500 mt-1">{title}</p>
            </CardContent>
        </Card>
    );
}

// Quick Action Button
interface QuickActionButtonProps {
    icon: React.ElementType;
    label: string;
    href: string;
}

function QuickActionButton({ icon: Icon, label, href }: QuickActionButtonProps) {
    return (
        <Link
            to={href}
            className="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all group"
        >
            <div className="w-12 h-12 rounded-xl bg-blue-100 group-hover:bg-blue-200 flex items-center justify-center mb-3 transition-colors">
                <Icon className="w-6 h-6 text-blue-600" />
            </div>
            <span className="text-sm font-medium text-gray-700">{label}</span>
        </Link>
    );
}
