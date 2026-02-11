import { useState, useEffect, useCallback, useMemo } from 'react';
import { router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import {
    Ship,
    MapPin,
    Anchor,
    Clock,
    User,
    Phone,
    Plus,
    Trash2,
    Save,
    CreditCard,
    Printer,
    AlertCircle,
    X,
    Search,
    CheckCircle,
    DollarSign,
    Car,
} from 'lucide-react';

// Currency formatter for Indian Rupees
const formatCurrency = (amount) => {
    const num = parseFloat(amount) || 0;
    return num.toFixed(2);
};

// Round to 2 decimal places to avoid floating point issues
const roundTo2 = (num) => Math.round((num + Number.EPSILON) * 100) / 100;

export default function TicketEntry({
    branches,
    branchId,
    branchName,
    ferryboatsBranch,
    ferryBoatsPerBranch,
    ferrySchedulesPerBranch,
    paymentModes,
    nextFerryTime,
    user,
    hideFerryTime,
    beforeFirstFerry,
    auth,
}) {
    const { flash } = usePage().props;
    const isAdmin = [1, 2].includes(user?.role_id);

    // Form state
    const [selectedBranchId, setSelectedBranchId] = useState(branchId || '');
    const [ferryBoatId, setFerryBoatId] = useState('');
    const [ferryTime, setFerryTime] = useState('');
    const [customerName, setCustomerName] = useState('');
    const [customerMobile, setCustomerMobile] = useState('');
    const [guestId, setGuestId] = useState('');
    const [paymentMode, setPaymentMode] = useState('Cash');
    const [discountPct, setDiscountPct] = useState(0);
    const [discountRs, setDiscountRs] = useState(0);
    const [printAfterSave, setPrintAfterSave] = useState(false);

    // Line items
    const [lines, setLines] = useState([createEmptyLine(0)]);
    const [cachedItems, setCachedItems] = useState([]);
    const [itemsLoading, setItemsLoading] = useState(false);

    // Modals
    const [showPaymentModal, setShowPaymentModal] = useState(false);
    const [showGuestModal, setShowGuestModal] = useState(false);
    const [givenAmount, setGivenAmount] = useState('');

    // Guest search
    const [guestSearchId, setGuestSearchId] = useState('');
    const [guestSearchName, setGuestSearchName] = useState('');
    const [guestResults, setGuestResults] = useState([]);
    const [selectedGuestId, setSelectedGuestId] = useState('');

    // Submission state
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [errors, setErrors] = useState({});
    const [successMessage, setSuccessMessage] = useState('');

    // Get available ferries and schedules for selected branch
    const availableFerries = useMemo(() => {
        if (isAdmin && ferryBoatsPerBranch) {
            return ferryBoatsPerBranch[selectedBranchId] || [];
        }
        return ferryboatsBranch || [];
    }, [isAdmin, selectedBranchId, ferryBoatsPerBranch, ferryboatsBranch]);

    const availableSchedules = useMemo(() => {
        if (isAdmin && ferrySchedulesPerBranch) {
            return ferrySchedulesPerBranch[selectedBranchId] || [];
        }
        return [];
    }, [isAdmin, selectedBranchId, ferrySchedulesPerBranch]);

    // Create empty line item
    function createEmptyLine(index) {
        return {
            id: Date.now() + index,
            item_id: '',
            item_name: '',
            qty: 1,
            rate: 0,
            levy: 0,
            amount: 0,
            vehicle_name: '',
            vehicle_no: '',
            is_vehicle: false,
        };
    }

    // Load items when branch changes
    useEffect(() => {
        if (selectedBranchId) {
            loadItemsForBranch(selectedBranchId);
        }
    }, [selectedBranchId]);

    // Set initial ferry boat when ferries load
    useEffect(() => {
        if (availableFerries.length > 0 && !ferryBoatId) {
            setFerryBoatId(availableFerries[0].id);
        }
    }, [availableFerries, ferryBoatId]);

    // Load items from API
    const loadItemsForBranch = async (branchId) => {
        setItemsLoading(true);
        try {
            const response = await fetch(`/ajax/item-rates/list?branch_id=${branchId}`, {
                headers: { Accept: 'application/json' },
            });
            if (response.ok) {
                const data = await response.json();
                setCachedItems(data);
            }
        } catch (error) {
            console.error('Error loading items:', error);
        }
        setItemsLoading(false);
    };

    // Calculate line amount
    const calculateLineAmount = (qty, rate, levy) => {
        const qtyNum = parseFloat(qty) || 0;
        const rateNum = parseFloat(rate) || 0;
        const levyNum = parseFloat(levy) || 0;
        return roundTo2(qtyNum * (rateNum + levyNum));
    };

    // Calculate subtotal
    const subtotal = useMemo(() => {
        return roundTo2(lines.reduce((sum, line) => sum + (parseFloat(line.amount) || 0), 0));
    }, [lines]);

    // Calculate net total
    const netTotal = useMemo(() => {
        let net = subtotal;
        const dPct = parseFloat(discountPct) || 0;
        const dRs = parseFloat(discountRs) || 0;
        if (dPct > 0) net -= roundTo2((subtotal * dPct) / 100);
        if (dRs > 0) net -= dRs;
        return roundTo2(Math.max(net, 0));
    }, [subtotal, discountPct, discountRs]);

    // Calculate change
    const changeAmount = useMemo(() => {
        const given = parseFloat(givenAmount) || 0;
        return roundTo2(given - netTotal);
    }, [givenAmount, netTotal]);

    // Handle branch change
    const handleBranchChange = (newBranchId) => {
        setSelectedBranchId(newBranchId);
        setFerryBoatId('');
        setFerryTime('');
    };

    // Handle schedule selection
    const handleScheduleChange = (time) => {
        if (!time) {
            setFerryTime('');
            return;
        }
        // Convert HH:MM to full datetime
        const today = new Date();
        const [hours, minutes] = time.split(':');
        today.setHours(parseInt(hours), parseInt(minutes), 0, 0);
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const hh = String(today.getHours()).padStart(2, '0');
        const min = String(today.getMinutes()).padStart(2, '0');
        setFerryTime(`${yyyy}-${mm}-${dd}T${hh}:${min}`);
    };

    // Handle item selection in a line
    const handleItemChange = (lineIndex, itemId) => {
        const item = cachedItems.find((i) => i.id.toString() === itemId.toString());
        setLines((prev) =>
            prev.map((line, idx) => {
                if (idx !== lineIndex) return line;
                if (!item) {
                    return { ...line, item_id: '', item_name: '', rate: 0, levy: 0, amount: 0, is_vehicle: false };
                }
                const newAmount = calculateLineAmount(line.qty, item.item_rate, item.item_lavy);
                return {
                    ...line,
                    item_id: item.id,
                    item_name: item.item_name,
                    rate: item.item_rate,
                    levy: item.item_lavy,
                    amount: newAmount,
                    is_vehicle: item.is_vehicle,
                };
            })
        );
    };

    // Handle quantity change
    const handleQtyChange = (lineIndex, qty) => {
        setLines((prev) =>
            prev.map((line, idx) => {
                if (idx !== lineIndex) return line;
                const newAmount = calculateLineAmount(qty, line.rate, line.levy);
                return { ...line, qty: parseInt(qty) || 0, amount: newAmount };
            })
        );
    };

    // Handle line field change
    const handleLineFieldChange = (lineIndex, field, value) => {
        setLines((prev) =>
            prev.map((line, idx) => {
                if (idx !== lineIndex) return line;
                return { ...line, [field]: value };
            })
        );
    };

    // Add new line
    const addLine = () => {
        setLines((prev) => [...prev, createEmptyLine(prev.length)]);
    };

    // Remove line
    const removeLine = (lineIndex) => {
        if (lines.length === 1) return; // Keep at least one line
        setLines((prev) => prev.filter((_, idx) => idx !== lineIndex));
    };

    // Handle payment mode change
    const handlePaymentModeChange = (mode) => {
        setPaymentMode(mode);
        if (mode === 'Guest Pass') {
            setShowPaymentModal(false);
            setTimeout(() => setShowGuestModal(true), 200);
        }
    };

    // Search guest by ID
    const searchGuestById = async (id) => {
        if (!id) return;
        try {
            const response = await fetch(`/ajax/search-guest-by-id?id=${encodeURIComponent(id)}`);
            if (response.ok) {
                const data = await response.json();
                if (data) {
                    setGuestSearchName(data.name || '');
                    setSelectedGuestId(data.id || id);
                }
            }
        } catch (error) {
            console.error('Error searching guest:', error);
        }
    };

    // Search guest by name
    const searchGuestByName = async (name) => {
        if (name.length < 2) {
            setGuestResults([]);
            return;
        }
        try {
            const response = await fetch(`/ajax/search-guest-by-name?name=${encodeURIComponent(name)}`);
            if (response.ok) {
                const data = await response.json();
                setGuestResults(data || []);
            }
        } catch (error) {
            console.error('Error searching guests:', error);
        }
    };

    // Select guest from list
    const selectGuestFromList = (guest) => {
        setGuestSearchId(guest.id);
        setGuestSearchName(guest.name);
        setSelectedGuestId(guest.id);
        setGuestResults([]);
    };

    // Confirm guest selection
    const confirmGuestSelection = () => {
        if (!guestSearchId && !guestSearchName) {
            alert('Please enter Guest ID or Name');
            return;
        }
        setGuestId(selectedGuestId || guestSearchId || guestSearchName);
        setPaymentMode('Cash');
        setShowGuestModal(false);
        submitTicket('Guest Pass');
    };

    // Submit ticket
    const submitTicket = async (overridePaymentMode = null) => {
        setIsSubmitting(true);
        setErrors({});
        setSuccessMessage('');

        // Validate lines
        const validLines = lines.filter((line) => line.item_id && line.qty > 0);
        if (validLines.length === 0) {
            setErrors({ lines: 'At least one item is required' });
            setIsSubmitting(false);
            return;
        }

        const payload = {
            branch_id: selectedBranchId,
            ferry_boat_id: ferryBoatId,
            ferry_time: ferryTime || null,
            ferry_type: 'REGULAR',
            customer_name: customerName || null,
            customer_mobile: customerMobile || null,
            guest_id: guestId || null,
            payment_mode: overridePaymentMode || paymentMode,
            discount_pct: discountPct || 0,
            discount_rs: discountRs || 0,
            lines: validLines.map((line) => ({
                item_id: line.item_id,
                item_name: line.item_name,
                qty: line.qty,
                rate: line.rate,
                levy: line.levy,
                amount: line.amount,
                vehicle_name: line.vehicle_name || null,
                vehicle_no: line.vehicle_no || null,
            })),
        };

        try {
            const response = await fetch(route('ticket-entry.store'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (response.ok && data.ok) {
                setSuccessMessage(`Ticket saved successfully! Total: ₹${formatCurrency(data.total)}`);

                // Print if enabled
                if (printAfterSave && data.ticket_id) {
                    window.open(`/tickets/${data.ticket_id}/print`, '_blank');
                }

                // Reset form
                resetForm();
                setShowPaymentModal(false);
            } else {
                setErrors(data.errors || { general: data.message || 'Failed to save ticket' });
            }
        } catch (error) {
            console.error('Error saving ticket:', error);
            setErrors({ general: 'Network error. Please try again.' });
        }

        setIsSubmitting(false);
    };

    // Reset form
    const resetForm = () => {
        setLines([createEmptyLine(0)]);
        setCustomerName('');
        setCustomerMobile('');
        setGuestId('');
        setDiscountPct(0);
        setDiscountRs(0);
        setGivenAmount('');
        setGuestSearchId('');
        setGuestSearchName('');
        setSelectedGuestId('');
        setGuestResults([]);
    };

    // Group items by type for dropdown
    const passengerItems = cachedItems.filter((item) => !item.is_vehicle);
    const vehicleItems = cachedItems.filter((item) => item.is_vehicle);

    return (
        <div className="space-y-6">
            {/* Success Message */}
            {successMessage && (
                <div className="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                    <CheckCircle className="w-5 h-5 text-green-600 flex-shrink-0" />
                    <p className="text-green-800 font-medium">{successMessage}</p>
                    <button
                        onClick={() => setSuccessMessage('')}
                        className="ml-auto text-green-600 hover:text-green-800"
                    >
                        <X className="w-5 h-5" />
                    </button>
                </div>
            )}

            {/* Error Messages */}
            {Object.keys(errors).length > 0 && (
                <div className="bg-red-50 border border-red-200 rounded-xl p-4">
                    <div className="flex items-start gap-3">
                        <AlertCircle className="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
                        <div>
                            <p className="font-medium text-red-800">Please fix the following errors:</p>
                            <ul className="mt-2 text-sm text-red-700 list-disc list-inside">
                                {Object.entries(errors).map(([key, value]) => (
                                    <li key={key}>{Array.isArray(value) ? value.join(', ') : value}</li>
                                ))}
                            </ul>
                        </div>
                    </div>
                </div>
            )}

            {/* Main Grid */}
            <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">
                {/* Trip Information Card */}
                <div className="xl:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div className="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                        <div className="flex items-center gap-2">
                            <Ship className="w-5 h-5 text-indigo-600" />
                            <h2 className="font-semibold text-slate-800">Trip Information</h2>
                        </div>
                        <span className="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                            Active Schedule
                        </span>
                    </div>

                    <div className="p-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {/* Branch */}
                            <div>
                                <label className="flex items-center gap-2 text-sm font-medium text-slate-700 mb-2">
                                    <MapPin className="w-4 h-4 text-slate-400" />
                                    Branch Name
                                </label>
                                {isAdmin ? (
                                    <select
                                        value={selectedBranchId}
                                        onChange={(e) => handleBranchChange(e.target.value)}
                                        className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                                    >
                                        <option value="">-- Select Branch --</option>
                                        {branches?.map((branch) => (
                                            <option key={branch.id} value={branch.id}>
                                                {branch.branch_name}
                                            </option>
                                        ))}
                                    </select>
                                ) : (
                                    <input
                                        type="text"
                                        value={branchName}
                                        readOnly
                                        className="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-600"
                                    />
                                )}
                            </div>

                            {/* Ferry Boat */}
                            <div>
                                <label className="flex items-center gap-2 text-sm font-medium text-slate-700 mb-2">
                                    <Anchor className="w-4 h-4 text-slate-400" />
                                    Ferry Boat
                                </label>
                                <select
                                    value={ferryBoatId}
                                    onChange={(e) => setFerryBoatId(e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                                    required
                                >
                                    <option value="">-- Select Boat --</option>
                                    {availableFerries.map((ferry) => (
                                        <option key={ferry.id} value={ferry.id}>
                                            {ferry.name} {ferry.pax_capacity ? `(Capacity: ${ferry.pax_capacity})` : ''}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        {/* Schedule */}
                        <div className="mt-6 pt-6 border-t border-slate-200">
                            <label className="flex items-center gap-2 text-sm font-medium text-slate-700 mb-2">
                                <Clock className="w-4 h-4 text-slate-400" />
                                Schedule & Time
                            </label>
                            <div className="flex items-center gap-4">
                                {isAdmin ? (
                                    <select
                                        onChange={(e) => handleScheduleChange(e.target.value)}
                                        className="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                                    >
                                        <option value="">-- Select Schedule --</option>
                                        {availableSchedules.map((schedule) => (
                                            <option key={schedule.id} value={schedule.time}>
                                                {schedule.time}
                                            </option>
                                        ))}
                                    </select>
                                ) : (
                                    <input
                                        type="text"
                                        value={
                                            nextFerryTime
                                                ? new Date(nextFerryTime).toLocaleTimeString('en-IN', {
                                                      hour: '2-digit',
                                                      minute: '2-digit',
                                                      hour12: true,
                                                  })
                                                : 'No schedule'
                                        }
                                        readOnly
                                        className="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-600"
                                    />
                                )}
                                <span className="px-3 py-1.5 bg-green-100 text-green-700 text-xs font-medium rounded-full whitespace-nowrap">
                                    Status: On Time
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Passenger Details Card */}
                <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div className="px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <div className="flex items-center gap-2">
                            <User className="w-5 h-5 text-indigo-600" />
                            <h2 className="font-semibold text-slate-800">Passenger Details</h2>
                        </div>
                    </div>

                    <div className="p-6 space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">Customer Name</label>
                            <input
                                type="text"
                                value={customerName}
                                onChange={(e) => setCustomerName(e.target.value)}
                                placeholder="Enter name"
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                            />
                        </div>

                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-slate-700 mb-2">
                                <Phone className="w-4 h-4 text-slate-400" />
                                Mobile Number
                            </label>
                            <input
                                type="tel"
                                value={customerMobile}
                                onChange={(e) => setCustomerMobile(e.target.value)}
                                placeholder="+91 XXXXX XXXXX"
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                            />
                        </div>

                        <div className="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                            <p className="text-sm font-semibold text-amber-800">Note:</p>
                            <p className="text-sm text-amber-700 mt-1">
                                Verify ID proof for non-local passengers before issuing tickets.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Line Items Card */}
            <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div className="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h2 className="font-semibold text-slate-800">Line Items</h2>
                    <button
                        type="button"
                        onClick={addLine}
                        className="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors"
                    >
                        <Plus className="w-4 h-4" />
                        Add Row
                    </button>
                </div>

                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead>
                            <tr className="bg-slate-50 border-b border-slate-200">
                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-12">
                                    #
                                </th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider min-w-[200px]">
                                    Item
                                </th>
                                <th className="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-20">
                                    Qty
                                </th>
                                <th className="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">
                                    Rate
                                </th>
                                <th className="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider w-20">
                                    Levy
                                </th>
                                <th className="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider w-28">
                                    Amount
                                </th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-32">
                                    Vehicle Name
                                </th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-32">
                                    Vehicle No
                                </th>
                                <th className="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-16">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {lines.map((line, index) => (
                                <tr key={line.id} className="hover:bg-slate-50 transition-colors">
                                    <td className="px-4 py-3 text-center text-sm text-slate-500 font-medium">
                                        {index + 1}
                                    </td>
                                    <td className="px-4 py-3">
                                        <select
                                            value={line.item_id}
                                            onChange={(e) => handleItemChange(index, e.target.value)}
                                            className="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm transition-all"
                                            disabled={itemsLoading}
                                        >
                                            <option value="">-- Select Item --</option>
                                            {passengerItems.length > 0 && (
                                                <optgroup label="Passengers">
                                                    {passengerItems.map((item) => (
                                                        <option key={item.id} value={item.id}>
                                                            {item.id} - {item.item_name}
                                                        </option>
                                                    ))}
                                                </optgroup>
                                            )}
                                            {vehicleItems.length > 0 && (
                                                <optgroup label="Vehicles">
                                                    {vehicleItems.map((item) => (
                                                        <option key={item.id} value={item.id}>
                                                            {item.id} - {item.item_name}
                                                        </option>
                                                    ))}
                                                </optgroup>
                                            )}
                                        </select>
                                    </td>
                                    <td className="px-4 py-3">
                                        <input
                                            type="number"
                                            value={line.qty}
                                            onChange={(e) => handleQtyChange(index, e.target.value)}
                                            min="1"
                                            step="1"
                                            className="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm text-center transition-all"
                                        />
                                    </td>
                                    <td className="px-4 py-3">
                                        <input
                                            type="text"
                                            value={formatCurrency(line.rate)}
                                            readOnly
                                            className="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm text-right"
                                        />
                                    </td>
                                    <td className="px-4 py-3">
                                        <input
                                            type="text"
                                            value={formatCurrency(line.levy)}
                                            readOnly
                                            className="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm text-right"
                                        />
                                    </td>
                                    <td className="px-4 py-3">
                                        <input
                                            type="text"
                                            value={formatCurrency(line.amount)}
                                            readOnly
                                            className="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm text-right font-semibold text-green-600"
                                        />
                                    </td>
                                    <td className="px-4 py-3">
                                        <input
                                            type="text"
                                            value={line.vehicle_name}
                                            onChange={(e) => handleLineFieldChange(index, 'vehicle_name', e.target.value)}
                                            placeholder="Optional"
                                            className={`w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm transition-all ${
                                                !line.is_vehicle ? 'bg-slate-50 text-slate-400' : ''
                                            }`}
                                            disabled={!line.is_vehicle}
                                        />
                                    </td>
                                    <td className="px-4 py-3">
                                        <input
                                            type="text"
                                            value={line.vehicle_no}
                                            onChange={(e) => handleLineFieldChange(index, 'vehicle_no', e.target.value)}
                                            placeholder="Optional"
                                            className={`w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm transition-all ${
                                                !line.is_vehicle ? 'bg-slate-50 text-slate-400' : ''
                                            }`}
                                            disabled={!line.is_vehicle}
                                        />
                                    </td>
                                    <td className="px-4 py-3 text-center">
                                        <button
                                            type="button"
                                            onClick={() => removeLine(index)}
                                            disabled={lines.length === 1}
                                            className="p-2 rounded-lg text-red-600 hover:bg-red-50 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                        >
                                            <Trash2 className="w-4 h-4" />
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                {/* Footer Section */}
                <div className="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    {/* Print Checkbox */}
                    <label className="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            checked={printAfterSave}
                            onChange={(e) => setPrintAfterSave(e.target.checked)}
                            className="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <Printer className="w-4 h-4 text-slate-500" />
                        <span className="text-sm text-slate-600">Print Receipt automatically</span>
                    </label>

                    {/* Summary & Actions */}
                    <div className="flex flex-col sm:flex-row items-start sm:items-end gap-6">
                        {/* Summary */}
                        <div className="space-y-2 text-right">
                            <div className="flex items-center justify-end gap-4">
                                <span className="text-sm text-slate-500">Subtotal</span>
                                <span className="font-semibold text-slate-800 min-w-[80px]">
                                    ₹{formatCurrency(subtotal)}
                                </span>
                            </div>
                            <div className="flex items-center justify-end gap-4">
                                <span className="text-sm text-slate-500">Discount %</span>
                                <input
                                    type="number"
                                    value={discountPct}
                                    onChange={(e) => setDiscountPct(parseFloat(e.target.value) || 0)}
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    className="w-20 px-2 py-1 rounded-lg border border-slate-200 text-sm text-right"
                                />
                            </div>
                            <div className="flex items-center justify-end gap-4">
                                <span className="text-sm text-slate-500">Discount Amt</span>
                                <input
                                    type="number"
                                    value={discountRs}
                                    onChange={(e) => setDiscountRs(parseFloat(e.target.value) || 0)}
                                    min="0"
                                    step="0.01"
                                    className="w-20 px-2 py-1 rounded-lg border border-slate-200 text-sm text-right"
                                />
                            </div>
                            <div className="flex items-center justify-end gap-4 pt-2 border-t border-slate-200">
                                <span className="text-sm font-medium text-slate-700">Net Total</span>
                                <span className="font-bold text-lg text-green-600 min-w-[80px]">
                                    ₹{formatCurrency(netTotal)}
                                </span>
                            </div>
                        </div>

                        {/* Buttons */}
                        <div className="flex flex-col gap-3">
                            <button
                                type="button"
                                onClick={() => setShowPaymentModal(true)}
                                disabled={isSubmitting || netTotal === 0}
                                className="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl font-semibold hover:from-indigo-700 hover:to-indigo-800 transition-all shadow-lg shadow-indigo-500/30 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <Save className="w-5 h-5" />
                                Save Ticket
                            </button>
                            <button
                                type="button"
                                onClick={() => setShowPaymentModal(true)}
                                disabled={isSubmitting || netTotal === 0}
                                className="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-medium hover:bg-slate-50 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <CreditCard className="w-5 h-5" />
                                Pay & Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {/* Payment Modal */}
            {showPaymentModal && (
                <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                    <div className="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-in fade-in zoom-in duration-200">
                        <div className="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white flex items-center justify-between">
                            <h3 className="font-semibold text-lg">Confirm Payment</h3>
                            <button
                                onClick={() => setShowPaymentModal(false)}
                                className="p-1.5 hover:bg-white/20 rounded-lg transition-colors"
                            >
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        <div className="p-6 space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-2">Net Total</label>
                                <input
                                    type="text"
                                    value={`₹${formatCurrency(netTotal)}`}
                                    readOnly
                                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 font-semibold text-lg"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-2">Payment Mode</label>
                                <select
                                    value={paymentMode}
                                    onChange={(e) => handlePaymentModeChange(e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                                >
                                    <option value="Cash">Cash</option>
                                    <option value="Guest Pass">Guest Pass</option>
                                    <option value="GPay">GPay</option>
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-2">Given Amount</label>
                                <input
                                    type="number"
                                    value={givenAmount}
                                    onChange={(e) => setGivenAmount(e.target.value)}
                                    placeholder="Enter given amount"
                                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-2">Change to Return</label>
                                <input
                                    type="text"
                                    value={`₹${formatCurrency(changeAmount)}`}
                                    readOnly
                                    className={`w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 font-semibold ${
                                        changeAmount >= 0 ? 'text-green-600' : 'text-red-600'
                                    }`}
                                />
                            </div>
                        </div>

                        <div className="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-3">
                            <button
                                onClick={() => setShowPaymentModal(false)}
                                className="px-5 py-2.5 border border-slate-200 text-slate-700 rounded-xl font-medium hover:bg-slate-100 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                onClick={() => submitTicket()}
                                disabled={isSubmitting}
                                className="px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-semibold hover:from-green-600 hover:to-green-700 transition-all disabled:opacity-50"
                            >
                                {isSubmitting ? 'Saving...' : 'Store'}
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Guest Modal */}
            {showGuestModal && (
                <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                    <div className="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-in fade-in zoom-in duration-200">
                        <div className="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white flex items-center justify-between">
                            <h3 className="font-semibold text-lg">Guest Details</h3>
                            <button
                                onClick={() => setShowGuestModal(false)}
                                className="p-1.5 hover:bg-white/20 rounded-lg transition-colors"
                            >
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        <div className="p-6 space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-2">Search by Guest ID</label>
                                <input
                                    type="text"
                                    value={guestSearchId}
                                    onChange={(e) => {
                                        setGuestSearchId(e.target.value);
                                        searchGuestById(e.target.value);
                                    }}
                                    placeholder="Enter Guest ID"
                                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                                />
                            </div>

                            <div className="text-center text-sm text-slate-400">OR</div>

                            <div className="relative">
                                <label className="block text-sm font-medium text-slate-700 mb-2">Search by Guest Name</label>
                                <input
                                    type="text"
                                    value={guestSearchName}
                                    onChange={(e) => {
                                        setGuestSearchName(e.target.value);
                                        searchGuestByName(e.target.value);
                                    }}
                                    placeholder="Enter Guest Name"
                                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                                />
                                {guestResults.length > 0 && (
                                    <ul className="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-40 overflow-y-auto">
                                        {guestResults.map((guest) => (
                                            <li
                                                key={guest.id}
                                                onClick={() => selectGuestFromList(guest)}
                                                className="px-4 py-2 hover:bg-slate-50 cursor-pointer text-sm"
                                            >
                                                {guest.name}
                                            </li>
                                        ))}
                                    </ul>
                                )}
                            </div>
                        </div>

                        <div className="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end">
                            <button
                                onClick={confirmGuestSelection}
                                className="px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-semibold hover:from-green-600 hover:to-green-700 transition-all"
                            >
                                Continue
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

TicketEntry.layout = (page) => <Layout children={page} title="Ticket Entry" />;
