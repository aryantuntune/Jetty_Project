import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import PublicLayout from '@/Layouts/PublicLayout';
import {
    Ship,
    Users,
    Car,
    Clock,
    Shield,
    MapPin,
    ArrowRight,
    Phone,
    ChevronDown,
    Anchor,
    Waves,
    Check,
} from 'lucide-react';

// Ferry routes data
const ferryRoutes = [
    {
        slug: 'dabhol-dhopave',
        name: 'Dabhol – Dhopave',
        description: 'The very first site which was started on 21.10.2003 & constantly working at all times and in all seasons since its first day.',
        image: '/images/carferry/routes/dabhol-dhopave.jpg',
    },
    {
        slug: 'jaigad-tawsal',
        name: 'Jaigad – Tawsal',
        description: 'This Ferry service was started for the easy & better transportation from Guhaghar to Ratnagiri region.',
        image: '/images/carferry/routes/jaigad-tawsal.jpg',
    },
    {
        slug: 'dighi-agardande',
        name: 'Dighi – Agardande',
        description: 'Connecting to National Highway 17, this route provides easy access to destinations like Murud-Janjeera, Kashid beach, and Alibaug.',
        image: '/images/carferry/routes/dighi-agardande.jpg',
    },
    {
        slug: 'veshvi-bagmandale',
        name: 'Veshvi – Bagmandale',
        description: 'Operating since 2007, this ferry made the journey from Raigad to Ratnagiri very easy and quick.',
        image: '/images/carferry/routes/veshvi-bagmandale.jpg',
    },
    {
        slug: 'vasai-bhayander',
        name: 'Vasai – Bhayander',
        description: 'Our newest RORO service operating under the Sagarmala Project, connecting Vasai and Bhayander.',
        image: '/images/carferry/routes/vasai-bhayander.jpg',
    },
    {
        slug: 'ambet-mahpral',
        name: 'Ambet – Mahpral',
        description: 'Connecting coastal communities with reliable ferry services for passengers and vehicles.',
        image: '/images/carferry/routes/ambet-mahpral.jpg',
    },
];

// Features data
const features = [
    {
        icon: Shield,
        title: 'Safe & Reliable',
        description: 'All our ferries meet strict safety standards with trained crew members.',
    },
    {
        icon: Clock,
        title: 'On-Time Service',
        description: 'Reliable schedules you can count on, running 7 days a week.',
    },
    {
        icon: Car,
        title: 'Vehicle Transport',
        description: 'RORO ferries accommodate cars, bikes, and commercial vehicles.',
    },
    {
        icon: Users,
        title: 'Comfortable Journey',
        description: 'Spacious seating and scenic views of the Konkan coast.',
    },
    {
        icon: MapPin,
        title: '6 Major Routes',
        description: 'Extensive network connecting key destinations across Maharashtra.',
    },
    {
        icon: Anchor,
        title: 'Since 2003',
        description: 'Over 20 years of trusted service to the coastal communities.',
    },
];

