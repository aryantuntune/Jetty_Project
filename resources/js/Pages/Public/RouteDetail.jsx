import { Link } from '@inertiajs/react';
import { route as ziggyRoute } from 'ziggy-js';
import PublicLayout from '@/Layouts/PublicLayout';
import {
    Phone,
    Clock,
    MapPin,
    ArrowRight,
    ChevronRight,
    Home,
    Calendar,
    IndianRupee,
} from 'lucide-react';

export default function RouteDetail({ route, otherRoutes }) {
    return (
        <>
            {/* Page Hero */}
            <section className="relative bg-gradient-to-br from-sky-600 to-sky-800 py-24 md:py-32">
                <div className="absolute inset-0 overflow-hidden">
                    <div className="absolute top-0 left-0 w-96 h-96 bg-sky-400/20 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2" />
                    <div className="absolute bottom-0 right-0 w-96 h-96 bg-amber-400/10 rounded-full blur-3xl translate-x-1/2 translate-y-1/2" />
                </div>

                <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h1 className="text-4xl md:text-5xl font-bold text-white mb-4">
                        {route.name}
                    </h1>
                    <p className="text-lg text-sky-100 max-w-2xl mx-auto mb-8">
                        {route.tagline}
                    </p>

                    {/* Breadcrumb */}
                    <nav className="flex items-center justify-center gap-2 text-sm text-sky-200">
                        <Link href={ziggyRoute('public.home')} className="hover:text-white transition-colors flex items-center gap-1">
                            <Home className="w-4 h-4" />
                            Home
                        </Link>
                        <ChevronRight className="w-4 h-4" />
                        <span className="text-sky-300">Ferry Services</span>
                        <ChevronRight className="w-4 h-4" />
                        <span className="text-amber-300 font-medium">{route.name}</span>
                    </nav>
                </div>
            </section>

            {/* Route Content */}
            <section className="py-16 md:py-24">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid lg:grid-cols-3 gap-12">
                        {/* Main Content */}
                        <div className="lg:col-span-2">
                            <h2 className="text-2xl md:text-3xl font-bold text-sky-800 mb-6">
                                About This Route
                            </h2>

                            <div className="prose prose-lg prose-slate max-w-none">
                                {route.paragraphs?.map((paragraph, index) => (
                                    <p key={index} className="text-slate-600 leading-relaxed mb-4">
                                        {paragraph}
                                    </p>
                                ))}
                            </div>

                            {/* Route Image */}
                            {route.image && (
                                <div className="mt-8 rounded-3xl overflow-hidden shadow-xl">
                                    <img
                                        src={`/images/carferry/routes/${route.image}`}
                                        alt={`${route.name} Ferry`}
                                        className="w-full h-80 object-cover"
                                        onError={(e) => {
                                            e.target.src = '/images/carferry/routes/default.jpg';
                                        }}
                                    />
                                </div>
                            )}

                            {/* Additional Info */}
                            {route.additional_info && (
                                <div className="mt-12">
                                    <h2 className="text-2xl font-bold text-sky-800 mb-4">
                                        Tourist Destinations
                                    </h2>
                                    <p className="text-slate-600 leading-relaxed">
                                        {route.additional_info}
                                    </p>
                                </div>
                            )}
                        </div>

                        {/* Sidebar */}
                        <div className="space-y-6">
                            {/* Contact Info Card */}
                            <div className="bg-sky-50 rounded-3xl p-6 border border-sky-100">
                                <h3 className="text-lg font-bold text-sky-800 mb-6 pb-4 border-b-2 border-sky-200">
                                    Contact Information
                                </h3>

                                <div className="space-y-4">
                                    {route.contacts && Object.entries(route.contacts).map(([location, numbers]) => (
                                        <div key={location} className="flex items-start gap-3">
                                            <div className="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center flex-shrink-0">
                                                <Phone className="w-5 h-5 text-sky-600" />
                                            </div>
                                            <div>
                                                <p className="font-semibold text-slate-800">{location}</p>
                                                {numbers.map((number, idx) => (
                                                    <a
                                                        key={idx}
                                                        href={`tel:${number.replace(/[^0-9+]/g, '')}`}
                                                        className="block text-sm text-slate-600 hover:text-sky-600 transition-colors"
                                                    >
                                                        {number}
                                                    </a>
                                                ))}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            {/* Operating Hours Card */}
                            <div className="bg-sky-50 rounded-3xl p-6 border border-sky-100">
                                <h3 className="text-lg font-bold text-sky-800 mb-6 pb-4 border-b-2 border-sky-200">
                                    Operating Hours
                                </h3>

                                <div className="flex items-start gap-3">
                                    <div className="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center flex-shrink-0">
                                        <Clock className="w-5 h-5 text-sky-600" />
                                    </div>
                                    <div>
                                        <p className="font-semibold text-slate-800">Daily Service</p>
                                        <p className="text-sm text-slate-600">Check timetable for schedule</p>
                                    </div>
                                </div>
                            </div>

                            {/* Book Now Card */}
                            <div className="bg-gradient-to-br from-amber-400 to-orange-500 rounded-3xl p-6 text-center shadow-lg shadow-amber-500/20">
                                <h3 className="text-lg font-bold text-slate-900 mb-2">
                                    Book Your Ticket
                                </h3>
                                <p className="text-slate-800 text-sm mb-6">
                                    Skip the queue! Book your ferry ticket online and travel hassle-free.
                                </p>
                                <Link
                                    href={ziggyRoute('customer.login')}
                                    className="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-amber-600 font-semibold hover:bg-sky-50 transition-colors w-full justify-center"
                                >
                                    Book Now
                                    <ArrowRight className="w-4 h-4" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Schedule & Rates Section */}
            {(route.timetable || route.ratecard) && (
                <section className="py-16 bg-sky-50">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="text-center mb-12">
                            <h2 className="text-2xl md:text-3xl font-bold text-slate-800 mb-2">
                                Schedule & Rates
                            </h2>
                            <p className="text-slate-600">Check our timetable and fare information</p>
                        </div>

                        <div className="grid md:grid-cols-2 gap-8">
                            {/* Timetable */}
                            {route.timetable && (
                                <div className="bg-white rounded-3xl overflow-hidden shadow-lg border border-sky-100">
                                    <div className="bg-sky-600 px-6 py-4 flex items-center gap-3">
                                        <Calendar className="w-5 h-5 text-white" />
                                        <h3 className="text-lg font-bold text-white">Ferry Time Table</h3>
                                    </div>
                                    <div className="p-6">
                                        <img
                                            src={`/images/carferry/timetables/${route.timetable}`}
                                            alt={`${route.name} Timetable`}
                                            className="w-full rounded-xl"
                                            onError={(e) => {
                                                e.target.parentElement.innerHTML = '<p class="text-center text-slate-500 py-8">Timetable image not available</p>';
                                            }}
                                        />
                                    </div>
                                </div>
                            )}

                            {/* Rate Card */}
                            {route.ratecard && (
                                <div className="bg-white rounded-3xl overflow-hidden shadow-lg border border-sky-100">
                                    <div className="bg-sky-600 px-6 py-4 flex items-center gap-3">
                                        <IndianRupee className="w-5 h-5 text-white" />
                                        <h3 className="text-lg font-bold text-white">Ferry Rate Card</h3>
                                    </div>
                                    <div className="p-6">
                                        <img
                                            src={`/images/carferry/ratecards/${route.ratecard}`}
                                            alt={`${route.name} Rate Card`}
                                            className="w-full rounded-xl"
                                            onError={(e) => {
                                                e.target.parentElement.innerHTML = '<p class="text-center text-slate-500 py-8">Rate card image not available</p>';
                                            }}
                                        />
                                    </div>
                                </div>
                            )}
                        </div>

                        <p className="text-center text-sm text-slate-500 mt-8">
                            * Schedules may vary based on weather and tide conditions. Please call to confirm.
                        </p>
                    </div>
                </section>
            )}

            {/* Other Routes Section */}
            {otherRoutes && otherRoutes.length > 0 && (
                <section className="py-16 bg-white">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="text-center mb-12">
                            <h2 className="text-2xl md:text-3xl font-bold text-slate-800 mb-2">
                                Explore Other Routes
                            </h2>
                        </div>

                        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {otherRoutes
                                .filter((r) => r.slug !== route.slug)
                                .slice(0, 5)
                                .map((otherRoute) => (
                                    <Link
                                        key={otherRoute.slug}
                                        href={ziggyRoute('public.route', otherRoute.slug)}
                                        className="group bg-white rounded-2xl p-6 border border-sky-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
                                    >
                                        <h4 className="text-lg font-bold text-sky-700 mb-2 group-hover:text-sky-800">
                                            {otherRoute.name}
                                        </h4>
                                        <p className="text-sm text-slate-600 line-clamp-2">
                                            {otherRoute.tagline}
                                        </p>
                                    </Link>
                                ))}
                        </div>
                    </div>
                </section>
            )}

            {/* Contact Bar */}
            <section className="bg-gradient-to-r from-amber-400 to-orange-500 py-8">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid md:grid-cols-3 gap-6 text-center">
                        <div>
                            <h4 className="font-bold text-slate-900 mb-1">Need Help?</h4>
                            <a href="tel:+919422431371" className="text-slate-800 hover:underline">
                                +91 9422431371
                            </a>
                        </div>
                        <div>
                            <h4 className="font-bold text-slate-900 mb-1">Email Us</h4>
                            <a href="mailto:ssmsdapoli@rediffmail.com" className="text-slate-800 hover:underline">
                                ssmsdapoli@rediffmail.com
                            </a>
                        </div>
                        <div>
                            <h4 className="font-bold text-slate-900 mb-1">Operating Hours</h4>
                            <p className="text-slate-800">9:00 AM - 5:00 PM (7 Days)</p>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
}

RouteDetail.layout = (page) => <PublicLayout>{page}</PublicLayout>;
