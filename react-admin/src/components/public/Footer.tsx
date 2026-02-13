import { Link } from 'react-router-dom';
import { Ship, Phone, Mail, MapPin, Facebook, Twitter, Instagram, Anchor } from 'lucide-react';

const logoWhite = '/images/carferry/logo-white.png';

export function Footer() {
    return (
        <footer className="bg-gradient-to-b from-slate-800 to-slate-900 text-white">
            {/* Main Footer */}
            <div className="container mx-auto px-4 py-12">
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    {/* Brand */}
                    <div>
                        <div className="flex items-center gap-2 mb-4">
                            <div className="w-10 h-10 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center p-1.5">
                                <img src={logoWhite} alt="Jetty Ferry" className="w-full h-full object-contain" />
                            </div>
                            <div>
                                <span className="text-xl font-bold">Jetty Ferry</span>
                                <span className="block text-xs text-slate-400">Suvarnadurga</span>
                            </div>
                        </div>
                        <p className="text-slate-400 mb-4">
                            Maharashtra's premier ferry service connecting coastal communities since 2003.
                        </p>
                        <div className="flex gap-4">
                            <a href="#" className="w-10 h-10 bg-slate-700 hover:bg-cyan-500 rounded-lg flex items-center justify-center transition-colors">
                                <Facebook className="w-5 h-5" />
                            </a>
                            <a href="#" className="w-10 h-10 bg-slate-700 hover:bg-cyan-500 rounded-lg flex items-center justify-center transition-colors">
                                <Twitter className="w-5 h-5" />
                            </a>
                            <a href="#" className="w-10 h-10 bg-slate-700 hover:bg-orange-500 rounded-lg flex items-center justify-center transition-colors">
                                <Instagram className="w-5 h-5" />
                            </a>
                        </div>
                    </div>

                    {/* Quick Links */}
                    <div>
                        <h3 className="text-lg font-semibold mb-4 flex items-center gap-2">
                            <Anchor className="w-4 h-4 text-cyan-400" />
                            Quick Links
                        </h3>
                        <ul className="space-y-2">
                            <li>
                                <Link to="/" className="text-slate-400 hover:text-cyan-400 transition-colors">
                                    Home
                                </Link>
                            </li>
                            <li>
                                <Link to="/about" className="text-slate-400 hover:text-cyan-400 transition-colors">
                                    About Us
                                </Link>
                            </li>
                            <li>
                                <Link to="/routes" className="text-slate-400 hover:text-cyan-400 transition-colors">
                                    Ferry Routes
                                </Link>
                            </li>
                            <li>
                                <Link to="/houseboat" className="text-slate-400 hover:text-cyan-400 transition-colors">
                                    Houseboat
                                </Link>
                            </li>
                            <li>
                                <Link to="/contact" className="text-slate-400 hover:text-cyan-400 transition-colors">
                                    Contact
                                </Link>
                            </li>
                        </ul>
                    </div>

                    {/* Ferry Routes */}
                    <div>
                        <h3 className="text-lg font-semibold mb-4 flex items-center gap-2">
                            <Ship className="w-4 h-4 text-orange-400" />
                            Our Routes
                        </h3>
                        <ul className="space-y-2">
                            <li>
                                <Link to="/routes/dabhol-dhopave" className="text-slate-400 hover:text-orange-400 transition-colors">
                                    Dabhol – Dhopave
                                </Link>
                            </li>
                            <li>
                                <Link to="/routes/jaigad-tawsal" className="text-slate-400 hover:text-orange-400 transition-colors">
                                    Jaigad – Tawsal
                                </Link>
                            </li>
                            <li>
                                <Link to="/routes/dighi-agardande" className="text-slate-400 hover:text-orange-400 transition-colors">
                                    Dighi – Agardande
                                </Link>
                            </li>
                            <li>
                                <Link to="/routes/veshvi-bagmandale" className="text-slate-400 hover:text-orange-400 transition-colors">
                                    Veshvi – Bagmandale
                                </Link>
                            </li>
                        </ul>
                    </div>

                    {/* Contact Info */}
                    <div>
                        <h3 className="text-lg font-semibold mb-4">Contact Us</h3>
                        <ul className="space-y-3">
                            <li className="flex items-start gap-3">
                                <div className="w-8 h-8 bg-cyan-500/20 rounded-lg flex items-center justify-center mt-0.5">
                                    <Phone className="w-4 h-4 text-cyan-400" />
                                </div>
                                <div className="text-slate-400">
                                    +91 02348-248900<br />
                                    +91 9767248900
                                </div>
                            </li>
                            <li className="flex items-start gap-3">
                                <div className="w-8 h-8 bg-orange-500/20 rounded-lg flex items-center justify-center mt-0.5">
                                    <Mail className="w-4 h-4 text-orange-400" />
                                </div>
                                <div className="text-slate-400">
                                    ssmsdapoli@rediffmail.com
                                </div>
                            </li>
                            <li className="flex items-start gap-3">
                                <div className="w-8 h-8 bg-cyan-500/20 rounded-lg flex items-center justify-center mt-0.5">
                                    <MapPin className="w-4 h-4 text-cyan-400" />
                                </div>
                                <div className="text-slate-400">
                                    Dabhol FerryBoat Jetty, Dapoli<br />
                                    Dist. Ratnagiri, Maharashtra - 415712
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {/* Bottom Bar */}
            <div className="border-t border-slate-700/50">
                <div className="container mx-auto px-4 py-4">
                    <div className="flex flex-col md:flex-row items-center justify-between gap-4">
                        <p className="text-slate-400 text-sm">
                            © 2026 Jetty Ferry Suvarnadurga. All rights reserved.
                        </p>
                        <div className="flex gap-6 text-sm">
                            <Link to="/privacy" className="text-slate-400 hover:text-cyan-400 transition-colors">
                                Privacy Policy
                            </Link>
                            <Link to="/terms" className="text-slate-400 hover:text-cyan-400 transition-colors">
                                Terms of Service
                            </Link>
                            <Link to="/refund-policy" className="text-slate-400 hover:text-cyan-400 transition-colors">
                                Refund Policy
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    );
}
