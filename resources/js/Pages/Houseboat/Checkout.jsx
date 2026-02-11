import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import {
    ChevronLeft,
    ArrowRight,
    Lock,
    User,
    Phone,
    Mail,
} from 'lucide-react';

export default function HouseboatCheckout({ cartItems, checkIn, checkOut, nights, grandTotal, errors }) {
    const [formData, setFormData] = useState({
        customer_name: '',
        customer_phone: '',
        customer_email: '',
    });
    const [loading, setLoading] = useState(false);

    const handleSubmit = (e) => {
        e.preventDefault();
        setLoading(true);

        router.post(route('houseboat.book'), {
            ...formData,
            cart_data: JSON.stringify(cartItems?.map(item => ({
                room_id: item.room?.id,
                qty: item.qty,
            }))),
            check_in: checkIn,
            check_out: checkOut,
            total_amount: grandTotal,
        }, {
            onFinish: () => setLoading(false),
        });
    };

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            minimumFractionDigits: 0,
        }).format(amount);
    };

    const formatDate = (dateString) => {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-IN', { month: 'short', day: 'numeric', year: 'numeric' });
    };

    return (
        <>
            <Head>
                <title>Checkout - Supriya Houseboat</title>
            </Head>

            <div className="min-h-screen bg-gray-50">
                <div className="max-w-4xl mx-auto px-4 py-12">
                    <div className="mb-8">
                        <Link
                            href={route('houseboat.booking')}
                            className="inline-flex items-center text-sm text-gray-500 hover:text-gray-900 transition-colors"
                        >
                            <ChevronLeft className="w-4 h-4 mr-1" />
                            Back to Rooms
                        </Link>
                        <h1 className="text-3xl font-bold text-gray-900 mt-4">Confirm Your Booking</h1>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {/* Booking Form */}
                        <div className="md:col-span-2">
                            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                                <h2 className="text-xl font-bold mb-6">Guest Details</h2>
                                <form onSubmit={handleSubmit}>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                        <div>
                                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                                Full Name
                                            </label>
                                            <div className="relative">
                                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                    <User className="w-4 h-4 text-gray-400" />
                                                </div>
                                                <input
                                                    type="text"
                                                    value={formData.customer_name}
                                                    onChange={(e) => setFormData({ ...formData, customer_name: e.target.value })}
                                                    required
                                                    className="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-black focus:border-black outline-none transition"
                                                    placeholder="John Doe"
                                                />
                                            </div>
                                            {errors?.customer_name && (
                                                <p className="mt-1 text-sm text-red-600">{errors.customer_name}</p>
                                            )}
                                        </div>
                                        <div>
                                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                                Phone Number
                                            </label>
                                            <div className="relative">
                                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                    <Phone className="w-4 h-4 text-gray-400" />
                                                </div>
                                                <input
                                                    type="tel"
                                                    value={formData.customer_phone}
                                                    onChange={(e) => setFormData({ ...formData, customer_phone: e.target.value })}
                                                    required
                                                    className="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-black focus:border-black outline-none transition"
                                                    placeholder="+91 98765 43210"
                                                />
                                            </div>
                                            {errors?.customer_phone && (
                                                <p className="mt-1 text-sm text-red-600">{errors.customer_phone}</p>
                                            )}
                                        </div>
                                    </div>

                                    <div className="mb-8">
                                        <label className="block text-sm font-semibold text-gray-700 mb-2">
                                            Email Address
                                        </label>
                                        <div className="relative">
                                            <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <Mail className="w-4 h-4 text-gray-400" />
                                            </div>
                                            <input
                                                type="email"
                                                value={formData.customer_email}
                                                onChange={(e) => setFormData({ ...formData, customer_email: e.target.value })}
                                                required
                                                className="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-black focus:border-black outline-none transition"
                                                placeholder="john@example.com"
                                            />
                                        </div>
                                        {errors?.customer_email && (
                                            <p className="mt-1 text-sm text-red-600">{errors.customer_email}</p>
                                        )}
                                    </div>

                                    <button
                                        type="submit"
                                        disabled={loading}
                                        className="w-full bg-black text-white font-bold py-4 rounded-xl hover:bg-gray-800 transition shadow-lg flex items-center justify-center gap-2 disabled:opacity-70"
                                    >
                                        {loading ? (
                                            <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                                        ) : (
                                            <>
                                                <span>Confirm & Pay {formatCurrency(grandTotal)}</span>
                                                <ArrowRight className="w-5 h-5" />
                                            </>
                                        )}
                                    </button>
                                    <p className="text-xs text-gray-500 text-center mt-4 flex items-center justify-center gap-1">
                                        <Lock className="w-3 h-3" /> Secure Booking
                                    </p>
                                </form>
                            </div>
                        </div>

                        {/* Order Summary */}
                        <div className="md:col-span-1">
                            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-8">
                                <div className="bg-gray-50 px-6 py-4 border-b border-gray-100">
                                    <h3 className="font-bold text-gray-900">Order Summary</h3>
                                    <div className="text-xs text-gray-500 mt-1">
                                        {formatDate(checkIn)} - {formatDate(checkOut)}
                                        <span className="mx-1">•</span> {nights} Night(s)
                                    </div>
                                </div>

                                <div className="p-6">
                                    {cartItems?.map((item, index) => (
                                        <div
                                            key={index}
                                            className={`flex gap-4 mb-4 pb-4 ${
                                                index < cartItems.length - 1 ? 'border-b border-gray-50' : ''
                                            }`}
                                        >
                                            <img
                                                src={item.room?.image_url}
                                                alt={item.room?.name}
                                                className="w-16 h-16 rounded-lg object-cover bg-gray-100"
                                            />
                                            <div className="flex-1">
                                                <h4 className="font-bold text-sm text-gray-900 line-clamp-1">
                                                    {item.room?.name}
                                                </h4>
                                                <div className="flex justify-between items-center mt-1">
                                                    <span className="text-xs text-gray-500">{item.qty} Room(s)</span>
                                                    <span className="font-medium text-sm">
                                                        {formatCurrency(item.total_price)}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    ))}

                                    <div className="border-t border-dashed border-gray-200 mt-6 pt-4">
                                        <div className="flex justify-between items-end">
                                            <span className="font-bold text-gray-900 text-lg">Total</span>
                                            <span className="font-bold text-gray-900 text-2xl">
                                                {formatCurrency(grandTotal)}
                                            </span>
                                        </div>
                                        <p className="text-xs text-gray-400 text-right mt-1">Includes all taxes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

// Full-page layout (no admin sidebar)
HouseboatCheckout.layout = (page) => page;
