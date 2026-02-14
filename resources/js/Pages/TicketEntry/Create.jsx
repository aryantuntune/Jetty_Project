import { useState, useEffect, useMemo } from 'react';
import { useForm, router, Head } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import CashCalculatorModal from '@/Components/CashCalculatorModal';
import { Plus, X, Ship, Clock, User, Phone, Printer, Save, AlertCircle, CheckCircle } from 'lucide-react';

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
    const [successInfo, setSuccessInfo] = useState(null); // { ticketNo, total }

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

    // Filter ferry schedules: show 3 times (1 past, current, next)
    const { filteredSchedules, currentScheduleIndex } = useMemo(() => {
        const schedules = ferrySchedulesPerBranch?.[data.branch_id] || [];
        if (!schedules.length) return { filteredSchedules: [], currentScheduleIndex: -1 };

        const today = new Date().toISOString().split('T')[0];
        const selectedDate = data.ticket_date;

        // Future date: show all schedules (extract only time)
        if (selectedDate > today) {
            return {
                filteredSchedules: schedules.map(s => ({ time: s.time, _label: '' })),
                currentScheduleIndex: 0,
            };
        }

        // Past date: no schedules
        if (selectedDate < today) return { filteredSchedules: [], currentScheduleIndex: -1 };

        // Today: show 3 times (1 past + current + next)
        const now = new Date();
        const currentMinutes = now.getHours() * 60 + now.getMinutes();

        // Find the first schedule that is >= current time (next available boat)
        let nextIdx = schedules.findIndex((s) => {
            const [h, m] = (s.time || '00:00').split(':').map(Number);
            return h * 60 + m >= currentMinutes;
        });

        // If no future schedule found, everything is past — show last 2 + nothing
        if (nextIdx === -1) nextIdx = schedules.length;

        // Collect: 1 past, current (nextIdx), 1 after — only extract time string
        const result = [];
        const pastIdx = nextIdx - 1;
        let selectedInResult = -1;

        if (pastIdx >= 0) {
            result.push({ time: schedules[pastIdx].time, _label: 'Previous' });
        }
        if (nextIdx < schedules.length) {
            result.push({ time: schedules[nextIdx].time, _label: 'Current' });
            selectedInResult = result.length - 1;
        }
        if (nextIdx + 1 < schedules.length) {
            result.push({ time: schedules[nextIdx + 1].time, _label: 'Next' });
        }

        return { filteredSchedules: result, currentScheduleIndex: selectedInResult };
    }, [ferrySchedulesPerBranch, data.branch_id, data.ticket_date]);

    // Auto-select the current/next schedule when schedules change
    useEffect(() => {
        if (currentScheduleIndex >= 0 && filteredSchedules[currentScheduleIndex]) {
            const currentTime = filteredSchedules[currentScheduleIndex].time;
            if (currentTime && !data.ferry_time) {
                setData('ferry_time', currentTime);
            }
        }
    }, [filteredSchedules, currentScheduleIndex]);

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

    // Validation: require route, ferry boat, time, and at least 1 item
    const canSubmit = data.branch_id && data.ferry_boat_id && data.ferry_time && items.length > 0;

    const handleSubmit = (e, shouldPrint = false) => {
        e.preventDefault();
        if (!canSubmit) return;

        // Always open checkout modal
        setPendingPrint(shouldPrint);
        setShowCalculator(true);
    };

    const submitTicket = (shouldPrint) => {
        post(route('ticket-entry.store'), {
            onSuccess: (page) => {
                reset();
                setItems([]);
                setShowCalculator(false);
                // Get ticket info from flash session
                const ticket = page.props.flash?.ticket;

                // Show success animation
                setSuccessInfo({
                    ticketNo: ticket?.ticket_no || ticket?.id || '—',
                    total: ticket?.total ? Number(ticket.total).toFixed(2) : '0.00',
                });
                setTimeout(() => setSuccessInfo(null), 2500);

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
                <div className="space-y-6">
                    {/* Trip Information + Items */}
                    <div className="space-y-6">
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
                                                    <option key={idx} value={s.time}>
                                                        {s.time}{s._label ? ` (${s._label})` : ''}
                                                    </option>
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
                                                disabled={processing || !canSubmit}
                                                className="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 font-medium"
                                            >
                                                <Save className="w-4 h-4" />
                                                Save
                                            </button>
                                            <button
                                                type="button"
                                                onClick={(e) => handleSubmit(e, true)}
                                                disabled={processing || !canSubmit}
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
                </div>
            </form>

            {/* Cash/GPay Calculator Modal */}
            <CashCalculatorModal
                isOpen={showCalculator}
                onClose={() => setShowCalculator(false)}
                onConfirm={handleCalculatorConfirm}
                totalAmount={netTotal}
                customerName={data.customer_name}
                onCustomerNameChange={(v) => setData('customer_name', v)}
                customerMobile={data.customer_mobile}
                onCustomerMobileChange={(v) => setData('customer_mobile', v)}
                paymentMode={data.payment_mode}
                onPaymentModeChange={(v) => setData('payment_mode', v)}
                paymentModes={paymentModes}
                guestId={data.guest_id}
                onGuestIdChange={(v) => setData('guest_id', v)}
                guests={guests}
                remarks={data.remarks}
                onRemarksChange={(v) => setData('remarks', v)}
            />

            {/* Success Animation Overlay */}
            {successInfo && (
                <div
                    className="fixed inset-0 z-[9999] flex items-center justify-center"
                    style={{
                        backgroundColor: 'rgba(0,0,0,0.35)',
                        backdropFilter: 'blur(4px)',
                        animation: 'fadeIn 0.2s ease-out',
                    }}
                >
                    <div
                        className="bg-white rounded-3xl shadow-2xl p-10 flex flex-col items-center gap-4 max-w-sm mx-4"
                        style={{ animation: 'popIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1)' }}
                    >
                        <div className="w-20 h-20 rounded-full bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center shadow-lg shadow-green-200">
                            <CheckCircle className="w-10 h-10 text-white" strokeWidth={2.5} />
                        </div>
                        <h3 className="text-xl font-bold text-gray-900">Ticket Booked!</h3>
                        <div className="text-center space-y-1">
                            <p className="text-sm text-gray-500">Ticket No.</p>
                            <p className="text-2xl font-bold text-indigo-600">#{successInfo.ticketNo}</p>
                        </div>
                        <div className="px-5 py-2 rounded-full bg-green-50 border border-green-200">
                            <span className="text-green-700 font-semibold">₹ {successInfo.total}</span>
                        </div>
                    </div>
                </div>
            )}

            {/* Success animation keyframes */}
            <style>{`
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                @keyframes popIn {
                    0% { transform: scale(0.5); opacity: 0; }
                    100% { transform: scale(1); opacity: 1; }
                }
            `}</style>
        </>
    );
}

Create.layout = (page) => <Layout title="Ticket Entry">{page}</Layout>;
