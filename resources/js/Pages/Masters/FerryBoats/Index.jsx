import { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import { Ship, Plus, Pencil, Trash2, Filter, Search, X, MapPin } from 'lucide-react';

export default function FerryBoatsIndex({ boats, total, branches, branchId, auth }) {
    const { flash } = usePage().props;
    const [selectedBranch, setSelectedBranch] = useState(branchId || '');
    const userRoleId = auth?.user?.role_id;
    const canManage = [1, 2].includes(userRoleId);

    const handleFilter = (e) => {
        e.preventDefault();
        router.get(route('ferryboats.index'), {
            branch_id: selectedBranch || undefined,
        }, { preserveState: true });
    };

    const handleClearFilter = () => {
        setSelectedBranch('');
        router.get(route('ferryboats.index'));
    };

    const handleDelete = (boat) => {
        if (confirm(`Delete ferry boat "${boat.name}"?`)) {
            router.delete(route('ferryboats.destroy', boat.id));
        }
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-slate-800 tracking-tight">
                        Ferry Boats
                    </h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Manage your fleet of ferry boats
                    </p>
                </div>
                {canManage && (
                    <div className="mt-4 sm:mt-0">
                        <Link
                            href={route('ferryboats.create')}
                            className="inline-flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors shadow-sm"
                        >
                            <Plus className="w-5 h-5" />
                            <span>Add Ferry Boat</span>
                        </Link>
                    </div>
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
                    <h2 className="font-semibold text-slate-700 flex items-center space-x-2">
                        <Filter className="w-4 h-4" />
                        <span>Filters</span>
                    </h2>
                </div>
                <form onSubmit={handleFilter} className="p-6">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Branch
                            </label>
                            <select
                                value={selectedBranch}
                                onChange={(e) => setSelectedBranch(e.target.value)}
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                            >
                                <option value="">All Branches</option>
                                {branches?.map((branch) => (
                                    <option key={branch.id} value={branch.id}>
                                        {branch.branch_name}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="flex items-end space-x-2">
                            <button
                                type="submit"
                                className="inline-flex items-center space-x-2 bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-xl font-medium transition-colors"
                            >
                                <Search className="w-4 h-4" />
                                <span>Filter</span>
                            </button>
                            <button
                                type="button"
                                onClick={handleClearFilter}
                                className="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium transition-colors"
                            >
                                <X className="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {/* Table Card */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                {/* Table Header */}
                <div className="px-6 py-4 border-b border-slate-200 flex items-center space-x-3">
                    <div className="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                        <Ship className="w-5 h-5 text-purple-600" />
                    </div>
                    <div>
                        <h2 className="font-semibold text-slate-800">All Ferry Boats</h2>
                        <p className="text-sm text-slate-500">
                            Total: {total} {total === 1 ? 'boat' : 'boats'}
                        </p>
                    </div>
                </div>

                {/* Table */}
                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead className="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th className="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    ID
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Boat No.
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Boat Name
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Branch
                                </th>
                                {canManage && (
                                    <th className="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        Actions
                                    </th>
                                )}
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {boats?.length > 0 ? (
                                boats.map((boat) => (
                                    <tr key={boat.id} className="hover:bg-slate-50 transition-colors">
                                        <td className="px-6 py-4">
                                            <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                                #{boat.id}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 font-medium text-slate-800">
                                            {boat.number}
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center space-x-3">
                                                <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white">
                                                    <Ship className="w-4 h-4" />
                                                </div>
                                                <span className="font-medium text-slate-800">
                                                    {boat.name}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700">
                                                <MapPin className="w-3 h-3 mr-1" />
                                                {boat.branch?.branch_name || '-'}
                                            </span>
                                        </td>
                                        {canManage && (
                                            <td className="px-6 py-4">
                                                <div className="flex items-center justify-end space-x-2">
                                                    <Link
                                                        href={route('ferryboats.edit', boat.id)}
                                                        className="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors flex items-center justify-center"
                                                        title="Edit"
                                                    >
                                                        <Pencil className="w-4 h-4" />
                                                    </Link>
                                                    <button
                                                        onClick={() => handleDelete(boat)}
                                                        className="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors flex items-center justify-center"
                                                        title="Delete"
                                                    >
                                                        <Trash2 className="w-4 h-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        )}
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan={canManage ? 5 : 4} className="px-6 py-12 text-center">
                                        <div className="flex flex-col items-center">
                                            <div className="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                                <Ship className="w-8 h-8 text-slate-400" />
                                            </div>
                                            <h3 className="text-lg font-medium text-slate-800 mb-1">
                                                No ferry boats found
                                            </h3>
                                            <p className="text-sm text-slate-500">
                                                Try adjusting your filter or add a new ferry boat.
                                            </p>
                                            {canManage && (
                                                <Link
                                                    href={route('ferryboats.create')}
                                                    className="mt-4 inline-flex items-center space-x-2 text-indigo-600 hover:text-indigo-700 font-medium"
                                                >
                                                    <Plus className="w-4 h-4" />
                                                    <span>Add ferry boat</span>
                                                </Link>
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Footer */}
                <div className="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    <p className="text-sm text-slate-600">
                        Showing <span className="font-medium">{boats?.length || 0}</span> of{' '}
                        <span className="font-medium">{total}</span> ferry boats
                    </p>
                </div>
            </div>
        </div>
    );
}

FerryBoatsIndex.layout = (page) => <Layout children={page} title="Ferry Boats" />;
