import { useState, Fragment } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import { Tag, Plus, Pencil, Trash2, Filter, Search, X, MapPin } from 'lucide-react';

export default function ItemRatesIndex({ itemRates, branches, categories, filters, auth }) {
    const { flash } = usePage().props;
    const userRoleId = auth?.user?.role_id;
    const canManage = [1, 2].includes(userRoleId);

    const [formFilters, setFormFilters] = useState({
        branch_id: filters?.branch_id || '',
        item_category_id: filters?.item_category_id || '',
        search: filters?.search || '',
    });

    // Pagination data
    const rates = itemRates?.data || [];
    const total = itemRates?.total || 0;
    const from = itemRates?.from || 0;
    const to = itemRates?.to || 0;

    const handleFilter = (e) => {
        e.preventDefault();
        const params = {};
        if (formFilters.branch_id) params.branch_id = formFilters.branch_id;
        if (formFilters.item_category_id) params.item_category_id = formFilters.item_category_id;
        if (formFilters.search) params.search = formFilters.search;

        router.get(route('item-rates.index'), params, { preserveState: true });
    };

    const handleClearFilters = () => {
        setFormFilters({
            branch_id: '',
            item_category_id: '',
            search: '',
        });
        router.get(route('item-rates.index'));
    };

    const handleDelete = (rate) => {
        if (confirm(`Delete item rate "${rate.item_name}"?`)) {
            router.delete(route('item-rates.destroy', rate.id));
        }
    };

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(amount || 0);
    };

    const formatDate = (dateString) => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-IN', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        });
    };

    // Calculate total (rate + levy)
    const calculateTotal = (rate, levy) => {
        const rateNum = parseFloat(rate) || 0;
        const levyNum = parseFloat(levy) || 0;
        return rateNum + levyNum;
    };

    // Group by branch for display
    let lastBranchId = null;

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold text-slate-800 tracking-tight">
                        Item Rate Slabs
                    </h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Manage pricing slabs for ferry items
                    </p>
                </div>
                {canManage && (
                    <Link
                        href={route('item-rates.create')}
                        className="inline-flex items-center justify-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors shadow-sm"
                    >
                        <Plus className="w-5 h-5" />
                        <span>Add New Rate Slab</span>
                    </Link>
                )}
            </div>

            {/* Flash Message */}
            {flash?.success && (
                <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                    {flash.success}
                </div>
            )}

            {/* Filters Card */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div className="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <div className="flex items-center space-x-2">
                        <Filter className="w-5 h-5 text-slate-400" />
                        <span className="font-semibold text-slate-700">Filters</span>
                    </div>
                </div>
                <form onSubmit={handleFilter} className="p-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        {/* Branch Filter */}
                        <div>
                            <label className="block text-xs font-medium text-slate-500 mb-1">
                                Branch
                            </label>
                            <select
                                value={formFilters.branch_id}
                                onChange={(e) => setFormFilters({ ...formFilters, branch_id: e.target.value })}
                                className="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                            >
                                {[1, 2].includes(userRoleId) && (
                                    <option value="">All Branches</option>
                                )}
                                {branches?.map((branch) => (
                                    <option key={branch.id} value={branch.id}>
                                        {branch.branch_name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Category Filter */}
                        <div>
                            <label className="block text-xs font-medium text-slate-500 mb-1">
                                Category
                            </label>
                            <select
                                value={formFilters.item_category_id}
                                onChange={(e) => setFormFilters({ ...formFilters, item_category_id: e.target.value })}
                                className="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                            >
                                <option value="">All Categories</option>
                                {categories?.map((cat) => (
                                    <option key={cat.id} value={cat.id}>
                                        {cat.category_name}
                                    </option>
                                ))}
                            </select>
                        </div>


                        {/* Search */}
                        <div>
                            <label className="block text-xs font-medium text-slate-500 mb-1">
                                Search Item
                            </label>
                            <input
                                type="text"
                                value={formFilters.search}
                                onChange={(e) => setFormFilters({ ...formFilters, search: e.target.value })}
                                placeholder="Item name..."
                                className="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                            />
                        </div>

                        {/* Buttons */}
                        <div className="flex items-end space-x-2">
                            <button
                                type="submit"
                                className="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900 transition-colors text-sm font-medium"
                            >
                                <Search className="w-4 h-4 inline mr-1" />
                                Filter
                            </button>
                            <button
                                type="button"
                                onClick={handleClearFilters}
                                className="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors text-sm"
                            >
                                <X className="w-4 h-4 inline" />
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {/* Table Card */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead>
                            <tr className="bg-slate-50 border-b border-slate-200">
                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Branch
                                </th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Item Name
                                </th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Category
                                </th>
                                <th className="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Rate
                                </th>
                                <th className="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Levy
                                </th>
                                <th className="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Total
                                </th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Valid From
                                </th>
                                {canManage && (
                                    <th className="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                )}
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {rates.length > 0 ? (
                                rates.map((rate) => {
                                    // Show branch header row when branch changes (only when not filtering by branch)
                                    const showBranchHeader = !formFilters.branch_id && lastBranchId !== rate.branch_id;
                                    if (showBranchHeader) {
                                        lastBranchId = rate.branch_id;
                                    }

                                    return (
                                        <Fragment key={rate.id}>
                                            {showBranchHeader && (
                                                <tr className="bg-blue-50">
                                                    <td colSpan={canManage ? 8 : 7} className="px-4 py-2">
                                                        <span className="font-bold text-blue-800 text-sm flex items-center">
                                                            <MapPin className="w-4 h-4 mr-1" />
                                                            {rate.branch?.branch_name?.toUpperCase() || 'UNKNOWN'}
                                                        </span>
                                                    </td>
                                                </tr>
                                            )}
                                            <tr className="hover:bg-slate-50 transition-colors">
                                                <td className="px-4 py-3">
                                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700">
                                                        {rate.branch?.branch_name?.toUpperCase() || '-'}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3">
                                                    <span className="font-medium text-slate-800">
                                                        {rate.item_name?.toUpperCase()}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 text-sm text-slate-600">
                                                    {rate.category?.category_name?.toUpperCase() || '-'}
                                                </td>
                                                <td className="px-4 py-3 text-right">
                                                    <span className="font-semibold text-slate-800">
                                                        {formatCurrency(rate.item_rate)}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 text-right text-sm text-slate-600">
                                                    {formatCurrency(rate.item_lavy)}
                                                </td>
                                                <td className="px-4 py-3 text-right">
                                                    <span className="font-bold text-green-600">
                                                        {formatCurrency(calculateTotal(rate.item_rate, rate.item_lavy))}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 text-sm text-slate-600">
                                                    {formatDate(rate.starting_date)}
                                                </td>
                                                {canManage && (
                                                    <td className="px-4 py-3">
                                                        <div className="flex items-center justify-center space-x-2">
                                                            <Link
                                                                href={route('item-rates.edit', rate.id)}
                                                                className="p-2 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors"
                                                                title="Edit"
                                                            >
                                                                <Pencil className="w-4 h-4" />
                                                            </Link>
                                                            <button
                                                                onClick={() => handleDelete(rate)}
                                                                className="p-2 rounded-lg text-red-600 hover:bg-red-50 transition-colors"
                                                                title="Delete"
                                                            >
                                                                <Trash2 className="w-4 h-4" />
                                                            </button>
                                                        </div>
                                                    </td>
                                                )}
                                            </tr>
                                        </Fragment>
                                    );
                                })
                            ) : (
                                <tr>
                                    <td colSpan={canManage ? 8 : 7} className="px-4 py-12 text-center">
                                        <div className="flex flex-col items-center">
                                            <div className="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                                <Tag className="w-8 h-8 text-slate-400" />
                                            </div>
                                            <p className="text-slate-500 font-medium">No rate slabs found</p>
                                            <p className="text-slate-400 text-sm mt-1">Add your first item rate slab</p>
                                            {canManage && (
                                                <Link
                                                    href={route('item-rates.create')}
                                                    className="mt-4 inline-flex items-center space-x-2 text-indigo-600 hover:text-indigo-700 font-medium"
                                                >
                                                    <Plus className="w-4 h-4" />
                                                    <span>Add rate slab</span>
                                                </Link>
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Footer with Pagination */}
                <div className="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <p className="text-sm text-slate-600">
                        Showing <span className="font-semibold">{from}</span> - <span className="font-semibold">{to}</span> of{' '}
                        <span className="font-semibold">{total}</span> records
                    </p>
                    <div className="flex items-center space-x-2">
                        {itemRates?.prev_page_url && (
                            <Link
                                href={itemRates.prev_page_url}
                                className="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors"
                            >
                                Previous
                            </Link>
                        )}
                        {itemRates?.next_page_url && (
                            <Link
                                href={itemRates.next_page_url}
                                className="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors"
                            >
                                Next
                            </Link>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}

ItemRatesIndex.layout = (page) => <Layout children={page} title="Item Rate Slabs" />;
