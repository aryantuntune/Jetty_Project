<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Supriya Houseboat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <div class="max-w-4xl mx-auto px-4 py-12">
        <div class="mb-8">
            <a href="{{ route('houseboat.index') }}"
                class="flex items-center text-sm text-gray-500 hover:text-gray-900">
                <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i> Back to Rooms
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mt-4">Confirm Your Booking</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Booking Form -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h2 class="text-xl font-bold mb-6">Guest Details</h2>
                    <form action="{{ route('houseboat.book') }}" method="POST">
                        @csrf
                        <!-- Pass Cart Data -->
                        <input type="hidden" name="cart_data" value="{{ json_encode($cartItems->map(function ($item) {
    return ['room_id' => $item->room->id, 'qty' => $item->qty];
})) }}">

                        <input type="hidden" name="check_in" value="{{ $checkIn }}">
                        <input type="hidden" name="check_out" value="{{ $checkOut }}">
                        <input type="hidden" name="total_amount" value="{{ $grandTotal }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                                <input type="text" name="customer_name" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-black focus:border-black outline-none transition"
                                    placeholder="John Doe">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" name="customer_phone" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-black focus:border-black outline-none transition"
                                    placeholder="+91 98765 43210">
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="customer_email" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-black focus:border-black outline-none transition"
                                placeholder="john@example.com">
                        </div>

                        <button type="submit"
                            class="w-full bg-black text-white font-bold py-4 rounded-xl hover:bg-gray-800 transition shadow-lg flex items-center justify-center gap-2">
                            <span>Confirm & Pay ₹{{ number_format($grandTotal) }}</span>
                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </button>
                        <p class="text-xs text-gray-500 text-center mt-4 flex items-center justify-center gap-1">
                            <i data-lucide="lock" class="w-3 h-3"></i> Secure Booking
                        </p>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-8">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900">Order Summary</h3>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ \Carbon\Carbon::parse($checkIn)->format('M d') }} -
                            {{ \Carbon\Carbon::parse($checkOut)->format('M d, Y') }}
                            <span class="mx-1">•</span> {{ $nights }} Night(s)
                        </div>
                    </div>

                    <div class="p-6">
                        @foreach($cartItems as $item)
                            <div class="flex gap-4 mb-4 pb-4 border-b border-gray-50 last:border-0 last:pb-0 last:mb-0">
                                <img src="{{ asset($item->room->image_url) }}" alt="{{ $item->room->name }}"
                                    class="w-16 h-16 rounded-lg object-cover bg-gray-100">
                                <div class="flex-1">
                                    <h4 class="font-bold text-sm text-gray-900 line-clamp-1">{{ $item->room->name }}</h4>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-xs text-gray-500">{{ $item->qty }} Room(s)</span>
                                        <span class="font-medium text-sm">₹{{ number_format($item->total_price) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="border-t border-dashed border-gray-200 mt-6 pt-4">
                            <div class="flex justify-between items-end">
                                <span class="font-bold text-gray-900 text-lg">Total</span>
                                <span class="font-bold text-gray-900 text-2xl">₹{{ number_format($grandTotal) }}</span>
                            </div>
                            <p class="text-[10px] text-gray-400 text-right mt-1">Includes all taxes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>