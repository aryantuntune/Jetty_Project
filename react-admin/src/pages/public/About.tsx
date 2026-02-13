import { Card } from '@/components/ui';
import { Ship, Users, Award, MapPin, Target, Eye, Anchor, Waves } from 'lucide-react';

// Import ferry images
import ferryAvantikaImg from '@/assets/ferry_avantika.jpg';
import ferrySupriyaImg from '@/assets/ferry_supriya.jpg';
import waterRipplesImg from '@/assets/water_ripples.jpg';

export function About() {
    const stats = [
        { icon: Ship, value: '20+', label: 'Years of Service', color: 'bg-cyan-500' },
        { icon: MapPin, value: '8', label: 'Ferry Terminals', color: 'bg-orange-500' },
        { icon: Users, value: '2M+', label: 'Passengers Annually', color: 'bg-cyan-600' },
        { icon: Award, value: '100%', label: 'Safety Record', color: 'bg-orange-600' },
    ];

    const milestones = [
        { year: '2003', title: 'First Route Launch', description: 'Dabhol-Dhopave route inaugurated' },
        { year: '2005', title: 'Expansion', description: 'Jaigad-Tawsal route added' },
        { year: '2008', title: 'Fleet Growth', description: 'Dighi-Agardande route operational' },
        { year: '2010', title: 'Full Network', description: 'Veshvi-Bagmandale completes the network' },
        { year: '2020', title: 'Modernization', description: 'Digital booking system launched' },
        { year: '2024', title: 'Mobile App', description: 'Ferry booking app released' },
    ];

    return (
        <div className="overflow-hidden">
            {/* Hero Section with Water Background */}
            <section className="relative py-24">
                <div
                    className="absolute inset-0 bg-cover bg-center"
                    style={{ backgroundImage: `url(${waterRipplesImg})` }}
                >
                    <div className="absolute inset-0 bg-gradient-to-b from-slate-900/80 via-slate-800/70 to-white"></div>
                </div>

                <div className="container mx-auto px-4 relative z-10">
                    <div className="text-center text-white">
                        <span className="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium mb-6">
                            <Anchor className="w-4 h-4" />
                            About Us
                        </span>
                        <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">About Jetty Ferry</h1>
                        <p className="text-xl text-slate-200 max-w-3xl mx-auto">
                            Providing safe, reliable, and affordable ferry transportation along Maharashtra's beautiful coastline since 2003.
                        </p>
                    </div>
                </div>
            </section>

            {/* Stats Section */}
            <section className="relative -mt-8 z-20 pb-16">
                <div className="container mx-auto px-4">
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        {stats.map((stat) => {
                            const Icon = stat.icon;
                            return (
                                <div
                                    key={stat.label}
                                    className="bg-white rounded-2xl p-6 shadow-xl shadow-slate-900/10 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 text-center"
                                >
                                    <div className={`w-14 h-14 ${stat.color} rounded-2xl flex items-center justify-center mx-auto mb-4`}>
                                        <Icon className="w-7 h-7 text-white" />
                                    </div>
                                    <div className="text-3xl font-bold text-slate-800 mb-1">{stat.value}</div>
                                    <div className="text-slate-600">{stat.label}</div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* Mission & Vision */}
            <section className="py-16 bg-slate-50">
                <div className="container mx-auto px-4">
                    <div className="grid md:grid-cols-2 gap-8">
                        <Card className="p-8 border-0 shadow-lg hover:shadow-xl transition-shadow bg-gradient-to-br from-cyan-50 to-white">
                            <div className="flex items-center gap-3 mb-6">
                                <div className="p-3 bg-cyan-500 rounded-xl">
                                    <Target className="w-6 h-6 text-white" />
                                </div>
                                <h2 className="text-2xl font-bold text-slate-800">Our Mission</h2>
                            </div>
                            <p className="text-slate-600 leading-relaxed text-lg">
                                To provide safe, reliable, and affordable ferry transportation services that connect coastal communities and promote economic development in Maharashtra. We are committed to delivering exceptional service while maintaining the highest safety standards.
                            </p>
                        </Card>

                        <Card className="p-8 border-0 shadow-lg hover:shadow-xl transition-shadow bg-gradient-to-br from-orange-50 to-white">
                            <div className="flex items-center gap-3 mb-6">
                                <div className="p-3 bg-orange-500 rounded-xl">
                                    <Eye className="w-6 h-6 text-white" />
                                </div>
                                <h2 className="text-2xl font-bold text-slate-800">Our Vision</h2>
                            </div>
                            <p className="text-slate-600 leading-relaxed text-lg">
                                To be the preferred ferry service provider in Maharashtra, known for excellence in service, safety, and customer satisfaction. We envision expanding our network to connect more coastal communities.
                            </p>
                        </Card>
                    </div>
                </div>
            </section>

            {/* Our Fleet Section */}
            <section className="py-16 bg-white">
                <div className="container mx-auto px-4">
                    <div className="grid lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <span className="inline-block px-4 py-1.5 bg-cyan-100 text-cyan-700 rounded-full text-sm font-semibold mb-4">
                                Our Fleet
                            </span>
                            <h2 className="text-3xl md:text-4xl font-bold mb-6 text-slate-800">Modern & Safe Vessels</h2>
                            <p className="text-lg text-slate-600 mb-6 leading-relaxed">
                                Our fleet includes state-of-the-art ferries like AVANTIKA and SUPRIYA, each equipped with modern safety equipment and amenities for passenger comfort.
                            </p>
                            <div className="space-y-4">
                                {['Regular safety inspections', 'Comfortable seating', 'Life jackets for all passengers', 'Experienced crew members'].map((item) => (
                                    <div key={item} className="flex items-center gap-3">
                                        <div className="w-8 h-8 rounded-full bg-cyan-100 flex items-center justify-center">
                                            <svg className="w-4 h-4 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <span className="text-slate-700 font-medium">{item}</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                            <img
                                src={ferryAvantikaImg}
                                alt="AVANTIKA Ferry"
                                className="rounded-2xl shadow-2xl w-full h-56 object-cover hover:scale-105 transition-transform duration-500"
                            />
                            <img
                                src={ferrySupriyaImg}
                                alt="SUPRIYA Ferry"
                                className="rounded-2xl shadow-2xl w-full h-56 object-cover mt-8 hover:scale-105 transition-transform duration-500"
                            />
                        </div>
                    </div>
                </div>
            </section>

            {/* History Timeline */}
            <section className="py-16 bg-gradient-to-br from-slate-700 via-slate-800 to-slate-900 text-white">
                <div className="container mx-auto px-4">
                    <div className="text-center mb-12">
                        <span className="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium mb-4">
                            <Waves className="w-4 h-4" />
                            Our Journey
                        </span>
                        <h2 className="text-3xl md:text-4xl font-bold">Two Decades of Service</h2>
                    </div>

                    <div className="max-w-4xl mx-auto">
                        <div className="space-y-6">
                            {milestones.map((milestone, index) => (
                                <div key={milestone.year} className="flex gap-6 items-start">
                                    <div className="flex-shrink-0 w-20 h-20 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-2xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                        {milestone.year}
                                    </div>
                                    <div className="flex-1 bg-white/10 backdrop-blur-sm rounded-xl p-5 border border-white/10">
                                        <h3 className="font-bold text-lg text-cyan-300">{milestone.title}</h3>
                                        <p className="text-slate-300">{milestone.description}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            {/* Our Story */}
            <section className="py-16 bg-white">
                <div className="container mx-auto px-4">
                    <div className="max-w-4xl mx-auto">
                        <span className="inline-block px-4 py-1.5 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold mb-4">
                            Our Story
                        </span>
                        <h2 className="text-3xl md:text-4xl font-bold mb-8 text-slate-800">From Humble Beginnings</h2>
                        <div className="prose prose-lg max-w-none text-slate-600 space-y-6">
                            <p>
                                Suvarnadurga Shipping & Marine Services Pvt. Ltd. was established in October 2003 by Dr. Mokal C.J. with the inauguration of the Dabhol-Dhopave route, the first of its kind in Maharashtra. What started as a small operation has grown into the region's most trusted ferry service provider.
                            </p>
                            <p>
                                Over the years, we have expanded our services to include four major routes connecting various coastal towns and villages. Our fleet has grown from a single ferry to a modern fleet of well-maintained vessels, each equipped with safety equipment and amenities for passenger comfort.
                            </p>
                            <p>
                                Today, we serve over two million passengers annually and continue to invest in our fleet, infrastructure, and technology to provide the best possible service. Our commitment to safety, reliability, and customer satisfaction has made us the trusted choice for ferry transportation in the region.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    );
}
