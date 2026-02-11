import { useEffect, useRef } from 'react';
import { QRCodeSVG } from 'qrcode.react';

export default function Print({ ticket }) {
    const printRef = useRef();

    useEffect(() => {
        // Auto-print when page loads
        const timer = setTimeout(() => {
            window.print();
        }, 500);
        return () => clearTimeout(timer);
    }, []);

    const formatDate = (date) => {
        if (!date) return '-';
        return new Date(date).toLocaleDateString('en-IN', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        });
    };

    const formatTime = (time) => {
        if (!time) return '-';
        return time;
    };

    return (
        <div className="min-h-screen bg-gray-100 py-4 print:bg-white print:py-0">
            <div
                ref={printRef}
                className="max-w-[80mm] mx-auto bg-white p-4 shadow-lg print:shadow-none print:max-w-full"
                style={{ fontFamily: 'monospace' }}
            >
                {/* Header */}
                <div className="text-center border-b border-dashed border-gray-400 pb-3 mb-3">
                    <h1 className="text-lg font-bold">JETTY TICKET</h1>
                    <p className="text-sm text-gray-600">Ferry Service</p>
                </div>

                {/* Ticket Info */}
                <div className="border-b border-dashed border-gray-400 pb-3 mb-3">
                    <div className="flex justify-between text-sm mb-1">
                        <span className="text-gray-600">Ticket No:</span>
                        <span className="font-bold">#{ticket?.ticket_no || ticket?.id}</span>
                    </div>
                    <div className="flex justify-between text-sm mb-1">
                        <span className="text-gray-600">Date:</span>
                        <span>{formatDate(ticket?.ticket_date || ticket?.created_at)}</span>
                    </div>
                    <div className="flex justify-between text-sm">
                        <span className="text-gray-600">Time:</span>
                        <span>{ticket?.ferry_time || '-'}</span>
                    </div>
                </div>

                {/* Route */}
                <div className="border-b border-dashed border-gray-400 pb-3 mb-3">
                    <div className="flex justify-between items-center">
                        <div className="text-center flex-1">
                            <p className="text-xs text-gray-500">FROM</p>
                            <p className="font-bold text-sm">{ticket?.branch?.branch_name || '-'}</p>
                        </div>
                        <div className="px-2">
                            <span className="text-lg">→</span>
                        </div>
                        <div className="text-center flex-1">
                            <p className="text-xs text-gray-500">TO</p>
                            <p className="font-bold text-sm">{ticket?.dest_branch?.branch_name || '-'}</p>
                        </div>
                    </div>
                </div>

                {/* Items */}
                {ticket?.lines && ticket.lines.length > 0 && (
                    <div className="border-b border-dashed border-gray-400 pb-3 mb-3">
                        <p className="text-xs text-gray-500 mb-2">ITEMS</p>
                        <table className="w-full text-xs">
                            <thead>
                                <tr className="border-b border-gray-200">
                                    <th className="text-left pb-1">Item</th>
                                    <th className="text-center pb-1">Qty</th>
                                    <th className="text-right pb-1">Amt</th>
                                </tr>
                            </thead>
                            <tbody>
                                {ticket.lines.map((line, idx) => (
                                    <tr key={idx}>
                                        <td className="py-1">{line.item_name}</td>
                                        <td className="text-center">{line.qty}</td>
                                        <td className="text-right">₹{line.amount}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}

                {/* Total */}
                <div className="border-b border-dashed border-gray-400 pb-3 mb-3">
                    <div className="flex justify-between items-center">
                        <span className="font-bold">TOTAL</span>
                        <span className="text-xl font-bold">₹{ticket?.total_amount || 0}</span>
                    </div>
                    <div className="flex justify-between text-sm text-gray-600 mt-1">
                        <span>Payment:</span>
                        <span>{ticket?.payment_mode || 'Cash'}</span>
                    </div>
                </div>

                {/* QR Code */}
                <div className="text-center py-3">
                    <div className="inline-block">
                        <QRCodeSVG
                            value={ticket?.qr_hash || ticket?.id?.toString() || 'TICKET'}
                            size={80}
                        />
                    </div>
                    <p className="text-xs text-gray-500 mt-2">Scan for verification</p>
                </div>

                {/* Footer */}
                <div className="text-center border-t border-dashed border-gray-400 pt-3">
                    <p className="text-xs text-gray-500">Thank you for travelling with us!</p>
                    <p className="text-xs text-gray-400 mt-1">Operator: {ticket?.user?.name || '-'}</p>
                </div>
            </div>

            {/* Print button for screen */}
            <div className="text-center mt-4 print:hidden">
                <button
                    onClick={() => window.print()}
                    className="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                >
                    Print Ticket
                </button>
            </div>

            <style>{`
                @media print {
                    @page {
                        size: 80mm auto;
                        margin: 0;
                    }
                    body {
                        print-color-adjust: exact;
                        -webkit-print-color-adjust: exact;
                    }
                }
            `}</style>
        </div>
    );
}

// No layout for print page - it should be standalone
