import { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import { toSafeArray } from '@/utils/safeData';
import {
    Filter,
    Search,
    Download,
    Car,
    Calendar,
    X,
    ChevronLeft,
    ChevronRight,
} from 'lucide-react';

// Format currency with 2 decimal places
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount || 0);
};

// Format date for display
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
};

// Format time for display
const formatTime = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-IN', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    });
};

// Get today's date in YYYY-MM-DD format
const getTodayDate = () => {
    return new Date().toISOString().split('T')[0];
};

// Validate date range
const isValidDateRange = (from, to) => {
    if (!from || !to) return true;
    return new Date(from) <= new Date(to);
};

export default function VehicleTicketReports({
    tickets,
    branches,
    branchId,
    dateFrom,
    dateTo,
    vehicleName,
    vehicleNo,
    totalAmount,
    pageTotalAmount,
    auth,
}) {
    branches = toSafeArray(branches);

    const { flash } = usePage().props;
    const userRoleId = auth?.user?.role_id;
    const canExport = [1, 2].includes(userRoleId);

    // Filter state
    const [filters, setFilters] = useState({
        branch_id: branchId || '',
        date_from: dateFrom || getTodayDate(),
        date_to: dateTo || getTodayDate(),
        vehicle_name: vehicleName || '',
        vehicle_no: vehicleNo || '',
    });

    // Date validation error
    const [dateError, setDateError] = useState('');

    // Handle filter change
    const handleFilterChange = (field, value) => {
        const newFilters = { ...filters, [field]: value };

        // Validate date range
        if (field === 'date_from' || field === 'date_to') {
            if (!isValidDateRange(newFilters.date_from, newFilters.date_to)) {
                setDateError('From date cannot be after To date');
            } else {
                setDateError('');
            }
        }

        setFilters(newFilters);
    };

    // Apply filters
    const handleApplyFilters = (e) => {
        e.preventDefault();

        if (!isValidDateRange(filters.date_from, filters.date_to)) {
            setDateError('From date cannot be after To date');
            return;
        }

        const params = {};
        Object.entries(filters).forEach(([key, value]) => {
            if (value) params[key] = value;
        });

        router.get(route('reports.vehicle_tickets'), params, { preserveState: true });
    };

    // Reset filters
    const handleResetFilters = () => {
        const defaultFilters = {
            branch_id: '',
            date_from: getTodayDate(),
            date_to: getTodayDate(),
            vehicle_name: '',
            vehicle_no: '',
        };
        setFilters(defaultFilters);
        setDateError('');
        router.get(route('reports.vehicle_tickets'));
    };

    // Export CSV
    const handleExport = () => {
        const params = new URLSearchParams();
        Object.entries(filters).forEach(([key, value]) => {
            if (value) params.append(key, value);
        });
        window.open(`${route('reports.vehicle_tickets.export')}?${params.toString()}`, '_blank');
    };

    // Payment mode badge color
    const getPaymentModeColor = (mode) => {
        const colors = {
            Cash: 'bg-green-100 text-green-700',
            'CASH MEMO': 'bg-green-100 text-green-700',
            Credit: 'bg-blue-100 text-blue-700',
            'CREDIT MEMO': 'bg-blue-100 text-blue-700',
            'Guest Pass': 'bg-purple-100 text-purple-700',
            'GUEST PASS': 'bg-purple-100 text-purple-700',
            GPay: 'bg-indigo-100 text-indigo-700',
        };
        return colors[mode] || 'bg-slate-100 text-slate-700';
    };

    // Pagination data
    const ticketData = tickets?.data || tickets || [];
    const currentPage = tickets?.current_page || 1;
    const hasMorePages = tickets?.next_page_url;
    const prevPageUrl = tickets?.prev_page_url;
    const nextPageUrl = tickets?.next_page_url;

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold text-slate-800 tracking-tight font-[Inter]">
                        Vehicle-wise Ticket Report
                    </h1>
                    <p className="mt-1 text-sm text-slate-500">
                        View ticket records filtered by vehicle information
                    </p>
                </div>
                {canExport && (
                    <button
                        onClick={handleExport}
                        className="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all shadow-lg shadow-emerald-500/30"
                    >
                        <Download className="w-5 h-5" />
                        Export CSV
                    </button>
                )}
            </div>

            {/* Filters Card */}
            <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div className="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <div className="flex items-center gap-2">
                        <Filter className="w-5 h-5 text-slate-400" />
                        <span className="font-semibold text-slate-700">Filters</span>
                    </div>
                </div>

                <form onSubmit={handleApplyFilters} className="p-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-7 gap-4">
                        {/* Branch */}
                        <div>
                            <label className="block text-xs font-medium text-slate-500 mb-1">Branch</label>
                            <select
                                value={filters.branch_id}
                                onChange={(e) => handleFilterChange('branch_id', e.target.value)}
                                className="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                            >
                                <option value="">All Branches</option>
                                {branches?.map((branch) => (
                                    <option key={branch.id} value={branch.id}>
                                        {branch.branch_name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Date From */}
                        <div>
                            <label className="block text-xs font-medium text-slate-500 mb-1">From Date</label>
                            <input
                                type="date"
                                value={filters.date_from}
                                onChange={(e) => handleFilterChange('date_from', e.target.value)}
                                className={`w-full px-3 py-2 rounded-lg border focus:ring-2 outline-none text-sm ${dateError
                                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20'
                                    : 'border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20'
                                    }`}
                            />
                        </div>

                        {/* Date To */}
                        <div>
                            <label className="block text-xs font-medium text-slate-500 mb-1">To Date</label>
                            <input
                                type="date"
                                value={filters.date_to}
                                onChange={(e) => handleFilterChange('date_to', e.target.value)}
                                className={`w-full px-3 py-2 rounded-lg border focus:ring-2 outline-none text-sm ${dateError
                                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20'
                                    : 'border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20'
                                    }`}
                            />
                        </div>

                        {/* Vehicle No */}
                        <div>
                            <label className="block text-xs font-medium text-slate-500 mb-1">Vehicle No</label>
                            <input
                                type="text"
                                value={filters.vehicle_no}
                                onChange={(e) => handleFilterChange('vehicle_no', e.target.value)}
                                placeholder="Enter vehicle no"
                                className="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                            />
                        </div>

                        {/* Vehicle Name */}
                        <div>
                            <label className="block text-xs font-medium text-slate-500 mb-1">Vehicle Name</label>
                            <input
                                type="text"
                                value={filters.vehicle_name}
                                onChange={(e) => handleFilterChange('vehicle_name', e.target.value)}
                                placeholder="Enter vehicle name"
                                className="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                            />
                        </div>

                        {/* Buttons */}
                        <div className="flex items-end gap-2 lg:col-span-2">
                            <button
                                type="submit"
                                className="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium inline-flex items-center gap-1"
                            >
                                <Search className="w-4 h-4" />
                                Filter
                            </button>
                            <button
                                type="button"
                                onClick={handleResetFilters}
                                className="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors text-sm"
                            >
                                Reset
                            </button>
                        </div>
                    </div>

                    {/* Date Error */}
                    {dateError && <p className="mt-2 text-sm text-red-600">{dateError}</p>}
                </form>
            </div>

            {/* Table Card */}
            <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full font-[Inter]">
                        <thead>
                            <tr className="bg-slate-50 border-b border-slate-200">
                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Ticket #
                                </th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Pay Mode
                                </th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Boat
                                </th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Time
                                </th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Vehicle No
                                </th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Vehicle Name
                                </th>
                                <th className="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {ticketData.length > 0 ? (
                                ticketData.map((ticket) => (
                                    <tr key={ticket.id} className="hover:bg-slate-50 transition-colors">
                                        <td className="px-4 py-3 text-sm text-slate-600">
                                            {formatDate(ticket.ticket_date || ticket.created_at)}
                                        </td>
                                        <td className="px-4 py-3">
                                            <span className="font-medium text-slate-800">
                                                #{ticket.ticket_no || ticket.id}
                                            </span>
                                            {ticket.ticket_no && (
                                                <span className="text-xs text-slate-400 block">ID: {ticket.id}</span>
                                            )}
                                        </td>
                                        <td className="px-4 py-3">
                                            <span
                                                className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getPaymentModeColor(
                                                    ticket.payment_mode
                                                )}`}
                                            >
                                                {ticket.payment_mode}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-sm text-slate-600">
                                            {ticket.ferry_boat?.name || '-'}
                                        </td>
                                        <td className="px-4 py-3 text-sm text-slate-600">
                                            {formatTime(ticket.ferry_time)}
                                        </td>
                                        <td className="px-4 py-3 text-sm text-slate-600">
                                            {ticket.ferry_type || '-'}
                                        </td>
                                        <td className="px-4 py-3">
                                            {ticket.vehicle_no_join ? (
                                                <span className="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-amber-100 text-amber-700">
                                                    <Car className="w-3 h-3 mr-1" />
                                                    {ticket.vehicle_no_join}
                                                </span>
                                            ) : (
                                                <span className="text-slate-400">-</span>
                                            )}
                                        </td>
                                        <td className="px-4 py-3 text-sm text-slate-600">
                                            {ticket.vehicle_name_join || '-'}
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <span className="font-semibold text-slate-800">
                                                ₹{formatCurrency(ticket.total_amount)}
                                            </span>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="9" className="px-4 py-12 text-center">
                                        <div className="flex flex-col items-center">
                                            <div className="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                                <Car className="w-8 h-8 text-slate-400" />
                                            </div>
                                            <p className="text-slate-500 font-medium">No vehicle tickets found</p>
                                            <p className="text-slate-400 text-sm mt-1">Try adjusting your filters</p>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                        {ticketData.length > 0 && (
                            <tfoot>
                                <tr className="bg-slate-50 border-t-2 border-slate-200">
                                    <td colSpan="8" className="px-4 py-3 text-right font-semibold text-slate-700">
                                        Total (this page):
                                    </td>
                                    <td className="px-4 py-3 text-right font-bold text-lg text-indigo-600">
                                        ₹{formatCurrency(pageTotalAmount)}
                                    </td>
                                </tr>
                            </tfoot>
                        )}
                    </table>
                </div>

                {/* Footer with Pagination */}
                <div className="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <p className="text-sm text-slate-600">
                        Page {currentPage} | Showing {ticketData.length} records
                    </p>
                    <div className="flex items-center gap-2">
                        {prevPageUrl ? (
                            <Link
                                href={prevPageUrl}
                                className="px-4 py-2 text-sm text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors inline-flex items-center gap-1"
                            >
                                <ChevronLeft className="w-4 h-4" />
                                Previous
                            </Link>
                        ) : (
                            <span className="px-4 py-2 text-sm text-slate-400 bg-slate-100 rounded-lg cursor-not-allowed inline-flex items-center gap-1">
                                <ChevronLeft className="w-4 h-4" />
                                Previous
                            </span>
                        )}

                        <span className="px-4 py-2 text-sm text-slate-600 bg-white border border-slate-200 rounded-lg">
                            Page {currentPage}
                        </span>

                        {hasMorePages ? (
                            <Link
                                href={nextPageUrl}
                                className="px-4 py-2 text-sm text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors inline-flex items-center gap-1"
                            >
                                Next
                                <ChevronRight className="w-4 h-4" />
                            </Link>
                        ) : (
                            <span className="px-4 py-2 text-sm text-slate-400 bg-slate-100 rounded-lg cursor-not-allowed inline-flex items-center gap-1">
                                Next
                                <ChevronRight className="w-4 h-4" />
                            </span>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}

VehicleTicketReports.layout = (page) => <Layout children={page} title="Vehicle Reports" />;
