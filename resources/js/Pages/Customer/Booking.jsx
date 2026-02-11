import { useState, useEffect } from 'react';
import { router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import CustomerLayout from '@/Layouts/CustomerLayout';
import {
    MapPin,
    Calendar,
    Clock,
    Package,
    Plus,
    Trash2,
    ArrowRight,
    Eye,
    X,
    Wallet,
    CheckCircle,
    AlertTriangle,
    Loader2,
} from 'lucide-react';

// Round to 2 decimal places with precision
const roundTo2 = (num) => Math.round((num + Number.EPSILON) * 100) / 100;

export default function Booking({ branches }) {
    const { flash } = usePage().props;
    const [fromBranch, setFromBranch] = useState('');
    const [toBranch, setToBranch] = useState('');
    const [travelDate, setTravelDate] = useState('');
    const [ferryTime, setFerryTime] = useState('');
    const [toBranches, setToBranches] = useState([]);
    const [ferrySchedules, setFerrySchedules] = useState([]);
    const [availableItems, setAvailableItems] = useState([]);
    const [items, setItems] = useState([{ id: 1, item_rate_id: '', quantity: 1, vehicle_no: '', rate: 0, levy: 0 }]);
    const [loading, setLoading] = useState(false);
    const [showPreview, setShowPreview] = useState(false);
    const [showError, setShowError] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');
    const [paymentLoading, setPaymentLoading] = useState(false);

    // Set default date to today
    useEffect(() => {
        const today = new Date().toISOString().split('T')[0];
        setTravelDate(today);
    }, []);

    // Load destination branches when from branch changes
    useEffect(() => {
        if (fromBranch) {
            fetch(`/booking/to-branches/${fromBranch}`)
                .then((res) => res.json())
                .then((data) => {
                    const branches = data || [];
                    setToBranches(branches);
                    // Auto-fill destination if only one option exists
                    if (branches.length === 1) {
                        setToBranch(String(branches[0].id || branches[0].branch_id));
                    } else {
                        setToBranch('');
                    }
                })
                .catch(() => setToBranches([]));

            // Load available items for this branch
            fetch(`/booking/items/${fromBranch}`)
                .then((res) => res.json())
                .then((data) => setAvailableItems(data || []))
                .catch(() => setAvailableItems([]));

            // Load ferry schedules for this branch
            fetch(`/booking/schedules/${fromBranch}`)
                .then((res) => res.json())
                .then((data) => {
                    setFerrySchedules(data || []);
                    setFerryTime(''); // Reset ferry time when branch changes
                })
                .catch(() => setFerrySchedules([]));

            // Reset items when branch changes
            setItems([{ id: 1, item_rate_id: '', quantity: 1, vehicle_no: '', rate: 0, levy: 0 }]);
        }
    }, [fromBranch]);

    // Add new item row
    const addItem = () => {
        const newId = Math.max(...items.map((i) => i.id)) + 1;
        setItems([...items, { id: newId, item_rate_id: '', quantity: 1, vehicle_no: '', rate: 0, levy: 0 }]);
    };

    // Remove item row
    const removeItem = (id) => {
        if (items.length > 1) {
            setItems(items.filter((item) => item.id !== id));
        }
    };

    // Update item field
    const updateItem = (id, field, value) => {
        setItems(
            items.map((item) => {
                if (item.id === id) {
                    const updated = { ...item, [field]: value };

                    // If item_rate_id changed, fetch rate and levy
                    if (field === 'item_rate_id' && value) {
                        fetch(`/booking/item-rate/${value}`)
                            .then((res) => res.json())
                            .then((data) => {
                                setItems((prev) =>
                                    prev.map((i) =>
                                        i.id === id
                                            ? { ...i, rate: data.item_rate || 0, levy: data.item_lavy || 0 }
                                            : i
                                    )
                                );
                            });
                    }

                    return updated;
                }
                return item;
            })
        );
    };

    // Calculate line total
    const calculateLineTotal = (item) => {
        const qty = parseInt(item.quantity) || 0;
        const rate = parseFloat(item.rate) || 0;
        const levy = parseFloat(item.levy) || 0;
        return roundTo2(qty * rate + levy);
    };

    // Calculate grand total
    const grandTotal = items.reduce((sum, item) => sum + calculateLineTotal(item), 0);

    // Get branch name by ID
    const getBranchName = (id, list) => {
        const branch = list.find((b) => b.id == id || b.branch_id == id);
        return branch?.branch_name || branch?.name || 'Unknown';
    };

    // Get item name by ID
    const getItemName = (id) => {
        const item = availableItems.find((i) => i.id == id);
        return item?.item_name || 'Unknown';
    };

    // Validate form before preview
    const validateForm = () => {
        if (!fromBranch) {
            setErrorMessage('Please select a departure point.');
            setShowError(true);
            return false;
        }
        if (!toBranch) {
            setErrorMessage('Please select a destination.');
            setShowError(true);
            return false;
        }
        if (!travelDate) {
            setErrorMessage('Please select a travel date.');
            setShowError(true);
            return false;
        }
        if (!ferryTime) {
            setErrorMessage('Please select a ferry time.');
            setShowError(true);
            return false;
        }

        for (const item of items) {
            if (!item.item_rate_id) {
                setErrorMessage('Please select a description for all items.');
                setShowError(true);
                return false;
            }
            if (!item.quantity || item.quantity <= 0) {
                setErrorMessage('Please enter a valid quantity for all items.');
                setShowError(true);
                return false;
            }
        }

        return true;
    };

    // Show preview modal
    const handlePreview = () => {
        if (validateForm()) {
            setShowPreview(true);
        }
    };

    // Handle payment (Razorpay integration)
    const handlePayment = async () => {
        setPaymentLoading(true);

        try {
            const response = await fetch(route('payment.createOrder'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                },
                body: JSON.stringify({
                    grand_total: grandTotal,
                    from_branch: fromBranch,
                    to_branch: toBranch,
                    date: travelDate,
                    departure_time: ferryTime,
                    items: items.map((item) => ({
                        item_rate_id: item.item_rate_id,
                        quantity: item.quantity,
                        rate: item.rate,
                        lavy: item.levy,
                        vehicle_no: item.vehicle_no,
                        total: calculateLineTotal(item),
                    })),
                }),
            });

            const data = await response.json();

            // Check for server-side errors
            if (!response.ok || data.error) {
                throw new Error(data.error || data.message || 'Payment creation failed');
            }

            if (!data.key || !data.order_id) {
                throw new Error('Invalid payment response from server');
            }

            const options = {
                key: data.key,
                amount: data.amount,
                currency: 'INR',
                name: 'Jetty - Ferry Booking',
                description: 'Online Ferry Ticket',
                order_id: data.order_id,
                handler: async (response) => {
                    // Verify payment
                    await fetch(route('payment.verify'), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        },
                        body: JSON.stringify(response),
                    });
                    window.location.href = route('customer.dashboard') + '?success=1';
                },
                modal: {
                    ondismiss: () => {
                        setPaymentLoading(false);
                    },
                },
                theme: { color: '#0ea5e9' },
            };

            const rzp = new window.Razorpay(options);
            rzp.open();
        } catch (error) {
            console.error('Payment error:', error);
            setErrorMessage(error.message || 'Payment failed. Please try again.');
            setShowError(true);
            setPaymentLoading(false);
        }
    };

    return (
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            {/* Page Header */}
            <div className="text-center mb-8">
                <h1 className="text-3xl md:text-4xl font-bold text-slate-800 mb-2">Book Your Ferry</h1>
                <p className="text-slate-500">Select your route and items to begin your journey</p>
            </div>

            {/* Success Message */}
            {(flash?.success || new URLSearchParams(window.location.search).get('success')) && (
                <div className="mb-6 p-4 rounded-2xl bg-green-50 border border-green-200 flex items-center gap-3">
                    <div className="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                        <CheckCircle className="w-5 h-5 text-green-600" />
                    </div>
                    <p className="text-green-700 font-medium">Booking successful! Check your booking history for details.</p>
                </div>
            )}

            {/* Booking Form Card */}
            <div className="bg-white/95 backdrop-blur-xl rounded-3xl shadow-xl border border-sky-100 overflow-hidden">
                {/* Route Selection Section */}
                <div className="p-6 md:p-8 border-b border-slate-100">
                    <div className="flex items-center gap-3 mb-6">
                        <div className="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center">
                            <MapPin className="w-5 h-5 text-sky-600" />
                        </div>
                        <h2 className="text-xl font-bold text-slate-800">Select Route</h2>
                    </div>

                    <div className="grid md:grid-cols-3 gap-6">
                        {/* From Branch */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">Departure</label>
                            <select
                                value={fromBranch}
                                onChange={(e) => setFromBranch(e.target.value)}
                                className="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all"
                                required
                            >
                                <option value="">Select departure point</option>
                                {branches?.map((branch) => (
                                    <option key={branch.id || branch.branch_id} value={branch.id || branch.branch_id}>
                                        {branch.branch_name || branch.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Route Visualization */}
                        <div className="hidden md:flex items-end justify-center pb-4">
                            <div className="flex items-center gap-2">
                                <div className="w-3 h-3 rounded-full bg-sky-500" />
                                <div className="w-16 h-0.5 bg-gradient-to-r from-sky-500 to-amber-500" />
                                <div className="w-3 h-3 rounded-full bg-amber-500" />
                            </div>
                        </div>

                        {/* To Branch */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">Destination</label>
                            {toBranches.length === 1 ? (
                                // Show as read-only input when only one destination
                                <input
                                    type="text"
                                    value={toBranches[0]?.branch_name || toBranches[0]?.name || ''}
                                    readOnly
                                    className="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-medium cursor-not-allowed"
                                />
                            ) : (
                                // Show dropdown when multiple destinations
                                <select
                                    value={toBranch}
                                    onChange={(e) => setToBranch(e.target.value)}
                                    className="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all"
                                    required
                                >
                                    <option value="">Select destination</option>
                                    {toBranches.map((branch) => (
                                        <option key={branch.id || branch.branch_id} value={branch.id || branch.branch_id}>
                                            {branch.branch_name || branch.name}
                                        </option>
                                    ))}
                                </select>
                            )}
                        </div>
                    </div>

                    {/* Travel Date & Ferry Time */}
                    <div className="mt-6 grid md:grid-cols-2 gap-6">
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">Travel Date</label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <Calendar className="w-5 h-5 text-slate-400" />
                                </div>
                                <input
                                    type="date"
                                    value={travelDate}
                                    onChange={(e) => setTravelDate(e.target.value)}
                                    min={new Date().toISOString().split('T')[0]}
                                    className="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all cursor-pointer"
                                    required
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">Ferry Time</label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <Clock className="w-5 h-5 text-slate-400" />
                                </div>
                                <select
                                    value={ferryTime}
                                    onChange={(e) => setFerryTime(e.target.value)}
                                    disabled={!fromBranch || ferrySchedules.length === 0}
                                    className="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all disabled:bg-slate-100 disabled:cursor-not-allowed"
                                    required
                                >
                                    <option value="">Select ferry time</option>
                                    {ferrySchedules.map((schedule, index) => (
                                        <option key={index} value={schedule.schedule_time}>
                                            {schedule.schedule_time}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            {fromBranch && ferrySchedules.length === 0 && (
                                <p className="mt-1 text-xs text-amber-600">No schedules available for this departure point</p>
                            )}
                        </div>
                    </div>
                </div>

                {/* Items Section */}
                <div className="p-6 md:p-8 bg-slate-50/50">
                    <div className="flex items-center justify-between mb-6">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                                <Package className="w-5 h-5 text-amber-600" />
                            </div>
                            <h2 className="text-xl font-bold text-slate-800">Items & Passengers</h2>
                        </div>
                        <button
                            type="button"
                            onClick={addItem}
                            className="flex items-center gap-2 px-4 py-2 rounded-xl bg-green-500 text-white font-medium hover:bg-green-600 transition-colors shadow-lg shadow-green-500/20"
                        >
                            <Plus className="w-4 h-4" />
                            <span>Add Item</span>
                        </button>
                    </div>

                    {/* Items Container */}
                    <div className="space-y-4">
                        {items.map((item) => (
                            <div
                                key={item.id}
                                className="bg-white rounded-2xl border border-slate-200 p-4 animate-slide-up"
                            >
                                <div className="grid grid-cols-12 gap-4">
                                    {/* Description */}
                                    <div className="col-span-12 md:col-span-3">
                                        <label className="block text-xs font-medium text-slate-500 mb-1">
                                            Description
                                        </label>
                                        <select
                                            value={item.item_rate_id}
                                            onChange={(e) => updateItem(item.id, 'item_rate_id', e.target.value)}
                                            className="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-sm"
                                            required
                                        >
                                            <option value="">Select item</option>
                                            {availableItems.map((opt) => (
                                                <option key={opt.id} value={opt.id}>
                                                    {opt.item_name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>

                                    {/* Quantity */}
                                    <div className="col-span-4 md:col-span-1">
                                        <label className="block text-xs font-medium text-slate-500 mb-1">Qty</label>
                                        <input
                                            type="number"
                                            value={item.quantity}
                                            onChange={(e) => updateItem(item.id, 'quantity', e.target.value)}
                                            min="1"
                                            className="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-sm text-center"
                                            required
                                        />
                                    </div>

                                    {/* Vehicle No */}
                                    <div className="col-span-8 md:col-span-2">
                                        <label className="block text-xs font-medium text-slate-500 mb-1">
                                            Vehicle No
                                        </label>
                                        <input
                                            type="text"
                                            value={item.vehicle_no}
                                            onChange={(e) =>
                                                updateItem(item.id, 'vehicle_no', e.target.value.toUpperCase())
                                            }
                                            placeholder="MH-00-XX-0000"
                                            className="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-sm uppercase"
                                        />
                                    </div>

                                    {/* Rate */}
                                    <div className="col-span-4 md:col-span-2">
                                        <label className="block text-xs font-medium text-slate-500 mb-1">Rate</label>
                                        <input
                                            type="number"
                                            value={item.rate}
                                            readOnly
                                            className="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm"
                                        />
                                    </div>

                                    {/* Levy */}
                                    <div className="col-span-4 md:col-span-1">
                                        <label className="block text-xs font-medium text-slate-500 mb-1">Levy</label>
                                        <input
                                            type="number"
                                            value={item.levy}
                                            readOnly
                                            className="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm"
                                        />
                                    </div>

                                    {/* Total */}
                                    <div className="col-span-4 md:col-span-2">
                                        <label className="block text-xs font-medium text-slate-500 mb-1">Total</label>
                                        <input
                                            type="text"
                                            value={`₹${calculateLineTotal(item).toFixed(2)}`}
                                            readOnly
                                            className="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-sky-50 text-sky-700 font-semibold text-sm"
                                        />
                                    </div>

                                    {/* Delete Button */}
                                    <div className="col-span-12 md:col-span-1 flex items-end justify-end">
                                        <button
                                            type="button"
                                            onClick={() => removeItem(item.id)}
                                            disabled={items.length === 1}
                                            className="p-2.5 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <Trash2 className="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Grand Total */}
                    <div className="mt-6 p-4 rounded-2xl bg-sky-50 border border-sky-100">
                        <div className="flex items-center justify-between">
                            <span className="text-slate-600 font-medium">Grand Total</span>
                            <span className="text-2xl font-bold text-sky-600">
                                <span className="text-lg">₹</span> {grandTotal.toFixed(2)}
                            </span>
                        </div>
                    </div>
                </div>

                {/* Submit Section */}
                <div className="p-6 md:p-8 border-t border-slate-100">
                    <button
                        type="button"
                        onClick={handlePreview}
                        className="group w-full py-4 rounded-xl bg-gradient-to-r from-amber-400 to-orange-500 font-bold text-slate-900 text-lg flex items-center justify-center gap-2 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-amber-500/30 transition-all duration-300"
                    >
                        <Eye className="w-5 h-5" />
                        <span>Review & Submit Booking</span>
                        <ArrowRight className="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                    </button>
                </div>
            </div>

            {/* Preview Modal */}
            {showPreview && (
                <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                    <div className="bg-white rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden animate-scale-up">
                        {/* Header */}
                        <div className="p-6 border-b border-slate-100 bg-gradient-to-r from-sky-500 to-sky-600">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-3">
                                    <div className="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                        <Eye className="w-5 h-5 text-white" />
                                    </div>
                                    <h2 className="text-xl font-bold text-white">Review Your Booking</h2>
                                </div>
                                <button
                                    onClick={() => setShowPreview(false)}
                                    className="p-2 rounded-lg hover:bg-white/10 transition-colors"
                                >
                                    <X className="w-5 h-5 text-white" />
                                </button>
                            </div>
                        </div>

                        {/* Body */}
                        <div className="p-6 overflow-y-auto max-h-[60vh]">
                            {/* Route Info */}
                            <div className="mb-6">
                                <h3 className="text-sm font-medium text-slate-500 mb-2">Ferry Route</h3>
                                <p className="text-lg font-semibold text-slate-800">
                                    <span className="text-sky-600">{getBranchName(fromBranch, branches)}</span>
                                    <span className="text-slate-400 mx-2">→</span>
                                    <span className="text-amber-600">{getBranchName(toBranch, toBranches)}</span>
                                </p>
                            </div>

                            {/* Travel Date & Time */}
                            <div className="mb-6 grid grid-cols-2 gap-4">
                                <div>
                                    <h3 className="text-sm font-medium text-slate-500 mb-2">Travel Date</h3>
                                    <p className="text-lg font-semibold text-slate-800">{travelDate}</p>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium text-slate-500 mb-2">Ferry Time</h3>
                                    <p className="text-lg font-semibold text-slate-800">{ferryTime}</p>
                                </div>
                            </div>

                            {/* Items Table */}
                            <div className="mb-6">
                                <h3 className="text-sm font-medium text-slate-500 mb-3">Items</h3>
                                <div className="overflow-x-auto rounded-xl border border-slate-200">
                                    <table className="w-full">
                                        <thead className="bg-slate-50">
                                            <tr>
                                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">
                                                    Description
                                                </th>
                                                <th className="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase">
                                                    Qty
                                                </th>
                                                <th className="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase">
                                                    Rate
                                                </th>
                                                <th className="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase">
                                                    Levy
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">
                                                    Vehicle No.
                                                </th>
                                                <th className="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase">
                                                    Total
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-slate-100">
                                            {items.map((item) => (
                                                <tr key={item.id} className="hover:bg-slate-50">
                                                    <td className="px-4 py-3 text-sm text-slate-800">
                                                        {getItemName(item.item_rate_id)}
                                                    </td>
                                                    <td className="px-4 py-3 text-sm text-slate-600 text-center">
                                                        {item.quantity}
                                                    </td>
                                                    <td className="px-4 py-3 text-sm text-slate-600 text-right">
                                                        ₹{item.rate}
                                                    </td>
                                                    <td className="px-4 py-3 text-sm text-slate-600 text-right">
                                                        ₹{item.levy}
                                                    </td>
                                                    <td className="px-4 py-3 text-sm text-slate-600">
                                                        {item.vehicle_no || '-'}
                                                    </td>
                                                    <td className="px-4 py-3 text-sm font-semibold text-slate-800 text-right">
                                                        ₹{calculateLineTotal(item).toFixed(2)}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {/* Grand Total */}
                            <div className="p-4 rounded-2xl bg-sky-50 border border-sky-100">
                                <div className="flex items-center justify-between">
                                    <span className="text-slate-600 font-medium">Grand Total</span>
                                    <span className="text-2xl font-bold text-sky-600">₹{grandTotal.toFixed(2)}</span>
                                </div>
                            </div>
                        </div>

                        {/* Footer */}
                        <div className="p-6 border-t border-slate-100 bg-slate-50 flex flex-col sm:flex-row gap-3">
                            <button
                                onClick={() => setShowPreview(false)}
                                className="flex-1 py-3 rounded-xl border border-slate-300 text-slate-700 font-medium hover:bg-slate-100 transition-colors"
                            >
                                Edit Booking
                            </button>
                            <button
                                onClick={handlePayment}
                                disabled={paymentLoading}
                                className="flex-1 py-3 rounded-xl bg-gradient-to-r from-amber-400 to-orange-500 font-bold text-slate-900 flex items-center justify-center gap-2 disabled:opacity-50"
                            >
                                {paymentLoading ? (
                                    <Loader2 className="w-5 h-5 animate-spin" />
                                ) : (
                                    <>
                                        <Wallet className="w-5 h-5" />
                                        <span>Proceed to Pay</span>
                                    </>
                                )}
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Error Modal */}
            {showError && (
                <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                    <div className="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 text-center animate-scale-up">
                        <div className="w-16 h-16 mx-auto rounded-2xl bg-red-100 flex items-center justify-center mb-4">
                            <AlertTriangle className="w-8 h-8 text-red-600" />
                        </div>
                        <h2 className="text-xl font-bold text-slate-800 mb-2">Missing Details</h2>
                        <p className="text-slate-600 mb-6">{errorMessage}</p>
                        <button
                            onClick={() => setShowError(false)}
                            className="w-full py-3 rounded-xl bg-red-500 text-white font-medium hover:bg-red-600 transition-colors"
                        >
                            OK, Got it
                        </button>
                    </div>
                </div>
            )}

            <style>{`
                .animate-slide-up {
                    animation: slideUp 0.3s ease-out forwards;
                }
                .animate-scale-up {
                    animation: scaleUp 0.3s ease-out forwards;
                }
                @keyframes slideUp {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                @keyframes scaleUp {
                    from { opacity: 0; transform: scale(0.95); }
                    to { opacity: 1; transform: scale(1); }
                }
            `}</style>
        </div>
    );
}

Booking.layout = (page) => <CustomerLayout title="Book Your Ferry">{page}</CustomerLayout>;
