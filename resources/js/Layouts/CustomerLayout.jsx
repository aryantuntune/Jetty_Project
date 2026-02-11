import { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import {
    Ship,
    Menu,
    X,
    Ticket,
    History,
    Home,
    LogOut,
    User,
    ChevronDown,
} from 'lucide-react';

// Customer Portal: Lighter ocean theme with glass morphism

export default function CustomerLayout({ children, title }) {
    const { auth, url } = usePage().props;
    const currentUrl = usePage().url;
    const customer = auth?.customer;
    const [isMobileOpen, setIsMobileOpen] = useState(false);
    const [isProfileOpen, setIsProfileOpen] = useState(false);

    const handleLogout = (e) => {
        e.preventDefault();
        router.post(route('customer.logout'));
    };

    const navLinks = [
        { label: 'Book Ferry', href: 'customer.dashboard', icon: Ticket },
        { label: 'Booking History', href: 'booking.history', icon: History },
        { label: 'Home', href: 'public.home', icon: Home },
    ];

    const isActive = (routeName) => {
        try {
            return currentUrl.startsWith(route(routeName, undefined, false));
        } catch {
            return false;
        }
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-sky-50 via-white to-blue-50">
            {/* Glass Navigation */}
            <header className="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-xl border-b border-sky-100 shadow-sm">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center justify-between h-16">
                        {/* Logo */}
                        <Link href={route('public.home')} className="flex items-center gap-3 group">
                            <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center shadow-lg shadow-sky-500/20 group-hover:shadow-sky-500/40 transition-all duration-300">
                                <Ship className="w-5 h-5 text-white" />
                            </div>
                            <span className="text-xl font-bold text-sky-900 tracking-tight">Jetty</span>
                        </Link>

                        {/* Desktop Navigation */}
                        <nav className="hidden md:flex items-center gap-2">
                            {navLinks.map((link) => {
                                const Icon = link.icon;
                                const active = isActive(link.href);
                                return (
                                    <Link
                                        key={link.label}
                                        href={route(link.href)}
                                        className={`flex items-center gap-2 px-4 py-2 rounded-xl font-medium transition-all duration-200 ${active
                                            ? 'bg-sky-100 text-sky-700'
                                            : 'text-slate-600 hover:bg-sky-50 hover:text-sky-700'
                                            }`}
                                    >
                                        <Icon className="w-4 h-4" />
                                        <span>{link.label}</span>
                                    </Link>
                                );
                            })}
                        </nav>

                        {/* User Menu (Desktop) */}
                        <div className="hidden md:flex items-center gap-4">
                            {customer ? (
                                <div className="relative">
                                    <button
                                        onClick={() => setIsProfileOpen(!isProfileOpen)}
                                        className="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-sky-50 transition-colors"
                                    >
                                        <div className="w-8 h-8 rounded-full bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center text-white text-sm font-bold">
                                            {customer.name?.charAt(0).toUpperCase()}
                                        </div>
                                        <span className="text-sm font-medium text-slate-700">{customer.name}</span>
                                        <ChevronDown className={`w-4 h-4 text-slate-400 transition-transform ${isProfileOpen ? 'rotate-180' : ''}`} />
                                    </button>

                                    {isProfileOpen && (
                                        <div className="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-sky-100 py-2 z-50">
                                            <div className="px-4 py-3 border-b border-sky-100">
                                                <p className="text-sm font-medium text-slate-800">{customer.name}</p>
                                                <p className="text-xs text-slate-500">{customer.email}</p>
                                            </div>
                                            <button
                                                onClick={handleLogout}
                                                className="w-full flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 transition-colors text-sm"
                                            >
                                                <LogOut className="w-4 h-4" />
                                                <span>Logout</span>
                                            </button>
                                        </div>
                                    )}
                                </div>
                            ) : (
                                <Link
                                    href={route('customer.login')}
                                    className="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-orange-500 text-white font-semibold shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50 transition-all duration-300"
                                >
                                    Login
                                </Link>
                            )}
                        </div>

                        {/* Mobile Menu Button */}
                        <button
                            onClick={() => setIsMobileOpen(!isMobileOpen)}
                            className="md:hidden p-2 rounded-xl text-slate-600 hover:bg-sky-50 transition-colors"
                            aria-label="Toggle menu"
                        >
                            {isMobileOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
                        </button>
                    </div>
                </div>

                {/* Mobile Menu */}
                {isMobileOpen && (
                    <div className="md:hidden border-t border-sky-100 bg-white">
                        <div className="px-4 py-4 space-y-2">
                            {navLinks.map((link) => {
                                const Icon = link.icon;
                                const active = isActive(link.href);
                                return (
                                    <Link
                                        key={link.label}
                                        href={route(link.href)}
                                        onClick={() => setIsMobileOpen(false)}
                                        className={`flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors ${active
                                            ? 'bg-sky-100 text-sky-700'
                                            : 'text-slate-600 hover:bg-sky-50'
                                            }`}
                                    >
                                        <Icon className="w-5 h-5" />
                                        <span>{link.label}</span>
                                    </Link>
                                );
                            })}

                            {customer && (
                                <div className="pt-4 border-t border-sky-100">
                                    <div className="flex items-center gap-3 px-4 py-3">
                                        <div className="w-10 h-10 rounded-full bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center text-white font-bold">
                                            {customer.name?.charAt(0).toUpperCase()}
                                        </div>
                                        <div>
                                            <p className="text-sm font-medium text-slate-800">{customer.name}</p>
                                            <p className="text-xs text-slate-500">{customer.email}</p>
                                        </div>
                                    </div>
                                    <button
                                        onClick={handleLogout}
                                        className="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 font-medium transition-colors"
                                    >
                                        <LogOut className="w-5 h-5" />
                                        <span>Logout</span>
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>
                )}
            </header>

            {/* Main Content */}
            <main className="pt-20 pb-12 min-h-screen">
                {/* Page Title */}
                {title && (
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        <h1 className="text-3xl font-bold text-slate-800">{title}</h1>
                    </div>
                )}

                {children}
            </main>

            {/* Simple Footer */}
            <footer className="bg-sky-900 text-white py-6">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <p className="text-sky-200 text-sm">
                        &copy; {new Date().getFullYear()} Suvarnadurga Shipping. All rights reserved.
                    </p>
                </div>
            </footer>
        </div>
    );
}
