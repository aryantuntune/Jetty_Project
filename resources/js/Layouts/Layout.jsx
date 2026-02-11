import { useState, useEffect } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import {
    LayoutDashboard,
    Receipt,
    BarChart3,
    Database,
    Ship,
    Anchor,
    ArrowLeftRight,
    CheckCircle,
    Users,
    ChevronDown,
    ChevronLeft,
    Menu,
    Search,
    Bell,
    LogOut,
} from 'lucide-react';

// Navigation items configuration
const navItems = [
    { id: 'dashboard', label: 'Dashboard', href: 'dashboard', icon: LayoutDashboard },
    { id: 'counter', label: 'Counter Options', href: 'ticket-entry.create', icon: Receipt },
    {
        id: 'reports',
        label: 'Reports',
        icon: BarChart3,
        children: [
            { id: 'reports-tickets', label: 'Ticket Details', href: 'reports.tickets' },
            { id: 'reports-vehicle', label: 'Vehicle-wise Details', href: 'reports.vehicle_tickets' },
        ],
    },
    {
        id: 'masters',
        label: 'Masters',
        icon: Database,
        children: [
            { id: 'masters-items', label: 'Items', href: 'items.from_rates.index' },
            { id: 'masters-categories', label: 'Item Categories', href: 'item_categories.index' },
            { id: 'masters-rates', label: 'Item Rate Slabs', href: 'item-rates.index' },
            { id: 'masters-ferryboats', label: 'Ferry Boats', href: 'ferryboats.index' },
            { id: 'masters-schedules', label: 'Ferry Schedules', href: 'ferry_schedules.index' },
            { id: 'masters-guests', label: 'Guests', href: 'guests.index' },
            { id: 'masters-guest-categories', label: 'Guest Categories', href: 'guest_categories.index' },
            { id: 'masters-branches', label: 'Branches', href: 'branches.index' },
            { id: 'masters-special-charges', label: 'Special Charges', href: 'special-charges.index', roles: [1, 2] },
        ],
    },
    {
        id: 'houseboat',
        label: 'Houseboat',
        icon: Anchor,
        roles: [1, 2],
        children: [
            { id: 'houseboat-dashboard', label: 'Dashboard', href: 'houseboat.dashboard' },
            { id: 'houseboat-rooms', label: 'Rooms', href: 'admin.houseboat.rooms' },
        ],
    },
    { id: 'transfer', label: 'Transfer', href: 'employees.transfer.index', icon: ArrowLeftRight, roles: [1, 2] },
    { id: 'verify', label: 'Verify Ticket', href: 'verify.index', icon: CheckCircle, roles: [1, 2, 5] },
];

const adminItems = [
    {
        id: 'user-management',
        label: 'User Management',
        icon: Users,
        roles: [1, 2],
        children: [
            { id: 'admin-overview', label: 'Staff Overview', href: 'users.index', roles: [1, 2] },
            { id: 'admin-admins', label: 'Administrators', href: 'admin.index', roles: [1] },
            { id: 'admin-managers', label: 'Managers', href: 'manager.index', roles: [1, 2] },
            { id: 'admin-operators', label: 'Operators', href: 'operator.index', roles: [1, 2, 3] },
            { id: 'admin-checkers', label: 'Checkers', href: 'checker.index', roles: [1, 2, 3] },
        ],
    },
];

// Sidebar Navigation Item Component
function NavItem({ item, userRoleId, isCollapsed, onExpandSidebar }) {
    const [isOpen, setIsOpen] = useState(false);
    const { url } = usePage();
    const Icon = item.icon;

    // Check if user can access this item
    const canAccess = !item.roles || item.roles.includes(userRoleId);
    if (!canAccess) return null;

    // Check if any child is active
    const isChildActive = item.children?.some(child => {
        try {
            return url.startsWith(route(child.href, undefined, false));
        } catch {
            return false;
        }
    });

    // Check if current item is active
    const isActive = item.href && (() => {
        try {
            return url.startsWith(route(item.href, undefined, false));
        } catch {
            return false;
        }
    })();

    const handleClick = () => {
        if (isCollapsed && item.children) {
            // Expand sidebar when clicking a parent item while collapsed
            onExpandSidebar?.();
        }
        setIsOpen(!isOpen);
    };

    if (item.children) {
        return (
            <div>
                <button
                    onClick={handleClick}
                    className={`w-full sidebar-link ${isChildActive ? 'bg-white/10 text-white' : ''}`}
                    aria-expanded={isOpen}
                    aria-controls={`nav-submenu-${item.id}`}
                >
                    <Icon className="w-5 h-5 flex-shrink-0" />
                    {!isCollapsed && (
                        <>
                            <span className="flex-1 text-left">{item.label}</span>
                            <ChevronDown className={`w-4 h-4 transition-transform ${isOpen ? 'rotate-180' : ''}`} />
                        </>
                    )}
                </button>
                {!isCollapsed && isOpen && (
                    <div id={`nav-submenu-${item.id}`} className="ml-4 mt-1 space-y-1 border-l border-white/10 pl-4">
                        {item.children.filter(child => !child.roles || child.roles.includes(userRoleId)).map((child) => (
                            <Link
                                key={child.id}
                                href={route(child.href)}
                                className="block px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors"
                            >
                                {child.label}
                            </Link>
                        ))}
                    </div>
                )}
            </div>
        );
    }

    return (
        <Link
            href={route(item.href)}
            className={`sidebar-link ${isActive ? 'sidebar-link-active' : ''}`}
        >
            <Icon className="w-5 h-5 flex-shrink-0" />
            {!isCollapsed && <span>{item.label}</span>}
        </Link>
    );
}

