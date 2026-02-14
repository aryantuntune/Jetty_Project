import { useState, useEffect, useRef } from 'react';
import { X, Calculator, Banknote, Smartphone, Check, AlertCircle } from 'lucide-react';

/**
 * Cash/GPay Calculator Modal
 * Shows before ticket submission to calculate change for cash and GPay payments.
 *
 * @param {Object} props
 * @param {boolean} props.isOpen - Whether modal is visible
 * @param {function} props.onClose - Close the modal without saving
 * @param {function} props.onConfirm - Confirm and proceed with ticket submission
 * @param {number} props.totalAmount - Total amount due
 * @param {string} props.paymentMode - Payment mode (Cash, GPay, etc.)
 */
export default function CashCalculatorModal({
    isOpen,
    onClose,
    onConfirm,
    totalAmount = 0,
    paymentMode = 'Cash',
}) {
    const [amountReceived, setAmountReceived] = useState('');
    const inputRef = useRef(null);

    // Reset and focus when modal opens
    useEffect(() => {
        if (isOpen) {
            setAmountReceived('');
            setTimeout(() => inputRef.current?.focus(), 100);
        }
    }, [isOpen]);

    if (!isOpen) return null;

    const received = parseFloat(amountReceived) || 0;
    const change = received - totalAmount;
    const isExact = received === totalAmount;
    const isInsufficient = received > 0 && received < totalAmount;
    const hasEntered = amountReceived !== '';

    // Quick denomination buttons for cash
    const denominations = [10, 20, 50, 100, 200, 500];

    const handleQuickAdd = (amount) => {
        setAmountReceived(prev => {
            const current = parseFloat(prev) || 0;
            return String(current + amount);
        });
    };

    const handleConfirm = () => {
        onConfirm();
    };

    const isCash = paymentMode === 'Cash';
    const isGPay = paymentMode === 'GPay' || paymentMode === 'UPI';

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            {/* Backdrop */}
            <div
                className="absolute inset-0 bg-black/50 backdrop-blur-sm"
                onClick={onClose}
            />

            {/* Modal */}
            <div className="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                {/* Header */}
                <div className={`px-6 py-4 flex items-center justify-between ${isCash
                        ? 'bg-gradient-to-r from-emerald-600 to-emerald-700'
                        : 'bg-gradient-to-r from-indigo-600 to-indigo-700'
                    }`}>
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                            {isCash ? (
                                <Banknote className="w-5 h-5 text-white" />
                            ) : (
                                <Smartphone className="w-5 h-5 text-white" />
                            )}
                        </div>
                        <div>
                            <h2 className="text-lg font-semibold text-white">
                                {isCash ? 'Cash' : 'GPay'} Calculator
                            </h2>
                            <p className="text-sm text-white/80">
                                Payment via {paymentMode}
                            </p>
                        </div>
                    </div>
                    <button
                        onClick={onClose}
                        className="w-8 h-8 rounded-lg bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors"
                    >
                        <X className="w-4 h-4 text-white" />
                    </button>
                </div>

                {/* Body */}
                <div className="p-6 space-y-5">
                    {/* Total Amount Display */}
                    <div className="text-center p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <p className="text-sm font-medium text-slate-500 mb-1">Total Amount Due</p>
                        <p className="text-3xl font-bold text-slate-800">
                            ₹{totalAmount.toFixed(2)}
                        </p>
                    </div>

                    {/* Amount Received Input */}
                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-2">
                            Amount Received from Customer
                        </label>
                        <div className="relative">
                            <span className="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-medium text-slate-400">₹</span>
                            <input
                                ref={inputRef}
                                type="number"
                                value={amountReceived}
                                onChange={(e) => setAmountReceived(e.target.value)}
                                placeholder="0.00"
                                min="0"
                                step="any"
                                className="w-full pl-10 pr-4 py-3 text-xl font-semibold border-2 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-right"
                            />
                        </div>
                    </div>

                    {/* Quick denomination buttons (for Cash only) */}
                    {isCash && (
                        <div>
                            <p className="text-xs font-medium text-slate-500 mb-2">Quick Add</p>
                            <div className="grid grid-cols-6 gap-2">
                                {denominations.map((d) => (
                                    <button
                                        key={d}
                                        type="button"
                                        onClick={() => handleQuickAdd(d)}
                                        className="px-2 py-2 text-sm font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-colors"
                                    >
                                        +₹{d}
                                    </button>
                                ))}
                            </div>
                            <button
                                type="button"
                                onClick={() => setAmountReceived(String(totalAmount))}
                                className="mt-2 w-full px-3 py-2 text-sm font-medium bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg transition-colors border border-emerald-200"
                            >
                                Exact Amount (₹{totalAmount.toFixed(2)})
                            </button>
                        </div>
                    )}

                    {/* GPay: just an exact amount button */}
                    {isGPay && (
                        <button
                            type="button"
                            onClick={() => setAmountReceived(String(totalAmount))}
                            className="w-full px-3 py-2.5 text-sm font-medium bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg transition-colors border border-indigo-200"
                        >
                            Exact Amount Received (₹{totalAmount.toFixed(2)})
                        </button>
                    )}

                    {/* Change Display */}
                    {hasEntered && (
                        <div className={`p-4 rounded-xl border-2 ${isInsufficient
                                ? 'bg-red-50 border-red-200'
                                : isExact
                                    ? 'bg-emerald-50 border-emerald-200'
                                    : 'bg-amber-50 border-amber-200'
                            }`}>
                            <div className="flex items-center gap-2 mb-1">
                                {isInsufficient ? (
                                    <AlertCircle className="w-4 h-4 text-red-500" />
                                ) : (
                                    <Calculator className="w-4 h-4 text-emerald-600" />
                                )}
                                <p className={`text-sm font-medium ${isInsufficient ? 'text-red-600' : isExact ? 'text-emerald-600' : 'text-amber-700'
                                    }`}>
                                    {isInsufficient
                                        ? 'Insufficient Amount'
                                        : isExact
                                            ? 'Exact Amount — No Change'
                                            : 'Change to Return'}
                                </p>
                            </div>
                            {!isInsufficient && !isExact && (
                                <p className="text-2xl font-bold text-amber-800">
                                    ₹{change.toFixed(2)}
                                </p>
                            )}
                            {isInsufficient && (
                                <p className="text-sm text-red-600">
                                    Short by ₹{Math.abs(change).toFixed(2)}
                                </p>
                            )}
                        </div>
                    )}
                </div>

                {/* Footer */}
                <div className="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-3">
                    <button
                        type="button"
                        onClick={onClose}
                        className="px-5 py-2.5 text-slate-700 font-medium rounded-xl border border-slate-300 hover:bg-slate-100 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        onClick={handleConfirm}
                        disabled={hasEntered && isInsufficient}
                        className={`inline-flex items-center gap-2 px-6 py-2.5 font-medium rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed ${isCash
                                ? 'bg-emerald-600 hover:bg-emerald-700 text-white'
                                : 'bg-indigo-600 hover:bg-indigo-700 text-white'
                            }`}
                    >
                        <Check className="w-4 h-4" />
                        Confirm & Save Ticket
                    </button>
                </div>
            </div>
        </div>
    );
}
