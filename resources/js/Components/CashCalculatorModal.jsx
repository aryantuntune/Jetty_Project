import { useState, useEffect, useRef, useMemo } from 'react';
import { X, Calculator, Banknote, Smartphone, Check, AlertCircle, User, Phone, Search, CreditCard, MessageSquare } from 'lucide-react';

/**
 * Checkout Modal (formerly Cash Calculator)
 * Step 1: Customer info + Payment mode
 * Step 2: Cash/GPay calculator
 * Then confirm to save ticket.
 */
export default function CashCalculatorModal({
    isOpen,
    onClose,
    onConfirm,
    totalAmount = 0,
    // Passenger & payment props
    customerName,
    onCustomerNameChange,
    customerMobile,
    onCustomerMobileChange,
    paymentMode,
    onPaymentModeChange,
    paymentModes = [],
    guestId,
    onGuestIdChange,
    guests = [],
    remarks,
    onRemarksChange,
}) {
    const [amountReceived, setAmountReceived] = useState('');
    const [guestSearch, setGuestSearch] = useState('');
    const inputRef = useRef(null);
    const guestSearchRef = useRef(null);

    // Reset state when modal opens
    useEffect(() => {
        if (isOpen) {
            setAmountReceived('');
            setGuestSearch('');
        }
    }, [isOpen]);

    if (!isOpen) return null;

    const received = parseFloat(amountReceived) || 0;
    const change = received - totalAmount;
    const isExact = received === totalAmount;
    const isInsufficient = received > 0 && received < totalAmount;
    const hasEntered = amountReceived !== '';

    const isCash = paymentMode === 'Cash';
    const isGPay = paymentMode === 'GPay' || paymentMode === 'UPI';
    const isGuestPass = paymentMode === 'Guest Pass';
    const needsCalculator = isCash || isGPay;

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

    // Ensure guests is always an array (PHP may serialize empty Collections as {})
    const safeGuests = Array.isArray(guests) ? guests : (guests && typeof guests === 'object' ? Object.values(guests) : []);

    // Filter guests by search
    const filteredGuests = useMemo(() => {
        if (!guestSearch.trim()) return safeGuests;
        const q = guestSearch.toLowerCase();
        return safeGuests.filter(g =>
            g.name?.toLowerCase().includes(q) ||
            g.category?.name?.toLowerCase().includes(q)
        );
    }, [safeGuests, guestSearch]);

    // Determine accent color based on payment mode
    const accentColor = isCash ? 'emerald' : isGPay ? 'indigo' : isGuestPass ? 'purple' : 'slate';

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            {/* Backdrop */}
            <div
                className="absolute inset-0 bg-black/50 backdrop-blur-sm"
                onClick={onClose}
            />

            {/* Modal */}
            <div className="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200 max-h-[90vh] flex flex-col">
                {/* Header */}
                <div className={`px-6 py-4 flex items-center justify-between flex-shrink-0 ${isCash
                    ? 'bg-gradient-to-r from-emerald-600 to-emerald-700'
                    : isGPay
                        ? 'bg-gradient-to-r from-indigo-600 to-indigo-700'
                        : isGuestPass
                            ? 'bg-gradient-to-r from-purple-600 to-purple-700'
                            : 'bg-gradient-to-r from-slate-600 to-slate-700'
                    }`}>
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                            <CreditCard className="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h2 className="text-lg font-semibold text-white">Checkout</h2>
                            <p className="text-sm text-white/80">
                                Total: ₹{totalAmount.toFixed(2)}
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

                {/* Scrollable Body */}
                <div className="overflow-y-auto flex-1 p-5 space-y-5">

                    {/* Customer Info Section */}
                    <div className="space-y-3">
                        <div className="flex items-center gap-2 text-sm font-semibold text-slate-500 uppercase tracking-wider">
                            <User className="w-4 h-4" />
                            Passenger Info
                        </div>

                        <div className="grid grid-cols-2 gap-3">
                            {/* Customer Name */}
                            <div>
                                <label className="block text-xs font-medium text-slate-500 mb-1">Name</label>
                                <input
                                    type="text"
                                    value={customerName}
                                    onChange={(e) => onCustomerNameChange(e.target.value)}
                                    placeholder="Optional"
                                    className="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none"
                                />
                            </div>

                            {/* Mobile */}
                            <div>
                                <label className="block text-xs font-medium text-slate-500 mb-1">Mobile</label>
                                <input
                                    type="tel"
                                    value={customerMobile}
                                    onChange={(e) => onCustomerMobileChange(e.target.value)}
                                    placeholder="Optional"
                                    className="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none"
                                />
                            </div>
                        </div>

                        {/* Remarks */}
                        <div>
                            <label className="block text-xs font-medium text-slate-500 mb-1">Remarks</label>
                            <input
                                type="text"
                                value={remarks}
                                onChange={(e) => onRemarksChange(e.target.value)}
                                placeholder="Optional remarks..."
                                className="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none"
                            />
                        </div>
                    </div>

                    {/* Divider */}
                    <hr className="border-slate-200" />

                    {/* Payment Mode Section */}
                    <div className="space-y-3">
                        <div className="flex items-center gap-2 text-sm font-semibold text-slate-500 uppercase tracking-wider">
                            <CreditCard className="w-4 h-4" />
                            Payment
                        </div>

                        {/* Payment Mode Tabs */}
                        <div className="flex flex-wrap gap-2">
                            {paymentModes?.map((pm, idx) => (
                                <button
                                    key={idx}
                                    type="button"
                                    onClick={() => {
                                        onPaymentModeChange(pm);
                                        if (pm !== 'Guest Pass') onGuestIdChange('');
                                        setAmountReceived('');
                                    }}
                                    className={`px-4 py-2 text-sm font-medium rounded-lg transition-all border ${paymentMode === pm
                                        ? pm === 'Cash'
                                            ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm'
                                            : pm === 'GPay' || pm === 'UPI'
                                                ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm'
                                                : pm === 'Guest Pass'
                                                    ? 'bg-purple-600 text-white border-purple-600 shadow-sm'
                                                    : 'bg-slate-700 text-white border-slate-700 shadow-sm'
                                        : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
                                        }`}
                                >
                                    {pm}
                                </button>
                            ))}
                        </div>

                        {/* Guest Pass: Guest Selection with Search */}
                        {isGuestPass && (
                            <div className="space-y-2">
                                <label className="block text-xs font-medium text-slate-500">
                                    Select Guest <span className="text-red-500">*</span>
                                </label>
                                {/* Search */}
                                <div className="relative">
                                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                                    <input
                                        ref={guestSearchRef}
                                        type="text"
                                        value={guestSearch}
                                        onChange={(e) => setGuestSearch(e.target.value)}
                                        placeholder="Search guests..."
                                        className="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 outline-none"
                                    />
                                </div>
                                {/* Guest list */}
                                <div className="max-h-32 overflow-y-auto border border-slate-200 rounded-lg divide-y divide-slate-100">
                                    {filteredGuests.length > 0 ? (
                                        filteredGuests.map((guest) => (
                                            <button
                                                key={guest.id}
                                                type="button"
                                                onClick={() => onGuestIdChange(String(guest.id))}
                                                className={`w-full text-left px-3 py-2 text-sm transition-colors ${String(guestId) === String(guest.id)
                                                    ? 'bg-purple-50 text-purple-700 font-medium'
                                                    : 'hover:bg-slate-50 text-slate-700'
                                                    }`}
                                            >
                                                {guest.name}
                                                {guest.category?.name && (
                                                    <span className="ml-2 text-xs text-slate-400">({guest.category.name})</span>
                                                )}
                                            </button>
                                        ))
                                    ) : (
                                        <p className="px-3 py-3 text-sm text-slate-400 text-center">
                                            {safeGuests.length === 0 ? 'No guests found. Add guests in Guest Master.' : 'No matches'}
                                        </p>
                                    )}
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Calculator Section — only for Cash / GPay */}
                    {needsCalculator && (
                        <>
                            <hr className="border-slate-200" />

                            <div className="space-y-3">
                                <div className="flex items-center gap-2 text-sm font-semibold text-slate-500 uppercase tracking-wider">
                                    <Calculator className="w-4 h-4" />
                                    {isCash ? 'Cash' : 'GPay'} Calculator
                                </div>

                                {/* Total Amount Display */}
                                <div className="text-center p-3 bg-slate-50 rounded-xl border border-slate-200">
                                    <p className="text-xs font-medium text-slate-500 mb-1">Amount Due</p>
                                    <p className="text-2xl font-bold text-slate-800">
                                        ₹{totalAmount.toFixed(2)}
                                    </p>
                                </div>

                                {/* Amount Received Input */}
                                <div>
                                    <label className="block text-xs font-medium text-slate-500 mb-1">
                                        Amount Received
                                    </label>
                                    <div className="relative">
                                        <span className="absolute left-3 top-1/2 -translate-y-1/2 text-lg font-medium text-slate-400">₹</span>
                                        <input
                                            ref={inputRef}
                                            type="number"
                                            value={amountReceived}
                                            onChange={(e) => setAmountReceived(e.target.value)}
                                            placeholder="0.00"
                                            min="0"
                                            step="any"
                                            className="w-full pl-9 pr-4 py-2.5 text-lg font-semibold border-2 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-right"
                                        />
                                    </div>
                                </div>

                                {/* Quick denomination buttons (for Cash only) */}
                                {isCash && (
                                    <div>
                                        <div className="grid grid-cols-6 gap-1.5">
                                            {denominations.map((d) => (
                                                <button
                                                    key={d}
                                                    type="button"
                                                    onClick={() => handleQuickAdd(d)}
                                                    className="px-2 py-1.5 text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-colors"
                                                >
                                                    +₹{d}
                                                </button>
                                            ))}
                                        </div>
                                        <button
                                            type="button"
                                            onClick={() => setAmountReceived(String(totalAmount))}
                                            className="mt-2 w-full px-3 py-1.5 text-sm font-medium bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg transition-colors border border-emerald-200"
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
                                        className="w-full px-3 py-2 text-sm font-medium bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg transition-colors border border-indigo-200"
                                    >
                                        Exact Amount Received (₹{totalAmount.toFixed(2)})
                                    </button>
                                )}

                                {/* Change Display */}
                                {hasEntered && (
                                    <div className={`p-3 rounded-xl border-2 ${isInsufficient
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
                                            <p className="text-xl font-bold text-amber-800">
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
                        </>
                    )}
                </div>

                {/* Footer */}
                <div className="px-5 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-3 flex-shrink-0">
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
                        disabled={(needsCalculator && hasEntered && isInsufficient) || (isGuestPass && !guestId)}
                        className={`inline-flex items-center gap-2 px-6 py-2.5 font-medium rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed ${isCash
                            ? 'bg-emerald-600 hover:bg-emerald-700 text-white'
                            : isGPay
                                ? 'bg-indigo-600 hover:bg-indigo-700 text-white'
                                : isGuestPass
                                    ? 'bg-purple-600 hover:bg-purple-700 text-white'
                                    : 'bg-slate-700 hover:bg-slate-800 text-white'
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
