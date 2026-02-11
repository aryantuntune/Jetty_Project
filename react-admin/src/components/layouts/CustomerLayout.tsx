import { Outlet, Link, useLocation, useNavigate } from 'react-router-dom';
import { Ship, LayoutDashboard, Ticket, History, User, LogOut, Menu, X } from 'lucide-react';
import { useCustomerAuthStore } from '@/store/customerAuthStore';
import { Button } from '../ui';
import { useState } from 'react';
import { toast } from 'sonner';

export function CustomerLayout() {
    const { customer, clearAuth } = useCustomerAuthStore();
    const location = useLocation();
    const navigate = useNavigate();
    const [sidebarOpen, setSidebarOpen] = useState(false);

    const menuItems = [
        { path: '/customer/dashboard', label: 'Dashboard', icon: LayoutDashboard },
        { path: '/customer/book', label: 'Book Ticket', icon: Ticket },
        { path: '/customer/history', label: 'Booking History', icon: History },
        { path: '/customer/profile', label: 'My Profile', icon: User },
    ];

    const handleLogout = () => {
        clearAuth();
        toast.success('Logged out successfully');
        navigate('/');
    };

    const isActive = (path: string) => location.pathname === path;

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Top Navbar */}
            <nav className="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-40">
                <div className="px-4 h-16 flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <button
                            className="lg:hidden p-2"
                            onClick={() => setSidebarOpen(!sidebarOpen)}
                        >
                            {sidebarOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
                        </button>
                        <Link to="/" className="flex items-center gap-2">
                            <Ship className="w-8 h-8 text-blue-600" />
                            <span className="text-xl font-bold text-gray-900">Jetty Ferry</span>
                        </Link>
                    </div>

                    <div className="flex items-center gap-4">
                        <div className="text-right hidden sm:block">
                            <div className="text-sm font-medium text-gray-900">
                                {customer?.first_name} {customer?.last_name}
                            </div>
                            <div className="text-xs text-gray-500">{customer?.email}</div>
                        </div>
                        <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <span className="text-blue-600 font-semibold">
                                {customer?.first_name?.charAt(0) || 'U'}
                            </span>
                        </div>
                    </div>
                </div>
            </nav>

            <div className="flex pt-16">
                {/* Sidebar */}
                <aside className={`
                    fixed lg:static inset-y-0 left-0 z-30 
                    w-64 bg-white border-r border-gray-200 
                    transform transition-transform duration-300 ease-in-out
                    ${sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}
                    pt-16 lg:pt-0
                `}>
                    <div className="p-4 space-y-2">
                        {menuItems.map((item) => {
                            const Icon = item.icon;
                            return (
                                <Link
                                    key={item.path}
                                    to={item.path}
                                    onClick={() => setSidebarOpen(false)}
                                    className={`flex items-center gap-3 px-4 py-3 rounded-lg transition-colors ${isActive(item.path)
                                            ? 'bg-blue-50 text-blue-700'
                                            : 'text-gray-700 hover:bg-gray-50'
                                        }`}
                                >
                                    <Icon className="w-5 h-5" />
                                    <span className="font-medium">{item.label}</span>
                                </Link>
                            );
                        })}

                        <hr className="my-4" />

                        <button
                            onClick={handleLogout}
                            className="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-red-600 hover:bg-red-50 transition-colors"
                        >
                            <LogOut className="w-5 h-5" />
                            <span className="font-medium">Logout</span>
                        </button>
                    </div>
                </aside>

                {/* Overlay for mobile */}
                {sidebarOpen && (
                    <div
                        className="fixed inset-0 bg-black/50 z-20 lg:hidden"
                        onClick={() => setSidebarOpen(false)}
                    />
                )}

                {/* Main Content */}
                <main className="flex-1 p-4 lg:p-6 min-h-[calc(100vh-4rem)]">
                    <Outlet />
                </main>
            </div>
        </div>
    );
}
