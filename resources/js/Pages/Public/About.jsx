import { Head, Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import PublicLayout from '@/Layouts/PublicLayout';
import {
    Shield,
    FileText,
    Clock,
    ChevronRight,
    Home,
    ArrowRight,
    Users,
    Ship,
    MapPin,
    Calendar,
} from 'lucide-react';

const stats = [
    { number: '20+', label: 'Years of Service' },
    { number: '7', label: 'Ferry Routes' },
    { number: '65+', label: 'Employees' },
    { number: '1M+', label: 'Passengers Served' },
];

const routes = [
    { name: 'Dabhol – Dhopave', slug: 'dabhol-dhopave' },
    { name: 'Jaigad – Tawsal', slug: 'jaigad-tawsal' },
    { name: 'Dighi – Agardande', slug: 'dighi-agardande' },
    { name: 'Veshvi – Bagmandale', slug: 'veshvi-bagmandale' },
    { name: 'Vasai – Bhayander', slug: 'vasai-bhayander' },
    { name: 'Virar – Saphale (Jalsar)', slug: 'virar-saphale' },
    { name: 'Ambet – Mahpral', slug: 'ambet-mahpral' },
];

const commitments = [
    {
        icon: Shield,
        title: 'Safety First',
        description: 'All our vessels are equipped with life-saving equipment and undergo annual inspections. Your safety is our priority.',
    },
    {
        icon: FileText,
        title: 'Government Approved',
        description: 'All ticket rates and permits are approved by the Maharashtra Maritime Board. We pay approximately ₹4,00,000 annually in levies per ferry boat.',
    },
    {
        icon: Clock,
        title: 'Reliable Service',
        description: 'Operating 7 days a week, in all seasons. Our ferries have been running continuously since 2003 with minimal disruptions.',
    },
];

export default function About() {
    return (
        <>
            <Head>
                <title>About Us - Suvarnadurga Shipping & Marine Services</title>
                <meta
                    name="description"
                    content="Learn about Suvarnadurga Shipping & Marine Services - Maharashtra's first ferry boat service provider since 2003. Over 20 years of reliable ferry services."
                />
            </Head>

            {/* Page Hero */}
            <section className="relative bg-gradient-to-br from-sky-600 to-sky-800 py-24 md:py-32">
                <div className="absolute inset-0 overflow-hidden">
                    <div className="absolute top-0 left-0 w-96 h-96 bg-sky-400/20 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2" />
                    <div className="absolute bottom-0 right-0 w-96 h-96 bg-amber-400/10 rounded-full blur-3xl translate-x-1/2 translate-y-1/2" />
                </div>

                <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h1 className="text-4xl md:text-5xl font-bold text-white mb-4">About Us</h1>
                    <p className="text-lg text-sky-100">Maharashtra's First Ferry Boat Service Since 2003</p>

                    {/* Breadcrumb */}
                    <nav className="flex items-center justify-center gap-2 mt-6 text-sm text-sky-200">
                        <Link href={route('public.home')} className="hover:text-white transition-colors flex items-center gap-1">
                            <Home className="w-4 h-4" />
                            Home
                        </Link>
                        <ChevronRight className="w-4 h-4" />
                        <span className="text-amber-300 font-medium">About Us</span>
                    </nav>
                </div>
            </section>

            {/* Main About Content */}
            <section className="py-16 md:py-24">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Intro */}
                    <div className="max-w-3xl mx-auto text-center mb-16">
                        <h2 className="text-3xl font-bold text-sky-800 mb-6">
                            Suvarnadurga Shipping & Marine Services Pvt. Ltd.
                        </h2>
                        <p className="text-lg text-slate-600 leading-relaxed">
                            We are a transportation company focused on ferry services across Maharashtra's coastal regions.
                            Our organization emphasizes fuel efficiency and serves both the public and tourism sectors.
                            Since our inception in 2003, we have been committed to providing safe, reliable, and affordable
                            ferry services to connect the beautiful Konkan coast.
                        </p>
                    </div>

                    {/* Story Section */}
                    <div className="grid lg:grid-cols-2 gap-12 items-center mb-16">
                        <div className="rounded-3xl overflow-hidden shadow-xl">
                            <img
                                src="/images/carferry/misc/team-photo.jpg"
                                alt="Our Leadership Team"
                                className="w-full h-96 object-cover"
                                onError={(e) => {
                                    e.target.src = '/images/carferry/routes/dabhol-dhopave.jpg';
                                }}
                            />
                        </div>

                        <div>
                            <h3 className="text-2xl md:text-3xl font-bold text-sky-800 mb-6">Our Story</h3>
                            <div className="space-y-4 text-slate-600 leading-relaxed">
                                <p>
                                    The company was established in October 2003 by <strong>Dr. Mokal C.J.</strong> (former MLA
                                    of Dapoli-Mandangad), with <strong>Dr. Mokal Y.C.</strong> serving as Managing Director.
                                </p>
                                <p>
                                    Our first venture was the Dabhol-Dhopave ferry service, described as "a first Ferry Boat
                                    Service in Maharashtra," eliminating the need for expensive highway travel. This pioneering
                                    service opened up new possibilities for coastal transportation and tourism.
                                </p>
                                <p>
                                    Since then, we have expanded to operate seven ferry routes across the Konkan coast,
                                    connecting communities, supporting local businesses, and promoting tourism in the region.
                                    Our ferries serve thousands of passengers daily, providing a vital link between coastal towns.
                                </p>
                                <p>
                                    With approximately <strong>65 employees</strong> across different locations, we continue
                                    to grow while maintaining our commitment to safety, reliability, and customer service.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Stats Section */}
            <section className="py-16 bg-sky-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
                        {stats.map((stat) => (
                            <div key={stat.label} className="text-center">
                                <div className="text-4xl md:text-5xl font-bold text-sky-600 mb-2 font-[Inter]">
                                    {stat.number}
                                </div>
                                <div className="text-sm text-slate-600 uppercase tracking-wide font-medium">
                                    {stat.label}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Routes Operated */}
            <section className="py-16 md:py-24">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 className="text-3xl font-bold text-sky-800 text-center mb-12">
                        Ferry Routes We Operate
                    </h2>

                    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-4xl mx-auto">
                        {routes.map((routeItem) => (
                            <Link
                                key={routeItem.slug}
                                href={route('public.route', routeItem.slug)}
                                className="group flex items-center gap-4 p-5 bg-white rounded-2xl border-2 border-sky-100 hover:border-sky-300 hover:shadow-lg transition-all duration-300"
                            >
                                <div className="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center group-hover:bg-sky-200 transition-colors">
                                    <Ship className="w-6 h-6 text-sky-600" />
                                </div>
                                <span className="font-semibold text-slate-800 group-hover:text-sky-700 transition-colors">
                                    {routeItem.name}
                                </span>
                            </Link>
                        ))}
                    </div>
                </div>
            </section>

            {/* Commitment Section */}
            <section className="py-16 md:py-24 bg-white">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-12">
                        <h2 className="text-3xl font-bold text-sky-800 mb-4">Our Commitment</h2>
                        <p className="text-lg text-slate-600">
                            Safety, compliance, and customer satisfaction are our top priorities
                        </p>
                    </div>

                    <div className="grid md:grid-cols-3 gap-8">
                        {commitments.map((item) => {
                            const Icon = item.icon;
                            return (
                                <div
                                    key={item.title}
                                    className="text-center p-8 bg-sky-50 rounded-3xl"
                                >
                                    <div className="w-16 h-16 mx-auto rounded-full bg-sky-600 flex items-center justify-center mb-6">
                                        <Icon className="w-8 h-8 text-white" />
                                    </div>
                                    <h4 className="text-xl font-bold text-slate-800 mb-3">{item.title}</h4>
                                    <p className="text-slate-600 leading-relaxed">{item.description}</p>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* Contact Bar */}
            <section className="bg-gradient-to-r from-amber-400 to-orange-500 py-12">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid md:grid-cols-3 gap-8 text-center">
                        <div>
                            <h4 className="font-bold text-slate-900 mb-2">Head Office</h4>
                            <p className="text-slate-800 text-sm">Dabhol FerryBoat Jetty, Dapoli</p>
                            <p className="text-slate-800 text-sm">Dist. Ratnagiri, Maharashtra - 415712</p>
                        </div>
                        <div>
                            <h4 className="font-bold text-slate-900 mb-2">Contact Numbers</h4>
                            <p className="text-slate-800">
                                <a href="tel:02348248900" className="hover:underline">02348-248900</a>
                            </p>
                            <p className="text-slate-800">
                                <a href="tel:+919767248900" className="hover:underline">+91 9767248900</a>
                            </p>
                        </div>
                        <div>
                            <h4 className="font-bold text-slate-900 mb-2">Email Us</h4>
                            <p className="text-slate-800">
                                <a href="mailto:ssmsdapoli@rediffmail.com" className="hover:underline">
                                    ssmsdapoli@rediffmail.com
                                </a>
                            </p>
                            <p className="text-slate-800">
                                <a href="mailto:y.mokal@rediffmail.com" className="hover:underline">
                                    y.mokal@rediffmail.com
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
}

About.layout = (page) => <PublicLayout>{page}</PublicLayout>;
