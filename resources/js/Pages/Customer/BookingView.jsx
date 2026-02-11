import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import CustomerLayout from '@/Layouts/CustomerLayout';
import {
    Calendar,
    MapPin,
    Clock,
    Ticket,
    ArrowLeft,
    Download,
    QrCode,
    IndianRupee,
    User,
    Phone,
    Ship,
    CheckCircle,
} from 'lucide-react';

// Format date
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN', {
        year: 'numeric',
        month: 'long',
        day: '2-digit',
    });
};

// Format time
const formatTime = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-IN', {
        hour: '2-digit',
        minute: '2-digit',
    });
};

// Format currency
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-IN', {
        maximumFractionDigits: 2,
    }).format(amount || 0);
};

// Status badge component
const StatusBadge = ({ status }) => {
    const statusStyles = {
        confirmed: 'bg-green-100 text-green-800 border-green-200',
        pending: 'bg-amber-100 text-amber-800 border-amber-200',
        cancelled: 'bg-red-100 text-red-800 border-red-200',
        completed: 'bg-sky-100 text-sky-800 border-sky-200',
    };

    return (
        <span
            className={`inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-semibold border ${statusStyles[status?.toLowerCase()] || 'bg-slate-100 text-slate-800 border-slate-200'
                }`}
        >
            <CheckCircle className="w-4 h-4" />
            {status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Unknown'}
        </span>
    );
};

export default function BookingView({ booking }) {
    return (
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            {/* Back Button */}
            <Link
                href={route('booking.history')}
                className="inline-flex items-center gap-2 text-slate-600 hover:text-sky-600 mb-6 transition-colors"
            >
                <ArrowLeft className="w-4 h-4" />
                <span>Back to History</span>
            </Link>

            {/* Ticket Card */}
            <div className="bg-white rounded-3xl shadow-xl border border-sky-100 overflow-hidden">
                {/* Header */}
                <div className="bg-gradient-to-r from-sky-500 to-sky-600 px-6 py-8 text-white">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-4">
                            <div className="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center">
                                <Ticket className="w-8 h-8 text-white" />
                            </div>
                            <div>
                                <p className="text-sky-100 text-sm">Booking Reference</p>
                                <p className="text-2xl font-bold font-mono">
                                    #{booking.ticket_id || booking.id}
                                </p>
                            </div>
                        </div>
                        <StatusBadge status={booking.status} />
                    </div>
                </div>

                {/* Content */}
                <div className="p-6 space-y-6">
                    {/* Route Section */}
                    <div className="bg-sky-50 rounded-2xl p-6">
                        <div className="flex items-center gap-4">
                            <div className="flex-1">
                                <p className="text-sm text-slate-500 mb-1">From</p>
                                <p className="text-lg font-bold text-slate-800">
                                    {booking.from_branch?.branch_name || 'N/A'}
                                </p>
                            </div>
                            <div className="w-12 h-12 rounded-full bg-sky-100 flex items-center justify-center">
                                <Ship className="w-6 h-6 text-sky-600" />
                            </div>
                            <div className="flex-1 text-right">
                                <p className="text-sm text-slate-500 mb-1">To</p>
                                <p className="text-lg font-bold text-amber-600">
                                    {booking.to_branch?.branch_name || 'N/A'}
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Details Grid */}
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div className="bg-slate-50 rounded-xl p-4">
                            <div className="flex items-center gap-2 text-slate-500 mb-1">
                                <Calendar className="w-4 h-4" />
                                <span className="text-xs">Date</span>
                            </div>
                            <p className="font-semibold text-slate-800">
                                {formatDate(booking.booking_date || booking.created_at)}
                            </p>
                        </div>
                        <div className="bg-slate-50 rounded-xl p-4">
                            <div className="flex items-center gap-2 text-slate-500 mb-1">
                                <Clock className="w-4 h-4" />
                                <span className="text-xs">Time</span>
                            </div>
                            <p className="font-semibold text-slate-800">
                                {booking.departure_time || formatTime(booking.created_at)}
                            </p>
                        </div>
                        <div className="bg-slate-50 rounded-xl p-4">
                            <div className="flex items-center gap-2 text-slate-500 mb-1">
                                <IndianRupee className="w-4 h-4" />
                                <span className="text-xs">Amount</span>
                            </div>
                            <p className="font-semibold text-sky-600">
                                ₹{formatCurrency(booking.total_amount)}
                            </p>
                        </div>
                        <div className="bg-slate-50 rounded-xl p-4">
                            <div className="flex items-center gap-2 text-slate-500 mb-1">
                                <Ticket className="w-4 h-4" />
                                <span className="text-xs">Source</span>
                            </div>
                            <p className="font-semibold text-slate-800 capitalize">
                                {booking.booking_source || 'Web'}
                            </p>
                        </div>
                    </div>

                    {/* Customer Info */}
                    {booking.customer && (
                        <div className="border-t border-slate-100 pt-6">
                            <h3 className="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-4">
                                Customer Details
                            </h3>
                            <div className="flex items-center gap-4">
                                <div className="w-12 h-12 rounded-full bg-sky-100 flex items-center justify-center">
                                    <User className="w-6 h-6 text-sky-600" />
                                </div>
                                <div>
                                    <p className="font-semibold text-slate-800">
                                        {booking.customer.name || booking.customer.first_name}
                                    </p>
                                    <p className="text-sm text-slate-500">{booking.customer.email}</p>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Items */}
                    {booking.items && booking.items.length > 0 && (
                        <div className="border-t border-slate-100 pt-6">
                            <h3 className="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-4">
                                Booked Items
                            </h3>
                            <div className="space-y-3">
                                {booking.items.map((item, idx) => (
                                    <div
                                        key={idx}
                                        className="flex items-center justify-between bg-slate-50 rounded-xl p-4"
                                    >
                                        <div>
                                            <p className="font-medium text-slate-800">
                                                {item.item_name || item.description}
                                            </p>
                                            {item.vehicle_no && (
                                                <p className="text-sm text-slate-500">
                                                    Vehicle: {item.vehicle_no}
                                                </p>
                                            )}
                                        </div>
                                        <div className="text-right">
                                            <p className="text-sm text-slate-500">
                                                x{item.qty || item.quantity}
                                            </p>
                                            <p className="font-semibold text-slate-800">
                                                ₹{formatCurrency(item.amount || item.rate * (item.qty || item.quantity))}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Actions */}
                    <div className="border-t border-slate-100 pt-6 flex flex-wrap gap-3">
                        {booking.status?.toLowerCase() === 'confirmed' && (
                            <button className="flex items-center gap-2 px-6 py-3 rounded-xl bg-sky-600 text-white font-semibold hover:bg-sky-700 transition-colors">
                                <QrCode className="w-5 h-5" />
                                <span>Show QR Code</span>
                            </button>
                        )}
                        <button className="flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition-colors">
                            <Download className="w-5 h-5" />
                            <span>Download Ticket</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}

BookingView.layout = (page) => <CustomerLayout title="Booking Details">{page}</CustomerLayout>;
