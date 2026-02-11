import { Head, Link, router, usePage } from '@inertiajs/react';
import { Users, Plus, Pencil, Trash2 } from 'lucide-react';
import Layout from '@/Layouts/Layout';

export default function ManagersIndex({ managers }) {
    const { flash } = usePage().props;

    const handleDelete = (manager) => {
        if (confirm(`Delete manager "${manager.name}"?`)) {
            router.delete(route('manager.destroy', manager.id));
        }
    };

    return (
        <>
            <Head title="Managers" />

            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-slate-800">Managers</h1>
                    <p className="mt-1 text-sm text-slate-500">Manage branch managers</p>
                </div>
                <div className="mt-4 sm:mt-0">
                    <Link
                        href={route('manager.create')}
                        className="inline-flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors shadow-sm"
                    >
                        <Plus className="w-5 h-5" />
                        <span>Add Manager</span>
                    </Link>
                </div>
            </div>

            {/* Flash Message */}
            {flash?.success && (
                <div className="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                    {flash.success}
                </div>
            )}

            {/* Table Card */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div className="px-6 py-4 border-b border-slate-200 flex items-center space-x-3">
                    <div className="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                        <Users className="w-5 h-5 text-blue-600" />
                    </div>
                    <div>
                        <h2 className="font-semibold text-slate-800">All Managers</h2>
                        <p className="text-sm text-slate-500">
                            Total: {managers.total} managers
                        </p>
                    </div>
                </div>

                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead className="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th className="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Name
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Email
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Mobile
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Branch
                                </th>
                                <th className="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {managers.data.length > 0 ? (
                                managers.data.map((manager) => (
                                    <tr key={manager.id} className="hover:bg-slate-50 transition-colors">
                                        <td className="px-6 py-4">
                                            <div className="flex items-center space-x-3">
                                                <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                                                    {manager.name.charAt(0).toUpperCase()}
                                                </div>
                                                <span className="font-medium text-slate-800">
                                                    {manager.name}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-slate-600">{manager.email}</td>
                                        <td className="px-6 py-4 text-slate-600">
                                            {manager.mobile || '-'}
                                        </td>
                                        <td className="px-6 py-4 text-slate-600">
                                            {manager.branch?.branch_name || '-'}
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center justify-end space-x-2">
                                                <Link
                                                    href={route('manager.edit', manager.id)}
                                                    className="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors flex items-center justify-center"
                                                    title="Edit"
                                                >
                                                    <Pencil className="w-4 h-4" />
                                                </Link>
                                                <button
                                                    onClick={() => handleDelete(manager)}
                                                    className="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors flex items-center justify-center"
                                                    title="Delete"
                                                >
                                                    <Trash2 className="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="5" className="px-6 py-12 text-center">
                                        <div className="flex flex-col items-center">
                                            <div className="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                                <Users className="w-8 h-8 text-slate-400" />
                                            </div>
                                            <h3 className="text-lg font-medium text-slate-800 mb-1">
                                                No managers found
                                            </h3>
                                            <p className="text-sm text-slate-500">
                                                Add your first manager to get started.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                {managers.data.length > 0 && (
                    <div className="px-6 py-4 border-t border-slate-200 bg-slate-50">
                        <div className="flex items-center justify-between">
                            <p className="text-sm text-slate-600">
                                Showing <span className="font-medium">{managers.from}</span> to{' '}
                                <span className="font-medium">{managers.to}</span> of{' '}
                                <span className="font-medium">{managers.total}</span> managers
                            </p>
                            <div className="flex space-x-2">
                                {managers.prev_page_url && (
                                    <Link
                                        href={managers.prev_page_url}
                                        className="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50"
                                    >
                                        Previous
                                    </Link>
                                )}
                                {managers.next_page_url && (
                                    <Link
                                        href={managers.next_page_url}
                                        className="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50"
                                    >
                                        Next
                                    </Link>
                                )}
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}

ManagersIndex.layout = (page) => <Layout children={page} />;