export default function Welcome() {
    return (
        <>
            {/* Hero Section */}
            <section className="relative min-h-[90vh] flex items-center justify-center overflow-hidden">
                {/* Video Background */}
                <div className="absolute inset-0 z-0">
                    <video
                        autoPlay
                        muted
                        loop
                        playsInline
                        className="w-full h-full object-cover"
                    >
                        <source src="/videos/1.mp4" type="video/mp4" />
                    </video>
                    {/* Gradient Overlay */}
                    <div className="absolute inset-0 bg-gradient-to-br from-sky-900/80 via-sky-800/70 to-slate-900/80" />
                </div>

                {/* Hero Content */}
                <div className="relative z-10 max-w-4xl mx-auto px-4 text-center">
                    <div className="animate-fade-in-up">
                        <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white/90 text-sm mb-8">
                            <Waves className="w-4 h-4" />
                            <span>Maharashtra's Premier Ferry Service</span>
                        </div>

                        <h1 className="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
                            Ready to Begin Your{' '}
                            <span className="text-transparent bg-clip-text bg-gradient-to-r from-amber-300 to-orange-400">
                                Journey?
                            </span>
                        </h1>

                        <p className="text-lg md:text-xl text-sky-100 mb-10 max-w-2xl mx-auto leading-relaxed">
                            Experience seamless ferry travel across Maharashtra's beautiful Konkan coast.
                            Safe, reliable, and scenic journeys since 2003.
                        </p>

                        <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <Link
                                href={route('customer.login')}
                                className="group px-8 py-4 rounded-2xl bg-gradient-to-r from-amber-400 to-orange-500 text-slate-900 font-bold text-lg shadow-2xl shadow-amber-500/30 hover:shadow-amber-500/50 hover:-translate-y-1 transition-all duration-300 flex items-center gap-2"
                            >
                                Book Your Ferry
                                <ArrowRight className="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                            </Link>
                            <a
                                href="#routes"
                                className="px-8 py-4 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/30 text-white font-semibold hover:bg-white/20 transition-all duration-300"
                            >
                                View Routes
                            </a>
                        </div>
                    </div>
                </div>

                {/* Scroll Indicator */}
                <div className="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 animate-bounce">
                    <a href="#routes" className="flex flex-col items-center gap-2 text-white/70 hover:text-white transition-colors">
                        <span className="text-sm">Scroll to explore</span>
                        <ChevronDown className="w-6 h-6" />
                    </a>
                </div>
            </section>

            {/* Ferry Routes Section */}
            <section id="routes" className="py-24 bg-gradient-to-b from-white to-sky-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-16">
                        <span className="inline-block px-4 py-2 rounded-full bg-sky-100 text-sky-700 text-sm font-semibold mb-4">
                            Our Routes
                        </span>
                        <h2 className="text-3xl md:text-4xl font-bold text-slate-800 mb-4">
                            Ferry Services Across Konkan
                        </h2>
                        <p className="text-lg text-slate-600 max-w-2xl mx-auto">
                            Connecting Maharashtra's beautiful coastal communities with reliable ferry services since 2003.
                        </p>
                    </div>

                    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {ferryRoutes.map((ferryRoute, index) => (
                            <div
                                key={ferryRoute.slug}
                                className="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 border border-sky-100"
                                style={{ animationDelay: `${index * 0.1}s` }}
                            >
                                {/* Route Image */}
                                <div className="relative h-48 overflow-hidden">
                                    <img
                                        src={ferryRoute.image}
                                        alt={ferryRoute.name}
                                        className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                        onError={(e) => {
                                            e.target.src = '/images/carferry/routes/default.jpg';
                                        }}
                                    />
                                    <div className="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent" />
                                    <div className="absolute bottom-4 left-4 right-4">
                                        <h3 className="text-xl font-bold text-white">{ferryRoute.name}</h3>
                                    </div>
                                </div>

                                {/* Route Content */}
                                <div className="p-6">
                                    <p className="text-slate-600 text-sm leading-relaxed mb-4">
                                        {ferryRoute.description}
                                    </p>
                                    <Link
                                        href={route('public.route', ferryRoute.slug)}
                                        className="inline-flex items-center gap-2 text-sky-600 font-semibold hover:text-sky-700 group/link"
                                    >
                                        Know More
                                        <ArrowRight className="w-4 h-4 group-hover/link:translate-x-1 transition-transform" />
                                    </Link>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Services Section */}
            <section className="py-24 bg-sky-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-16">
                        <span className="inline-block px-4 py-2 rounded-full bg-amber-100 text-amber-700 text-sm font-semibold mb-4">
                            What We Offer
                        </span>
                        <h2 className="text-3xl md:text-4xl font-bold text-slate-800 mb-4">
                            Comprehensive Ferry Services
                        </h2>
                        <p className="text-lg text-slate-600 max-w-2xl mx-auto">
                            From passenger transport to vehicle shipping, we've got all your ferry needs covered.
                        </p>
                    </div>

                    <div className="grid md:grid-cols-2 gap-8">
                        {/* Passenger Services */}
                        <div className="relative group overflow-hidden rounded-3xl">
                            <img
                                src="/images/carferry/backgrounds/cruise-services.jpg"
                                alt="Passenger Ferry Services"
                                className="w-full h-80 object-cover group-hover:scale-105 transition-transform duration-500"
                                onError={(e) => {
                                    e.target.src = '/images/carferry/misc/team-photo.jpg';
                                }}
                            />
                            <div className="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent" />
                            <div className="absolute bottom-0 left-0 right-0 p-8">
                                <h3 className="text-2xl font-bold text-white mb-2">Passenger Ferry Services</h3>
                                <p className="text-sky-100">
                                    Safe and comfortable ferry rides for passengers across all our routes.
                                    Travel with ease and enjoy the scenic Konkan coastline.
                                </p>
                            </div>
                        </div>

                        {/* Vehicle Transport */}
                        <div className="relative group overflow-hidden rounded-3xl">
                            <img
                                src="/images/carferry/backgrounds/inland-services.jpg"
                                alt="Vehicle Transport"
                                className="w-full h-80 object-cover group-hover:scale-105 transition-transform duration-500"
                                onError={(e) => {
                                    e.target.src = '/images/carferry/misc/team-photo.jpg';
                                }}
                            />
                            <div className="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent" />
                            <div className="absolute bottom-0 left-0 right-0 p-8">
                                <h3 className="text-2xl font-bold text-white mb-2">Vehicle Transportation</h3>
                                <p className="text-sky-100">
                                    Transport your cars, bikes, and commercial vehicles safely.
                                    Our RORO ferries can accommodate all types of vehicles.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Features Section */}
            <section className="py-24 bg-white">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-16">
                        <span className="inline-block px-4 py-2 rounded-full bg-sky-100 text-sky-700 text-sm font-semibold mb-4">
                            Why Choose Us
                        </span>
                        <h2 className="text-3xl md:text-4xl font-bold text-slate-800 mb-4">
                            The Trusted Choice for Ferry Travel
                        </h2>
                    </div>

                    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {features.map((feature, index) => {
                            const Icon = feature.icon;
                            return (
                                <div
                                    key={feature.title}
                                    className="group p-8 rounded-3xl bg-gradient-to-br from-sky-50 to-white border border-sky-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
                                >
                                    <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center mb-6 shadow-lg shadow-sky-500/30 group-hover:shadow-sky-500/50 transition-shadow">
                                        <Icon className="w-7 h-7 text-white" />
                                    </div>
                                    <h3 className="text-xl font-bold text-slate-800 mb-3">{feature.title}</h3>
                                    <p className="text-slate-600">{feature.description}</p>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* About Preview Section */}
            <section className="py-24 bg-gradient-to-br from-sky-900 to-slate-900 text-white">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid lg:grid-cols-2 gap-12 items-center">
                        {/* Image */}
                        <div className="relative">
                            <div className="rounded-3xl overflow-hidden shadow-2xl">
                                <img
                                    src="/images/carferry/misc/team-photo.jpg"
                                    alt="Our Leadership"
                                    className="w-full h-96 object-cover"
                                    onError={(e) => {
                                        e.target.src = '/images/carferry/routes/dabhol-dhopave.jpg';
                                    }}
                                />
                            </div>
                            {/* Stats Card */}
                            <div className="absolute -bottom-6 -right-6 bg-gradient-to-r from-amber-400 to-orange-500 rounded-2xl p-6 shadow-xl">
                                <div className="text-4xl font-bold text-slate-900">20+</div>
                                <div className="text-slate-800 font-medium">Years of Service</div>
                            </div>
                        </div>

                        {/* Content */}
                        <div>
                            <span className="inline-block px-4 py-2 rounded-full bg-white/10 text-sky-200 text-sm font-semibold mb-6">
                                About Us
                            </span>
                            <h2 className="text-3xl md:text-4xl font-bold mb-6">
                                About Suvarnadurga Shipping
                            </h2>
                            <div className="space-y-4 text-sky-100 leading-relaxed">
                                <p>
                                    Suvarnadurga Shipping & Marine Services Pvt. Ltd. was established in October 2003
                                    by Dr. Mokal C.J. (former MLA of Dapoli-Mandangad), with Dr. Mokal Y.C. serving as Managing Director.
                                </p>
                                <p>
                                    Our first venture was the Dabhol-Dhopave ferry service - the first Ferry Boat Service in Maharashtra.
                                    Since then, we have expanded to operate 6 routes across the Konkan coast, serving thousands of passengers daily.
                                </p>
                                <p>
                                    With approximately 65 employees and a commitment to safety and reliability,
                                    we continue to connect coastal communities and boost tourism in the region.
                                </p>
                            </div>

                            <div className="mt-8 flex flex-wrap gap-6">
                                <div className="flex items-center gap-2">
                                    <Check className="w-5 h-5 text-amber-400" />
                                    <span>65+ Employees</span>
                                </div>
                                <div className="flex items-center gap-2">
                                    <Check className="w-5 h-5 text-amber-400" />
                                    <span>6 Active Routes</span>
                                </div>
                                <div className="flex items-center gap-2">
                                    <Check className="w-5 h-5 text-amber-400" />
                                    <span>1000s of Daily Passengers</span>
                                </div>
                            </div>

                            <div className="mt-8">
                                <Link
                                    href="/about"
                                    className="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-sky-900 font-semibold hover:bg-sky-50 transition-colors"
                                >
                                    Learn More About Us
                                    <ArrowRight className="w-4 h-4" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Contact Bar */}
            <section className="bg-gradient-to-r from-amber-400 to-orange-500 py-12">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid md:grid-cols-3 gap-8 text-center">
                        <div>
                            <h4 className="text-lg font-bold text-slate-900 mb-2">Dabhol Office</h4>
                            <p className="text-slate-800">
                                <a href="tel:02348248900" className="hover:underline">02348-248900</a>
                            </p>
                            <p className="text-slate-800">
                                <a href="tel:+919767248900" className="hover:underline">+91 9767248900</a>
                            </p>
                        </div>
                        <div>
                            <h4 className="text-lg font-bold text-slate-900 mb-2">Veshvi Office</h4>
                            <p className="text-slate-800">
                                <a href="tel:02350223300" className="hover:underline">02350-223300</a>
                            </p>
                            <p className="text-slate-800">
                                <a href="tel:+918767980300" className="hover:underline">+91 8767980300</a>
                            </p>
                        </div>
                        <div>
                            <h4 className="text-lg font-bold text-slate-900 mb-2">Operating Hours</h4>
                            <p className="text-slate-800">9:00 AM - 5:00 PM</p>
                            <p className="text-slate-800">Open 7 Days a Week</p>
                        </div>
                    </div>
                </div>
            </section>

            <style>{`
                .animate-fade-in-up {
                    animation: fadeInUp 0.8s ease-out forwards;
                }

                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
            `}</style>
        </>
    );
}

Welcome.layout = (page) => <PublicLayout>{page}</PublicLayout>;
