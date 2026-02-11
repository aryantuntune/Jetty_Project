import { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import {
    QrCode,
    Search,
    Ticket as TicketIcon,
    MapPin,
    Calendar,
    Clock,
    CheckCircle,
    AlertCircle,
    X,
    Ship,
    User,
    Car,
} from 'lucide-react';

// Currency formatter
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount || 0);
};

// Date formatter
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
};

// Time formatter
const formatTime = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-IN', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
    });
};

export default function TicketVerify({ ticket, auth }) {
    const { flash } = usePage().props;
    const [searchCode, setSearchCode] = useState('');
    const [isSearching, setIsSearching] = useState(false);
    const [isVerifying, setIsVerifying] = useState(false);

    // Handle search
    const handleSearch = (e) => {
        e.preventDefault();
        if (!searchCode.trim()) return;

        setIsSearching(true);
        router.get(
            route('verify.index'),
            { code: searchCode },
            {
                preserveState: true,
                onFinish: () => setIsSearching(false),
            }
        );
    };

    // Handle verify
    const handleVerify = () => {
        if (!ticket?.id) return;

        setIsVerifying(true);
        router.post(
            route('verify.ticket'),
            { ticket_id: ticket.id },
            {
                preserveState: true,
                onFinish: () => setIsVerifying(false),
            }
        );
    };

    // Check if ticket is verified
    const isVerified = ticket?.verified_at !== null && ticket?.verified_at !== undefined;
    const verifiedAt = ticket?.verified_at ? new Date(ticket.verified_at) : null;

    // Ticket date and ferry time
    const ticketDate = ticket?.ticket_date || ticket?.created_at;
    const ferryTime = ticket?.ferry_time || ticket?.created_at;

    return (
        <div className="max-w-3xl mx-auto space-y-6">
            {/* Header */}
            <div>
                <h1 className="text-2xl font-bold text-slate-800 tracking-tight">Ticket Verification</h1>
                <p className="text-slate-500 mt-1">Scan or search for tickets to verify</p>
            </div>

            {/* Flash Messages */}
            {flash?.error && (
                <div className="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
                    <AlertCircle className="w-5 h-5 text-red-500 flex-shrink-0" />
                    <p className="text-red-800">{flash.error}</p>
                </div>
            )}

            {flash?.success && (
                <div className="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                    <CheckCircle className="w-5 h-5 text-green-500 flex-shrink-0" />
                    <p className="text-green-800">{flash.success}</p>
                </div>
            )}

            {/* Search Card */}
            <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div className="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <div className="flex items-center gap-2">
                        <QrCode className="w-5 h-5 text-slate-400" />
                        <span className="font-semibold text-slate-700">Search Ticket</span>
                    </div>
                </div>

                <form onSubmit={handleSearch} className="p-6">
                    <div className="space-y-4">
                        <div>
                            <label className="block text-sm font-semibold text-slate-700 mb-2">
                                Scan or Enter Ticket Code
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <QrCode className="w-5 h-5 text-slate-400" />
                                </div>
                                <input
                                    type="text"
                                    value={searchCode}
                                    onChange={(e) => setSearchCode(e.target.value)}
                                    className="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all"
                                    placeholder="Scan QR code or type ticket number..."
                                    autoFocus
                                    required
                                />
                            </div>
                        </div>
                        <button
                            type="submit"
                            disabled={isSearching}
                            className="w-full px-5 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-indigo-700 transition-all duration-200 shadow-lg shadow-indigo-500/30 flex items-center justify-center gap-2 disabled:opacity-50"
                        >
                            <Search className="w-5 h-5" />
                            {isSearching ? 'Searching...' : 'Search Ticket'}
                        </button>
                    </div>
                </form>
            </div>

            {/* Ticket Details Card */}
            {ticket && (
                <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div className="px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-2">
                                <TicketIcon className="w-5 h-5 text-slate-400" />
                                <span className="font-semibold text-slate-700">
                                    Ticket #{ticket.ticket_no || ticket.id}
                                </span>
                                {ticket.ticket_no && (
                                    <span className="text-xs text-slate-400">(ID: {ticket.id})</span>
                                )}
                            </div>
                            {isVerified ? (
                                <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <CheckCircle className="w-3 h-3 mr-1" />
                                    Verified
                                </span>
                            ) : (
                                <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    <Clock className="w-3 h-3 mr-1" />
                                    Pending
                                </span>
                            )}
                        </div>
                    </div>

                    <div className="p-6 space-y-6">
                        {/* Ticket Info Grid */}
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div className="bg-slate-50 rounded-xl p-4">
                                <div className="flex items-center gap-1 text-xs text-slate-500 mb-1">
                                    <MapPin className="w-3 h-3" />
                                    Branch
                                </div>
                                <p className="font-semibold text-slate-800">
                                    {ticket.branch?.branch_name || '-'}
                                </p>
                            </div>
                            <div className="bg-slate-50 rounded-xl p-4">
                                <div className="flex items-center gap-1 text-xs text-slate-500 mb-1">
                                    <Calendar className="w-3 h-3" />
                                    Date
                                </div>
                                <p className="font-semibold text-slate-800">{formatDate(ticketDate)}</p>
                            </div>
                            <div className="bg-slate-50 rounded-xl p-4">
                                <div className="flex items-center gap-1 text-xs text-slate-500 mb-1">
                                    <Clock className="w-3 h-3" />
                                    Ferry Time
                                </div>
                                <p className="font-semibold text-slate-800">{formatTime(ferryTime)}</p>
                            </div>
                            <div className="bg-slate-50 rounded-xl p-4">
                                <div className="flex items-center gap-1 text-xs text-slate-500 mb-1">
                                    <Ship className="w-3 h-3" />
                                    Total
                                </div>
                                <p className="font-semibold text-lg text-indigo-600">
                                    ₹{formatCurrency(ticket.total_amount)}
                                </p>
                            </div>
                        </div>

                        {/* Verification Status */}
                        {isVerified && verifiedAt && (
                            <div className="bg-green-50 border border-green-200 rounded-xl p-4">
                                <div className="flex items-center gap-3">
                                    <CheckCircle className="w-5 h-5 text-green-500" />
                                    <div>
                                        <p className="font-medium text-green-800">Verified</p>
                                        <p className="text-sm text-green-600">
                                            {formatDate(verifiedAt)} at {formatTime(verifiedAt)}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Items Table */}
                        <div>
                            <h3 className="text-sm font-semibold text-slate-700 mb-3">Ticket Items</h3>
                            <div className="overflow-x-auto border border-slate-200 rounded-xl">
                                <table className="w-full">
                                    <thead>
                                        <tr className="bg-slate-50 border-b border-slate-200">
                                            <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                                Description
                                            </th>
                                            <th className="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                                Qty
                                            </th>
                                            <th className="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                                Rate
                                            </th>
                                            <th className="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                                Levy
                                            </th>
                                            <th className="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                                Amount
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100">
                                        {ticket.lines?.map((line, index) => (
                                            <tr key={index} className="hover:bg-slate-50">
                                                <td className="px-4 py-3">
                                                    <span className="font-medium text-slate-800">
                                                        {line.item_name}
                                                    </span>
                                                    {(line.vehicle_no || line.vehicle_name) && (
                                                        <div className="flex items-center gap-1 text-xs text-slate-500 mt-0.5">
                                                            <Car className="w-3 h-3" />
                                                            {line.vehicle_name} {line.vehicle_no}
                                                        </div>
                                                    )}
                                                </td>
                                                <td className="px-4 py-3 text-center text-slate-600">
                                                    {line.qty}
                                                </td>
                                                <td className="px-4 py-3 text-right text-slate-600">
                                                    {formatCurrency(line.rate)}
                                                </td>
                                                <td className="px-4 py-3 text-right text-slate-600">
                                                    {formatCurrency(line.levy)}
                                                </td>
                                                <td className="px-4 py-3 text-right font-medium text-slate-800">
                                                    ₹{formatCurrency(line.amount)}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                    <tfoot>
                                        <tr className="bg-slate-50 border-t border-slate-200">
                                            <td colSpan="4" className="px-4 py-3 text-right font-semibold text-slate-700">
                                                Total Amount:
                                            </td>
                                            <td className="px-4 py-3 text-right font-bold text-lg text-indigo-600">
                                                ₹{formatCurrency(ticket.total_amount)}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        {/* Verify Button */}
                        {!isVerified && (
                            <button
                                type="button"
                                onClick={handleVerify}
                                disabled={isVerifying}
                                className="w-full px-5 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-lg shadow-green-500/30 flex items-center justify-center gap-2 disabled:opacity-50"
                            >
                                <CheckCircle className="w-5 h-5" />
                                {isVerifying ? 'Verifying...' : 'Mark as Verified'}
                            </button>
                        )}
                    </div>
                </div>
            )}

            {/* Empty State */}
            {!ticket && !flash?.error && (
                <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-12">
                    <div className="flex flex-col items-center text-center">
                        <div className="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                            <TicketIcon className="w-10 h-10 text-slate-400" />
                        </div>
                        <h3 className="text-lg font-semibold text-slate-700">No Ticket Selected</h3>
                        <p className="text-slate-500 mt-1 max-w-sm">
                            Enter a ticket number or scan a QR code to view and verify ticket details.
                        </p>
                    </div>
                </div>
            )}
        </div>
    );
}

TicketVerify.layout = (page) => <Layout children={page} title="Ticket Verification" />;
