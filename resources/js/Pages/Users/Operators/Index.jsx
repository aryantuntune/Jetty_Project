import { Head, Link, usePage, router } from '@inertiajs/react';
import { Ticket, Plus, Pencil, Trash2, Info, ChevronLeft, ChevronRight } from 'lucide-react';

export default function Index({ operators, isManager }) {
    const { flash } = usePage().props;

    const handleDelete = (operator) => {
        if (confirm(`Are you sure you want to delete ${operator.name}?`)) {
            router.delete(route('operator.destroy', operator.id));
        }
    };

    return (
        <>
            <Head title="Operators" />

            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Operators</h1>
                    <p className="text-sm text-gray-500 mt-1">
                        Manage ferry ticket counter operators
                    </p>
                </div>
                <Link
                    href={route('operator.create')}
                    className="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-colors"
                >
                    <Plus className="w-5 h-5" />
                    <span>Add Operator</span>
                </Link>
            </div>

            {/* Manager Note */}
            {isManager && (
                <div className="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200">
                    <div className="flex items-start gap-3">
                        <Info className="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
                        <p className="text-sm text-amber-800">
                            You can only manage operators on your ferry route
                        </p>
                    </div>
                </div>
            )}

            {/* Success Flash Message */}
            {flash?.success && (
                <div className="mb-6 p-4 rounded-xl bg-green-50 border border-green-200">
                    <p className="text-sm text-green-700 font-medium">{flash.success}</p>
                </div>
            )}

            {/* Operators Table Card */}
            <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                {operators.data && operators.data.length > 0 ? (
                    <>
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="border-b border-slate-200 bg-slate-50">
                                        <th className="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th className="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th className="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Mobile
                                        </th>
                                        <th className="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Branch
                                        </th>
                                        <th className="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Ferry
                                        </th>
                                        <th className="text-right px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-200">
                                    {operators.data.map((operator) => (
                                        <tr
                                            key={operator.id}
                                            className="hover:bg-slate-50 transition-colors"
                                        >
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                                                        {operator.name?.charAt(0).toUpperCase()}
                                                    </div>
                                                    <span className="font-medium text-gray-900">
                                                        {operator.name}
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 text-gray-600">
                                                {operator.email}
                                            </td>
                                            <td className="px-6 py-4 text-gray-600">
                                                {operator.mobile || '-'}
                                            </td>
                                            <td className="px-6 py-4 text-gray-600">
                                                {operator.branch?.name || '-'}
                                            </td>
                                            <td className="px-6 py-4 text-gray-600">
                                                {operator.ferryboat?.name || '-'}
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="flex items-center justify-end gap-2">
                                                    <Link
                                                        href={route('operator.edit', operator.id)}
                                                        className="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                                        title="Edit operator"
                                                    >
                                                        <Pencil className="w-4 h-4" />
                                                    </Link>
                                                    <button
                                                        onClick={() => handleDelete(operator)}
                                                        className="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                        title="Delete operator"
                                                    >
                                                        <Trash2 className="w-4 h-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        {operators.links && operators.links.length > 3 && (
                            <div className="px-6 py-4 border-t border-slate-200 bg-slate-50">
                                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <p className="text-sm text-gray-600">
                                        Showing {operators.from} to {operators.to} of{' '}
                                        {operators.total} results
                                    </p>
                                    <div className="flex items-center gap-1">
                                        {operators.links.map((link, index) => {
                                            // Skip "Previous" and "Next" text links
                                            if (index === 0) {
                                                return (
                                                    <Link
                                                        key={index}
                                                        href={link.url || '#'}
                                                        className={`p-2 rounded-lg transition-colors ${
                                                            link.url
                                                                ? 'text-gray-600 hover:bg-gray-100'
                                                                : 'text-gray-300 cursor-not-allowed'
                                                        }`}
                                                        preserveScroll
                                                        as={link.url ? 'a' : 'span'}
                                                    >
                                                        <ChevronLeft className="w-5 h-5" />
                                                    </Link>
                                                );
                                            }
                                            if (index === operators.links.length - 1) {
                                                return (
                                                    <Link
                                                        key={index}
                                                        href={link.url || '#'}
                                                        className={`p-2 rounded-lg transition-colors ${
                                                            link.url
                                                                ? 'text-gray-600 hover:bg-gray-100'
                                                                : 'text-gray-300 cursor-not-allowed'
                                                        }`}
                                                        preserveScroll
                                                        as={link.url ? 'a' : 'span'}
                                                    >
                                                        <ChevronRight className="w-5 h-5" />
                                                    </Link>
                                                );
                                            }
                                            return (
                                                <Link
                                                    key={index}
                                                    href={link.url || '#'}
                                                    className={`min-w-[40px] h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors ${
                                                        link.active
                                                            ? 'bg-indigo-600 text-white'
                                                            : link.url
                                                            ? 'text-gray-600 hover:bg-gray-100'
                                                            : 'text-gray-300'
                                                    }`}
                                                    preserveScroll
                                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                                />
                                            );
                                        })}
                                    </div>
                                </div>
                            </div>
                        )}
                    </>
                ) : (
                    /* Empty State */
                    <div className="py-16 text-center">
                        <div className="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                            <Ticket className="w-8 h-8 text-slate-400" />
                        </div>
                        <h3 className="text-lg font-medium text-gray-900 mb-2">
                            No operators found
                        </h3>
                        <p className="text-sm text-gray-500 mb-6">
                            Get started by adding your first operator
                        </p>
                        <Link
                            href={route('operator.create')}
                            className="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-colors"
                        >
                            <Plus className="w-5 h-5" />
                            <span>Add Operator</span>
                        </Link>
                    </div>
                )}
            </div>
        </>
    );
}
