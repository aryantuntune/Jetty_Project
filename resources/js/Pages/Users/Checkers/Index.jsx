import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { Users, Plus, Pencil, Trash2, Search, ChevronLeft, ChevronRight, CheckCircle } from 'lucide-react';
import Layout from '@/Layouts/Layout';

export default function CheckersIndex({ checkers, isManager }) {
    const [search, setSearch] = useState('');
    const [deleting, setDeleting] = useState(null);

    const filteredCheckers = checkers.data?.filter(checker =>
        checker.name.toLowerCase().includes(search.toLowerCase()) ||
        checker.email.toLowerCase().includes(search.toLowerCase())
    ) || [];

    const handleDelete = (checker) => {
        if (confirm(`Are you sure you want to delete ${checker.name}?`)) {
            setDeleting(checker.id);
            router.delete(route('checker.destroy', checker.id), {
                onFinish: () => setDeleting(null),
            });
        }
    };

    return (
        <>
            <Head title="Checkers" />

            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-slate-800">Ticket Checkers</h1>
                    <p className="mt-1 text-sm text-slate-500">
                        {isManager ? 'Checkers assigned to your ferry route' : 'Manage ticket verification staff'}
                    </p>
                </div>
                <Link
                    href={route('checker.create')}
                    className="inline-flex items-center justify-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors"
                >
                    <Plus className="w-5 h-5" />
                    <span>Add Checker</span>
                </Link>
            </div>

            {/* Search & Stats Card */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                {/* Stats Bar */}
                <div className="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div className="flex items-center space-x-3">
                        <div className="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                            <CheckCircle className="w-5 h-5 text-orange-600" />
                        </div>
                        <div>
                            <p className="text-sm text-slate-500">Total Checkers</p>
                            <p className="text-xl font-semibold text-slate-800">{checkers.total || 0}</p>
                        </div>
                    </div>

                    {/* Search */}
                    <div className="relative w-full sm:w-72">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                        <input
                            type="text"
                            placeholder="Search checkers..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors"
                        />
                    </div>
                </div>

                {/* Table */}
                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead>
                            <tr className="bg-slate-50 border-b border-slate-200">
                                <th className="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Checker</th>
                                <th className="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Contact</th>
                                <th className="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Branch</th>
                                <th className="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Ferry</th>
                                <th className="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-200">
                            {filteredCheckers.length === 0 ? (
                                <tr>
                                    <td colSpan="5" className="px-6 py-12 text-center">
                                        <div className="flex flex-col items-center">
                                            <Users className="w-12 h-12 text-slate-300 mb-3" />
                                            <p className="text-slate-500 font-medium">No checkers found</p>
                                            <p className="text-sm text-slate-400 mt-1">
                                                {search ? 'Try a different search term' : 'Add your first checker to get started'}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            ) : (
                                filteredCheckers.map((checker) => (
                                    <tr key={checker.id} className="hover:bg-slate-50 transition-colors">
                                        <td className="px-6 py-4">
                                            <div className="flex items-center space-x-3">
                                                <div className="w-10 h-10 rounded-full bg-gradient-to-br from-orange-500 to-amber-600 flex items-center justify-center text-white font-semibold">
                                                    {checker.name?.charAt(0).toUpperCase()}
                                                </div>
                                                <div>
                                                    <p className="font-medium text-slate-800">{checker.name}</p>
                                                    <p className="text-sm text-slate-500">{checker.email}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <p className="text-slate-600">{checker.mobile || '-'}</p>
                                        </td>
                                        <td className="px-6 py-4">
                                            <p className="text-slate-600">{checker.branch?.branch_name || '-'}</p>
                                        </td>
                                        <td className="px-6 py-4">
                                            <p className="text-slate-600">{checker.ferryboat?.name || '-'}</p>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center justify-end space-x-2">
                                                <Link
                                                    href={route('checker.edit', checker.id)}
                                                    className="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                                    title="Edit"
                                                >
                                                    <Pencil className="w-4 h-4" />
                                                </Link>
                                                <button
                                                    onClick={() => handleDelete(checker)}
                                                    disabled={deleting === checker.id}
                                                    className="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-50"
                                                    title="Delete"
                                                >
                                                    <Trash2 className="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                {checkers.last_page > 1 && (
                    <div className="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
                        <p className="text-sm text-slate-500">
                            Showing {checkers.from} to {checkers.to} of {checkers.total} checkers
                        </p>
                        <div className="flex items-center space-x-2">
                            {checkers.prev_page_url && (
                                <Link
                                    href={checkers.prev_page_url}
                                    className="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors"
                                >
                                    <ChevronLeft className="w-5 h-5" />
                                </Link>
                            )}
                            <span className="px-3 py-1 text-sm font-medium text-slate-600">
                                Page {checkers.current_page} of {checkers.last_page}
                            </span>
                            {checkers.next_page_url && (
                                <Link
                                    href={checkers.next_page_url}
                                    className="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors"
                                >
                                    <ChevronRight className="w-5 h-5" />
                                </Link>
                            )}
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}

CheckersIndex.layout = (page) => <Layout children={page} />;