// Sidebar Component
function Sidebar({ user, isCollapsed, setIsCollapsed, isMobileOpen, setIsMobileOpen }) {
    const handleLogout = (e) => {
        e.preventDefault();
        router.post(route('logout'));
    };

    const handleExpandSidebar = () => {
        setIsCollapsed(false);
    };

    return (
        <>
            {/* Mobile Overlay */}
            {isMobileOpen && (
                <div
                    className="fixed inset-0 bg-black/50 z-40 lg:hidden"
                    onClick={() => setIsMobileOpen(false)}
                    aria-hidden="true"
                />
            )}

            {/* Sidebar */}
            <aside className={`
                fixed top-0 left-0 h-full bg-primary-950 text-white z-50
                transition-all duration-300 flex flex-col
                ${isCollapsed ? 'w-20' : 'w-64'}
                ${isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}
            `}>
                {/* Logo & Collapse Toggle */}
                <div className="flex items-center justify-between px-4 py-5 border-b border-white/10">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <Ship className="w-6 h-6" />
                        </div>
                        {!isCollapsed && <span className="text-xl font-bold">Jetty</span>}
                    </div>
                    <button
                        onClick={() => setIsCollapsed(!isCollapsed)}
                        className="hidden lg:flex p-1.5 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition-colors"
                        aria-label={isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'}
                    >
                        <ChevronLeft className={`w-5 h-5 transition-transform ${isCollapsed ? 'rotate-180' : ''}`} />
                    </button>
                </div>

                {/* Navigation */}
                <nav className="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                    {navItems.map((item) => (
                        <NavItem
                            key={item.id}
                            item={item}
                            userRoleId={user.role_id}
                            isCollapsed={isCollapsed}
                            onExpandSidebar={handleExpandSidebar}
                        />
                    ))}

                    {/* Admin Section */}
                    {adminItems.some(item => !item.roles || item.roles.includes(user.role_id)) && (
                        <>
                            {!isCollapsed && (
                                <div className="pt-4 pb-2 px-4">
                                    <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Administration
                                    </span>
                                </div>
                            )}
                            {adminItems.map((item) => (
                                <NavItem
                                    key={item.id}
                                    item={item}
                                    userRoleId={user.role_id}
                                    isCollapsed={isCollapsed}
                                    onExpandSidebar={handleExpandSidebar}
                                />
                            ))}
                        </>
                    )}
                </nav>

                {/* User Profile */}
                <div className="border-t border-white/10 p-4">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold">
                            {user.name?.charAt(0).toUpperCase()}
                        </div>
                        {!isCollapsed && (
                            <div className="flex-1 min-w-0">
                                <p className="text-sm font-medium truncate">{user.name}</p>
                                <p className="text-xs text-gray-400 truncate">{user.email}</p>
                            </div>
                        )}
                        <button
                            onClick={handleLogout}
                            className="p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition-colors"
                            aria-label="Logout"
                        >
                            <LogOut className="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </aside>
        </>
    );
}

// Top Bar Component
function TopBar({ title, user, onMenuClick }) {
    return (
        <header className="sticky top-0 z-30 bg-white border-b border-gray-200">
            <div className="flex items-center justify-between px-4 lg:px-6 h-16">
                <div className="flex items-center gap-4">
                    <button
                        onClick={onMenuClick}
                        className="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg lg:hidden"
                        aria-label="Open menu"
                    >
                        <Menu className="w-6 h-6" />
                    </button>
                    <h1 className="text-xl font-semibold text-gray-900">{title || 'Dashboard'}</h1>
                </div>

                <div className="flex items-center gap-4">
                    {/* Search - placeholder for future functionality */}
                    <div className="hidden md:flex items-center gap-2 bg-gray-100 rounded-lg px-3 py-2">
                        <Search className="w-4 h-4 text-gray-400" />
                        <input
                            type="text"
                            placeholder="Search..."
                            className="bg-transparent border-none outline-none text-sm w-40"
                            disabled
                            aria-label="Search (coming soon)"
                        />
                    </div>

                    {/* Notifications - placeholder for future functionality */}
                    <button
                        className="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg"
                        aria-label="Notifications"
                        disabled
                    >
                        <Bell className="w-5 h-5" />
                    </button>

                    {/* User Menu (Mobile) */}
                    <div className="lg:hidden flex items-center gap-2">
                        <div className="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                            {user.name?.charAt(0).toUpperCase()}
                        </div>
                    </div>
                </div>
            </div>
        </header>
    );
}

// Main Layout Component
export default function Layout({ children, title }) {
    const { auth, url } = usePage().props;
    const currentUrl = usePage().url;
    const user = auth?.user;
    const [isCollapsed, setIsCollapsed] = useState(false);
    const [isMobileOpen, setIsMobileOpen] = useState(false);

    // Close mobile menu on route change
    useEffect(() => {
        setIsMobileOpen(false);
    }, [currentUrl]);

    if (!user) {
        return <>{children}</>;
    }

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Sidebar */}
            <Sidebar
                user={user}
                isCollapsed={isCollapsed}
                setIsCollapsed={setIsCollapsed}
                isMobileOpen={isMobileOpen}
                setIsMobileOpen={setIsMobileOpen}
            />

            {/* Main Content */}
            <div className={`transition-all duration-300 ${isCollapsed ? 'lg:ml-20' : 'lg:ml-64'}`}>
                {/* Top Bar */}
                <TopBar
                    title={title}
                    user={user}
                    onMenuClick={() => setIsMobileOpen(true)}
                />

                {/* Page Content */}
                <main className="p-4 lg:p-6">
                    {children}
                </main>

                {/* Footer */}
                <footer className="px-4 lg:px-6 py-4 text-center text-sm text-gray-500 border-t border-gray-200">
                    &copy; {new Date().getFullYear()} Jetty. All rights reserved.
                </footer>
            </div>
        </div>
    );
}
