import { useState, useEffect, useMemo } from 'react';
import { useForm, router, Head } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import CashCalculatorModal from '@/Components/CashCalculatorModal';
import { Plus, X, Ship, Clock, User, Phone, Printer, Save, AlertCircle } from 'lucide-react';

export default function Create({
    branches,
    branchId,
    branchName,
    ferryboatsBranch,
    ferryBoatsPerBranch,
    ferrySchedulesPerBranch,
    destBranchesPerBranch,
    paymentModes,
    guests,
    nextFerryTime,
    user,
    last_row_ferry_schedule,
    first_row_ferry_schedule,
    hideFerryTime,
    beforeFirstFerry,
}) {
    const [items, setItems] = useState([]);
    const [itemRates, setItemRates] = useState([]);
    const [loadingItems, setLoadingItems] = useState(false);
    const [printReceipt, setPrintReceipt] = useState(true);
    const [showCalculator, setShowCalculator] = useState(false);
    const [pendingPrint, setPendingPrint] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        branch_id: branchId || '',
        dest_branch_id: '',
        ticket_date: new Date().toISOString().split('T')[0],
        ferry_time: '',
        ferry_boat_id: '',
        payment_mode: 'Cash',
        guest_id: '',
        customer_name: '',
        customer_mobile: '',
        lines: [],
        remarks: '',
        discount_percent: 0,
        print_receipt: true,
    });

    // Filter ferry schedules based on selected date
    const filteredSchedules = useMemo(() => {
        const schedules = ferrySchedulesPerBranch?.[data.branch_id] || [];
        if (!schedules.length) return [];

        const today = new Date().toISOString().split('T')[0];
        const selectedDate = data.ticket_date;

        if (selectedDate > today) return schedules;

        if (selectedDate === today) {
            const now = new Date();
            const currentTimeInMinutes = now.getHours() * 60 + now.getMinutes();

            return schedules.filter((schedule) => {
                const [hours, minutes] = (schedule.time || '00:00').split(':').map(Number);
                return hours * 60 + minutes > currentTimeInMinutes - 5;
            });
        }

        return [];
    }, [ferrySchedulesPerBranch, data.branch_id, data.ticket_date]);

    // Fetch items when branch changes
    useEffect(() => {
        if (data.branch_id) {
            setLoadingItems(true);
            fetch(`/ajax/item-rates/list?branch_id=${data.branch_id}&on=${data.ticket_date}`)
                .then(res => res.json())
                .then(items => {
                    setItemRates(items);
                    setLoadingItems(false);
                })
                .catch(() => setLoadingItems(false));
        }
    }, [data.branch_id, data.ticket_date]);

    // Calculate totals
    const subtotal = items.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
    const discountAmount = (subtotal * (parseFloat(data.discount_percent) || 0)) / 100;
    const netTotal = subtotal - discountAmount;

    const addRow = () => {
        const newItem = {
            item_rate_id: '',
            item_name: '',
            qty: 1,
            rate: 0,
            levy: 0,
            amount: 0,
            vehicle_name: '',
            vehicle_no: '',
            is_vehicle: false,
        };
        const newItems = [...items, newItem];
        setItems(newItems);
        setData('lines', newItems);
    };

    const removeRow = (idx) => {
        const newItems = items.filter((_, i) => i !== idx);
        setItems(newItems);
        setData('lines', newItems);
    };

    const updateRow = (idx, field, value) => {
        const newItems = [...items];
        newItems[idx][field] = value;

        // If item_rate_id changed, populate item details
        if (field === 'item_rate_id') {
            const selectedItem = itemRates.find(ir => ir.id == value);
            if (selectedItem) {
                newItems[idx].item_name = selectedItem.item_name;
                newItems[idx].rate = parseFloat(selectedItem.item_rate) || 0;
                newItems[idx].levy = parseFloat(selectedItem.item_lavy) || 0;
                newItems[idx].is_vehicle = selectedItem.is_vehicle;
            }
        }

        // Recalculate amount
        const qty = parseInt(newItems[idx].qty) || 0;
        const rate = parseFloat(newItems[idx].rate) || 0;
        const levy = parseFloat(newItems[idx].levy) || 0;
        newItems[idx].amount = qty * (rate + levy);

        setItems(newItems);
        setData('lines', newItems);
    };

    const handleSubmit = (e, shouldPrint = false) => {
        e.preventDefault();

        // For Cash or GPay, show calculator modal first
        const mode = data.payment_mode;
        if (mode === 'Cash' || mode === 'GPay' || mode === 'UPI') {
            setPendingPrint(shouldPrint);
            setShowCalculator(true);
            return;
        }

        // For other payment modes (Guest Pass, etc.), submit directly
        submitTicket(shouldPrint);
    };

    const submitTicket = (shouldPrint) => {
        post(route('ticket-entry.store'), {
            onSuccess: (page) => {
                reset();
                setItems([]);
                setShowCalculator(false);
                // Get ticket info from flash session
                const ticket = page.props.flash?.ticket;
                if (shouldPrint && ticket?.print_url) {
                    window.open(ticket.print_url, '_blank');
                }
            },
        });
    };

    const handleCalculatorConfirm = () => {
        setShowCalculator(false);
        submitTicket(pendingPrint);
    };

    // Get ferry boat info
    const selectedBoat = ferryBoatsPerBranch?.[data.branch_id]?.find(fb => fb.id == data.ferry_boat_id);

    return (
        <>
            <Head title="Ticket Entry / Create New Booking" />

            <div className="mb-6">
                <h1 className="text-xl font-bold text-slate-800">Ticket Entry / Create New Booking</h1>
            </div>

            <form onSubmit={(e) => handleSubmit(e, printReceipt)}>
                <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    {/* Left Column - Trip Information */}
                    <div className="lg:col-span-3 space-y-6">
                        {/* Trip Information Card */}
                        <div className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div className="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
                                <div className="flex items-center gap-2">
                                    <Ship className="w-5 h-5 text-indigo-600" />
                                    <h2 className="font-semibold text-slate-800">Trip Information</h2>
                                </div>
                                <a href={route('ferry_schedules.index')} className="text-sm text-indigo-600 hover:underline">
                                    Active Schedule
                                </a>
                            </div>

                            <div className="p-6 space-y-6">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {/* Route Selection - Role based visibility */}
                                    <div>
                                        <label className="block text-sm font-medium text-slate-600 mb-2">
                                            <span className="flex items-center gap-1">
                                                <Ship className="w-4 h-4" />
                                                Route
                                            </span>
                                        </label>

                                        {/* Operators (role 4+): Fixed route display - no dropdown */}
                                        {user?.role_id >= 4 ? (
                                            (() => {
                                                // Get the operator's branch and its destination
                                                const operatorBranch = branches?.find(b => b.id == branchId);
                                                const destinations = destBranchesPerBranch?.[branchId] || [];
                                                const firstDest = destinations[0];

                                                // Auto-set the route if not already set
                                                if (operatorBranch && firstDest && !data.dest_branch_id) {
                                                    setTimeout(() => {
                                                        setData('branch_id', branchId);
                                                        setData('dest_branch_id', firstDest.id);
                                                    }, 0);
                                                }

                                                return (
                                                    <div className="w-full px-4 py-2.5 bg-slate-100 border border-slate-300 rounded-lg text-slate-700 font-medium">
                                                        {operatorBranch?.branch_name || 'Unknown'} → {firstDest?.branch_name || 'Unknown'}
                                                    </div>
                                                );
                                            })()
                                        ) : (
                                            /* Admin/Manager: Show dropdown */
                                            <select
                                                value={`${data.branch_id}-${data.dest_branch_id}`}
                                                onChange={(e) => {
                                                    const [branchId, destId] = e.target.value.split('-');
                                                    setData('branch_id', branchId);
                                                    setData('dest_branch_id', destId);
                                                    setItems([]);
                                                }}
                                                className="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            >
                                                <option value="-">Select Route</option>
                                                {(() => {
                                                    // Build route pairs
                                                    const allRoutes = [];

                                                    // For Manager (role 3): Only show routes for their branch
                                                    const branchesToShow = user?.role_id === 3
                                                        ? branches?.filter(b => b.id == branchId)
                                                        : branches;

                                                    branchesToShow?.forEach((b) => {
                                                        const destinations = destBranchesPerBranch?.[b.id] || [];
                                                        destinations.forEach((dest) => {
                                                            allRoutes.push({
                                                                from: b,
                                                                to: dest,
                                                                key: `${b.id}-${dest.id}`,
                                                                groupKey: b.id < dest.id
                                                                    ? `${b.branch_name} ↔ ${dest.branch_name}`
                                                                    : `${dest.branch_name} ↔ ${b.branch_name}`
                                                            });
                                                        });
                                                    });

                                                    // For Manager: Also add reverse routes (dest → branch)
                                                    if (user?.role_id === 3) {
                                                        const managerBranch = branches?.find(b => b.id == branchId);
                                                        const destinations = destBranchesPerBranch?.[branchId] || [];
                                                        destinations.forEach((dest) => {
                                                            // Check if reverse route exists
                                                            const destRoutes = destBranchesPerBranch?.[dest.id] || [];
                                                            if (destRoutes.some(d => d.id == branchId)) {
                                                                allRoutes.push({
                                                                    from: dest,
                                                                    to: managerBranch,
                                                                    key: `${dest.id}-${branchId}`,
                                                                    groupKey: dest.id < branchId
                                                                        ? `${dest.branch_name} ↔ ${managerBranch?.branch_name}`
                                                                        : `${managerBranch?.branch_name} ↔ ${dest.branch_name}`
                                                                });
                                                            }
                                                        });
                                                    }

                                                    // Group routes
                                                    const groups = {};
                                                    allRoutes.forEach(route => {
                                                        if (!groups[route.groupKey]) {
                                                            groups[route.groupKey] = [];
                                                        }
                                                        // Avoid duplicates
                                                        if (!groups[route.groupKey].some(r => r.key === route.key)) {
                                                            groups[route.groupKey].push(route);
                                                        }
                                                    });

                                                    // For Admin/SuperAdmin (role 1,2): Show grouped options
                                                    if (user?.role_id <= 2) {
                                                        return Object.entries(groups).map(([groupName, routes]) => (
                                                            <optgroup key={groupName} label={groupName}>
                                                                {routes.map(r => (
                                                                    <option key={r.key} value={r.key}>
                                                                        {r.from.branch_name} → {r.to.branch_name}
                                                                    </option>
                                                                ))}
                                                            </optgroup>
                                                        ));
                                                    }

                                                    // For Manager (role 3): Show flat list (no grouping needed for just 2 options)
                                                    return allRoutes.map(r => (
                                                        <option key={r.key} value={r.key}>
                                                            {r.from.branch_name} → {r.to.branch_name}
                                                        </option>
                                                    ));
                                                })()}
                                            </select>
                                        )}
                                        {errors.branch_id && <p className="text-red-500 text-sm mt-1">{errors.branch_id}</p>}
                                    </div>

                                    {/* Ferry Boat */}
                                    <div>
                                        <label className="block text-sm font-medium text-slate-600 mb-2">
                                            <span className="flex items-center gap-1">
                                                <Ship className="w-4 h-4" />
                                                Ferry Boat
                                            </span>
                                        </label>
                                        <select
                                            value={data.ferry_boat_id}
                                            onChange={(e) => setData('ferry_boat_id', e.target.value)}
                                            className="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        >
                                            <option value="">Select Ferry Boat</option>
                                            {ferryBoatsPerBranch?.[data.branch_id]?.map((fb) => (
                                                <option key={fb.id} value={fb.id}>
                                                    {fb.name} (Capacity: {fb.capacity || 'N/A'})
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {/* Schedule & Time */}
                                    <div>
                                        <label className="block text-sm font-medium text-slate-600 mb-2">
                                            <span className="flex items-center gap-1">
                                                <Clock className="w-4 h-4" />
                                                Schedule & Time
                                            </span>
                                        </label>
                                        <div className="flex gap-3">
                                            <input
                                                type="date"
                                                value={data.ticket_date}
                                                onChange={(e) => {
                                                    setData('ticket_date', e.target.value);
                                                    setData('ferry_time', '');
                                                }}
                                                min={new Date().toISOString().split('T')[0]}
                                                className="flex-1 px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                            />
                                            <select
                                                value={data.ferry_time}
                                                onChange={(e) => setData('ferry_time', e.target.value)}
                                                className="flex-1 px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                                disabled={!filteredSchedules.length}
                                            >
                                                <option value="">
                                                    {filteredSchedules.length ? 'Select Time' : 'No times available'}
                                                </option>
                                                {filteredSchedules.map((s, idx) => (
                                                    <option key={idx} value={s.time}>{s.time}</option>
                                                ))}
                                            </select>
                                        </div>
                                    </div>

                                    {/* Status */}
                                    <div className="flex items-end">
                                        {data.ferry_time && (
                                            <span className="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg font-medium">
                                                Status: On Time
                                            </span>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Line Items Card */}
                        <div className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div className="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
                                <h2 className="font-semibold text-slate-800">Line Items</h2>
                                <button
                                    type="button"
                                    onClick={addRow}
                                    className="inline-flex items-center gap-2 px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                                >
                                    <Plus className="w-4 h-4" />
                                    Add Row
                                </button>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="w-full">
                                    <thead className="bg-slate-50 text-slate-600 text-sm">
                                        <tr>
                                            <th className="px-4 py-3 text-left font-medium">#</th>
                                            <th className="px-4 py-3 text-left font-medium">ITEM ID</th>
                                            <th className="px-4 py-3 text-left font-medium">ITEM NAME</th>
                                            <th className="px-4 py-3 text-center font-medium">QTY</th>
                                            <th className="px-4 py-3 text-right font-medium">RATE</th>
                                            <th className="px-4 py-3 text-right font-medium">LEVY</th>
                                            <th className="px-4 py-3 text-right font-medium">AMOUNT</th>
                                            <th className="px-4 py-3 text-left font-medium">VEHICLE NAME</th>
                                            <th className="px-4 py-3 text-left font-medium">VEHICLE NO</th>
                                            <th className="px-4 py-3 text-center font-medium">ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100">
                                        {items.map((item, idx) => (
                                            <tr key={idx} className="hover:bg-slate-50">
                                                <td className="px-4 py-3 text-slate-600">{idx + 1}</td>
                                                <td className="px-4 py-3">
                                                    <select
                                                        value={item.item_rate_id}
                                                        onChange={(e) => updateRow(idx, 'item_rate_id', e.target.value)}
                                                        className="w-full min-w-[280px] px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                                                    >
                                                        <option value="">Select Item</option>
                                                        {itemRates.map((ir) => (
                                                            <option key={ir.id} value={ir.id}>
                                                                {ir.id} - {ir.item_name}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </td>
                                                <td className="px-4 py-3">
                                                    <input
                                                        type="text"
                                                        value={item.item_name}
                                                        readOnly
                                                        className="w-full min-w-[180px] px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"
                                                        placeholder="Auto-filled"
                                                    />
                                                </td>
                                                <td className="px-4 py-3">
                                                    <input
                                                        type="number"
                                                        value={item.qty}
                                                        onChange={(e) => updateRow(idx, 'qty', e.target.value)}
                                                        min="1"
                                                        className="w-20 px-3 py-2 border border-slate-300 rounded-lg text-center text-sm focus:ring-2 focus:ring-indigo-500"
                                                    />
                                                </td>
                                                <td className="px-4 py-3">
                                                    <input
                                                        type="number"
                                                        value={item.rate}
                                                        readOnly
                                                        className="w-24 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-right text-sm"
                                                    />
                                                </td>
                                                <td className="px-4 py-3">
                                                    <input
                                                        type="number"
                                                        value={item.levy}
                                                        readOnly
                                                        className="w-20 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-right text-sm"
                                                    />
                                                </td>
                                                <td className="px-4 py-3">
                                                    <input
                                                        type="number"
                                                        value={item.amount.toFixed(2)}
                                                        readOnly
                                                        className="w-24 px-3 py-2 bg-indigo-50 border border-indigo-200 rounded-lg text-right text-sm font-medium text-indigo-700"
                                                    />
                                                </td>
                                                <td className="px-4 py-3">
                                                    <input
                                                        type="text"
                                                        value={item.vehicle_name}
                                                        onChange={(e) => updateRow(idx, 'vehicle_name', e.target.value)}
                                                        placeholder="Optional"
                                                        disabled={!item.is_vehicle}
                                                        className="w-28 px-3 py-2 border border-slate-300 rounded-lg text-sm disabled:bg-slate-100 disabled:text-slate-400"
                                                    />
                                                </td>
                                                <td className="px-4 py-3">
                                                    <input
                                                        type="text"
                                                        value={item.vehicle_no}
                                                        onChange={(e) => updateRow(idx, 'vehicle_no', e.target.value)}
                                                        placeholder="Optional"
                                                        disabled={!item.is_vehicle}
                                                        className="w-28 px-3 py-2 border border-slate-300 rounded-lg text-sm disabled:bg-slate-100 disabled:text-slate-400"
                                                    />
                                                </td>
                                                <td className="px-4 py-3 text-center">
                                                    <button
                                                        type="button"
                                                        onClick={() => removeRow(idx)}
                                                        className="p-2 text-red-500 hover:bg-red-50 rounded-lg"
                                                    >
                                                        <X className="w-4 h-4" />
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                                        {items.length === 0 && (
                                            <tr>
                                                <td colSpan="10" className="px-4 py-8 text-center text-slate-500">
                                                    No items added. Click "Add Row" to start.
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>

                            {/* Summary Row */}
                            <div className="px-6 py-4 bg-slate-50 border-t border-slate-200">
                                <div className="flex items-center justify-between">
                                    <label className="flex items-center gap-2 text-sm text-slate-600">
                                        <input
                                            type="checkbox"
                                            checked={printReceipt}
                                            onChange={(e) => setPrintReceipt(e.target.checked)}
                                            className="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <Printer className="w-4 h-4" />
                                        Print Receipt automatically
                                    </label>

                                    <div className="flex items-center gap-8">
                                        <div className="text-right">
                                            <div className="text-sm text-slate-500">Subtotal</div>
                                            <div className="font-semibold">{subtotal.toFixed(2)}</div>
                                        </div>
                                        <div className="text-right">
                                            <div className="text-sm text-slate-500">Discount %</div>
                                            <input
                                                type="number"
                                                value={data.discount_percent}
                                                onChange={(e) => setData('discount_percent', e.target.value)}
                                                min="0"
                                                max="100"
                                                className="w-16 px-2 py-1 border border-slate-300 rounded text-right text-sm"
                                            />
                                        </div>
                                        <div className="text-right">
                                            <div className="text-sm text-slate-500">Discount Amt</div>
                                            <div className="font-semibold">{discountAmount.toFixed(2)}</div>
                                        </div>
                                        <div className="text-right">
                                            <div className="text-sm text-slate-500">Net Total</div>
                                            <div className="text-xl font-bold text-green-600">{netTotal.toFixed(2)}</div>
                                        </div>

                                        <div className="flex gap-3">
                                            <button
                                                type="submit"
                                                disabled={processing || items.length === 0}
                                                className="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 font-medium"
                                            >
                                                <Save className="w-4 h-4" />
                                                Save
                                            </button>
                                            <button
                                                type="button"
                                                onClick={(e) => handleSubmit(e, true)}
                                                disabled={processing || items.length === 0}
                                                className="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 disabled:opacity-50 font-medium border border-slate-300"
                                            >
                                                <Printer className="w-4 h-4" />
                                                Save and Print
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right Column - Passenger Details */}
                    <div className="lg:col-span-1">
                        <div className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden sticky top-6">
                            <div className="flex items-center gap-2 px-6 py-4 border-b border-slate-200 bg-slate-50">
                                <User className="w-5 h-5 text-indigo-600" />
                                <h2 className="font-semibold text-slate-800">Passenger Details</h2>
                            </div>

                            <div className="p-6 space-y-4">
                                <div>
                                    <label className="block text-sm font-medium text-slate-600 mb-2">
                                        Customer Name
                                    </label>
                                    <input
                                        type="text"
                                        value={data.customer_name}
                                        onChange={(e) => setData('customer_name', e.target.value)}
                                        placeholder="Enter name (optional)"
                                        className="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-slate-600 mb-2">
                                        Mobile Number
                                    </label>
                                    <input
                                        type="tel"
                                        value={data.customer_mobile}
                                        onChange={(e) => setData('customer_mobile', e.target.value)}
                                        placeholder="Enter mobile (optional)"
                                        className="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                </div>


                                {/* Payment Mode */}
                                <div>
                                    <label className="block text-sm font-medium text-slate-600 mb-2">
                                        Payment Mode
                                    </label>
                                    <select
                                        value={data.payment_mode}
                                        onChange={(e) => {
                                            setData('payment_mode', e.target.value);
                                            // Clear guest_id if switching away from Guest Pass
                                            if (e.target.value !== 'Guest Pass') {
                                                setData('guest_id', '');
                                            }
                                        }}
                                        className="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    >
                                        {paymentModes?.map((pm, idx) => (
                                            <option key={idx} value={pm}>{pm}</option>
                                        ))}
                                    </select>
                                </div>

                                {/* Guest Dropdown - Only shown when Guest Pass is selected */}
                                {data.payment_mode === 'Guest Pass' && (
                                    <div>
                                        <label className="block text-sm font-medium text-slate-600 mb-2">
                                            Select Guest <span className="text-red-500">*</span>
                                        </label>
                                        <select
                                            value={data.guest_id}
                                            onChange={(e) => setData('guest_id', e.target.value)}
                                            className="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                            required
                                        >
                                            <option value="">Select a guest</option>
                                            {guests?.map((guest) => (
                                                <option key={guest.id} value={guest.id}>
                                                    {guest.name} {guest.category?.name ? `(${guest.category.name})` : ''}
                                                </option>
                                            ))}
                                        </select>
                                        {!guests?.length && (
                                            <p className="text-amber-600 text-sm mt-1">
                                                No guests found. Add guests in Guest Master.
                                            </p>
                                        )}
                                        {errors.guest_id && (
                                            <p className="text-red-500 text-sm mt-1">{errors.guest_id}</p>
                                        )}
                                    </div>
                                )}

                                {/* Remarks */}
                                <div>
                                    <label className="block text-sm font-medium text-slate-600 mb-2">
                                        Remarks
                                    </label>
                                    <textarea
                                        value={data.remarks}
                                        onChange={(e) => setData('remarks', e.target.value)}
                                        placeholder="Optional remarks..."
                                        rows="3"
                                        className="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 resize-none"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {/* Cash/GPay Calculator Modal */}
            <CashCalculatorModal
                isOpen={showCalculator}
                onClose={() => setShowCalculator(false)}
                onConfirm={handleCalculatorConfirm}
                totalAmount={netTotal}
                paymentMode={data.payment_mode}
            />
        </>
    );
}

Create.layout = (page) => <Layout title="Ticket Entry">{page}</Layout>;
