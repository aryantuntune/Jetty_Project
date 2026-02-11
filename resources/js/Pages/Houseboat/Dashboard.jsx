import { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import {
    CalendarCheck,
    Clock,
    IndianRupee,
    Download,
    Inbox,
    ChevronLeft,
    ChevronRight,
    Ship,
} from 'lucide-react';

// Format currency
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-IN', {
        maximumFractionDigits: 0,
    }).format(amount || 0);
};

// Format date
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN', {
        month: 'short',
        day: '2-digit',
    });
};

// Status badge component
const StatusBadge = ({ status }) => {
    const statusStyles = {
        confirmed: 'bg-green-100 text-green-800',
        pending: 'bg-orange-100 text-orange-800',
        cancelled: 'bg-red-100 text-red-800',
        completed: 'bg-slate-100 text-slate-800',
    };

    return (
        <span
            className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                statusStyles[status] || 'bg-slate-100 text-slate-800'
            }`}
        >
            {status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Unknown'}
        </span>
    );
};

export default function HouseboatDashboard({ bookings, totalRevenue, activeBookings, pendingBookings, auth }) {
    const { flash } = usePage().props;
    const [updatingId, setUpdatingId] = useState(null);

    // Handle status change
    const handleStatusChange = (bookingId, newStatus) => {
        setUpdatingId(bookingId);
        router.patch(
            route('houseboat.bookings.status', bookingId),
            { status: newStatus },
            {
                preserveState: true,
                onFinish: () => setUpdatingId(null),
            }
        );
    };

    // Pagination data
    const bookingData = bookings?.data || [];
    const currentPage = bookings?.current_page || 1;
    const prevPageUrl = bookings?.prev_page_url;
    const nextPageUrl = bookings?.next_page_url;

    return (
        <div className="space-y-8">
            {/* Flash Messages */}
            {flash?.success && (
                <div className="bg-green-50 border border-green-200 rounded-xl p-4 text-green-800">
                    {flash.success}
                </div>
            )}

            {/* Stats Cards */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {/* Active Bookings */}
                <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div className="flex items-center justify-between mb-4">
                        <h3 className="text-sm font-medium text-slate-500">Active Bookings</h3>
                        <div className="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                            <CalendarCheck className="w-5 h-5 text-blue-600" />
                        </div>
                    </div>
                    <p className="text-3xl font-bold text-slate-800 font-[Inter]">{activeBookings || 0}</p>
                    <div className="mt-2 text-xs text-green-600 font-medium">Currently confirmed</div>
                </div>

                {/* Pending Bookings */}
                <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div className="flex items-center justify-between mb-4">
                        <h3 className="text-sm font-medium text-slate-500">Pending Requests</h3>
                        <div className="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center">
                            <Clock className="w-5 h-5 text-orange-600" />
                        </div>
                    </div>
                    <p className="text-3xl font-bold text-slate-800 font-[Inter]">{pendingBookings || 0}</p>
                    <div className="mt-2 text-xs text-orange-600 font-medium">Needs attention</div>
                </div>

                {/* Revenue */}
                <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div className="flex items-center justify-between mb-4">
                        <h3 className="text-sm font-medium text-slate-500">Total Revenue</h3>
                        <div className="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
                            <IndianRupee className="w-5 h-5 text-green-600" />
                        </div>
                    </div>
                    <p className="text-3xl font-bold text-slate-800 font-[Inter]">₹{formatCurrency(totalRevenue)}</p>
                    <div className="mt-2 text-xs text-slate-500">All time earnings</div>
                </div>
            </div>

            {/* Bookings Table */}
            <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div className="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                    <h2 className="text-lg font-bold text-slate-800">Recent Bookings</h2>
                    <button className="text-sm text-indigo-600 font-medium hover:text-indigo-700 inline-flex items-center gap-1">
                        <Download className="w-4 h-4" />
                        Export CSV
                    </button>
                </div>

                <div className="overflow-x-auto">
                    <table className="w-full text-left text-sm text-slate-600 font-[Inter]">
                        <thead className="bg-slate-50 text-xs uppercase font-semibold text-slate-500">
                            <tr>
                                <th className="px-6 py-4">Ref #</th>
                                <th className="px-6 py-4">Customer</th>
                                <th className="px-6 py-4">Room</th>
                                <th className="px-6 py-4">Dates</th>
                                <th className="px-6 py-4">Amount</th>
                                <th className="px-6 py-4">Status</th>
                                <th className="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {bookingData.length > 0 ? (
                                bookingData.map((booking) => (
                                    <tr key={booking.id} className="hover:bg-slate-50 transition-colors">
                                        <td className="px-6 py-4 font-mono text-xs">
                                            {booking.booking_reference}
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="font-medium text-slate-900">
                                                {booking.customer_name}
                                            </div>
                                            <div className="text-xs text-slate-400">{booking.customer_phone}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            {booking.room?.name || 'Unknown'} (x{booking.room_count || 1})
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="text-xs">
                                                <span className="font-medium">In:</span>{' '}
                                                {formatDate(booking.check_in)}
                                                <br />
                                                <span className="font-medium">Out:</span>{' '}
                                                {formatDate(booking.check_out)}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 font-bold text-slate-800">
                                            ₹{formatCurrency(booking.total_amount)}
                                        </td>
                                        <td className="px-6 py-4">
                                            <StatusBadge status={booking.status} />
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <select
                                                value={booking.status}
                                                onChange={(e) => handleStatusChange(booking.id, e.target.value)}
                                                disabled={updatingId === booking.id}
                                                className="text-xs border-slate-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-50"
                                            >
                                                <option value="pending">Pending</option>
                                                <option value="confirmed">Confirm</option>
                                                <option value="cancelled">Cancel</option>
                                                <option value="completed">Complete</option>
                                            </select>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="7" className="px-6 py-12 text-center text-slate-500">
                                        <div className="flex flex-col items-center justify-center">
                                            <Inbox className="w-12 h-12 text-slate-300 mb-3" />
                                            <p>No bookings found.</p>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                <div className="px-6 py-4 border-t border-slate-100 flex justify-between items-center">
                    <p className="text-sm text-slate-600">Page {currentPage}</p>
                    <div className="flex items-center gap-2">
                        {prevPageUrl ? (
                            <Link
                                href={prevPageUrl}
                                className="px-3 py-1.5 text-sm text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors inline-flex items-center gap-1"
                            >
                                <ChevronLeft className="w-4 h-4" />
                                Previous
                            </Link>
                        ) : (
                            <span className="px-3 py-1.5 text-sm text-slate-400 bg-slate-100 rounded-lg cursor-not-allowed inline-flex items-center gap-1">
                                <ChevronLeft className="w-4 h-4" />
                                Previous
                            </span>
                        )}
                        {nextPageUrl ? (
                            <Link
                                href={nextPageUrl}
                                className="px-3 py-1.5 text-sm text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors inline-flex items-center gap-1"
                            >
                                Next
                                <ChevronRight className="w-4 h-4" />
                            </Link>
                        ) : (
                            <span className="px-3 py-1.5 text-sm text-slate-400 bg-slate-100 rounded-lg cursor-not-allowed inline-flex items-center gap-1">
                                Next
                                <ChevronRight className="w-4 h-4" />
                            </span>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}

HouseboatDashboard.layout = (page) => <Layout children={page} title="Houseboat Dashboard" />;
