import { Head, Link, router } from '@inertiajs/react';
import {
    Ticket,
    IndianRupee,
    Ship,
    Clock,
    PlusCircle,
    FileText,
    Users,
    Anchor,
    ChevronLeft,
    ChevronRight,
    TrendingUp,
    TrendingDown,
    CheckCircle,
    Inbox,
    Info,
    Globe,
    MapPin,
    Shield,
} from 'lucide-react';

// Stat Card Component
function StatCard({ icon: Icon, iconBg, title, value, change, changeLabel, prefix = '' }) {
    const isPositive = change >= 0;
    const hasChange = change !== 0;

    return (
        <div className="stat-card">
            <div className="flex items-center justify-between mb-4">
                <div className={`w-12 h-12 rounded-xl ${iconBg} flex items-center justify-center`}>
                    <Icon className="w-5 h-5" />
                </div>
                {hasChange ? (
                    <span className={`text-xs font-medium px-2 py-1 rounded-full flex items-center gap-1 ${
                        isPositive
                            ? 'text-green-700 bg-green-100'
                            : 'text-red-700 bg-red-100'
                    }`}>
                        {isPositive ? <TrendingUp className="w-3 h-3" /> : <TrendingDown className="w-3 h-3" />}
                        {Math.abs(change)}%
                    </span>
                ) : (
                    <span className="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                        {changeLabel}
                    </span>
                )}
            </div>
            <h3 className="text-2xl font-bold text-gray-900">
                {prefix}{typeof value === 'number' ? value.toLocaleString() : value}
            </h3>
            <p className="text-sm text-gray-500 mt-1">{title}</p>
        </div>
    );
}

// Quick Action Button Component
function QuickAction({ href, icon: Icon, iconBg, label }) {
    return (
        <Link
            href={href}
            className="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 bg-white card-hover"
        >
            <div className={`w-12 h-12 rounded-xl ${iconBg} flex items-center justify-center mb-3`}>
                <Icon className="w-5 h-5" />
            </div>
            <span className="text-sm font-medium text-gray-700">{label}</span>
        </Link>
    );
}

// Recent Ticket Item Component
function RecentTicketItem({ ticket }) {
    const isVerified = ticket.verified_at !== null;

    return (
        <div className="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors">
            <div className={`w-10 h-10 rounded-full flex items-center justify-center ${
                isVerified ? 'bg-green-100' : 'bg-primary-100'
            }`}>
                {isVerified ? (
                    <CheckCircle className={`w-5 h-5 text-green-600`} />
                ) : (
                    <Ticket className={`w-5 h-5 text-primary-600`} />
                )}
            </div>
            <div className="flex-1 min-w-0">
                <p className="text-sm font-medium text-gray-900 truncate">
                    Ticket #{ticket.ticket_no ?? ticket.id} - Rs. {Number(ticket.total_amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                </p>
                <p className="text-sm text-gray-500">
                    {ticket.ferry_name} | {ticket.operator_name}
                </p>
            </div>
            <span className="text-sm text-gray-400">{ticket.ticket_date ?? ticket.created_at_human}</span>
        </div>
    );
}

