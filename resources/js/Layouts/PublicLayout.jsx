import { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import {
    Ship,
    Menu,
    X,
    Phone,
    Mail,
    MapPin,
    Facebook,
    Instagram,
    Twitter,
} from 'lucide-react';

// Ocean Blue & Sand color palette
// Primary: Ocean Blue (#0369a1 to #0ea5e9)
// Accent: Sand/Gold (#fbbf24 to #f97316)
// Background: Light blue-gray (#f0f9ff)

export default function PublicLayout({ children }) {
    const { url } = usePage();
    const [isMobileOpen, setIsMobileOpen] = useState(false);

    const navLinks = [
        { label: 'Home', href: '/', routeName: 'public.home' },
        { label: 'Ferry Routes', href: '/#routes', hash: true },
        { label: 'About Us', href: '/about', routeName: 'public.about' },
        { label: 'Contact', href: '/contact', routeName: 'public.contact' },
    ];

    const isActive = (path) => {
        if (path === '/') return url === '/' || url === '/';
        return url.startsWith(path);
    };

    return (
        <div className="min-h-screen bg-gradient-to-b from-sky-50 to-white">
            {/* Top Bar */}
            <div className="bg-sky-900 text-white text-sm py-2 hidden md:block">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                    <div className="flex items-center gap-6">
                        <a href="tel:+919767248900" className="flex items-center gap-2 hover:text-amber-300 transition-colors">
                            <Phone className="w-4 h-4" />
                            <span>+91 9767248900</span>
                        </a>
                        <a href="mailto:ssmsdapoli@rediffmail.com" className="flex items-center gap-2 hover:text-amber-300 transition-colors">
                            <Mail className="w-4 h-4" />
                            <span>ssmsdapoli@rediffmail.com</span>
                        </a>
                    </div>
                    <div className="flex items-center gap-4">
                        <span className="text-sky-300">Operating: 9 AM - 5 PM (7 Days)</span>
                    </div>
                </div>
            </div>

            {/* Main Navigation */}
            <header className="sticky top-0 z-50 bg-white/95 backdrop-blur-lg border-b border-sky-100 shadow-sm">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center justify-between h-20">
                        {/* Logo */}
                        <Link href={route('public.home')} className="flex items-center gap-3 group">
                            <div className="w-12 h-12 rounded-2xl bg-gradient-to-br from-sky-500 to-sky-700 flex items-center justify-center shadow-lg shadow-sky-500/30 group-hover:shadow-sky-500/50 transition-all duration-300">
                                <Ship className="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <span className="text-xl font-bold text-sky-900 tracking-tight">Suvarnadurga</span>
                                <span className="block text-xs text-sky-600">Shipping & Marine Services</span>
                            </div>
                        </Link>

                        {/* Desktop Navigation */}
                        <nav className="hidden lg:flex items-center gap-2">
                            {navLinks.map((link) => (
                                link.hash ? (
                                    <a
                                        key={link.label}
                                        href={link.href}
                                        className={`px-4 py-2 rounded-xl font-medium transition-colors ${isActive(link.href)
                                            ? 'bg-sky-100 text-sky-700'
                                            : 'text-slate-600 hover:bg-sky-50 hover:text-sky-700'
                                            }`}
                                    >
                                        {link.label}
                                    </a>
                                ) : (
                                    <Link
                                        key={link.label}
                                        href={route(link.routeName)}
                                        className={`px-4 py-2 rounded-xl font-medium transition-colors ${isActive(link.href)
                                            ? 'bg-sky-100 text-sky-700'
                                            : 'text-slate-600 hover:bg-sky-50 hover:text-sky-700'
                                            }`}
                                    >
                                        {link.label}
                                    </Link>
                                )
                            ))}
                        </nav>

                        {/* CTA Buttons */}
                        <div className="hidden lg:flex items-center gap-3">
                            <Link
                                href={route('customer.login')}
                                className="px-5 py-2.5 rounded-xl text-sky-700 font-medium hover:bg-sky-50 transition-colors"
                            >
                                Login
                            </Link>
                            <Link
                                href={route('customer.login')}
                                className="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-orange-500 text-white font-semibold shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50 hover:-translate-y-0.5 transition-all duration-300"
                            >
                                Book Now
                            </Link>
                        </div>

                        {/* Mobile Menu Button */}
                        <button
                            onClick={() => setIsMobileOpen(!isMobileOpen)}
                            className="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-sky-50 transition-colors"
                            aria-label="Toggle menu"
                        >
                            {isMobileOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
                        </button>
                    </div>
                </div>

                {/* Mobile Menu */}
                {isMobileOpen && (
                    <div className="lg:hidden border-t border-sky-100 bg-white">
                        <div className="px-4 py-4 space-y-2">
                            {navLinks.map((link) => (
                                link.hash ? (
                                    <a
                                        key={link.label}
                                        href={link.href}
                                        onClick={() => setIsMobileOpen(false)}
                                        className={`block px-4 py-3 rounded-xl font-medium transition-colors ${isActive(link.href)
                                            ? 'bg-sky-100 text-sky-700'
                                            : 'text-slate-600 hover:bg-sky-50'
                                            }`}
                                    >
                                        {link.label}
                                    </a>
                                ) : (
                                    <Link
                                        key={link.label}
                                        href={route(link.routeName)}
                                        onClick={() => setIsMobileOpen(false)}
                                        className={`block px-4 py-3 rounded-xl font-medium transition-colors ${isActive(link.href)
                                            ? 'bg-sky-100 text-sky-700'
                                            : 'text-slate-600 hover:bg-sky-50'
                                            }`}
                                    >
                                        {link.label}
                                    </Link>
                                )
                            ))}
                            <div className="pt-4 border-t border-sky-100 space-y-2">
                                <Link
                                    href={route('customer.login')}
                                    className="block px-4 py-3 rounded-xl text-center text-sky-700 font-medium hover:bg-sky-50 transition-colors"
                                >
                                    Login
                                </Link>
                                <Link
                                    href={route('customer.login')}
                                    className="block px-4 py-3 rounded-xl text-center bg-gradient-to-r from-amber-400 to-orange-500 text-white font-semibold"
                                >
                                    Book Now
                                </Link>
                            </div>
                        </div>
                    </div>
                )}
            </header>

            {/* Main Content */}
            <main>{children}</main>

            {/* Footer */}
            <footer className="bg-sky-950 text-white">
                {/* Main Footer */}
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                        {/* Company Info */}
                        <div>
                            <div className="flex items-center gap-3 mb-6">
                                <div className="w-12 h-12 rounded-2xl bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center">
                                    <Ship className="w-6 h-6 text-white" />
                                </div>
                                <div>
                                    <span className="text-lg font-bold">Suvarnadurga</span>
                                    <span className="block text-xs text-sky-300">Shipping & Marine</span>
                                </div>
                            </div>
                            <p className="text-sky-200 text-sm leading-relaxed">
                                Connecting Maharashtra's beautiful Konkan coast with reliable ferry services since 2003.
                            </p>
                        </div>

                        {/* Quick Links */}
                        <div>
                            <h3 className="text-lg font-semibold mb-6">Quick Links</h3>
                            <ul className="space-y-3">
                                {['Home', 'About Us', 'Ferry Routes', 'Contact'].map((link) => (
                                    <li key={link}>
                                        <a href="#" className="text-sky-200 hover:text-amber-400 transition-colors text-sm">
                                            {link}
                                        </a>
                                    </li>
                                ))}
                            </ul>
                        </div>

                        {/* Ferry Routes */}
                        <div>
                            <h3 className="text-lg font-semibold mb-6">Ferry Routes</h3>
                            <ul className="space-y-3">
                                {['Dabhol - Dhopave', 'Jaigad - Tawsal', 'Dighi - Agardande', 'Veshvi - Bagmandale', 'Vasai - Bhayander'].map((route) => (
                                    <li key={route}>
                                        <a href="#" className="text-sky-200 hover:text-amber-400 transition-colors text-sm">
                                            {route}
                                        </a>
                                    </li>
                                ))}
                            </ul>
                        </div>

                        {/* Contact */}
                        <div>
                            <h3 className="text-lg font-semibold mb-6">Contact Us</h3>
                            <ul className="space-y-4">
                                <li className="flex items-start gap-3">
                                    <MapPin className="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" />
                                    <span className="text-sky-200 text-sm">
                                        Suvarnadurga Shipping & Marine Services Pvt. Ltd., Dapoli, Maharashtra
                                    </span>
                                </li>
                                <li className="flex items-center gap-3">
                                    <Phone className="w-5 h-5 text-amber-400 flex-shrink-0" />
                                    <a href="tel:+919767248900" className="text-sky-200 hover:text-amber-400 transition-colors text-sm">
                                        +91 9767248900
                                    </a>
                                </li>
                                <li className="flex items-center gap-3">
                                    <Mail className="w-5 h-5 text-amber-400 flex-shrink-0" />
                                    <a href="mailto:ssmsdapoli@rediffmail.com" className="text-sky-200 hover:text-amber-400 transition-colors text-sm">
                                        ssmsdapoli@rediffmail.com
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {/* Bottom Bar */}
                <div className="border-t border-sky-800">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row items-center justify-between gap-4">
                        <p className="text-sky-300 text-sm">
                            &copy; {new Date().getFullYear()} Suvarnadurga Shipping & Marine Services. All rights reserved.
                            <span className="mx-2 text-sky-700">|</span>
                            <Link href={route('login')} className="text-sky-500 hover:text-sky-300 transition-colors">Staff Login</Link>
                        </p>
                        <div className="flex items-center gap-4">
                            <a href="#" className="w-10 h-10 rounded-full bg-sky-800 hover:bg-amber-500 flex items-center justify-center transition-colors">
                                <Facebook className="w-5 h-5" />
                            </a>
                            <a href="#" className="w-10 h-10 rounded-full bg-sky-800 hover:bg-amber-500 flex items-center justify-center transition-colors">
                                <Instagram className="w-5 h-5" />
                            </a>
                            <a href="#" className="w-10 h-10 rounded-full bg-sky-800 hover:bg-amber-500 flex items-center justify-center transition-colors">
                                <Twitter className="w-5 h-5" />
                            </a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    );
}
