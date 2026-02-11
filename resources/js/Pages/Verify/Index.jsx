import { useState } from 'react';
import { useForm, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import { Search, CheckCircle, XCircle } from 'lucide-react';

export default function Index({ ticket }) {
    const [searchCode, setSearchCode] = useState('');

    const handleSearch = (e) => {
        e.preventDefault();
        if (searchCode.trim()) {
            router.get(route('verify.index'), { code: searchCode });
        }
    };

    const handleVerify = () => {
        if (ticket) {
            router.post(route('verify.ticket'), { ticket_id: ticket.id });
        }
    };

    return (
        <div className="max-w-3xl mx-auto space-y-6">
            <div>
                <h1 className="text-2xl font-bold text-slate-800">Verify Ticket</h1>
                <p className="text-slate-500 mt-1">Enter ticket ID or scan QR code to verify</p>
            </div>

            {/* Search Form */}
            <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <form onSubmit={handleSearch} className="flex gap-3">
                    <div className="flex-1 relative">
                        <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" />
                        <input
                            type="text"
                            value={searchCode}
                            onChange={(e) => setSearchCode(e.target.value)}
                            placeholder="Enter Ticket ID"
                            className="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        />
                    </div>
                    <button
                        type="submit"
                        className="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold"
                    >
                        Search
                    </button>
                </form>
            </div>

            {/* Ticket Details */}
            {ticket && (
                <div className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div className={`px-6 py-4 ${ticket.verified_at ? 'bg-green-500' : 'bg-amber-500'} text-white`}>
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-3">
                                {ticket.verified_at ? (
                                    <CheckCircle className="w-6 h-6" />
                                ) : (
                                    <XCircle className="w-6 h-6" />
                                )}
                                <span className="font-semibold text-lg">
                                    {ticket.verified_at ? 'Already Verified' : 'Not Verified'}
                                </span>
                            </div>
                            <span className="text-white/80">
                                Ticket #{ticket.ticket_no || ticket.id}
                            </span>
                        </div>
                    </div>

                    <div className="p-6 space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <p className="text-sm text-slate-500">From Branch</p>
                                <p className="font-semibold text-slate-800">{ticket.branch?.branch_name || '-'}</p>
                            </div>
                            <div>
                                <p className="text-sm text-slate-500">To Branch</p>
                                <p className="font-semibold text-slate-800">{ticket.dest_branch?.branch_name || '-'}</p>
                            </div>
                            <div>
                                <p className="text-sm text-slate-500">Payment Mode</p>
                                <p className="font-semibold text-slate-800">{ticket.payment_mode || '-'}</p>
                            </div>
                            <div>
                                <p className="text-sm text-slate-500">Total Amount</p>
                                <p className="font-semibold text-indigo-600">₹{ticket.total_amount || 0}</p>
                            </div>
                        </div>

                        {ticket.lines && ticket.lines.length > 0 && (
                            <div className="border-t pt-4">
                                <p className="text-sm font-semibold text-slate-500 mb-2">Items</p>
                                <div className="space-y-2">
                                    {ticket.lines.map((line, idx) => (
                                        <div key={idx} className="flex items-center justify-between bg-slate-50 rounded-lg p-3">
                                            <span className="text-slate-700">{line.item_name}</span>
                                            <span className="text-slate-600">x{line.qty} = ₹{line.amount}</span>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {!ticket.verified_at && (
                            <button
                                onClick={handleVerify}
                                className="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold text-lg"
                            >
                                Verify This Ticket
                            </button>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
}

Index.layout = (page) => <Layout title="Verify Ticket">{page}</Layout>;
