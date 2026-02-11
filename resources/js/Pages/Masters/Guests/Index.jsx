import { useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import {
    Users,
    Plus,
    Search,
    X,
    Pencil,
    Trash2,
    Filter,
} from 'lucide-react';

export default function GuestsIndex({ guests, branches, total, filters }) {
    const { auth } = usePage().props;
    const user = auth?.user;
    const canManage = user && [1, 2].includes(user.role_id);

    const [searchFilters, setSearchFilters] = useState({
        branch_id: filters?.branch_id || '',
    });

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('guests.index'), searchFilters, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const clearFilters = () => {
        setSearchFilters({ branch_id: '' });
        router.get(route('guests.index'));
    };

    const handleDelete = (guest) => {
        if (confirm(`Delete guest "${guest.name}"?`)) {
            router.delete(route('guests.destroy', guest.id));
        }
    };

    return (
        <>
            <Head>
                <title>Guests - Jetty Admin</title>
            </Head>

            {/* Page Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-slate-800">Guests</h1>
                    <p className="mt-1 text-sm text-slate-500">Manage registered guests</p>
                </div>
                {canManage && (
                    <div className="mt-4 sm:mt-0">
                        <Link
                            href={route('guests.create')}
                            className="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors shadow-sm"
                        >
                            <Plus className="w-5 h-5" />
                            <span>Add Guest</span>
                        </Link>
                    </div>
                )}
            </div>

            {/* Filters Card */}
            <div className="bg-white rounded-2xl border border-slate-200 mb-6 overflow-hidden">
                <div className="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 className="font-semibold text-slate-700 flex items-center gap-2">
                        <Filter className="w-4 h-4" />
                        <span>Filters</span>
                    </h2>
                </div>
                <form onSubmit={handleSearch} className="p-6">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Branch
                            </label>
                            <select
                                value={searchFilters.branch_id}
                                onChange={(e) => setSearchFilters({ ...searchFilters, branch_id: e.target.value })}
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                            >
                                {canManage && <option value="">All Branches</option>}
                                {branches?.map((branch) => (
                                    <option key={branch.id} value={branch.id}>
                                        {branch.branch_name}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="flex items-end gap-2">
                            <button
                                type="submit"
                                className="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-xl font-medium transition-colors"
                            >
                                <Search className="w-4 h-4" />
                                <span>Filter</span>
                            </button>
                            <button
                                type="button"
                                onClick={clearFilters}
                                className="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors"
                            >
                                <X className="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {/* Data Table */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div className="px-6 py-4 border-b border-slate-200 flex items-center gap-3">
                    <div className="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                        <Users className="w-5 h-5 text-orange-600" />
                    </div>
                    <div>
                        <h2 className="font-semibold text-slate-800">All Guests</h2>
                        <p className="text-sm text-slate-500">Total: {total || guests?.length || 0} guests</p>
                    </div>
                </div>

                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead className="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th className="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    #
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Name
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Category
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
                            {guests?.length > 0 ? (
                                guests.map((guest, index) => (
                                    <tr key={guest.id} className="hover:bg-slate-50/50 transition-colors">
                                        <td className="px-6 py-4">
                                            <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                                {index + 1}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center gap-3">
                                                <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-semibold text-sm">
                                                    {guest.name?.charAt(0).toUpperCase()}
                                                </div>
                                                <span className="font-medium text-slate-800">
                                                    {guest.name}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-purple-50 text-purple-700">
                                                {guest.category?.name || 'N/A'}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700">
                                                {guest.branch?.branch_name || '-'}
                                            </span>
                                        </td>
                                        {canManage && (
                                            <td className="px-6 py-4">
                                                <div className="flex items-center justify-end gap-2">
                                                    <Link
                                                        href={route('guests.edit', guest.id)}
                                                        className="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors flex items-center justify-center"
                                                        title="Edit"
                                                    >
                                                        <Pencil className="w-4 h-4" />
                                                    </Link>
                                                    <button
                                                        onClick={() => handleDelete(guest)}
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
                                                <Users className="w-8 h-8 text-slate-400" />
                                            </div>
                                            <h3 className="text-lg font-medium text-slate-800 mb-1">
                                                No guests found
                                            </h3>
                                            <p className="text-sm text-slate-500">
                                                Try adjusting your filter or add a new guest.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                <div className="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    <p className="text-sm text-slate-600">
                        Showing <span className="font-medium">{guests?.length || 0}</span> of{' '}
                        <span className="font-medium">{total || guests?.length || 0}</span> guests
                    </p>
                </div>
            </div>
        </>
    );
}

GuestsIndex.layout = (page) => <Layout>{page}</Layout>;
