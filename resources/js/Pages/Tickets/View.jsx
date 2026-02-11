import { Head, Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import {
    Ticket,
    MapPin,
    Calendar,
    Clock,
    Ship,
    User,
    CreditCard,
    QrCode,
    Printer,
    CheckCircle,
    AlertCircle,
    ArrowLeft,
} from 'lucide-react';

export default function TicketView({ ticket }) {
    const formatDate = (date) => {
        if (!date) return '--';
        return new Date(date).toLocaleDateString('en-IN', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        });
    };

    const formatTime = (time) => {
        if (!time) return '--:--';
        if (typeof time === 'string' && time.includes(':')) {
            return time.substring(0, 5);
        }
        return time;
    };

    const formatAmount = (amount) => {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            minimumFractionDigits: 2,
        }).format(amount || 0);
    };

    const isVerified = !!ticket?.verified_at;

    return (
        <>
            <Head>
                <title>Ticket #{ticket?.ticket_no || ticket?.id} - Jetty</title>
            </Head>

            <div className="max-w-3xl mx-auto">
                {/* Back Button */}
                <div className="mb-6">
                    <Link
                        href={route('verify.index')}
                        className="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition-colors"
                    >
                        <ArrowLeft className="w-4 h-4" />
                        <span>Back to Verification</span>
                    </Link>
                </div>

                {/* Ticket Card */}
                <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    {/* Header */}
                    <div className="px-6 py-5 bg-gradient-to-r from-indigo-600 to-indigo-700">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-4">
                                <div className="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center">
                                    <Ticket className="w-7 h-7 text-white" />
                                </div>
                                <div>
                                    <h1 className="text-xl font-bold text-white">
                                        Ticket #{ticket?.ticket_no || ticket?.id}
                                    </h1>
                                    <p className="text-indigo-100 text-sm">
                                        {ticket?.payment_mode || 'CASH MEMO'}
                                    </p>
                                </div>
                            </div>
                            <div className={`px-4 py-2 rounded-full text-sm font-semibold ${isVerified
                                    ? 'bg-green-500/20 text-green-100'
                                    : 'bg-amber-500/20 text-amber-100'
                                }`}>
                                {isVerified ? 'Verified' : 'Not Verified'}
                            </div>
                        </div>
                    </div>

                    {/* Journey Details */}
                    <div className="p-6 border-b border-slate-100">
                        <h2 className="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-4">
                            Journey Details
                        </h2>
                        <div className="grid md:grid-cols-2 gap-6">
                            <div className="flex items-start gap-3">
                                <div className="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                    <MapPin className="w-5 h-5 text-indigo-600" />
                                </div>
                                <div>
                                    <p className="text-sm text-slate-500">Route</p>
                                    <p className="font-semibold text-slate-800">
                                        {ticket?.branch?.branch_name || 'From'} → {ticket?.dest_branch?.branch_name || ticket?.to_branch?.branch_name || 'To'}
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-start gap-3">
                                <div className="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                    <Calendar className="w-5 h-5 text-indigo-600" />
                                </div>
                                <div>
                                    <p className="text-sm text-slate-500">Date</p>
                                    <p className="font-semibold text-slate-800">
                                        {formatDate(ticket?.ticket_date || ticket?.created_at)}
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-start gap-3">
                                <div className="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                    <Clock className="w-5 h-5 text-indigo-600" />
                                </div>
                                <div>
                                    <p className="text-sm text-slate-500">Departure Time</p>
                                    <p className="font-semibold text-slate-800">
                                        {formatTime(ticket?.ferry_time)}
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-start gap-3">
                                <div className="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                    <Ship className="w-5 h-5 text-indigo-600" />
                                </div>
                                <div>
                                    <p className="text-sm text-slate-500">Ferry</p>
                                    <p className="font-semibold text-slate-800">
                                        {ticket?.ferry_boat?.name || ticket?.ferryBoat?.name || '-'}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Items Table */}
                    <div className="p-6 border-b border-slate-100">
                        <h2 className="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-4">
                            Items
                        </h2>
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="border-b border-slate-200">
                                        <th className="text-left py-3 px-2 text-xs font-semibold text-slate-500 uppercase">Item</th>
                                        <th className="text-center py-3 px-2 text-xs font-semibold text-slate-500 uppercase">Qty</th>
                                        <th className="text-right py-3 px-2 text-xs font-semibold text-slate-500 uppercase">Rate</th>
                                        <th className="text-right py-3 px-2 text-xs font-semibold text-slate-500 uppercase">Levy</th>
                                        <th className="text-right py-3 px-2 text-xs font-semibold text-slate-500 uppercase">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {ticket?.lines?.map((line, idx) => (
                                        <tr key={idx} className="border-b border-slate-100">
                                            <td className="py-3 px-2">
                                                <span className="font-medium text-slate-800">{line.item_name}</span>
                                                {(line.vehicle_no || line.vehicle_name) && (
                                                    <span className="block text-sm text-slate-500">
                                                        {[line.vehicle_name, line.vehicle_no].filter(Boolean).join(' - ')}
                                                    </span>
                                                )}
                                            </td>
                                            <td className="py-3 px-2 text-center text-slate-600">{line.qty}</td>
                                            <td className="py-3 px-2 text-right text-slate-600">₹{parseFloat(line.rate || 0).toFixed(2)}</td>
                                            <td className="py-3 px-2 text-right text-slate-600">₹{parseFloat(line.levy || 0).toFixed(2)}</td>
                                            <td className="py-3 px-2 text-right font-semibold text-slate-800">₹{parseFloat(line.amount || 0).toFixed(2)}</td>
                                        </tr>
                                    ))}
                                </tbody>
                                <tfoot>
                                    <tr className="bg-slate-50">
                                        <td colSpan="4" className="py-3 px-2 text-right font-semibold text-slate-700">
                                            Total Amount (incl. Tax):
                                        </td>
                                        <td className="py-3 px-2 text-right font-bold text-lg text-indigo-600">
                                            {formatAmount(ticket?.total_amount)}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    {/* QR Code & Status */}
                    <div className="p-6 grid md:grid-cols-2 gap-6">
                        {/* QR Code */}
                        <div className="text-center">
                            <div className="inline-block p-4 bg-white rounded-xl border-2 border-slate-200">
                                <img
                                    src={`https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(window.location.origin + '/verify?code=' + ticket?.id)}`}
                                    alt="QR Code"
                                    className="w-36 h-36"
                                />
                            </div>
                            <p className="mt-3 text-sm text-slate-500">Scan to verify ticket</p>
                        </div>

                        {/* Verification Status */}
                        <div className="flex flex-col justify-center">
                            {isVerified ? (
                                <div className="p-4 rounded-xl bg-green-50 border border-green-200">
                                    <div className="flex items-center gap-3">
                                        <CheckCircle className="w-6 h-6 text-green-600" />
                                        <div>
                                            <p className="font-semibold text-green-800">Ticket Verified</p>
                                            <p className="text-sm text-green-600">
                                                {formatDate(ticket?.verified_at)} at {new Date(ticket?.verified_at).toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit' })}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <div className="p-4 rounded-xl bg-amber-50 border border-amber-200">
                                    <div className="flex items-center gap-3">
                                        <AlertCircle className="w-6 h-6 text-amber-600" />
                                        <div>
                                            <p className="font-semibold text-amber-800">Not Yet Verified</p>
                                            <p className="text-sm text-amber-600">
                                                Show this ticket at the boarding gate
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Operator Info */}
                            <div className="mt-4 flex items-center gap-3 text-sm text-slate-500">
                                <User className="w-4 h-4" />
                                <span>Created by: {ticket?.user?.name || '-'}</span>
                            </div>
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                        <a
                            href={route('tickets.print', ticket?.id) + '?w=58'}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-white transition-colors"
                        >
                            <Printer className="w-4 h-4" />
                            <span>Print 58mm</span>
                        </a>
                        <a
                            href={route('tickets.print', ticket?.id) + '?w=80'}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors"
                        >
                            <Printer className="w-4 h-4" />
                            <span>Print 80mm</span>
                        </a>
                    </div>
                </div>
            </div>
        </>
    );
}

TicketView.layout = (page) => <Layout>{page}</Layout>;
