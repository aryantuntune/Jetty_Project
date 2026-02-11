import { useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { Building2, Plus, Pencil, Trash2, Search, X, Filter } from 'lucide-react';
import Layout from '@/Layouts/Layout';

export default function BranchesIndex({ branches, total, filters }) {
    const { flash, auth } = usePage().props;
    const userRoleId = auth?.user?.role_id;
    const canManage = [1, 2].includes(userRoleId);

    const [searchBranchId, setSearchBranchId] = useState(filters?.branch_id || '');
    const [searchBranchName, setSearchBranchName] = useState(filters?.branch_name || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(
            route('branches.index'),
            {
                branch_id: searchBranchId || undefined,
                branch_name: searchBranchName || undefined,
            },
            { preserveState: true, preserveScroll: true }
        );
    };

    const handleClearFilters = () => {
        setSearchBranchId('');
        setSearchBranchName('');
        router.get(route('branches.index'));
    };

    const handleDelete = (branch) => {
        if (confirm(`Delete branch "${branch.branch_name}"?`)) {
            router.delete(route('branches.destroy', branch.id));
        }
    };

    const hasFilters = searchBranchId || searchBranchName;

    return (
        <>
            <Head title="Branches" />

            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-slate-800">Branches</h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Manage your ferry branch locations
                    </p>
                </div>
                {canManage && (
                    <div className="mt-4 sm:mt-0">
                        <Link
                            href={route('branches.create')}
                            className="inline-flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors shadow-sm"
                        >
                            <Plus className="w-5 h-5" />
                            <span>Add Branch</span>
                        </Link>
                    </div>
                )}
            </div>

            {/* Flash Message */}
            {flash?.success && (
                <div className="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                    {flash.success}
                </div>
            )}

            {/* Filters Card */}
            <div className="bg-white rounded-2xl border border-slate-200 mb-6 overflow-hidden">
                <div className="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 className="font-semibold text-slate-700 flex items-center space-x-2">
                        <Filter className="w-4 h-4" />
                        <span>Filters</span>
                    </h2>
                </div>
                <form onSubmit={handleSearch} className="p-6">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Branch ID
                            </label>
                            <input
                                type="text"
                                value={searchBranchId}
                                onChange={(e) => setSearchBranchId(e.target.value)}
                                placeholder="Search by ID..."
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Branch Name
                            </label>
                            <input
                                type="text"
                                value={searchBranchName}
                                onChange={(e) => setSearchBranchName(e.target.value)}
                                placeholder="Search by name..."
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                            />
                        </div>
                        <div className="flex items-end space-x-2 sm:col-span-2 lg:col-span-2">
                            <button
                                type="submit"
                                className="flex-1 inline-flex items-center justify-center space-x-2 bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-xl font-medium transition-colors"
                            >
                                <Search className="w-4 h-4" />
                                <span>Search</span>
                            </button>
                            {hasFilters && (
                                <button
                                    type="button"
                                    onClick={handleClearFilters}
                                    className="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium transition-colors"
                                >
                                    <X className="w-4 h-4" />
                                </button>
                            )}
                        </div>
                    </div>
                </form>
            </div>

            {/* Table Card */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                {/* Table Header */}
                <div className="px-6 py-4 border-b border-slate-200 flex items-center space-x-3">
                    <div className="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <Building2 className="w-5 h-5 text-indigo-600" />
                    </div>
                    <div>
                        <h2 className="font-semibold text-slate-800">All Branches</h2>
                        <p className="text-sm text-slate-500">Total: {total} branches</p>
                    </div>
                </div>

                {/* Table */}
                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead className="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th className="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Branch ID
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Branch Name
                                </th>
                                <th className="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {branches.length > 0 ? (
                                branches.map((branch) => (
                                    <tr
                                        key={branch.id}
                                        className="hover:bg-slate-50 transition-colors"
                                    >
                                        <td className="px-6 py-4">
                                            <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                                {branch.branch_id}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center space-x-3">
                                                <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white font-semibold text-sm">
                                                    {branch.branch_name
                                                        .charAt(0)
                                                        .toUpperCase()}
                                                </div>
                                                <span className="font-medium text-slate-800">
                                                    {branch.branch_name}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center justify-end space-x-2">
                                                {canManage ? (
                                                    <>
                                                        <Link
                                                            href={route(
                                                                'branches.edit',
                                                                branch.id
                                                            )}
                                                            className="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors flex items-center justify-center"
                                                            title="Edit branch"
                                                            aria-label={`Edit ${branch.branch_name}`}
                                                        >
                                                            <Pencil className="w-4 h-4" />
                                                        </Link>
                                                        <button
                                                            onClick={() =>
                                                                handleDelete(branch)
                                                            }
                                                            className="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors flex items-center justify-center"
                                                            title="Delete branch"
                                                            aria-label={`Delete ${branch.branch_name}`}
                                                        >
                                                            <Trash2 className="w-4 h-4" />
                                                        </button>
                                                    </>
                                                ) : (
                                                    <span className="text-sm text-slate-400">
                                                        No actions
                                                    </span>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="3" className="px-6 py-12 text-center">
                                        <div className="flex flex-col items-center">
                                            <div className="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                                <Building2 className="w-8 h-8 text-slate-400" />
                                            </div>
                                            <h3 className="text-lg font-medium text-slate-800 mb-1">
                                                No branches found
                                            </h3>
                                            <p className="text-sm text-slate-500">
                                                {hasFilters
                                                    ? 'Try adjusting your search filters.'
                                                    : 'Add your first branch to get started.'}
                                            </p>
                                            {canManage && !hasFilters && (
                                                <Link
                                                    href={route('branches.create')}
                                                    className="mt-4 inline-flex items-center space-x-2 text-indigo-600 hover:text-indigo-700 font-medium"
                                                >
                                                    <Plus className="w-4 h-4" />
                                                    <span>Add new branch</span>
                                                </Link>
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Table Footer */}
                <div className="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    <p className="text-sm text-slate-600">
                        Showing <span className="font-medium">{branches.length}</span> of{' '}
                        <span className="font-medium">{total}</span> branches
                    </p>
                </div>
            </div>
        </>
    );
}

BranchesIndex.layout = (page) => <Layout children={page} />;
