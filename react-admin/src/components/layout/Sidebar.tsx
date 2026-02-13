import { useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { cn } from '@/lib/utils';
import {
    LayoutDashboard, Ticket, Ship, FileText, Users, Settings, MapPin, Clock, DollarSign,
    CheckCircle, UserCheck, BadgeDollarSign, Tag, Tags, UserCog, ScanLine, ArrowRightLeft,
    ChevronDown, ChevronRight, Database
} from 'lucide-react';
import { useAuthStore } from '@/store';
import { ROLE_IDS } from '@/types/auth';

interface MenuItem {
    icon: React.ElementType;
    label: string;
    path: string;
    roles?: number[];
}

interface MenuGroup {
    icon: React.ElementType;
    label: string;
    roles?: number[];
    children: MenuItem[];
}

// Standalone items (no grouping)
const standaloneItems: MenuItem[] = [
    { icon: LayoutDashboard, label: 'Dashboard', path: '/admin/dashboard' },
    { icon: Ticket, label: 'Ticket Entry', path: '/admin/tickets/entry' },
    { icon: CheckCircle, label: 'Verify Tickets', path: '/admin/tickets/verify' },
    { icon: FileText, label: 'Reports', path: '/admin/reports' },
];

// Grouped menu items
const menuGroups: MenuGroup[] = [
    {
        icon: Database,
        label: 'Masters',
        roles: [ROLE_IDS.ADMIN, ROLE_IDS.MANAGER],
        children: [
            { icon: Ship, label: 'Ferries', path: '/admin/masters/ferries' },
            { icon: MapPin, label: 'Branches', path: '/admin/masters/branches' },
            { icon: Clock, label: 'Schedules', path: '/admin/masters/schedules' },
            { icon: DollarSign, label: 'Rates', path: '/admin/masters/rates' },
            { icon: BadgeDollarSign, label: 'Special Charges', path: '/admin/masters/special-charges' },
            { icon: Tag, label: 'Item Categories', path: '/admin/masters/item-categories' },
        ],
    },
    {
        icon: UserCheck,
        label: 'Guests',
        roles: [ROLE_IDS.ADMIN, ROLE_IDS.MANAGER],
        children: [
            { icon: UserCheck, label: 'All Guests', path: '/admin/guests' },
            { icon: Tags, label: 'Guest Categories', path: '/admin/guests/categories' },
        ],
    },
    {
        icon: Users,
        label: 'Users',
        roles: [ROLE_IDS.ADMIN],
        children: [
            { icon: Users, label: 'All Users', path: '/admin/users' },
            { icon: UserCog, label: 'Operators', path: '/admin/users/operators' },
            { icon: UserCheck, label: 'Managers', path: '/admin/users/managers' },
            { icon: ScanLine, label: 'Checkers', path: '/admin/users/checkers' },
            { icon: ArrowRightLeft, label: 'Transfer', path: '/admin/users/transfer' },
        ],
    },
];

// Standalone bottom items
const bottomItems: MenuItem[] = [
    { icon: Settings, label: 'Settings', path: '/admin/settings', roles: [ROLE_IDS.ADMIN] },
];

export function Sidebar() {
    const location = useLocation();
    const { user } = useAuthStore();
    const [expandedGroups, setExpandedGroups] = useState<string[]>(['Masters', 'Users', 'Guests']);

    const toggleGroup = (label: string) => {
        setExpandedGroups(prev =>
            prev.includes(label) ? prev.filter(g => g !== label) : [...prev, label]
        );
    };

    const canAccess = (roles?: number[]) => {
        if (!roles || roles.length === 0) return true;
        return user?.role_id && roles.includes(user.role_id);
    };

    const isActive = (path: string) => location.pathname === path || location.pathname.startsWith(path + '/');

    const renderMenuItem = (item: MenuItem, indent = false) => {
        if (!canAccess(item.roles)) return null;
        const Icon = item.icon;
        return (
            <Link
                key={item.path}
                to={item.path}
                className={cn(
                    "flex items-center gap-3 px-4 py-2 rounded-lg transition-all text-sm",
                    indent && "ml-4",
                    isActive(item.path)
                        ? "bg-blue-600 text-white shadow-md"
                        : "text-gray-700 hover:bg-gray-100"
                )}
            >
                <Icon className="w-4 h-4" />
                <span className="font-medium">{item.label}</span>
            </Link>
        );
    };

    const renderMenuGroup = (group: MenuGroup) => {
        if (!canAccess(group.roles)) return null;
        const isExpanded = expandedGroups.includes(group.label);
        const hasActiveChild = group.children.some(child => isActive(child.path));
        const Icon = group.icon;

        return (
            <div key={group.label} className="space-y-1">
                <button
                    onClick={() => toggleGroup(group.label)}
                    className={cn(
                        "w-full flex items-center justify-between px-4 py-2 rounded-lg transition-all text-sm",
                        hasActiveChild ? "bg-blue-50 text-blue-700" : "text-gray-700 hover:bg-gray-100"
                    )}
                >
                    <div className="flex items-center gap-3">
                        <Icon className="w-4 h-4" />
                        <span className="font-medium">{group.label}</span>
                    </div>
                    {isExpanded ? <ChevronDown className="w-4 h-4" /> : <ChevronRight className="w-4 h-4" />}
                </button>
                {isExpanded && (
                    <div className="space-y-1 pl-2">
                        {group.children.map(child => renderMenuItem(child, true))}
                    </div>
                )}
            </div>
        );
    };

    return (
        <div className="w-64 bg-white border-r border-gray-200 flex flex-col h-full">
            {/* Logo */}
            <div className="h-16 flex items-center px-6 border-b border-gray-200">
                <div className="w-10 h-10 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center p-1.5 mr-3">
                    <img src="/images/carferry/logo-white.png" alt="Jetty Ferry" className="w-full h-full object-contain" />
                </div>
                <div>
                    <h1 className="text-lg font-bold text-gray-900">Jetty Ferry</h1>
                    <p className="text-xs text-gray-500">Admin Panel</p>
                </div>
            </div>

            {/* Navigation */}
            <nav className="flex-1 p-3 space-y-1 overflow-y-auto">
                {/* Standalone items */}
                {standaloneItems.map(item => renderMenuItem(item))}

                {/* Divider */}
                <div className="my-3 border-t border-gray-200" />

                {/* Grouped items */}
                {menuGroups.map(group => renderMenuGroup(group))}

                {/* Divider */}
                <div className="my-3 border-t border-gray-200" />

                {/* Bottom items */}
                {bottomItems.filter(item => canAccess(item.roles)).map(item => renderMenuItem(item))}
            </nav>

            {/* User Info */}
            <div className="p-4 border-t border-gray-200">
                <div className="flex items-center gap-3 px-3">
                    <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <span className="text-blue-600 font-semibold">{user?.name?.charAt(0) || 'A'}</span>
                    </div>
                    <div className="flex-1 min-w-0">
                        <p className="text-sm font-medium text-gray-900 truncate">{user?.name || 'Admin User'}</p>
                        <p className="text-xs text-gray-500 truncate">{user?.role_name || 'Administrator'}</p>
                    </div>
                </div>
            </div>
        </div>
    );
}
