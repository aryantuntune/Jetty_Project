import { Head, Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { Shield, UserCog, UserCheck, QrCode, Users, Plus, ArrowRight } from 'lucide-react';
import Layout from '@/Layouts/Layout';

const userTypes = [
    {
        id: 'admins',
        title: 'Administrators',
        description: 'Full system access and configuration',
        icon: Shield,
        color: 'red',
        bgGradient: 'from-red-400 to-red-600',
        bgLight: 'bg-red-50',
        textColor: 'text-red-600',
        borderColor: 'border-red-200',
        hoverBg: 'hover:bg-red-50',
        href: 'admin.index',
        createHref: 'admin.create',
        roles: [1],  // only superadmin can see/manage admins
    },
    {
        id: 'managers',
        title: 'Managers',
        description: 'Branch-level operations management',
        icon: UserCog,
        color: 'blue',
        bgGradient: 'from-blue-400 to-blue-600',
        bgLight: 'bg-blue-50',
        textColor: 'text-blue-600',
        borderColor: 'border-blue-200',
        hoverBg: 'hover:bg-blue-50',
        href: 'manager.index',
        createHref: 'manager.create',
        roles: [1, 2],
    },
    {
        id: 'operators',
        title: 'Operators',
        description: 'Ticket counter sales and daily operations',
        icon: UserCheck,
        color: 'green',
        bgGradient: 'from-green-400 to-green-600',
        bgLight: 'bg-green-50',
        textColor: 'text-green-600',
        borderColor: 'border-green-200',
        hoverBg: 'hover:bg-green-50',
        href: 'operator.index',
        createHref: 'operator.create',
        roles: [1, 2, 3],
    },
    {
        id: 'checkers',
        title: 'Checkers',
        description: 'Ticket verification at boarding gates',
        icon: QrCode,
        color: 'purple',
        bgGradient: 'from-purple-400 to-purple-600',
        bgLight: 'bg-purple-50',
        textColor: 'text-purple-600',
        borderColor: 'border-purple-200',
        hoverBg: 'hover:bg-purple-50',
        href: 'checker.index',
        createHref: 'checker.create',
        roles: [1, 2, 3],
    },
];

export default function UsersIndex({ counts, auth }) {
    const userRoleId = auth?.user?.role_id;

    const visibleTypes = userTypes.filter(
        (type) => type.roles.includes(userRoleId)
    );

    const totalStaff = Object.values(counts || {}).reduce((sum, count) => sum + count, 0);

    return (
        <>
            <Head title="Staff Management" />

            {/* Header */}
            <div className="mb-8">
                <div className="flex items-center space-x-3 mb-2">
                    <div className="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <Users className="w-6 h-6 text-indigo-600" />
                    </div>
                    <div>
                        <h1 className="text-2xl font-bold text-slate-800">Staff Management</h1>
                        <p className="text-sm text-slate-500">
                            Manage all staff members across your organization
                        </p>
                    </div>
                </div>
            </div>

            {/* Summary Card */}
            <div className="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl p-6 mb-8 text-white">
                <div className="flex items-center justify-between">
                    <div>
                        <p className="text-indigo-100 text-sm font-medium">Total Staff Members</p>
                        <p className="text-4xl font-bold mt-1">{totalStaff}</p>
                    </div>
                    <div className="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center">
                        <Users className="w-8 h-8" />
                    </div>
                </div>
                <div className="mt-4 flex flex-wrap gap-4">
                    {visibleTypes.map((type) => (
                        <div key={type.id} className="bg-white/10 rounded-lg px-3 py-1.5 text-sm">
                            <span className="font-medium">{counts?.[type.id] || 0}</span>
                            <span className="text-indigo-200 ml-1">{type.title}</span>
                        </div>
                    ))}
                </div>
            </div>

            {/* User Type Cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {visibleTypes.map((type) => {
                    const Icon = type.icon;
                    const count = counts?.[type.id] || 0;

                    return (
                        <div
                            key={type.id}
                            className={`bg-white rounded-2xl border ${type.borderColor} overflow-hidden transition-all hover:shadow-lg`}
                        >
                            {/* Card Header */}
                            <div className={`bg-gradient-to-r ${type.bgGradient} p-5`}>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center space-x-3">
                                        <div className="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                                            <Icon className="w-6 h-6 text-white" />
                                        </div>
                                        <div>
                                            <h3 className="text-lg font-semibold text-white">
                                                {type.title}
                                            </h3>
                                            <p className="text-sm text-white/80">
                                                {count} {count === 1 ? 'member' : 'members'}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Card Body */}
                            <div className="p-5">
                                <p className="text-slate-600 text-sm mb-4">
                                    {type.description}
                                </p>

                                <div className="flex items-center space-x-3">
                                    <Link
                                        href={route(type.href)}
                                        className={`flex-1 inline-flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl font-medium transition-colors ${type.bgLight} ${type.textColor} ${type.hoverBg}`}
                                    >
                                        <span>View All</span>
                                        <ArrowRight className="w-4 h-4" />
                                    </Link>
                                    <Link
                                        href={route(type.createHref)}
                                        className="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors"
                                        title={`Add new ${type.title.toLowerCase()}`}
                                        aria-label={`Add new ${type.title.toLowerCase()}`}
                                    >
                                        <Plus className="w-5 h-5" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    );
                })}
            </div>

            {/* Quick Tips */}
            <div className="mt-8 bg-slate-50 rounded-2xl border border-slate-200 p-6">
                <h3 className="font-semibold text-slate-800 mb-3">Role Permissions</h3>
                <div className="space-y-2 text-sm text-slate-600">
                    <p>
                        <span className="font-medium text-blue-600">Managers</span> can manage operators and checkers within their assigned branches.
                    </p>
                    <p>
                        <span className="font-medium text-green-600">Operators</span> handle ticket sales at ferry counters.
                    </p>
                    <p>
                        <span className="font-medium text-purple-600">Checkers</span> verify tickets at boarding gates using the mobile app.
                    </p>
                </div>
            </div>
        </>
    );
}

UsersIndex.layout = (page) => <Layout children={page} />;
