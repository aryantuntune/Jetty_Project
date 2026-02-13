import { useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { User, Menu, X } from 'lucide-react';
import { Button } from '../ui';

export function PublicNavbar() {
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const location = useLocation();

    const navLinks = [
        { path: '/', label: 'Home' },
        { path: '/about', label: 'About' },
        { path: '/routes', label: 'Ferry Routes' },
        { path: '/houseboat', label: 'Houseboat' },
        { path: '/contact', label: 'Contact' },
    ];

    const isActive = (path: string) => location.pathname === path;

    return (
        <nav className="bg-white/95 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-slate-100">
            <div className="container mx-auto px-4">
                <div className="flex items-center justify-between h-16">
                    {/* Logo */}
                    <Link to="/" className="flex items-center gap-2">
                        <div className="w-10 h-10 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center p-1.5">
                            <img src="/images/carferry/logo-white.png" alt="Jetty Ferry" className="w-full h-full object-contain" />
                        </div>
                        <div>
                            <span className="text-xl font-bold text-slate-800">Jetty Ferry</span>
                            <span className="block text-xs text-slate-500">Suvarnadurga</span>
                        </div>
                    </Link>

                    {/* Desktop Navigation */}
                    <div className="hidden md:flex items-center gap-8">
                        {navLinks.map((link) => (
                            <Link
                                key={link.path}
                                to={link.path}
                                className={`font-medium transition-colors relative py-1 ${isActive(link.path)
                                    ? 'text-cyan-600'
                                    : 'text-slate-600 hover:text-cyan-600'
                                    }`}
                            >
                                {link.label}
                                {isActive(link.path) && (
                                    <span className="absolute -bottom-1 left-0 right-0 h-0.5 bg-cyan-500 rounded-full"></span>
                                )}
                            </Link>
                        ))}
                    </div>

                    {/* Auth Buttons - Sunset Coral CTA */}
                    <div className="hidden md:flex items-center gap-3">
                        <Link to="/customer/login">
                            <Button variant="outline" size="sm" className="border-slate-300 text-slate-700 hover:border-cyan-500 hover:text-cyan-600">
                                <User className="w-4 h-4 mr-2" />
                                Login
                            </Button>
                        </Link>
                        <Link to="/customer/book">
                            <Button size="sm" className="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white shadow-md shadow-orange-500/20">
                                Book Ticket
                            </Button>
                        </Link>
                    </div>

                    {/* Mobile Menu Button */}
                    <button
                        className="md:hidden p-2 rounded-lg hover:bg-slate-100"
                        onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                    >
                        {mobileMenuOpen ? (
                            <X className="w-6 h-6 text-slate-700" />
                        ) : (
                            <Menu className="w-6 h-6 text-slate-700" />
                        )}
                    </button>
                </div>

                {/* Mobile Menu */}
                {mobileMenuOpen && (
                    <div className="md:hidden border-t py-4 space-y-3">
                        {navLinks.map((link) => (
                            <Link
                                key={link.path}
                                to={link.path}
                                className={`block py-2 px-3 rounded-lg font-medium ${isActive(link.path)
                                    ? 'text-cyan-600 bg-cyan-50'
                                    : 'text-slate-700 hover:bg-slate-50'
                                    }`}
                                onClick={() => setMobileMenuOpen(false)}
                            >
                                {link.label}
                            </Link>
                        ))}
                        <div className="flex gap-3 pt-3 border-t border-slate-100">
                            <Link to="/customer/login" className="flex-1">
                                <Button variant="outline" className="w-full border-slate-300">Login</Button>
                            </Link>
                            <Link to="/customer/book" className="flex-1">
                                <Button className="w-full bg-gradient-to-r from-orange-500 to-orange-600">Book</Button>
                            </Link>
                        </div>
                    </div>
                )}
            </div>
        </nav>
    );
}
