import { Head, Link, router, usePage } from '@inertiajs/react';
import { Shield, Plus, Pencil, Trash2 } from 'lucide-react';
import Layout from '@/Layouts/Layout';

export default function AdminsIndex({ administrators }) {
    const { flash } = usePage().props;

    const handleDelete = (admin) => {
        if (confirm(`Delete administrator "${admin.name}"?`)) {
            router.delete(route('admin.destroy', admin.id));
        }
    };

    return (
        <>
            <Head title="Administrators" />

            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-slate-800">Administrators</h1>
                    <p className="mt-1 text-sm text-slate-500">Manage system administrators</p>
                </div>
                <div className="mt-4 sm:mt-0">
                    <Link
                        href={route('admin.create')}
                        className="inline-flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors shadow-sm"
                    >
                        <Plus className="w-5 h-5" />
                        <span>Add Administrator</span>
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
                    <div className="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                        <Shield className="w-5 h-5 text-red-600" />
                    </div>
                    <div>
                        <h2 className="font-semibold text-slate-800">All Administrators</h2>
                        <p className="text-sm text-slate-500">
                            Total: {administrators.total} administrators
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
                                <th className="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {administrators.data.length > 0 ? (
                                administrators.data.map((admin) => (
                                    <tr key={admin.id} className="hover:bg-slate-50 transition-colors">
                                        <td className="px-6 py-4">
                                            <div className="flex items-center space-x-3">
                                                <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center text-white font-semibold text-sm">
                                                    {admin.name.charAt(0).toUpperCase()}
                                                </div>
                                                <span className="font-medium text-slate-800">
                                                    {admin.name}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-slate-600">{admin.email}</td>
                                        <td className="px-6 py-4 text-slate-600">
                                            {admin.mobile || '-'}
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center justify-end space-x-2">
                                                <Link
                                                    href={route('admin.edit', admin.id)}
                                                    className="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors flex items-center justify-center"
                                                    title="Edit"
                                                >
                                                    <Pencil className="w-4 h-4" />
                                                </Link>
                                                <button
                                                    onClick={() => handleDelete(admin)}
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
                                    <td colSpan="4" className="px-6 py-12 text-center">
                                        <div className="flex flex-col items-center">
                                            <div className="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                                <Shield className="w-8 h-8 text-slate-400" />
                                            </div>
                                            <h3 className="text-lg font-medium text-slate-800 mb-1">
                                                No administrators found
                                            </h3>
                                            <p className="text-sm text-slate-500">
                                                Add your first administrator to get started.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                {administrators.data.length > 0 && (
                    <div className="px-6 py-4 border-t border-slate-200 bg-slate-50">
                        <div className="flex items-center justify-between">
                            <p className="text-sm text-slate-600">
                                Showing <span className="font-medium">{administrators.from}</span> to{' '}
                                <span className="font-medium">{administrators.to}</span> of{' '}
                                <span className="font-medium">{administrators.total}</span> administrators
                            </p>
                            <div className="flex space-x-2">
                                {administrators.prev_page_url && (
                                    <Link
                                        href={administrators.prev_page_url}
                                        className="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50"
                                    >
                                        Previous
                                    </Link>
                                )}
                                {administrators.next_page_url && (
                                    <Link
                                        href={administrators.next_page_url}
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

AdminsIndex.layout = (page) => <Layout children={page} />;
