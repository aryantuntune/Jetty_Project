import { useState } from 'react';
import { Card, CardHeader, CardTitle, CardContent, Button, Input } from '@/components/ui';
import { Ship, Bed, Users, Calendar, Coffee, Wifi, Tv, Plus, Minus, ArrowRight } from 'lucide-react';
import { formatCurrency } from '@/lib/utils';
import { toast } from 'sonner';

interface Room {
    id: number;
    name: string;
    description: string;
    capacity: number;
    price: number;
    amenities: string[];
    image: string;
}

export function HouseboatBooking() {
    const [checkIn, setCheckIn] = useState('');
    const [checkOut, setCheckOut] = useState('');
    const [guests, setGuests] = useState(2);
    const [selectedRooms, setSelectedRooms] = useState<Record<number, number>>({});

    const rooms: Room[] = [
        {
            id: 1,
            name: 'Deluxe AC Room',
            description: 'Comfortable AC room with river view and attached bathroom',
            capacity: 2,
            price: 3500,
            amenities: ['AC', 'Attached Bathroom', 'River View', 'TV'],
            image: '/images/houseboat/deluxe.jpg',
        },
        {
            id: 2,
            name: 'Premium Suite',
            description: 'Spacious suite with balcony overlooking the backwaters',
            capacity: 4,
            price: 5500,
            amenities: ['AC', 'Balcony', 'Mini Bar', 'Attached Bathroom', 'TV', 'WiFi'],
            image: '/images/houseboat/premium.jpg',
        },
        {
            id: 3,
            name: 'Family Room',
            description: 'Large room perfect for families with children',
            capacity: 6,
            price: 7500,
            amenities: ['AC', 'Two Beds', 'Attached Bathroom', 'TV', 'Extra Space'],
            image: '/images/houseboat/family.jpg',
        },
    ];

    const updateRoomQty = (roomId: number, delta: number) => {
        setSelectedRooms(prev => {
            const current = prev[roomId] || 0;
            const newQty = Math.max(0, Math.min(5, current + delta));
            if (newQty === 0) {
                const { [roomId]: _, ...rest } = prev;
                return rest;
            }
            return { ...prev, [roomId]: newQty };
        });
    };

    const getRoomQty = (roomId: number) => selectedRooms[roomId] || 0;

    const totalAmount = Object.entries(selectedRooms).reduce((sum, [roomId, qty]) => {
        const room = rooms.find(r => r.id === parseInt(roomId));
        return sum + (room?.price || 0) * qty;
    }, 0);

    const handleBooking = () => {
        if (!checkIn || !checkOut) {
            toast.error('Please select check-in and check-out dates');
            return;
        }
        if (Object.keys(selectedRooms).length === 0) {
            toast.error('Please select at least one room');
            return;
        }
        toast.success('Booking request submitted! We will contact you shortly.');
    };

    return (
        <div className="py-12">
            <div className="container mx-auto px-4">
                {/* Hero */}
                <div className="bg-gradient-to-r from-teal-600 to-teal-700 rounded-2xl p-8 text-white mb-8">
                    <div className="max-w-2xl">
                        <span className="inline-block px-3 py-1 bg-white/20 rounded-full text-sm mb-4">
                            🏠 Houseboat Experience
                        </span>
                        <h1 className="text-3xl md:text-4xl font-bold mb-4">
                            Stay on the Beautiful Backwaters
                        </h1>
                        <p className="text-teal-100 mb-6">
                            Experience the serene backwaters of Maharashtra with our luxury houseboat stays.
                            Wake up to stunning views and enjoy authentic coastal cuisine.
                        </p>
                    </div>
                </div>

                <div className="grid lg:grid-cols-3 gap-8">
                    {/* Rooms */}
                    <div className="lg:col-span-2 space-y-6">
                        <h2 className="text-2xl font-bold">Available Rooms</h2>

                        {rooms.map((room) => (
                            <Card key={room.id} className="overflow-hidden hover:shadow-lg transition-shadow">
                                <div className="flex flex-col md:flex-row">
                                    {/* Room Image */}
                                    <div className="md:w-48 h-48 bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center">
                                        <Bed className="w-16 h-16 text-white/50" />
                                    </div>

                                    {/* Room Info */}
                                    <div className="flex-1 p-5">
                                        <div className="flex justify-between items-start mb-3">
                                            <div>
                                                <h3 className="text-xl font-bold">{room.name}</h3>
                                                <p className="text-gray-600 text-sm">{room.description}</p>
                                            </div>
                                            <div className="text-right">
                                                <div className="text-2xl font-bold text-teal-600">
                                                    {formatCurrency(room.price)}
                                                </div>
                                                <div className="text-sm text-gray-500">per night</div>
                                            </div>
                                        </div>

                                        <div className="flex items-center gap-4 mb-4 text-sm text-gray-600">
                                            <span className="flex items-center gap-1">
                                                <Users className="w-4 h-4" />
                                                Up to {room.capacity} guests
                                            </span>
                                        </div>

                                        <div className="flex flex-wrap gap-2 mb-4">
                                            {room.amenities.map((amenity) => (
                                                <span
                                                    key={amenity}
                                                    className="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs"
                                                >
                                                    {amenity}
                                                </span>
                                            ))}
                                        </div>

                                        <div className="flex items-center gap-3">
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                onClick={() => updateRoomQty(room.id, -1)}
                                                disabled={getRoomQty(room.id) === 0}
                                            >
                                                <Minus className="w-4 h-4" />
                                            </Button>
                                            <span className="w-8 text-center font-bold">{getRoomQty(room.id)}</span>
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                onClick={() => updateRoomQty(room.id, 1)}
                                            >
                                                <Plus className="w-4 h-4" />
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </Card>
                        ))}
                    </div>

                    {/* Booking Sidebar */}
                    <div className="space-y-6">
                        <Card className="sticky top-24">
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Calendar className="w-5 h-5" />
                                    Your Stay
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div>
                                    <label className="block text-sm font-medium mb-2">Check-in Date</label>
                                    <Input
                                        type="date"
                                        value={checkIn}
                                        onChange={(e) => setCheckIn(e.target.value)}
                                        min={new Date().toISOString().split('T')[0]}
                                    />
                                </div>

                                <div>
                                    <label className="block text-sm font-medium mb-2">Check-out Date</label>
                                    <Input
                                        type="date"
                                        value={checkOut}
                                        onChange={(e) => setCheckOut(e.target.value)}
                                        min={checkIn || new Date().toISOString().split('T')[0]}
                                    />
                                </div>

                                <div>
                                    <label className="block text-sm font-medium mb-2">Guests</label>
                                    <select
                                        value={guests}
                                        onChange={(e) => setGuests(parseInt(e.target.value))}
                                        className="w-full px-3 py-2 border border-gray-200 rounded-lg"
                                    >
                                        {[1, 2, 3, 4, 5, 6, 7, 8, 10].map(n => (
                                            <option key={n} value={n}>{n} Guest{n > 1 ? 's' : ''}</option>
                                        ))}
                                    </select>
                                </div>

                                {Object.keys(selectedRooms).length > 0 && (
                                    <div className="pt-4 border-t">
                                        <h4 className="font-medium mb-2">Selected Rooms</h4>
                                        {Object.entries(selectedRooms).map(([roomId, qty]) => {
                                            const room = rooms.find(r => r.id === parseInt(roomId));
                                            return room ? (
                                                <div key={roomId} className="flex justify-between text-sm py-1">
                                                    <span>{room.name} × {qty}</span>
                                                    <span>{formatCurrency(room.price * qty)}</span>
                                                </div>
                                            ) : null;
                                        })}
                                    </div>
                                )}

                                <div className="pt-4 border-t">
                                    <div className="flex justify-between items-center mb-4">
                                        <span className="font-medium">Total Amount</span>
                                        <span className="text-2xl font-bold text-teal-600">
                                            {formatCurrency(totalAmount)}
                                        </span>
                                    </div>

                                    <Button
                                        className="w-full bg-teal-600 hover:bg-teal-700"
                                        size="lg"
                                        onClick={handleBooking}
                                        disabled={totalAmount === 0}
                                    >
                                        Request Booking
                                        <ArrowRight className="w-4 h-4 ml-2" />
                                    </Button>

                                    <p className="text-xs text-gray-500 mt-3 text-center">
                                        Our team will contact you to confirm your booking
                                    </p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                {/* Amenities */}
                <div className="mt-12">
                    <h2 className="text-2xl font-bold mb-6 text-center">Houseboat Amenities</h2>
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        {[
                            { icon: Wifi, label: 'Free WiFi' },
                            { icon: Coffee, label: 'Breakfast Included' },
                            { icon: Tv, label: 'Entertainment' },
                            { icon: Ship, label: 'Cruise Experience' },
                        ].map((amenity) => {
                            const Icon = amenity.icon;
                            return (
                                <Card key={amenity.label} className="p-4 text-center">
                                    <Icon className="w-8 h-8 text-teal-600 mx-auto mb-2" />
                                    <span className="font-medium">{amenity.label}</span>
                                </Card>
                            );
                        })}
                    </div>
                </div>
            </div>
        </div>
    );
}
