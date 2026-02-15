import { useState } from 'react';
import { router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import { Download, Filter, Car, DollarSign } from 'lucide-react';
import { toSafeArray } from '@/utils/safeData';

export default function Index({
    tickets,
    branches,
    ferryBoats,
    paymentModes,
    ferryTypes,
    branchId,
    paymentMode,
    ferryType,
    ferryBoatId,
    dateFrom,
    dateTo,
    vehicleName,
    vehicleNo,
    totalAmount,
    pageTotalAmount,
}) {
    // Sanitize PHP props
    branches = toSafeArray(branches);
    ferryBoats = toSafeArray(ferryBoats);

    const [filters, setFilters] = useState({
        branch_id: branchId || '',
        payment_mode: paymentMode || '',
        ferry_type: ferryType || '',
        ferry_boat_id: ferryBoatId || '',
        date_from: dateFrom || '',
        date_to: dateTo || '',
        vehicle_name: vehicleName || '',
        vehicle_no: vehicleNo || '',
    });

    const handleFilter = () => {
        router.get(route('reports.vehicle_tickets'), filters);
    };

    const formatCurrency = (amount) => `₹${Number(amount || 0).toLocaleString()}`;

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-slate-800">Vehicle Ticket Reports</h1>
                    <p className="text-slate-500 mt-1">View vehicle-wise ticket sales reports</p>
                </div>
                <button className="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <Download className="w-4 h-4" />
                    Export CSV
                </button>
            </div>

            {/* Filters */}
            <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div className="flex items-center gap-2 mb-4">
                    <Filter className="w-5 h-5 text-slate-400" />
                    <span className="font-semibold text-slate-700">Filters</span>
                </div>
                <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4">
                    <select
                        value={filters.branch_id}
                        onChange={(e) => setFilters({ ...filters, branch_id: e.target.value })}
                        className="px-3 py-2 border border-slate-300 rounded-lg"
                    >
                        <option value="">All Branches</option>
                        {branches?.map((b) => (
                            <option key={b.id} value={b.id}>{b.branch_name}</option>
                        ))}
                    </select>
                    <select
                        value={filters.ferry_boat_id}
                        onChange={(e) => setFilters({ ...filters, ferry_boat_id: e.target.value })}
                        className="px-3 py-2 border border-slate-300 rounded-lg"
                    >
                        <option value="">All Ferry Boats</option>
                        {ferryBoats?.map((fb) => (
                            <option key={fb.id} value={fb.id}>{fb.name}</option>
                        ))}
                    </select>
                    <input
                        type="text"
                        value={filters.vehicle_name}
                        onChange={(e) => setFilters({ ...filters, vehicle_name: e.target.value })}
                        placeholder="Vehicle Name"
                        className="px-3 py-2 border border-slate-300 rounded-lg"
                    />
                    <input
                        type="text"
                        value={filters.vehicle_no}
                        onChange={(e) => setFilters({ ...filters, vehicle_no: e.target.value })}
                        placeholder="Vehicle Number"
                        className="px-3 py-2 border border-slate-300 rounded-lg"
                    />
                    <input
                        type="date"
                        value={filters.date_from}
                        onChange={(e) => setFilters({ ...filters, date_from: e.target.value })}
                        className="px-3 py-2 border border-slate-300 rounded-lg"
                    />
                    <input
                        type="date"
                        value={filters.date_to}
                        onChange={(e) => setFilters({ ...filters, date_to: e.target.value })}
                        className="px-3 py-2 border border-slate-300 rounded-lg"
                    />
                </div>
                <div className="mt-4 flex justify-end">
                    <button
                        onClick={handleFilter}
                        className="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                    >
                        Apply Filters
                    </button>
                </div>
            </div>

            {/* Summary */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl p-6 text-white">
                    <div className="flex items-center gap-3">
                        <DollarSign className="w-8 h-8" />
                        <div>
                            <p className="text-indigo-100">Total Amount (All Pages)</p>
                            <p className="text-3xl font-bold">{formatCurrency(totalAmount)}</p>
                        </div>
                    </div>
                </div>
                <div className="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl p-6 text-white">
                    <div className="flex items-center gap-3">
                        <Car className="w-8 h-8" />
                        <div>
                            <p className="text-emerald-100">Page Total</p>
                            <p className="text-3xl font-bold">{formatCurrency(pageTotalAmount)}</p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Table */}
            <div className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead className="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th className="text-left px-4 py-3 text-sm font-semibold text-slate-600">ID</th>
                                <th className="text-left px-4 py-3 text-sm font-semibold text-slate-600">Date</th>
                                <th className="text-left px-4 py-3 text-sm font-semibold text-slate-600">Vehicle</th>
                                <th className="text-left px-4 py-3 text-sm font-semibold text-slate-600">Number</th>
                                <th className="text-left px-4 py-3 text-sm font-semibold text-slate-600">Branch</th>
                                <th className="text-right px-4 py-3 text-sm font-semibold text-slate-600">Amount</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {tickets?.data?.map((t) => (
                                <tr key={t.id} className="hover:bg-slate-50">
                                    <td className="px-4 py-3 text-slate-600">{t.id}</td>
                                    <td className="px-4 py-3 text-slate-600">{t.ticket_date}</td>
                                    <td className="px-4 py-3 text-slate-700">{t.vehicle_name || '-'}</td>
                                    <td className="px-4 py-3 text-slate-700 font-mono">{t.vehicle_no || '-'}</td>
                                    <td className="px-4 py-3 text-slate-700">{t.branch?.branch_name || '-'}</td>
                                    <td className="px-4 py-3 text-right font-medium text-indigo-600">
                                        {formatCurrency(t.total_amount)}
                                    </td>
                                </tr>
                            ))}
                            {(!tickets?.data || tickets.data.length === 0) && (
                                <tr>
                                    <td colSpan="6" className="px-4 py-12 text-center text-slate-500">
                                        No vehicle tickets found.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}

Index.layout = (page) => <Layout title="Vehicle Ticket Reports">{page}</Layout>;
