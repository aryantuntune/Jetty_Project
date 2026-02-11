import { Link, router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { Plus, Pencil, Trash2 } from 'lucide-react';

/**
 * Role configuration for staff management pages
 */
export const roleConfig = {
    admin: {
        title: 'Administrators',
        singular: 'Administrator',
        description: 'Manage system administrators with full access',
        roleIds: [1, 2],
        icon: 'Shield',
        iconBg: 'bg-red-100',
        iconColor: 'text-red-600',
        avatarGradient: 'from-red-400 to-red-600',
        routes: {
            index: 'admin.index',
            create: 'admin.create',
            edit: 'admin.edit',
            destroy: 'admin.destroy',
        },
    },
    manager: {
        title: 'Managers',
        singular: 'Manager',
        description: 'Manage branch-level operations managers',
        roleIds: [3],
        icon: 'UserCog',
        iconBg: 'bg-blue-100',
        iconColor: 'text-blue-600',
        avatarGradient: 'from-blue-400 to-blue-600',
        routes: {
            index: 'manager.index',
            create: 'manager.create',
            edit: 'manager.edit',
            destroy: 'manager.destroy',
        },
    },
    operator: {
        title: 'Operators',
        singular: 'Operator',
        description: 'Manage ticket counter sales operators',
        roleIds: [4],
        icon: 'UserCheck',
        iconBg: 'bg-green-100',
        iconColor: 'text-green-600',
        avatarGradient: 'from-green-400 to-green-600',
        routes: {
            index: 'operator.index',
            create: 'operator.create',
            edit: 'operator.edit',
            destroy: 'operator.destroy',
        },
    },
    checker: {
        title: 'Checkers',
        singular: 'Checker',
        description: 'Manage ticket verification checkers',
        roleIds: [5],
        icon: 'QrCode',
        iconBg: 'bg-purple-100',
        iconColor: 'text-purple-600',
        avatarGradient: 'from-purple-400 to-purple-600',
        routes: {
            index: 'checker.index',
            create: 'checker.create',
            edit: 'checker.edit',
            destroy: 'checker.destroy',
        },
    },
};

/**
 * Reusable Staff Table Component
 *
 * @param {Object} props
 * @param {string} props.role - Role key from roleConfig (admin, manager, operator, checker)
 * @param {Object} props.data - Paginated data from Laravel
 * @param {React.ComponentType} props.Icon - Lucide icon component
 * @param {boolean} props.showBranch - Whether to show branch column
 * @param {boolean} props.showFerry - Whether to show ferry boat column
 */
export default function StaffTable({
    role,
    data,
    Icon,
    showBranch = false,
    showFerry = false,
}) {
    const { flash } = usePage().props;
    const config = roleConfig[role];

    if (!config) {
        return <div className="text-red-500">Invalid role configuration: {role}</div>;
    }

    const handleDelete = (item) => {
        if (confirm(`Delete ${config.singular.toLowerCase()} "${item.name}"?`)) {
            router.delete(route(config.routes.destroy, item.id));
        }
    };

    const staff = data?.data || [];
    const total = data?.total || 0;

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-slate-800 tracking-tight">
                        {config.title}
                    </h1>
                    <p className="mt-1 text-sm text-slate-500">{config.description}</p>
                </div>
                <div className="mt-4 sm:mt-0">
                    <Link
                        href={route(config.routes.create)}
                        className="inline-flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors shadow-sm"
                    >
                        <Plus className="w-5 h-5" />
                        <span>Add {config.singular}</span>
                    </Link>
                </div>
            </div>

            {/* Flash Message */}
            {flash?.success && (
                <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                    {flash.success}
                </div>
            )}

            {/* Table Card */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                {/* Table Header */}
                <div className="px-6 py-4 border-b border-slate-200 flex items-center space-x-3">
                    <div className={`w-10 h-10 rounded-xl ${config.iconBg} flex items-center justify-center`}>
                        <Icon className={`w-5 h-5 ${config.iconColor}`} />
                    </div>
                    <div>
                        <h2 className="font-semibold text-slate-800">All {config.title}</h2>
                        <p className="text-sm text-slate-500">
                            Total: {total} {total === 1 ? config.singular.toLowerCase() : config.title.toLowerCase()}
                        </p>
                    </div>
                </div>

                {/* Table */}
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
                                {showBranch && (
                                    <th className="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        Branch
                                    </th>
                                )}
                                {showFerry && (
                                    <th className="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        Ferry
                                    </th>
                                )}
                                <th className="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {staff.length > 0 ? (
                                staff.map((item) => (
                                    <tr key={item.id} className="hover:bg-slate-50 transition-colors">
                                        <td className="px-6 py-4">
                                            <div className="flex items-center space-x-3">
                                                <div className={`w-9 h-9 rounded-lg bg-gradient-to-br ${config.avatarGradient} flex items-center justify-center text-white font-semibold text-sm`}>
                                                    {item.name?.charAt(0).toUpperCase() || '?'}
                                                </div>
                                                <span className="font-medium text-slate-800">
                                                    {item.name}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-slate-600">{item.email}</td>
                                        <td className="px-6 py-4 text-slate-600">
                                            {item.mobile || '-'}
                                        </td>
                                        {showBranch && (
                                            <td className="px-6 py-4 text-slate-600">
                                                {item.branch?.branch_name || '-'}
                                            </td>
                                        )}
                                        {showFerry && (
                                            <td className="px-6 py-4 text-slate-600">
                                                {item.ferryboat?.name || '-'}
                                            </td>
                                        )}
                                        <td className="px-6 py-4">
                                            <div className="flex items-center justify-end space-x-2">
                                                <Link
                                                    href={route(config.routes.edit, item.id)}
                                                    className="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors flex items-center justify-center"
                                                    title={`Edit ${config.singular.toLowerCase()}`}
                                                    aria-label={`Edit ${item.name}`}
                                                >
                                                    <Pencil className="w-4 h-4" />
                                                </Link>
                                                <button
                                                    onClick={() => handleDelete(item)}
                                                    className="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors flex items-center justify-center"
                                                    title={`Delete ${config.singular.toLowerCase()}`}
                                                    aria-label={`Delete ${item.name}`}
                                                >
                                                    <Trash2 className="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan={showBranch && showFerry ? 6 : showBranch || showFerry ? 5 : 4} className="px-6 py-12 text-center">
                                        <div className="flex flex-col items-center">
                                            <div className={`w-16 h-16 rounded-full ${config.iconBg} flex items-center justify-center mb-4`}>
                                                <Icon className={`w-8 h-8 ${config.iconColor}`} />
                                            </div>
                                            <h3 className="text-lg font-medium text-slate-800 mb-1">
                                                No {config.title.toLowerCase()} found
                                            </h3>
                                            <p className="text-sm text-slate-500">
                                                Add your first {config.singular.toLowerCase()} to get started.
                                            </p>
                                            <Link
                                                href={route(config.routes.create)}
                                                className="mt-4 inline-flex items-center space-x-2 text-indigo-600 hover:text-indigo-700 font-medium"
                                            >
                                                <Plus className="w-4 h-4" />
                                                <span>Add {config.singular.toLowerCase()}</span>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                {staff.length > 0 && (
                    <div className="px-6 py-4 border-t border-slate-200 bg-slate-50">
                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <p className="text-sm text-slate-600">
                                Showing <span className="font-medium">{data?.from || 0}</span> to{' '}
                                <span className="font-medium">{data?.to || 0}</span> of{' '}
                                <span className="font-medium">{total}</span> {config.title.toLowerCase()}
                            </p>
                            <div className="flex space-x-2">
                                {data?.prev_page_url && (
                                    <Link
                                        href={data.prev_page_url}
                                        className="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors"
                                    >
                                        Previous
                                    </Link>
                                )}
                                {data?.next_page_url && (
                                    <Link
                                        href={data.next_page_url}
                                        className="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors"
                                    >
                                        Next
                                    </Link>
                                )}
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
