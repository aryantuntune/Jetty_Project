import { Link } from 'react-router-dom';
import { Card, CardContent, Button } from '@/components/ui';
import { Ship, Clock, MapPin, ArrowRight, Star, Anchor, Waves } from 'lucide-react';

// Import ferry images
import thumbDabholImg from '@/assets/thumb_dabhol.jpg';
import thumbDighiImg from '@/assets/thumb_dighi.jpg';
import thumbVeshviImg from '@/assets/thumb_veshvi.jpg';
import ferrySupriyaImg from '@/assets/ferry_supriya.jpg';
import waterRipplesImg from '@/assets/water_ripples.jpg';

export function FerryRoutes() {
    const routes = [
        {
            id: 'dabhol-dhopave',
            name: 'Dabhol – Dhopave',
            from: 'Dabhol',
            to: 'Dhopave',
            distance: '8 km',
            duration: '20 minutes',
            departures: 11,
            price: 'From ₹40',
            description: 'The first ferry route in Maharashtra, connecting Dabhol to Dhopave across the beautiful Vashishti River.',
            image: thumbDabholImg,
            rating: 4.8,
        },
        {
            id: 'jaigad-tawsal',
            name: 'Jaigad – Tawsal',
            from: 'Jaigad',
            to: 'Tawsal',
            distance: '6 km',
            duration: '15 minutes',
            departures: 9,
            price: 'From ₹35',
            description: 'Scenic route connecting Jaigad Fort area to Tawsal, popular with tourists visiting the historic fort.',
            image: ferrySupriyaImg,
            rating: 4.6,
        },
        {
            id: 'dighi-agardande',
            name: 'Dighi – Agardande',
            from: 'Dighi',
            to: 'Agardande',
            distance: '5 km',
            duration: '12 minutes',
            departures: 12,
            price: 'From ₹30',
            description: 'Quick connection between Dighi port and Agardande, reducing travel time significantly.',
            image: thumbDighiImg,
            rating: 4.7,
        },
        {
            id: 'veshvi-bagmandale',
            name: 'Veshvi – Bagmandale',
            from: 'Veshvi',
            to: 'Bagmandale',
            distance: '7 km',
            duration: '18 minutes',
            departures: 7,
            price: 'From ₹45',
            description: 'Connecting Veshvi to Bagmandale, serving the local fishing community and tourists.',
            image: thumbVeshviImg,
            rating: 4.5,
        },
    ];

    return (
        <div className="overflow-hidden">
            {/* Hero Section */}
            <section className="relative py-20">
                <div
                    className="absolute inset-0 bg-cover bg-center"
                    style={{ backgroundImage: `url(${waterRipplesImg})` }}
                >
                    <div className="absolute inset-0 bg-gradient-to-b from-slate-900/80 via-slate-800/70 to-white"></div>
                </div>

                <div className="container mx-auto px-4 relative z-10">
                    <div className="text-center text-white">
                        <span className="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium mb-6">
                            <Ship className="w-4 h-4" />
                            Our Ferry Routes
                        </span>
                        <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                            Explore Maharashtra's Coastal Routes
                        </h1>
                        <p className="text-xl text-slate-200 max-w-2xl mx-auto">
                            Discover our network of ferry routes connecting coastal communities.
                            Choose your route and book your journey today.
                        </p>
                    </div>
                </div>
            </section>

            {/* Routes Grid */}
            <section className="py-16 bg-slate-50">
                <div className="container mx-auto px-4">
                    <div className="grid md:grid-cols-2 gap-8">
                        {routes.map((route) => (
                            <Card key={route.id} className="overflow-hidden hover:shadow-2xl transition-all duration-500 group border-0 shadow-lg rounded-2xl">
                                {/* Image Header */}
                                <div className="h-48 relative overflow-hidden">
                                    <img
                                        src={route.image}
                                        alt={route.name}
                                        className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                    />
                                    <div className="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent"></div>

                                    {/* Rating Badge */}
                                    <div className="absolute top-4 right-4 flex items-center gap-1 px-2 py-1 bg-white/90 backdrop-blur-sm rounded-full">
                                        <Star className="w-4 h-4 fill-amber-400 text-amber-400" />
                                        <span className="text-sm font-semibold text-slate-800">{route.rating}</span>
                                    </div>

                                    {/* Route Name */}
                                    <div className="absolute bottom-4 left-4 right-4">
                                        <h3 className="text-2xl font-bold text-white">{route.name}</h3>
                                        <div className="flex items-center gap-4 mt-2 text-sm text-white/80">
                                            <span className="flex items-center gap-1">
                                                <MapPin className="w-4 h-4" />
                                                {route.distance}
                                            </span>
                                            <span className="flex items-center gap-1">
                                                <Clock className="w-4 h-4" />
                                                {route.duration}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {/* Content */}
                                <CardContent className="p-6">
                                    <p className="text-slate-600 mb-5 line-clamp-2">{route.description}</p>

                                    <div className="flex items-center justify-between mb-5 py-4 border-t border-b border-slate-100">
                                        <div>
                                            <div className="text-sm text-slate-500">Daily Departures</div>
                                            <div className="text-xl font-bold text-slate-800">{route.departures} trips</div>
                                        </div>
                                        <div className="text-right">
                                            <div className="text-sm text-slate-500">Starting Price</div>
                                            <div className="text-xl font-bold text-cyan-600">{route.price}</div>
                                        </div>
                                    </div>

                                    <div className="flex gap-3">
                                        <Link to={`/routes/${route.id}`} className="flex-1">
                                            <Button variant="outline" className="w-full border-slate-300 hover:border-cyan-500 hover:text-cyan-600">
                                                View Details
                                            </Button>
                                        </Link>
                                        <Link to="/customer/book">
                                            <Button className="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white shadow-lg shadow-orange-500/20">
                                                Book Now
                                                <ArrowRight className="w-4 h-4 ml-2" />
                                            </Button>
                                        </Link>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                </div>
            </section>

            {/* CTA Section */}
            <section className="py-16 bg-gradient-to-r from-slate-700 via-slate-800 to-slate-900 text-center">
                <div className="container mx-auto px-4">
                    <div className="flex items-center justify-center gap-3 mb-4">
                        <Anchor className="w-8 h-8 text-cyan-400" />
                        <Waves className="w-6 h-6 text-cyan-400" />
                    </div>
                    <h2 className="text-3xl md:text-4xl font-bold mb-4 text-white">Ready to Start Your Journey?</h2>
                    <p className="text-slate-300 mb-8 max-w-xl mx-auto">
                        Book your ferry tickets online and skip the queue at the jetty.
                    </p>
                    <Link to="/customer/book">
                        <Button size="lg" className="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white shadow-xl shadow-orange-500/30 text-lg px-8 py-6 rounded-full">
                            Book Your Ticket Now
                            <ArrowRight className="w-5 h-5 ml-2" />
                        </Button>
                    </Link>
                </div>
            </section>
        </div>
    );
}
