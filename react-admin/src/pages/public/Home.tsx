import { Link } from 'react-router-dom';
import { Card, CardContent, Button } from '@/components/ui';
import { Ship, Clock, Shield, DollarSign, ArrowRight, MapPin, Anchor, Star, Users } from 'lucide-react';

// Import ACTUAL scraped images from carferry.in
import ferryAvantikaImg from '@/assets/ferry_avantika.jpg';
import ferrySupriyaImg from '@/assets/ferry_supriya.jpg';
import waterRipplesImg from '@/assets/water_ripples.jpg';
import thumbDabholImg from '@/assets/thumb_dabhol.jpg';
import thumbDighiImg from '@/assets/thumb_dighi.jpg';
import thumbVeshviImg from '@/assets/thumb_veshvi.jpg';
import cruiseServiceImg from '@/assets/cruise_service.jpg';

export function Home() {
    const routes = [
        {
            id: 'dabhol-dhopave',
            name: 'Dabhol – Dhopave',
            image: thumbDabholImg,
            headerImage: ferryAvantikaImg,
            distance: '8 km',
            duration: '20 minutes',
            since: '2003',
            ferryName: 'AVANTIKA',
            status: 'active',
            serviceType: 'DABHOL - DHOPAVE FERRY SERVICE',
        },
        {
            id: 'jaigad-tawsal',
            name: 'Jaigad – Tawsal',
            image: ferrySupriyaImg,
            headerImage: ferrySupriyaImg,
            distance: '6 km',
            duration: '15 minutes',
            since: '2005',
            ferryName: 'SUPRIYA',
            status: 'active',
            serviceType: 'JAIGAD - TAWSAL FERRY SERVICE',
        },
        {
            id: 'dighi-agardande',
            name: 'Dighi – Agardande',
            image: thumbDighiImg,
            headerImage: thumbDighiImg,
            distance: '5 km',
            duration: '12 minutes',
            since: '2008',
            ferryName: 'AVANTIKA II',
            status: 'active',
            serviceType: 'DABHOL - DHOPAVE FERRY SERVICE',
        },
        {
            id: 'veshvi-bagmandale',
            name: 'Veshvi – Bagmandale',
            image: thumbVeshviImg,
            headerImage: thumbVeshviImg,
            distance: '7 km',
            duration: '18 minutes',
            since: '2010',
            ferryName: 'SUPRIYA II',
            status: 'active',
            serviceType: 'JAIGAD - TAWSAL FERRY SERVICE',
        },
    ];

    const features = [
        {
            icon: Shield,
            title: 'Safe & Reliable',
            description: 'All ferries undergo regular safety inspections and maintenance',
            color: 'bg-emerald-500',
        },
        {
            icon: DollarSign,
            title: 'Affordable Rates',
            description: 'Budget-friendly fares for passengers, vehicles, and goods',
            color: 'bg-amber-500',
        },
        {
            icon: Clock,
            title: 'Punctual Service',
            description: 'Ferries depart on schedule from 6 AM to 8 PM daily',
            color: 'bg-sky-500',
        },
        {
            icon: Anchor,
            title: 'Modern Fleet',
            description: 'Well-maintained vessels with modern amenities for comfort',
            color: 'bg-violet-500',
        },
    ];

    const stats = [
        { value: '20+', label: 'Years of Service', icon: Star },
        { value: '4', label: 'Ferry Routes', icon: Ship },
        { value: '2M+', label: 'Passengers Annually', icon: Users },
        { value: '100%', label: 'Safety Record', icon: Shield },
    ];

    return (
        <div className="overflow-hidden">
            {/* Hero Section - Using Real Water Ripples Background with Warm Sunset Tones */}
            <section className="relative min-h-[90vh] flex items-center">
                {/* Background with Water Ripples Image */}
                <div
                    className="absolute inset-0 bg-cover bg-center bg-no-repeat"
                    style={{ backgroundImage: `url(${waterRipplesImg})` }}
                >
                    {/* Ocean Teal & Sunset Gradient Overlay */}
                    <div className="absolute inset-0 bg-gradient-to-br from-cyan-600/40 via-slate-700/30 to-slate-900/70"></div>
                    <div className="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent"></div>
                </div>

                {/* Animated waves at bottom */}
                <div className="absolute bottom-0 left-0 right-0 h-32 overflow-hidden">
                    <svg className="absolute bottom-0 w-full" viewBox="0 0 1440 120" fill="none">
                        <path
                            d="M0 60L48 65C96 70 192 80 288 85C384 90 480 90 576 80C672 70 768 50 864 45C960 40 1056 50 1152 60C1248 70 1344 80 1392 85L1440 90V120H0V60Z"
                            fill="white"
                            fillOpacity="0.3"
                        />
                        <path
                            d="M0 80L60 85C120 90 240 100 360 100C480 100 600 90 720 85C840 80 960 80 1080 85C1200 90 1320 100 1380 105L1440 110V120H0V80Z"
                            fill="#f8fafc"
                        />
                    </svg>
                </div>

                <div className="container mx-auto px-4 relative z-10">
                    <div className="max-w-3xl text-white">
                        <span className="inline-flex items-center gap-2 px-5 py-2 bg-white/15 backdrop-blur-md rounded-full text-sm font-medium mb-8 border border-white/20">
                            <Ship className="w-4 h-4" />
                            Suvarnadurga Shipping & Marine Services
                        </span>

                        {/* Updated Headline with Warmer Colors */}
                        <h1 className="text-5xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight">
                            <span className="block text-white drop-shadow-lg">Ready to Begin</span>
                            <span className="block text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-orange-400 to-rose-400">
                                Your Journey?
                            </span>
                        </h1>

                        <p className="text-xl md:text-2xl mb-10 text-slate-100 leading-relaxed max-w-2xl">
                            Safe, reliable, and affordable ferry transportation across the beautiful Konkan coastline. Experience the scenic beauty of Maharashtra's waterways.
                        </p>

                        <div className="flex flex-col sm:flex-row gap-4">
                            <Link to="/customer/book">
                                <Button size="lg" className="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white border-0 shadow-xl shadow-orange-500/30 w-full sm:w-auto text-lg px-8 py-6 rounded-full">
                                    <span>Let's Get Started</span>
                                    <ArrowRight className="ml-2 w-5 h-5" />
                                </Button>
                            </Link>
                            <Link to="/routes">
                                <Button size="lg" variant="outline" className="border-2 border-white text-white hover:bg-white hover:text-slate-900 w-full sm:w-auto text-lg px-8 py-6 rounded-full backdrop-blur-sm">
                                    View Ferry Routes
                                </Button>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            {/* Stats Section - Clean Modern Design with New Palette */}
            <section className="relative -mt-8 z-20 pb-12">
                <div className="container mx-auto px-4">
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        {stats.map((stat, index) => {
                            const Icon = stat.icon;
                            const colors = [
                                'from-cyan-500 to-cyan-600',
                                'from-orange-500 to-orange-600',
                                'from-cyan-600 to-cyan-700',
                                'from-orange-600 to-orange-700'
                            ];
                            return (
                                <div
                                    key={stat.label}
                                    className="bg-white rounded-2xl p-6 shadow-xl shadow-slate-900/10 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300"
                                >
                                    <div className="flex items-center gap-3 mb-2">
                                        <div className={`w-10 h-10 rounded-xl bg-gradient-to-br ${colors[index]} flex items-center justify-center`}>
                                            <Icon className="w-5 h-5 text-white" />
                                        </div>
                                        <div className="text-3xl md:text-4xl font-bold text-slate-800">
                                            {stat.value}
                                        </div>
                                    </div>
                                    <div className="text-slate-600 font-medium">{stat.label}</div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* Ferry Routes Section - Using REAL Scraped Ferry Images */}
            <section className="py-20 bg-gradient-to-b from-slate-50 to-white">
                <div className="container mx-auto px-4">
                    <div className="text-center mb-16">
                        <span className="inline-block px-4 py-1.5 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold mb-4">
                            Our Services
                        </span>
                        <h2 className="text-4xl md:text-5xl font-bold mb-4 text-slate-900">Ferry Services</h2>
                        <p className="text-xl text-slate-600 max-w-2xl mx-auto">
                            Choose from 4 scenic routes along the beautiful Konkan coast of Maharashtra
                        </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {routes.map((route) => (
                            <Card key={route.id} className="overflow-hidden group border-0 shadow-lg hover:shadow-2xl transition-all duration-500 bg-white rounded-3xl">
                                <div className="h-52 relative overflow-hidden">
                                    <img
                                        src={route.image}
                                        alt={`${route.name} - ${route.ferryName}`}
                                        className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                    />
                                    <div className="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/50 to-transparent"></div>

                                    {/* Status Badge */}
                                    <div className="absolute top-4 right-4">
                                        <span className="px-3 py-1 bg-emerald-500 text-white text-xs font-semibold rounded-full shadow-lg">
                                            Active
                                        </span>
                                    </div>

                                    {/* Ferry Name Badge */}
                                    <div className="absolute top-4 left-4">
                                        <span className="px-3 py-1 bg-gradient-to-r from-orange-500 to-rose-500 text-white text-xs font-semibold rounded-full shadow-lg">
                                            {route.ferryName}
                                        </span>
                                    </div>

                                    {/* Route Info Overlay */}
                                    <div className="absolute bottom-4 left-4 right-4 text-white">
                                        <div className="text-xs opacity-80 font-medium uppercase tracking-wider">{route.serviceType}</div>
                                        <div className="text-sm opacity-90 font-medium">Since {route.since}</div>
                                        <h3 className="text-xl font-bold">{route.name}</h3>
                                    </div>
                                </div>

                                <CardContent className="p-5 bg-white">
                                    <div className="flex justify-between items-center mb-4">
                                        <div className="flex items-center gap-2 text-slate-600">
                                            <MapPin className="w-4 h-4 text-orange-500" />
                                            <span className="text-sm font-medium">{route.distance}</span>
                                        </div>
                                        <div className="flex items-center gap-2 text-slate-600">
                                            <Clock className="w-4 h-4 text-orange-500" />
                                            <span className="text-sm font-medium">{route.duration}</span>
                                        </div>
                                    </div>

                                    <Link to={`/routes/${route.id}`}>
                                        <Button className="w-full bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-600 hover:to-cyan-700 text-white shadow-lg shadow-cyan-500/20 rounded-xl">
                                            Know More
                                            <ArrowRight className="ml-2 w-4 h-4" />
                                        </Button>
                                    </Link>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                </div>
            </section>

            {/* Features Section with Modern Gradient - Updated Colors */}
            <section className="py-20 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-950 relative overflow-hidden">
                {/* Background decoration */}
                <div className="absolute inset-0 opacity-20">
                    <div className="absolute top-0 left-0 w-96 h-96 bg-orange-500 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
                    <div className="absolute bottom-0 right-0 w-96 h-96 bg-rose-500 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
                </div>

                <div className="container mx-auto px-4 relative z-10">
                    <div className="text-center mb-16">
                        <span className="inline-block px-4 py-1.5 bg-white/10 text-white rounded-full text-sm font-semibold mb-4 backdrop-blur-sm">
                            Why Choose Us
                        </span>
                        <h2 className="text-4xl md:text-5xl font-bold mb-4 text-white">Why Choose Jetty Ferry</h2>
                        <p className="text-xl text-slate-300 max-w-2xl mx-auto">
                            Over two decades of trusted ferry service across Maharashtra's coastline
                        </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {features.map((feature) => {
                            const Icon = feature.icon;
                            return (
                                <div
                                    key={feature.title}
                                    className="bg-white/5 backdrop-blur-md rounded-2xl p-8 border border-white/10 hover:bg-white/10 transition-all duration-300 hover:-translate-y-2"
                                >
                                    <div className={`inline-flex items-center justify-center w-16 h-16 rounded-2xl ${feature.color} mb-6 shadow-xl`}>
                                        <Icon className="w-8 h-8 text-white" />
                                    </div>
                                    <h3 className="text-xl font-bold mb-3 text-white">{feature.title}</h3>
                                    <p className="text-slate-300">{feature.description}</p>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* Gallery/Scenic Section - Using Real Cruise and Ferry Images */}
            <section className="py-20 bg-white">
                <div className="container mx-auto px-4">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <span className="inline-block px-4 py-1.5 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold mb-4">
                                Cruise Services
                            </span>
                            <h2 className="text-4xl md:text-5xl font-bold mb-6 text-slate-900">
                                Experience the Beauty of Konkan Coast
                            </h2>
                            <p className="text-lg text-slate-600 mb-8 leading-relaxed">
                                Now a days tourism has flourished well in 'Konkan Region'. Tourists are always seeking for something new and exciting.
                                Keeping in view this need, we have started CRUISE service like Goa-cruise at various seasons.
                            </p>
                            <div className="space-y-4 mb-8">
                                {[
                                    'Eco-friendly transportation option',
                                    'Save hours on road travel',
                                    'Comfortable journey with scenic views'
                                ].map((item) => (
                                    <div key={item} className="flex items-center gap-3">
                                        <div className="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                            <svg className="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <span className="text-slate-700 font-medium">{item}</span>
                                    </div>
                                ))}
                            </div>
                            <Link to="/about">
                                <Button variant="outline" className="border-2 border-orange-500 text-orange-600 hover:bg-orange-500 hover:text-white px-8 rounded-xl">
                                    Learn More About Us
                                    <ArrowRight className="ml-2 w-4 h-4" />
                                </Button>
                            </Link>
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                            <img
                                src={cruiseServiceImg}
                                alt="Cruise Service"
                                className="rounded-2xl shadow-2xl w-full h-64 object-cover hover:scale-105 transition-transform duration-500"
                            />
                            <img
                                src={ferryAvantikaImg}
                                alt="AVANTIKA Ferry - Dabhol Dhopave"
                                className="rounded-2xl shadow-2xl w-full h-64 object-cover mt-8 hover:scale-105 transition-transform duration-500"
                            />
                        </div>
                    </div>
                </div>
            </section>

            {/* How It Works */}
            <section className="py-20 bg-slate-50">
                <div className="container mx-auto px-4">
                    <div className="text-center mb-16">
                        <span className="inline-block px-4 py-1.5 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold mb-4">
                            Quick & Easy
                        </span>
                        <h2 className="text-4xl md:text-5xl font-bold mb-4 text-slate-900">Book in 3 Simple Steps</h2>
                        <p className="text-xl text-slate-600">Get your ferry ticket in minutes</p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                        {[
                            { step: 1, title: 'Select Route', desc: 'Choose your departure and destination from our available routes', icon: MapPin, color: 'from-orange-500 to-rose-500' },
                            { step: 2, title: 'Pick Date & Time', desc: 'Select your preferred travel date and ferry schedule', icon: Clock, color: 'from-sky-500 to-indigo-500' },
                            { step: 3, title: 'Pay & Get Ticket', desc: 'Complete payment and receive instant QR ticket', icon: Ship, color: 'from-emerald-500 to-teal-500' },
                        ].map((item) => {
                            const Icon = item.icon;
                            return (
                                <div key={item.step} className="relative text-center group">
                                    {item.step < 3 && (
                                        <div className="hidden md:block absolute top-12 left-[60%] w-[80%] h-0.5 bg-gradient-to-r from-slate-300 to-transparent"></div>
                                    )}
                                    <div className={`w-24 h-24 rounded-full bg-gradient-to-br ${item.color} text-white text-3xl font-bold flex items-center justify-center mx-auto mb-6 shadow-xl group-hover:scale-110 transition-transform duration-300`}>
                                        <Icon className="w-10 h-10" />
                                    </div>
                                    <div className="text-sm font-semibold text-orange-600 mb-2">Step {item.step}</div>
                                    <h3 className="text-xl font-bold mb-2 text-slate-900">{item.title}</h3>
                                    <p className="text-slate-600">{item.desc}</p>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* CTA Section - Using Real Ferry Image */}
            <section className="relative py-24 overflow-hidden">
                <div
                    className="absolute inset-0 bg-cover bg-center"
                    style={{ backgroundImage: `url(${ferrySupriyaImg})` }}
                >
                    <div className="absolute inset-0 bg-gradient-to-r from-slate-900/95 via-slate-900/85 to-slate-900/70"></div>
                </div>

                <div className="container mx-auto px-4 relative z-10 text-center text-white">
                    <h2 className="text-4xl md:text-5xl font-bold mb-6">Ready to Book Your Journey?</h2>
                    <p className="text-xl mb-10 text-slate-200 max-w-2xl mx-auto">
                        Book your ferry tickets online and skip the queue. Fast, easy, and secure.
                    </p>
                    <div className="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link to="/customer/register">
                            <Button size="lg" className="bg-gradient-to-r from-orange-500 to-rose-500 hover:from-orange-600 hover:to-rose-600 text-white border-0 shadow-xl shadow-orange-500/30 text-lg px-10 py-6 rounded-full">
                                Create Free Account
                                <ArrowRight className="ml-2 w-5 h-5" />
                            </Button>
                        </Link>
                        <Link to="/contact">
                            <Button size="lg" variant="outline" className="border-2 border-white text-white hover:bg-white hover:text-slate-900 text-lg px-10 py-6 rounded-full">
                                Contact Us
                            </Button>
                        </Link>
                    </div>
                </div>
            </section>
        </div>
    );
}