export default function Dashboard({ stats, filters, recentTickets, userContext, routes }) {
    const handleViewModeChange = (mode) => {
        const params = mode === 'day'
            ? { view: 'day', date: filters.selectedDate }
            : { view: 'month', month: filters.selectedMonth };
        router.get(route('dashboard'), params, { preserveState: true });
    };

    const handleDateChange = (e) => {
        router.get(route('dashboard'), { view: 'day', date: e.target.value }, { preserveState: true });
    };

    const handleMonthChange = (e) => {
        router.get(route('dashboard'), { view: 'month', month: e.target.value }, { preserveState: true });
    };

    const navigateDate = (direction) => {
        const newDate = direction === 'prev' ? filters.prevDate : filters.nextDate;
        router.get(route('dashboard'), { view: 'day', date: newDate }, { preserveState: true });
    };

    const navigateMonth = (direction) => {
        const newMonth = direction === 'prev' ? filters.prevMonth : filters.nextMonth;
        router.get(route('dashboard'), { view: 'month', month: newMonth }, { preserveState: true });
    };

    const goToToday = () => {
        router.get(route('dashboard'), { view: 'day' }, { preserveState: true });
    };

    const goToThisMonth = () => {
        router.get(route('dashboard'), { view: 'month' }, { preserveState: true });
    };

    return (
        <>
            <Head title="Dashboard" />

            {/* Welcome Banner with Date Filter */}
            <div className="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-6 md:p-8 mb-6 text-white">
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <h1 className="text-2xl md:text-3xl font-bold mb-2">Welcome back!</h1>
                        <p className="text-primary-200">
                            Viewing metrics for <span className="font-semibold text-white">{filters.periodLabel}</span>
                        </p>
                    </div>

                    {/* Date Filter Controls */}
                    <div className="flex flex-col sm:flex-row gap-3">
                        {/* View Mode Toggle */}
                        <div className="inline-flex rounded-lg overflow-hidden border border-white/20">
                            <button
                                type="button"
                                className={`px-4 py-2 text-sm font-medium transition-colors ${
                                    filters.viewMode === 'day'
                                        ? 'bg-white text-primary-700'
                                        : 'text-white hover:bg-white/10'
                                }`}
                                onClick={() => handleViewModeChange('day')}
                            >
                                Daily
                            </button>
                            <button
                                type="button"
                                className={`px-4 py-2 text-sm font-medium transition-colors ${
                                    filters.viewMode === 'month'
                                        ? 'bg-white text-primary-700'
                                        : 'text-white hover:bg-white/10'
                                }`}
                                onClick={() => handleViewModeChange('month')}
                            >
                                Monthly
                            </button>
                        </div>

                        {/* Date/Month Picker */}
                        {filters.viewMode === 'day' ? (
                            <div className="flex items-center gap-2">
                                <button
                                    className="p-2 rounded-lg border border-white/20 text-white hover:bg-white/10 transition-colors"
                                    onClick={() => navigateDate('prev')}
                                    aria-label="Previous day"
                                >
                                    <ChevronLeft className="w-5 h-5" />
                                </button>
                                <input
                                    type="date"
                                    className="px-3 py-2 rounded-lg bg-white text-gray-900 text-sm border-0 outline-none"
                                    value={filters.selectedDate}
                                    onChange={handleDateChange}
                                />
                                <button
                                    className="p-2 rounded-lg border border-white/20 text-white hover:bg-white/10 transition-colors disabled:opacity-50"
                                    onClick={() => navigateDate('next')}
                                    disabled={filters.isToday}
                                    aria-label="Next day"
                                >
                                    <ChevronRight className="w-5 h-5" />
                                </button>
                            </div>
                        ) : (
                            <div className="flex items-center gap-2">
                                <button
                                    className="p-2 rounded-lg border border-white/20 text-white hover:bg-white/10 transition-colors"
                                    onClick={() => navigateMonth('prev')}
                                    aria-label="Previous month"
                                >
                                    <ChevronLeft className="w-5 h-5" />
                                </button>
                                <input
                                    type="month"
                                    className="px-3 py-2 rounded-lg bg-white text-gray-900 text-sm border-0 outline-none"
                                    value={filters.selectedMonth}
                                    onChange={handleMonthChange}
                                />
                                <button
                                    className="p-2 rounded-lg border border-white/20 text-white hover:bg-white/10 transition-colors disabled:opacity-50"
                                    onClick={() => navigateMonth('next')}
                                    disabled={filters.isCurrentMonth}
                                    aria-label="Next month"
                                >
                                    <ChevronRight className="w-5 h-5" />
                                </button>
                            </div>
                        )}

                        {/* Today/This Month Button */}
                        {filters.viewMode === 'day' && !filters.isToday && (
                            <button
                                className="px-4 py-2 bg-white text-primary-700 rounded-lg font-medium text-sm hover:bg-gray-100 transition-colors"
                                onClick={goToToday}
                            >
                                Today
                            </button>
                        )}
                        {filters.viewMode === 'month' && !filters.isCurrentMonth && (
                            <button
                                className="px-4 py-2 bg-white text-primary-700 rounded-lg font-medium text-sm hover:bg-gray-100 transition-colors"
                                onClick={goToThisMonth}
                            >
                                This Month
                            </button>
                        )}
                    </div>
                </div>
            </div>

            {/* Quick Stats */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <StatCard
                    icon={Ticket}
                    iconBg="bg-primary-100 text-primary-600"
                    title="Tickets Issued"
                    value={stats.ticketsCount}
                    change={stats.ticketsChange}
                    changeLabel={filters.viewMode === 'day' ? 'Today' : 'This Month'}
                />
                <StatCard
                    icon={IndianRupee}
                    iconBg="bg-green-100 text-green-600"
                    title="Revenue"
                    value={stats.totalRevenue.toLocaleString(undefined, { minimumFractionDigits: 2 })}
                    prefix="Rs. "
                    change={stats.revenueChange}
                    changeLabel={filters.viewMode === 'day' ? 'Today' : 'This Month'}
                />
                <StatCard
                    icon={Ship}
                    iconBg="bg-blue-100 text-blue-600"
                    title="Ferry Boats"
                    value={stats.ferryBoatsCount}
                    change={0}
                    changeLabel="Active"
                />
                <StatCard
                    icon={Clock}
                    iconBg="bg-amber-100 text-amber-600"
                    title="Pending Verifications"
                    value={stats.pendingVerifications}
                    change={0}
                    changeLabel={stats.pendingVerifications > 0 ? 'Pending' : 'All Done'}
                />
            </div>

            {/* Quick Actions & Recent Activity */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Quick Actions Card */}
                <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div className="px-6 py-4 border-b border-gray-100">
                        <h5 className="font-semibold text-gray-900">Quick Actions</h5>
                    </div>
                    <div className="p-6">
                        <div className="grid grid-cols-2 gap-4">
                            <QuickAction
                                href={routes.ticketEntry}
                                icon={PlusCircle}
                                iconBg="bg-primary-100 text-primary-600"
                                label="New Ticket"
                            />
                            <QuickAction
                                href={routes.reports}
                                icon={FileText}
                                iconBg="bg-green-100 text-green-600"
                                label="View Reports"
                            />
                            <QuickAction
                                href={routes.guests}
                                icon={Users}
                                iconBg="bg-blue-100 text-blue-600"
                                label="Manage Guests"
                            />
                            <QuickAction
                                href={routes.ferryBoats}
                                icon={Anchor}
                                iconBg="bg-amber-100 text-amber-600"
                                label="Ferry Boats"
                            />
                        </div>
                    </div>
                </div>

                {/* Recent Activity */}
                <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div className="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h5 className="font-semibold text-gray-900">Recent Tickets</h5>
                        <Link href={routes.reports} className="text-sm text-primary-600 hover:text-primary-700 font-medium">
                            View All
                        </Link>
                    </div>
                    <div className="p-6">
                        {recentTickets.length > 0 ? (
                            <div className="space-y-3">
                                {recentTickets.map((ticket) => (
                                    <RecentTicketItem key={ticket.id} ticket={ticket} />
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-12 text-gray-400">
                                <Inbox className="w-12 h-12 mx-auto mb-3 opacity-50" />
                                <p className="text-sm">No tickets found for this period</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* System Info */}
            <div className="bg-gray-50 rounded-2xl p-6 mt-6">
                <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                            <Info className="w-5 h-5 text-gray-400" />
                        </div>
                        <div>
                            <p className="text-sm font-medium text-gray-900">System Status</p>
                            <p className="text-sm text-gray-500">All systems operational</p>
                        </div>
                    </div>
                    <div className="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                        <span className="flex items-center gap-1.5">
                            {userContext.scopeType === 'global' ? (
                                <Globe className="w-4 h-4" />
                            ) : (
                                <MapPin className="w-4 h-4" />
                            )}
                            {userContext.scopeType === 'global'
                                ? 'All Branches'
                                : `${userContext.scopeType === 'route' ? 'Route' : 'Branch'}: ${userContext.scope}`}
                        </span>
                        <span className="hidden md:inline text-gray-300">|</span>
                        <span className="flex items-center gap-1.5">
                            <Shield className="w-4 h-4" />
                            Role: {userContext.roleName}
                        </span>
                    </div>
                </div>
            </div>
        </>
    );
}
