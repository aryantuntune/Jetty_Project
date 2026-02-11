import { useParams, Link } from 'react-router-dom';
import { Card, CardHeader, CardTitle, CardContent, Button } from '@/components/ui';
import { Ship, Clock, MapPin, ArrowRight, Users, Car, Phone, Anchor } from 'lucide-react';

// Import images
import thumbDabholImg from '@/assets/thumb_dabhol.jpg';
import thumbDighiImg from '@/assets/thumb_dighi.jpg';
import thumbVeshviImg from '@/assets/thumb_veshvi.jpg';
import ferrySupriyaImg from '@/assets/ferry_supriya.jpg';

export function FerryRouteDetail() {
    const { routeId } = useParams();

    // Static route data (can be replaced with API)
    const routeData: Record<string, any> = {
        'dabhol-dhopave': {
            name: 'Dabhol – Dhopave',
            from: 'Dabhol',
            to: 'Dhopave',
            distance: '8 km',
            duration: '20 minutes',
            since: '2003',
            image: thumbDabholImg,
            description: 'The first ferry route in Maharashtra, connecting Dabhol to Dhopave across the beautiful Vashishti River.',
            schedules: ['06:30', '07:30', '08:30', '09:30', '10:30', '11:30', '14:00', '15:00', '16:00', '17:00', '18:00'],
            features: ['Vehicle Ferry', 'Passenger Ferry', 'Two-wheeler Ferry', 'Night Service Available'],
        },
        'jaigad-tawsal': {
            name: 'Jaigad – Tawsal',
            from: 'Jaigad',
            to: 'Tawsal',
            distance: '6 km',
            duration: '15 minutes',
            since: '2005',
            image: ferrySupriyaImg,
            description: 'Scenic route connecting Jaigad Fort area to Tawsal, popular with tourists visiting the historic fort.',
            schedules: ['07:00', '08:00', '09:00', '10:00', '11:00', '14:30', '15:30', '16:30', '17:30'],
            features: ['Vehicle Ferry', 'Passenger Ferry', 'Tourist Information'],
        },
        'dighi-agardande': {
            name: 'Dighi – Agardande',
            from: 'Dighi',
            to: 'Agardande',
            distance: '5 km',
            duration: '12 minutes',
            since: '2008',
            image: thumbDighiImg,
            description: 'Quick connection between Dighi port and Agardande, reducing travel time significantly.',
            schedules: ['06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00', '18:00'],
            features: ['Vehicle Ferry', 'Passenger Ferry', 'Express Service'],
        },
        'veshvi-bagmandale': {
            name: 'Veshvi – Bagmandale',
            from: 'Veshvi',
            to: 'Bagmandale',
            distance: '7 km',
            duration: '18 minutes',
            since: '2010',
            image: thumbVeshviImg,
            description: 'Connecting Veshvi to Bagmandale, serving the local fishing community and tourists.',
            schedules: ['07:00', '08:30', '10:00', '11:30', '14:00', '15:30', '17:00'],
            features: ['Vehicle Ferry', 'Passenger Ferry', 'Fishing Boat Support'],
        },
    };

    const route = routeData[routeId || ''] || routeData['dabhol-dhopave'];

    return (
        <div className="pb-12">
            {/* Hero with Route Image */}
            <div className="relative h-64 md:h-80 mb-8">
                <img
                    src={route.image}
                    alt={route.name}
                    className="w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>

                <div className="absolute bottom-0 left-0 right-0 p-6 md:p-8">
                    <div className="container mx-auto">
                        {/* Breadcrumb */}
                        <div className="mb-4 text-sm">
                            <Link to="/" className="text-cyan-400 hover:underline">Home</Link>
                            <span className="mx-2 text-slate-400">/</span>
                            <Link to="/routes" className="text-cyan-400 hover:underline">Ferry Routes</Link>
                            <span className="mx-2 text-slate-400">/</span>
                            <span className="text-white">{route.name}</span>
                        </div>

                        <div className="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div>
                                <div className="text-cyan-400 text-sm font-medium mb-1">Since {route.since}</div>
                                <h1 className="text-3xl md:text-4xl font-bold text-white mb-2">{route.name}</h1>
                                <div className="flex flex-wrap gap-4 text-sm text-slate-300">
                                    <span className="flex items-center gap-2">
                                        <MapPin className="w-4 h-4 text-orange-400" />
                                        {route.distance}
                                    </span>
                                    <span className="flex items-center gap-2">
                                        <Clock className="w-4 h-4 text-orange-400" />
                                        {route.duration}
                                    </span>
                                </div>
                            </div>
                            <Link to="/customer/book">
                                <Button size="lg" className="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 shadow-lg shadow-orange-500/30">
                                    Book This Route
                                    <ArrowRight className="w-5 h-5 ml-2" />
                                </Button>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div className="container mx-auto px-4">
                <div className="grid lg:grid-cols-3 gap-8">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* About */}
                        <Card className="border-0 shadow-lg">
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Anchor className="w-5 h-5 text-cyan-500" />
                                    About This Route
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="text-slate-600 leading-relaxed">{route.description}</p>
                            </CardContent>
                        </Card>

                        {/* Schedule */}
                        <Card className="border-0 shadow-lg">
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Clock className="w-5 h-5 text-cyan-500" />
                                    Ferry Schedule
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                    {route.schedules.map((time: string) => (
                                        <div
                                            key={time}
                                            className="px-4 py-3 bg-cyan-50 text-cyan-700 rounded-xl text-center font-medium border border-cyan-100"
                                        >
                                            {time}
                                        </div>
                                    ))}
                                </div>
                                <p className="text-sm text-slate-500 mt-4">
                                    * Schedule may vary on holidays. Contact for more information.
                                </p>
                            </CardContent>
                        </Card>

                        {/* Features */}
                        <Card className="border-0 shadow-lg">
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Ship className="w-5 h-5 text-cyan-500" />
                                    Available Services
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid grid-cols-2 gap-4">
                                    {route.features.map((feature: string) => (
                                        <div key={feature} className="flex items-center gap-3 p-4 bg-slate-50 rounded-xl">
                                            {feature.includes('Vehicle') && <Car className="w-5 h-5 text-cyan-600" />}
                                            {feature.includes('Passenger') && <Users className="w-5 h-5 text-orange-500" />}
                                            {!feature.includes('Vehicle') && !feature.includes('Passenger') && <Ship className="w-5 h-5 text-cyan-600" />}
                                            <span className="font-medium text-slate-700">{feature}</span>
                                        </div>
                                    ))}
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6">
                        {/* Quick Book */}
                        <Card className="bg-gradient-to-br from-cyan-50 to-cyan-100/50 border-cyan-200 border-0 shadow-lg">
                            <CardContent className="p-6">
                                <h3 className="text-lg font-bold mb-4 text-slate-800">Book Your Ticket</h3>
                                <p className="text-slate-600 text-sm mb-4">
                                    Book your ferry ticket online and skip the queue!
                                </p>
                                <Link to="/customer/book">
                                    <Button className="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 shadow-md shadow-orange-500/20">
                                        Book Now
                                    </Button>
                                </Link>
                            </CardContent>
                        </Card>

                        {/* Contact */}
                        <Card className="border-0 shadow-lg">
                            <CardContent className="p-6">
                                <h3 className="text-lg font-bold mb-4 text-slate-800">Route Information</h3>
                                <div className="space-y-3 text-sm">
                                    <div className="flex items-center gap-3">
                                        <div className="w-8 h-8 bg-cyan-100 rounded-lg flex items-center justify-center">
                                            <Phone className="w-4 h-4 text-cyan-600" />
                                        </div>
                                        <span className="text-slate-600">+91 02358-234567</span>
                                    </div>
                                    <div className="flex items-center gap-3">
                                        <div className="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                            <Clock className="w-4 h-4 text-orange-600" />
                                        </div>
                                        <span className="text-slate-600">6:00 AM - 7:00 PM</span>
                                    </div>
                                    <div className="flex items-start gap-3">
                                        <div className="w-8 h-8 bg-cyan-100 rounded-lg flex items-center justify-center mt-0.5">
                                            <MapPin className="w-4 h-4 text-cyan-600" />
                                        </div>
                                        <span className="text-slate-600">{route.from} Jetty, Ratnagiri District</span>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Other Routes */}
                        <Card className="border-0 shadow-lg">
                            <CardContent className="p-6">
                                <h3 className="text-lg font-bold mb-4 text-slate-800">Other Routes</h3>
                                <div className="space-y-2">
                                    {Object.entries(routeData)
                                        .filter(([id]) => id !== routeId)
                                        .slice(0, 3)
                                        .map(([id, r]: [string, any]) => (
                                            <Link
                                                key={id}
                                                to={`/routes/${id}`}
                                                className="block p-3 rounded-xl hover:bg-cyan-50 transition-colors border border-transparent hover:border-cyan-100"
                                            >
                                                <div className="font-medium text-slate-800">{r.name}</div>
                                                <div className="text-sm text-slate-500">{r.duration}</div>
                                            </Link>
                                        ))}
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    );
}
