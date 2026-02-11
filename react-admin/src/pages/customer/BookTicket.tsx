import { useState, useMemo } from 'react';
import { useNavigate } from 'react-router-dom';
import { useQuery, useMutation } from '@tanstack/react-query';
import { Card, CardHeader, CardTitle, CardContent, Button, Input, Select, Badge } from '@/components/ui';
import { useCustomerAuthStore } from '@/store/customerAuthStore';
import { Ship, MapPin, Calendar, Clock, Users, Car, Plus, Minus, CreditCard, ArrowRight, Check } from 'lucide-react';
import { toast } from 'sonner';
import { formatCurrency } from '@/lib/utils';
import apiClient from '@/lib/axios';

interface BookingItem {
    item_id: number;
    item_name: string;
    qty: number;
    rate: number;
    levy: number;
    amount: number;
}

export function BookTicket() {
    const navigate = useNavigate();
    const { customer, isAuthenticated } = useCustomerAuthStore();

    // Form state
    const [step, setStep] = useState(1);
    const [fromBranch, setFromBranch] = useState('');
    const [toBranch, setToBranch] = useState('');
    const [bookingDate, setBookingDate] = useState(getTomorrowDate());
    const [departureTime, setDepartureTime] = useState('');
    const [items, setItems] = useState<BookingItem[]>([]);
    const [guestDetails, setGuestDetails] = useState({
        name: customer?.first_name ? `${customer.first_name} ${customer.last_name}` : '',
        email: customer?.email || '',
        phone: customer?.mobile || '',
    });

    function getTomorrowDate() {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        return tomorrow.toISOString().split('T')[0];
    }

    // Fetch branches
    const { data: branches = [] } = useQuery({
        queryKey: ['branches-booking'],
        queryFn: async () => {
            const res = await apiClient.get('/api/admin/branches');
            return res.data.data || [];
        },
    });

    // Fetch schedules for selected route
    const { data: schedules = [] } = useQuery({
        queryKey: ['schedules-booking', fromBranch],
        queryFn: async () => {
            if (!fromBranch) return [];
            const res = await apiClient.get('/api/admin/schedules', {
                params: { branch_id: fromBranch }
            });
            return res.data.data || [];
        },
        enabled: !!fromBranch,
    });

    // Fetch items/rates
    const { data: availableItems = [] } = useQuery({
        queryKey: ['items-booking', fromBranch],
        queryFn: async () => {
            const res = await apiClient.get('/api/admin/rates', {
                params: { branch_id: fromBranch }
            });
            return res.data.data || [];
        },
        enabled: !!fromBranch,
    });

    // Calculate totals
    const subtotal = useMemo(() => {
        return items.reduce((sum, item) => sum + item.amount, 0);
    }, [items]);

    // Add/update item quantity
    const updateItemQty = (itemId: number, itemName: string, rate: number, levy: number, delta: number) => {
        setItems(prev => {
            const existing = prev.find(i => i.item_id === itemId);
            if (existing) {
                const newQty = Math.max(0, existing.qty + delta);
                if (newQty === 0) {
                    return prev.filter(i => i.item_id !== itemId);
                }
                return prev.map(i =>
                    i.item_id === itemId
                        ? { ...i, qty: newQty, amount: newQty * (rate + levy) }
                        : i
                );
            } else if (delta > 0) {
                return [...prev, {
                    item_id: itemId,
                    item_name: itemName,
                    qty: 1,
                    rate,
                    levy,
                    amount: rate + levy,
                }];
            }
            return prev;
        });
    };

    const getItemQty = (itemId: number) => {
        return items.find(i => i.item_id === itemId)?.qty || 0;
    };

    // Submit booking
    const bookingMutation = useMutation({
        mutationFn: async (data: any) => {
            const res = await apiClient.post('/api/bookings', data);
            return res.data;
        },
        onSuccess: (data) => {
            if (data.success) {
                toast.success('Booking confirmed! Check your email for details.');
                navigate('/customer/history');
            } else {
                toast.error(data.message || 'Booking failed');
            }
        },
        onError: (error: any) => {
            toast.error(error.response?.data?.message || 'Failed to create booking');
        },
    });

    const handleSubmit = () => {
        const bookingData = {
            from_branch: parseInt(fromBranch),
            to_branch: parseInt(toBranch),
            booking_date: bookingDate,
            departure_time: departureTime,
            items,
            customer_name: guestDetails.name,
            customer_email: guestDetails.email,
            customer_phone: guestDetails.phone,
            total_amount: subtotal,
            payment_mode: 'Online',
            booking_source: 'web',
        };
        bookingMutation.mutate(bookingData);
    };

    const canProceedStep1 = fromBranch && toBranch && bookingDate && departureTime;
    const canProceedStep2 = items.length > 0;
    const canProceedStep3 = guestDetails.name && guestDetails.email && guestDetails.phone;

    return (
        <div className="max-w-4xl mx-auto">
            {/* Progress Steps */}
            <div className="flex items-center justify-center mb-8">
                {[1, 2, 3, 4].map((s) => (
                    <div key={s} className="flex items-center">
                        <div className={`w-10 h-10 rounded-full flex items-center justify-center font-bold ${step >= s ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500'
                            }`}>
                            {step > s ? <Check className="w-5 h-5" /> : s}
                        </div>
                        {s < 4 && (
                            <div className={`w-16 h-1 ${step > s ? 'bg-blue-600' : 'bg-gray-200'}`}></div>
                        )}
                    </div>
                ))}
            </div>

            {/* Step 1: Route & Schedule */}
            {step === 1 && (
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <MapPin className="w-5 h-5 text-blue-600" />
                            Select Route & Schedule
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        <div className="grid md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-medium mb-2">From</label>
                                <Select
                                    value={fromBranch}
                                    onChange={(e) => {
                                        setFromBranch(e.target.value);
                                        setDepartureTime('');
                                    }}
                                >
                                    <option value="">Select departure</option>
                                    {branches.map((b: any) => (
                                        <option key={b.id} value={b.id}>{b.branch_name}</option>
                                    ))}
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-2">To</label>
                                <Select
                                    value={toBranch}
                                    onChange={(e) => setToBranch(e.target.value)}
                                >
                                    <option value="">Select destination</option>
                                    {branches
                                        .filter((b: any) => b.id.toString() !== fromBranch)
                                        .map((b: any) => (
                                            <option key={b.id} value={b.id}>{b.branch_name}</option>
                                        ))}
                                </Select>
                            </div>
                        </div>

                        <div className="grid md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-medium mb-2">
                                    <Calendar className="w-4 h-4 inline mr-1" />
                                    Booking Date
                                </label>
                                <Input
                                    type="date"
                                    value={bookingDate}
                                    onChange={(e) => setBookingDate(e.target.value)}
                                    min={getTomorrowDate()}
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-2">
                                    <Clock className="w-4 h-4 inline mr-1" />
                                    Departure Time
                                </label>
                                <Select
                                    value={departureTime}
                                    onChange={(e) => setDepartureTime(e.target.value)}
                                    disabled={!fromBranch}
                                >
                                    <option value="">Select time</option>
                                    {schedules.map((s: any) => (
                                        <option key={s.id} value={s.schedule_time}>
                                            {s.schedule_time}
                                        </option>
                                    ))}
                                </Select>
                            </div>
                        </div>

                        <div className="flex justify-end">
                            <Button
                                onClick={() => setStep(2)}
                                disabled={!canProceedStep1}
                                className="bg-blue-600 hover:bg-blue-700"
                            >
                                Continue
                                <ArrowRight className="w-4 h-4 ml-2" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* Step 2: Select Items */}
            {step === 2 && (
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Users className="w-5 h-5 text-blue-600" />
                            Select Passengers & Vehicles
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        {availableItems.length === 0 ? (
                            <div className="text-center py-8 text-gray-500">
                                No items available for this route
                            </div>
                        ) : (
                            <div className="space-y-3">
                                {availableItems.map((item: any) => (
                                    <div
                                        key={item.id}
                                        className="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50"
                                    >
                                        <div>
                                            <div className="font-medium">{item.item_name || item.name}</div>
                                            <div className="text-sm text-gray-600">
                                                {formatCurrency(item.rate)} + {formatCurrency(item.levy)} levy
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-3">
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                onClick={() => updateItemQty(item.id, item.item_name || item.name, item.rate, item.levy, -1)}
                                                disabled={getItemQty(item.id) === 0}
                                            >
                                                <Minus className="w-4 h-4" />
                                            </Button>
                                            <span className="w-8 text-center font-bold">{getItemQty(item.id)}</span>
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                onClick={() => updateItemQty(item.id, item.item_name || item.name, item.rate, item.levy, 1)}
                                            >
                                                <Plus className="w-4 h-4" />
                                            </Button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}

                        {items.length > 0 && (
                            <div className="mt-6 p-4 bg-blue-50 rounded-lg">
                                <div className="flex justify-between items-center">
                                    <span className="font-medium">Total:</span>
                                    <span className="text-2xl font-bold text-blue-600">{formatCurrency(subtotal)}</span>
                                </div>
                            </div>
                        )}

                        <div className="flex justify-between pt-4">
                            <Button variant="outline" onClick={() => setStep(1)}>Back</Button>
                            <Button
                                onClick={() => setStep(3)}
                                disabled={!canProceedStep2}
                                className="bg-blue-600 hover:bg-blue-700"
                            >
                                Continue
                                <ArrowRight className="w-4 h-4 ml-2" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* Step 3: Contact Details */}
            {step === 3 && (
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Users className="w-5 h-5 text-blue-600" />
                            Contact Details
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        <div>
                            <label className="block text-sm font-medium mb-2">Full Name *</label>
                            <Input
                                value={guestDetails.name}
                                onChange={(e) => setGuestDetails({ ...guestDetails, name: e.target.value })}
                                placeholder="John Doe"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium mb-2">Email *</label>
                            <Input
                                type="email"
                                value={guestDetails.email}
                                onChange={(e) => setGuestDetails({ ...guestDetails, email: e.target.value })}
                                placeholder="your@email.com"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium mb-2">Phone *</label>
                            <Input
                                type="tel"
                                value={guestDetails.phone}
                                onChange={(e) => setGuestDetails({ ...guestDetails, phone: e.target.value })}
                                placeholder="+91 98765 43210"
                            />
                        </div>

                        <div className="flex justify-between pt-4">
                            <Button variant="outline" onClick={() => setStep(2)}>Back</Button>
                            <Button
                                onClick={() => setStep(4)}
                                disabled={!canProceedStep3}
                                className="bg-blue-600 hover:bg-blue-700"
                            >
                                Review Booking
                                <ArrowRight className="w-4 h-4 ml-2" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* Step 4: Review & Pay */}
            {step === 4 && (
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <CreditCard className="w-5 h-5 text-blue-600" />
                            Review & Confirm
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        {/* Summary */}
                        <div className="grid md:grid-cols-2 gap-6">
                            <div className="space-y-4">
                                <h3 className="font-bold">Journey Details</h3>
                                <div className="space-y-2 text-sm">
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">From:</span>
                                        <span className="font-medium">
                                            {branches.find((b: any) => b.id.toString() === fromBranch)?.branch_name}
                                        </span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">To:</span>
                                        <span className="font-medium">
                                            {branches.find((b: any) => b.id.toString() === toBranch)?.branch_name}
                                        </span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">Date:</span>
                                        <span className="font-medium">{bookingDate}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">Time:</span>
                                        <span className="font-medium">{departureTime}</span>
                                    </div>
                                </div>
                            </div>

                            <div className="space-y-4">
                                <h3 className="font-bold">Contact</h3>
                                <div className="space-y-2 text-sm">
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">Name:</span>
                                        <span className="font-medium">{guestDetails.name}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">Email:</span>
                                        <span className="font-medium">{guestDetails.email}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">Phone:</span>
                                        <span className="font-medium">{guestDetails.phone}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Items */}
                        <div>
                            <h3 className="font-bold mb-3">Tickets</h3>
                            <div className="border rounded-lg divide-y">
                                {items.map((item) => (
                                    <div key={item.item_id} className="flex justify-between p-3">
                                        <span>{item.item_name} × {item.qty}</span>
                                        <span className="font-medium">{formatCurrency(item.amount)}</span>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Total */}
                        <div className="p-4 bg-green-50 rounded-lg">
                            <div className="flex justify-between items-center">
                                <span className="text-lg font-medium">Total Amount</span>
                                <span className="text-3xl font-bold text-green-600">{formatCurrency(subtotal)}</span>
                            </div>
                        </div>

                        <div className="flex justify-between pt-4">
                            <Button variant="outline" onClick={() => setStep(3)}>Back</Button>
                            <Button
                                onClick={handleSubmit}
                                disabled={bookingMutation.isPending}
                                className="bg-green-600 hover:bg-green-700"
                                size="lg"
                            >
                                {bookingMutation.isPending ? 'Processing...' : 'Confirm & Pay'}
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            )}
        </div>
    );
}
