import { useState, useEffect, useRef } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import {
    Calendar,
    Users,
    ChevronLeft,
    ChevronRight,
    ChevronDown,
    Search,
    Plus,
    Minus,
    Check,
    Star,
    Award,
    MapPin,
    Phone,
    Mail,
    Home,
    CalendarCheck,
    ArrowRight,
    X,
    Bed,
    Eye,
    Wind,
    ShieldCheck,
} from 'lucide-react';

export default function HouseboatBooking({ rooms, checkIn: initialCheckIn, checkOut: initialCheckOut, guests: initialGuests }) {
    // Hero Carousel
    const [currentSlide, setCurrentSlide] = useState(0);
    const heroImages = [
        '/images/houseboat/hero_banner_1.jpg',
        '/images/houseboat/hero_banner_2.jpg',
        '/images/houseboat/hero_banner_3.jpg',
        '/images/houseboat/about_deck.jpg',
    ];

    // Date Selection
    const today = new Date().toISOString().split('T')[0];
    const tomorrow = new Date(Date.now() + 86400000).toISOString().split('T')[0];
    const [checkIn, setCheckIn] = useState(initialCheckIn || today);
    const [checkOut, setCheckOut] = useState(initialCheckOut || tomorrow);

    // Occupancy
    const [showOccupancy, setShowOccupancy] = useState(false);
    const [occupancy, setOccupancy] = useState({
        rooms: 1,
        guests: initialGuests || 2,
        children: 0,
    });

    // Cart State
    const [cart, setCart] = useState({});

    // Room Drawer
    const [drawerRoom, setDrawerRoom] = useState(null);
    const [drawerSlide, setDrawerSlide] = useState(0);

    // Auto-advance carousel
    useEffect(() => {
        const timer = setInterval(() => {
            setCurrentSlide((prev) => (prev + 1) % heroImages.length);
        }, 5000);
        return () => clearInterval(timer);
    }, []);

    const formatDate = (dateString) => {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
    };

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('en-IN').format(amount);
    };

    const updateOccupancy = (type, change) => {
        setOccupancy((prev) => {
            const newValue = prev[type] + change;
            if (type === 'rooms' && (newValue < 1 || newValue > 5)) return prev;
            if (type === 'guests' && newValue < 1) return prev;
            if (type === 'children' && newValue < 0) return prev;
            return { ...prev, [type]: newValue };
        });
    };

    const addToCart = (roomId, name, price) => {
        setCart((prev) => ({
            ...prev,
            [roomId]: { qty: 1, price, name },
        }));
    };

    const updateCart = (roomId, change) => {
        setCart((prev) => {
            const current = prev[roomId];
            if (!current) return prev;
            const newQty = current.qty + change;
            if (newQty <= 0) {
                const { [roomId]: _, ...rest } = prev;
                return rest;
            }
            return { ...prev, [roomId]: { ...current, qty: newQty } };
        });
    };

    const cartTotal = Object.values(cart).reduce((sum, item) => sum + item.qty * item.price, 0);
    const cartRooms = Object.values(cart).reduce((sum, item) => sum + item.qty, 0);

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('houseboat.booking'), {
            check_in: checkIn,
            check_out: checkOut,
            guests: occupancy.guests,
        });
    };

    const handleProceed = () => {
        router.get(route('houseboat.checkout'), {
            cart_data: JSON.stringify(cart),
            check_in: checkIn,
            check_out: checkOut,
        });
    };

    return (
        <>
            <Head>
                <title>Supriya Houseboat | Booking</title>
            </Head>

            <div className="min-h-screen bg-white text-gray-900 antialiased">
                {/* Header */}
                <header className="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur shadow-sm">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex justify-between items-center h-20">
                            <div className="flex items-center gap-3">
                                <img
                                    src="https://d3ki85qs1zca4t.cloudfront.net/bookingEngine/uploads/1763102420667451.jpg"
                                    alt="Logo"
                                    className="h-12 w-12 rounded-full object-cover border-2 border-amber-200 shadow-md"
                                />
                                <div>
                                    <h1 className="text-sm font-bold uppercase tracking-widest text-gray-900">Supriya Houseboat</h1>
                                    <p className="text-xs text-gray-500">Dapoli, Maharashtra</p>
                                </div>
                            </div>
                            <nav className="hidden md:flex gap-8 text-sm font-bold uppercase tracking-wide">
                                <Link href={route('public.home')} className="text-gray-600 hover:text-amber-600 transition">Home</Link>
                                <span className="text-amber-600 border-b-2 border-amber-600 pb-1">Rooms</span>
                            </nav>
                            <div className="flex items-center gap-1 text-amber-500 font-bold">
                                4.6 <Star className="w-4 h-4 fill-current" />
                            </div>
                        </div>
                    </div>
                </header>

                {/* Hero Section */}
                <section className="relative h-[85vh] bg-gray-900 overflow-hidden pt-20">
                    {heroImages.map((src, idx) => (
                        <div
                            key={idx}
                            className={`absolute inset-0 transition-opacity duration-1000 ${idx === currentSlide ? 'opacity-100' : 'opacity-0'}`}
                        >
                            <img src={src} alt="" className="w-full h-full object-cover" />
                        </div>
                    ))}
                    <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-black/30" />

                    {/* Content */}
                    <div className="absolute inset-0 flex flex-col justify-center items-center text-center text-white px-4">
                        <span className="text-amber-400 text-sm font-bold tracking-widest uppercase mb-4">Welcome To</span>
                        <h1 className="text-5xl md:text-7xl font-serif font-bold mb-6 drop-shadow-2xl">Supriya Houseboat</h1>
                        <p className="text-lg md:text-xl text-gray-200 max-w-2xl mb-10 drop-shadow-lg">
                            Experience the serenity of backwaters with luxury and comfort in the heart of Dapoli.
                        </p>
                    </div>

                    {/* Arrows */}
                    <button
                        onClick={() => setCurrentSlide((prev) => (prev - 1 + heroImages.length) % heroImages.length)}
                        className="absolute left-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition p-2"
                    >
                        <ChevronLeft className="w-10 h-10" />
                    </button>
                    <button
                        onClick={() => setCurrentSlide((prev) => (prev + 1) % heroImages.length)}
                        className="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition p-2"
                    >
                        <ChevronRight className="w-10 h-10" />
                    </button>
                </section>

                {/* Booking Widget */}
                <div className="relative -mt-24 z-40 px-4 mb-20">
                    <div className="max-w-6xl mx-auto bg-white rounded-xl shadow-2xl p-6 md:p-8">
                        <form onSubmit={handleSearch} className="flex flex-col lg:flex-row gap-6 items-end">
                            <div className="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
                                {/* Date Selection */}
                                <div className="md:col-span-2">
                                    <label className="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                        Check In - Check Out
                                    </label>
                                    <div className="bg-gray-50 border border-gray-200 rounded-lg p-3 flex items-center gap-4">
                                        <Calendar className="w-5 h-5 text-gray-400" />
                                        <div className="flex-1 flex gap-4">
                                            <div>
                                                <span className="text-xs text-gray-500 block">Check In</span>
                                                <input
                                                    type="date"
                                                    value={checkIn}
                                                    onChange={(e) => setCheckIn(e.target.value)}
                                                    min={today}
                                                    className="font-bold text-gray-900 bg-transparent border-none p-0 focus:ring-0"
                                                />
                                            </div>
                                            <div className="border-l border-gray-300" />
                                            <div>
                                                <span className="text-xs text-gray-500 block">Check Out</span>
                                                <input
                                                    type="date"
                                                    value={checkOut}
                                                    onChange={(e) => setCheckOut(e.target.value)}
                                                    min={checkIn}
                                                    className="font-bold text-gray-900 bg-transparent border-none p-0 focus:ring-0"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Occupancy Selection */}
                                <div className="relative">
                                    <label className="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Guests</label>
                                    <button
                                        type="button"
                                        onClick={() => setShowOccupancy(!showOccupancy)}
                                        className="w-full bg-gray-50 border border-gray-200 rounded-lg p-3 flex items-center text-left hover:bg-gray-100 transition"
                                    >
                                        <Users className="w-5 h-5 text-gray-400 mr-3" />
                                        <div className="flex-1">
                                            <span className="text-xs text-gray-500 block">Occupancy</span>
                                            <span className="font-bold text-gray-900">
                                                {occupancy.rooms} Room{occupancy.rooms > 1 ? 's' : ''}, {occupancy.guests} Guest{occupancy.guests > 1 ? 's' : ''}
                                            </span>
                                        </div>
                                        <ChevronDown className="w-4 h-4 text-gray-400" />
                                    </button>

                                    {/* Popover */}
                                    {showOccupancy && (
                                        <div className="absolute top-full right-0 mt-2 w-72 bg-white rounded-xl shadow-2xl border border-gray-100 p-4 z-50">
                                            {[
                                                { key: 'rooms', label: 'Rooms', hint: 'Min 1, Max 5' },
                                                { key: 'guests', label: 'Adults', hint: '12+ years' },
                                                { key: 'children', label: 'Children', hint: '5-12 years' },
                                            ].map(({ key, label, hint }) => (
                                                <div key={key} className="flex justify-between items-center mb-4">
                                                    <div>
                                                        <p className="font-bold text-sm text-gray-900">{label}</p>
                                                        <p className="text-xs text-gray-400">{hint}</p>
                                                    </div>
                                                    <div className="flex items-center gap-3">
                                                        <button
                                                            type="button"
                                                            onClick={() => updateOccupancy(key, -1)}
                                                            className="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100"
                                                        >
                                                            <Minus className="w-3 h-3" />
                                                        </button>
                                                        <span className="font-bold text-gray-900 w-4 text-center">{occupancy[key]}</span>
                                                        <button
                                                            type="button"
                                                            onClick={() => updateOccupancy(key, 1)}
                                                            className="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100"
                                                        >
                                                            <Plus className="w-3 h-3" />
                                                        </button>
                                                    </div>
                                                </div>
                                            ))}
                                            <button
                                                type="button"
                                                onClick={() => setShowOccupancy(false)}
                                                className="w-full bg-gray-900 text-white text-xs font-bold py-3 rounded-lg hover:bg-black transition"
                                            >
                                                Done
                                            </button>
                                        </div>
                                    )}
                                </div>
                            </div>

                            <button
                                type="submit"
                                className="w-full lg:w-auto bg-gray-900 hover:bg-black text-white font-bold py-4 px-10 rounded-lg transition shadow-lg flex items-center justify-center gap-2"
                            >
                                <span>Search</span>
                                <Search className="w-5 h-5" />
                            </button>
                        </form>
                    </div>
                </div>

                {/* Room Cards */}
                <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
                    <div className="space-y-12">
                        {rooms?.map((room) => (
                            <div
                                key={room.id}
                                className="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 md:flex group border border-gray-100"
                            >
                                {/* Image Side */}
                                <div className="md:w-5/12 relative h-72 md:h-auto overflow-hidden">
                                    <img
                                        src={room.image_url}
                                        alt={room.name}
                                        className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                    />
                                    <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                        <button
                                            onClick={() => { setDrawerRoom(room); setDrawerSlide(0); }}
                                            className="text-white border border-white px-6 py-2 rounded-full font-bold uppercase tracking-widest text-sm hover:bg-white hover:text-black transition"
                                        >
                                            View Details
                                        </button>
                                    </div>
                                    {room.total_rooms <= 5 && (
                                        <div className="absolute top-4 right-4 bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded shadow-lg">
                                            Only {room.total_rooms} Rooms Left!
                                        </div>
                                    )}
                                </div>

                                {/* Content Side */}
                                <div className="md:w-7/12 p-8 flex flex-col justify-between">
                                    <div>
                                        <div className="flex justify-between items-start mb-4">
                                            <h2 className="text-3xl font-serif font-bold text-gray-900 group-hover:text-sky-600 transition">
                                                {room.name}
                                            </h2>
                                            <div className="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded border border-green-200">
                                                AVAILABLE TODAY
                                            </div>
                                        </div>

                                        {/* Amenities Chips */}
                                        <div className="flex flex-wrap gap-3 mb-6">
                                            {room.amenities?.map((amenity, idx) => (
                                                <span
                                                    key={idx}
                                                    className="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 text-gray-600 text-xs font-semibold rounded-full border border-gray-100"
                                                >
                                                    <Check className="w-3 h-3 text-sky-500" /> {amenity}
                                                </span>
                                            ))}
                                        </div>

                                        <p className="text-gray-500 text-sm leading-relaxed mb-8 line-clamp-3">
                                            {room.description}
                                        </p>
                                    </div>

                                    <div className="flex items-end justify-between border-t border-gray-100 pt-6">
                                        <div>
                                            <p className="text-xs text-gray-400 font-bold uppercase mb-1">Total Price</p>
                                            <div className="flex items-baseline gap-1">
                                                <span className="text-3xl font-bold text-gray-900">₹{formatCurrency(room.price)}</span>
                                                <span className="text-xs text-gray-500 font-medium">/ night</span>
                                            </div>
                                            <p className="text-xs text-gray-400 mt-1">Excludes GST</p>
                                        </div>

                                        {/* Add/Stepper */}
                                        <div className="flex flex-col items-end">
                                            {!cart[room.id] ? (
                                                <button
                                                    onClick={() => addToCart(room.id, room.name, room.price)}
                                                    className="bg-gradient-to-r from-gray-900 to-black hover:from-sky-600 hover:to-sky-700 text-white px-8 py-3.5 rounded-lg font-bold shadow-lg transition-all transform hover:-translate-y-1 flex items-center gap-2"
                                                >
                                                    Add Room <Plus className="w-4 h-4" />
                                                </button>
                                            ) : (
                                                <div className="flex items-center gap-4 bg-white border-2 border-sky-600 rounded-lg p-1.5 shadow-lg">
                                                    <button
                                                        onClick={() => updateCart(room.id, -1)}
                                                        className="w-8 h-8 rounded bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-700 font-bold transition"
                                                    >
                                                        <Minus className="w-4 h-4" />
                                                    </button>
                                                    <span className="font-bold text-lg text-sky-900 w-6 text-center">{cart[room.id].qty}</span>
                                                    <button
                                                        onClick={() => updateCart(room.id, 1)}
                                                        className="w-8 h-8 rounded bg-sky-600 hover:bg-sky-700 flex items-center justify-center text-white font-bold transition"
                                                    >
                                                        <Plus className="w-4 h-4" />
                                                    </button>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* About Section */}
                    <div className="mt-32 mb-20">
                        <div className="bg-sky-50 rounded-3xl p-8 md:p-16 grid md:grid-cols-2 gap-16 items-center">
                            <div>
                                <span className="text-sky-600 font-bold uppercase tracking-widest text-xs mb-3 block">Discover</span>
                                <h3 className="text-4xl font-serif font-bold text-gray-900 mb-6">A Floating Paradise</h3>
                                <p className="text-gray-600 mb-6 leading-relaxed">
                                    Immerse yourself in the tranquility of our premium houseboat. Designed to offer a perfect blend
                                    of modern luxury and traditional charm, we ensure your stay is nothing short of magical.
                                </p>
                                <ul className="space-y-4">
                                    {['Complimentary Authentic Breakfast', 'Panoramic Deck Views', '24/7 On-board Assistance'].map((item) => (
                                        <li key={item} className="flex items-center gap-3 text-gray-700">
                                            <div className="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-green-500">
                                                <Check className="w-4 h-4" />
                                            </div>
                                            <span>{item}</span>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                            <div className="relative">
                                <img
                                    src="/images/houseboat/about_deck.jpg"
                                    className="rounded-2xl shadow-2xl w-full object-cover h-96"
                                    alt="Deck"
                                />
                                <div className="absolute -bottom-6 -left-6 bg-white p-4 rounded-xl shadow-xl flex items-center gap-3">
                                    <div className="bg-amber-100 p-2 rounded-full text-amber-600">
                                        <Award className="w-6 h-6" />
                                    </div>
                                    <div>
                                        <p className="text-xs text-gray-500 font-bold uppercase">Rated</p>
                                        <p className="font-bold text-gray-900">#1 in Dapoli</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>

                {/* Footer */}
                <footer className="bg-gray-900 text-white pt-16 border-t border-gray-800">
                    <div className="max-w-7xl mx-auto px-4 pb-12">
                        <div className="grid md:grid-cols-4 gap-12">
                            <div>
                                <div className="flex items-center gap-3 mb-6">
                                    <img
                                        src="https://d3ki85qs1zca4t.cloudfront.net/bookingEngine/uploads/1763102420667451.jpg"
                                        className="w-12 h-12 rounded-full border border-gray-700"
                                        alt="Logo"
                                    />
                                    <div>
                                        <h4 className="font-bold text-lg">Supriya</h4>
                                        <p className="text-xs text-gray-400 uppercase tracking-widest">Houseboat</p>
                                    </div>
                                </div>
                                <p className="text-gray-400 text-sm leading-relaxed">
                                    Experience the finest hospitality on the waters of Dapoli. Book your stay with us today.
                                </p>
                            </div>

                            <div>
                                <h4 className="font-bold text-lg mb-6">Contact</h4>
                                <ul className="space-y-4 text-sm text-gray-400">
                                    <li className="flex items-start gap-3">
                                        <MapPin className="w-5 h-5 text-gray-500 shrink-0" />
                                        <span>Dapoli, Dist. Ratnagiri, Maharashtra 415712</span>
                                    </li>
                                    <li className="flex items-center gap-3">
                                        <Phone className="w-5 h-5 text-gray-500 shrink-0" />
                                        <span>+91 9422431371</span>
                                    </li>
                                    <li className="flex items-center gap-3">
                                        <Mail className="w-5 h-5 text-gray-500 shrink-0" />
                                        <span>booking@supriyahouseboat.com</span>
                                    </li>
                                </ul>
                            </div>

                            <div>
                                <h4 className="font-bold text-lg mb-6">Facilities</h4>
                                <ul className="space-y-3 text-sm text-gray-400">
                                    <li>• Restaurant</li>
                                    <li>• Ferry Service</li>
                                    <li>• Free Parking</li>
                                    <li>• Room Service</li>
                                </ul>
                            </div>

                            <div>
                                <h4 className="font-bold text-lg mb-6">Payment Methods</h4>
                                <p className="text-xs text-gray-500 flex items-center gap-1">
                                    <ShieldCheck className="w-3 h-3" /> Secure Payment Gateway
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-gray-950 py-6 text-center border-t border-gray-800">
                        <p className="text-xs text-gray-500">
                            © {new Date().getFullYear()} Supriya Houseboat (Jetty Services).
                        </p>
                    </div>
                </footer>

                {/* Sticky Checkout Bar */}
                {cartRooms > 0 && (
                    <div className="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur border-t border-gray-200 shadow-lg z-50">
                        <div className="max-w-7xl mx-auto px-4 py-4 flex flex-col md:flex-row items-center justify-between gap-4">
                            <div className="flex items-center gap-6 w-full md:w-auto overflow-x-auto pb-2 md:pb-0">
                                <div className="flex items-center gap-3 shrink-0">
                                    <div className="bg-sky-50 p-2 rounded-lg text-sky-600">
                                        <CalendarCheck className="w-5 h-5" />
                                    </div>
                                    <div>
                                        <p className="text-xs text-gray-500 font-bold uppercase">Dates</p>
                                        <p className="text-sm font-bold text-gray-900 whitespace-nowrap">
                                            {formatDate(checkIn)} - {formatDate(checkOut)}
                                        </p>
                                    </div>
                                </div>
                                <div className="w-px h-8 bg-gray-200 hidden md:block" />
                                <div className="flex items-center gap-3 shrink-0">
                                    <div className="bg-sky-50 p-2 rounded-lg text-sky-600">
                                        <Home className="w-5 h-5" />
                                    </div>
                                    <div>
                                        <p className="text-xs text-gray-500 font-bold uppercase">Selection</p>
                                        <p className="text-sm font-bold text-gray-900 whitespace-nowrap">
                                            {cartRooms} Room{cartRooms > 1 ? 's' : ''} Selected
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div className="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end">
                                <div className="text-right">
                                    <p className="text-xs text-gray-500 font-bold uppercase">Total (Excl. Tax)</p>
                                    <p className="text-2xl font-serif font-bold text-gray-900">₹{formatCurrency(cartTotal)}</p>
                                </div>
                                <button
                                    onClick={handleProceed}
                                    className="bg-gray-900 hover:bg-black text-white text-sm font-bold py-3 px-8 rounded-lg shadow-lg transition flex items-center gap-2"
                                >
                                    Proceed <ArrowRight className="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                )}

                {/* Room Details Drawer */}
                {drawerRoom && (
                    <div className="fixed inset-0 z-[60] overflow-hidden">
                        <div className="absolute inset-0 bg-gray-900/70 backdrop-blur-sm" onClick={() => setDrawerRoom(null)} />
                        <div className="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl overflow-y-auto">
                            {/* Drawer Header */}
                            <div className="relative h-72">
                                <img
                                    src={(drawerRoom.gallery_images || [drawerRoom.image_url])[drawerSlide] || drawerRoom.image_url}
                                    alt={drawerRoom.name}
                                    className="w-full h-full object-cover"
                                />
                                <div className="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/80 to-transparent" />
                                <button
                                    onClick={() => setDrawerRoom(null)}
                                    className="absolute top-4 right-4 rounded-full bg-white/20 backdrop-blur p-2 text-white hover:bg-white hover:text-black transition"
                                >
                                    <X className="w-5 h-5" />
                                </button>
                                <div className="absolute bottom-4 left-6 text-white">
                                    <h2 className="text-3xl font-serif font-bold mb-1">{drawerRoom.name}</h2>
                                    <span className="text-xl font-bold text-amber-400">₹{formatCurrency(drawerRoom.price)} / night</span>
                                </div>
                                {/* Carousel arrows */}
                                {(drawerRoom.gallery_images?.length || 0) > 1 && (
                                    <>
                                        <button
                                            onClick={() => setDrawerSlide((prev) => (prev - 1 + drawerRoom.gallery_images.length) % drawerRoom.gallery_images.length)}
                                            className="absolute left-4 top-1/2 -translate-y-1/2 bg-white/30 hover:bg-white/50 text-white p-2 rounded-full backdrop-blur-sm transition"
                                        >
                                            <ChevronLeft className="w-6 h-6" />
                                        </button>
                                        <button
                                            onClick={() => setDrawerSlide((prev) => (prev + 1) % drawerRoom.gallery_images.length)}
                                            className="absolute right-4 top-1/2 -translate-y-1/2 bg-white/30 hover:bg-white/50 text-white p-2 rounded-full backdrop-blur-sm transition"
                                        >
                                            <ChevronRight className="w-6 h-6" />
                                        </button>
                                    </>
                                )}
                            </div>

                            {/* Content */}
                            <div className="p-6 md:p-8 space-y-8">
                                <div className="grid grid-cols-4 gap-3">
                                    {[
                                        { icon: Bed, label: 'King' },
                                        { icon: Users, label: '4 Pax' },
                                        { icon: Eye, label: 'View' },
                                        { icon: Wind, label: 'AC' },
                                    ].map(({ icon: Icon, label }) => (
                                        <div key={label} className="flex flex-col items-center justify-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                                            <Icon className="w-5 h-5 text-gray-900 mb-2" />
                                            <span className="text-xs uppercase font-bold text-gray-500">{label}</span>
                                        </div>
                                    ))}
                                </div>

                                <div>
                                    <h3 className="font-bold text-xs uppercase tracking-widest text-gray-400 mb-4">Amenities</h3>
                                    <div className="flex flex-wrap gap-2">
                                        {drawerRoom.amenities?.map((amenity, idx) => (
                                            <span key={idx} className="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                                                {amenity}
                                            </span>
                                        ))}
                                    </div>
                                </div>

                                <div>
                                    <h3 className="font-bold text-xs uppercase tracking-widest text-gray-400 mb-3">Description</h3>
                                    <p className="text-gray-600 text-sm leading-relaxed">{drawerRoom.description}</p>
                                </div>

                                <div className="pt-6 border-t border-gray-100">
                                    <button
                                        onClick={() => setDrawerRoom(null)}
                                        className="w-full bg-gray-900 text-white font-bold py-4 rounded-xl hover:bg-black transition shadow-lg"
                                    >
                                        Close Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}

// Full-page layout (no admin sidebar)
HouseboatBooking.layout = (page) => page;
