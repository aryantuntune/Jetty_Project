<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supriya Houseboat | Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap"
        rel="stylesheet">
    <style>
        /* Force Font Family */
        body {
            font-family: 'Manrope', sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Playfair Display', serif;
        }

        /* Custom Scrollbar for 'App' feel */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c5a47e;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #b08d66;
        }

        .loader {
            border-top-color: transparent;
            -webkit-animation: spin 1s linear infinite;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
        
        /* Ultra-Fidelity: Flatpickr Deep Customization (Gold Theme) */
        .flatpickr-calendar {
            border: none !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important;
            border-radius: 20px !important;
            font-family: 'Manrope', sans-serif !important;
            padding: 16px !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day:hover, .flatpickr-day:focus {
            background: #c5a47e !important;
            border-color: #c5a47e !important;
            color: white !important;
        }
        .flatpickr-day.inRange {
            box-shadow: -5px 0 0 #f3e9de, 5px 0 0 #f3e9de !important;
            background: #f3e9de !important;
            border-color: #f3e9de !important;
            color: #1a1a1a !important;
        }
        .flatpickr-day.today {
            border-bottom: 2px solid #c5a47e !important;
            border-color: transparent !important;
        }
        .flatpickr-day.sold-out {
            background-color: #f9fafb !important;
            color: #d1d5db !important;
            text-decoration: line-through;
            pointer-events: none;
            border: 1px dashed #e5e7eb !important;
        }
        .flatpickr-day.sold-out .price-tag {
            color: #ef4444 !important;
            font-weight: 800;
        }
        
        /* Ultra-Fidelity: Ken Burns Effect */
        @keyframes kenBurns {
            0% { transform: scale(1); }
            100% { transform: scale(1.15); }
        }
        .animate-ken-burns {
            animation: kenBurns 20s ease-out forwards;
        }

        /* Ultra-Fidelity: Glassmorphism */
        .glass-panel {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        .carousel-slide {
            transition: opacity 1.5s ease-in-out;
            opacity: 0;
            position: absolute;
            inset: 0;
        }

        .carousel-slide.active {
            opacity: 1;
        }

        .header-transparent {
            background-color: transparent !important;
            box-shadow: none !important;
        }

        .header-scrolled {
            background-color: white !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }

        /* Flatpickr Custom Customization */
        .flatpickr-day.sold-out {
            background-color: #f3f4f6 !important;
            color: #d1d5db !important;
            text-decoration: line-through;
            pointer-events: none;
            border-color: #e5e7eb !important;
        }

        .flatpickr-day.sold-out .price-tag {
            color: #ef4444 !important;
            /* Red for Sold Out text */
            text-decoration: none !important;
            font-weight: bold;
        }
    </style>
</head>

<body
    class="bg-white text-[#1a1a1a] font-sans antialiased overflow-x-hidden selection:bg-[#c5a47e] selection:text-white">

    <!-- Global App Loader -->
    <div id="global-loader"
        class="fixed inset-0 z-[100] bg-white flex items-center justify-center transition-opacity duration-700">
        <div class="flex flex-col items-center">
            <span
                class="loader block w-12 h-12 border-4 border-[#c5a47e] border-b-transparent rounded-full animate-spin mb-4"></span>
            <span class="text-xs font-bold uppercase tracking-[0.2em] text-gray-400 animate-pulse">Loading
                Experience</span>
        </div>
    </div>

    <!-- Floating "Our Price" Widget -->
    <div class="fixed left-0 top-1/2 -translate-y-1/2 z-50 hidden md:flex flex-col">
        <div
            class="bg-red-600 text-white text-xs font-bold py-3 px-1 writing-mode-vertical rounded-r-lg shadow-lg cursor-pointer hover:bg-red-700 transition">
            <span style="writing-mode: vertical-rl; text-orientation: mixed;">OUR PRICE</span>
        </div>
    </div>

    <!-- Floating WhatsApp Widget -->
    <a href="https://wa.me/919422431371" target="_blank"
        class="fixed bottom-6 right-6 z-50 bg-green-500 text-white p-4 rounded-full shadow-2xl hover:bg-green-600 hover:scale-110 transition duration-300 animate-bounce">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L3 21" />
            <path
                d="M9 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0 .5-.5a.5.5 0 0 0-.5-.5H9a.5.5 0 0 0-.5.5v2.25a.5.5 0 0 0 .5.5z" />
        </svg>
    </a>

    @if(session('success'))
        <div
            class="fixed top-24 left-1/2 -translate-x-1/2 z-50 bg-green-900 text-white px-8 py-4 rounded-xl shadow-2xl flex items-center gap-3 animate-bounce">
            <i data-lucide="check-circle" class="w-6 h-6 text-green-400"></i>
            <div>
                <p class="font-bold">Booking Request Sent!</p>
                <p class="text-xs text-green-200">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Header -->
    <header id="main-header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 bg-transparent">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-24 transition-all duration-300" id="header-container">
                <div class="flex items-center gap-3">
                    <img src="https://d3ki85qs1zca4t.cloudfront.net/bookingEngine/uploads/1763102420667451.jpg"
                        alt="Logo" class="h-12 w-12 rounded-full object-cover border-2 border-white shadow-md">
                    <div id="header-text" class="text-white drop-shadow-md transition-colors duration-300">
                        <h1 class="text-sm font-bold uppercase tracking-widest">Supriya Houseboat</h1>
                        <p class="text-xs opacity-90">Dapoli, Maharashtra</p>
                    </div>
                </div>
                <!-- Nav -->
                <nav class="hidden md:flex gap-8 text-sm font-bold uppercase tracking-wide" id="nav-links">
                    <a href="{{ url('/') }}" class="text-white hover:text-yellow-400 transition drop-shadow-md">Home</a>
                    <a href="#" class="text-yellow-400 border-b-2 border-yellow-400 pb-1 drop-shadow-md">Rooms</a>
                </nav>

                <div class="flex items-center gap-6">
                    <div class="hidden md:flex items-center gap-3 text-sm" id="header-rating">
                        <div class="flex items-center gap-1 text-yellow-400 font-bold drop-shadow-md">4.6 <i
                                data-lucide="star" class="w-4 h-4 fill-current"></i></div>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button onclick="toggleMobileMenu()" class="md:hidden text-white drop-shadow-md focus:outline-none">
                        <i data-lucide="menu" class="w-8 h-8"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Drawer -->
    <div id="mobile-menu" class="fixed inset-0 z-[60] bg-black/50 hidden opacity-0 transition-opacity duration-300">
        <div id="mobile-menu-panel"
            class="absolute right-0 top-0 h-full w-64 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 flex flex-col p-6">
            <div class="flex justify-end mb-8">
                <button onclick="toggleMobileMenu()" class="text-gray-500 hover:text-gray-900">
                    <i data-lucide="x" class="w-8 h-8"></i>
                </button>
            </div>
            <nav class="flex flex-col gap-6 text-lg font-bold text-[#1a1a1a]">
                <a href="{{ url('/') }}" class="hover:text-[#c5a47e] transition">Home</a>
                <a href="#" class="text-[#c5a47e]">Rooms</a>
                <a href="#" class="hover:text-[#c5a47e] transition">Contact</a>
                <div class="border-t border-gray-100 pt-6 mt-2">
                    <p class="text-xs text-gray-400 uppercase tracking-widest mb-2">Book Now</p>
                    <p class="text-sm font-medium">+91 9422431371</p>
                </div>
            </nav>
        </div>
    </div>

    <!-- Hero Section (Carousel) -->
    <section class="relative h-[85vh] bg-gray-900 overflow-hidden">
        <div id="hero-carousel">
            <!-- Slides - Adding Ken Burns class -->
            <div class="carousel-slide active">
                <img src="{{ asset('images/houseboat/hero_banner_1.jpg') }}"
                    class="w-full h-full object-cover animate-ken-burns">
            </div>
            <div class="carousel-slide">
                <img src="{{ asset('images/houseboat/hero_banner_2.jpg') }}"
                    class="w-full h-full object-cover animate-ken-burns">
            </div>
            <div class="carousel-slide">
                <img src="{{ asset('images/houseboat/hero_banner_3.jpg') }}"
                    class="w-full h-full object-cover animate-ken-burns">
            </div>
            <div class="carousel-slide">
                <img src="{{ asset('images/houseboat/about_deck.jpg') }}"
                    class="w-full h-full object-cover animate-ken-burns">
            </div>
        </div>

        <!-- Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-black/30"></div>

        <!-- Content -->
        <div class="absolute inset-0 flex flex-col justify-center items-center text-center text-white px-4 mt-10">
            <span class="text-yellow-400 text-sm font-bold tracking-[0.2em] uppercase mb-4 animate-fade-in-up">Welcome
                To</span>
            <h1 class="text-5xl md:text-7xl font-serif font-bold mb-6 drop-shadow-2xl animate-fade-in-up"
                style="animation-delay: 0.2s">Supriya Houseboat</h1>
            <p class="text-lg md:text-xl text-gray-200 max-w-2xl mb-10 drop-shadow-lg animate-fade-in-up"
                style="animation-delay: 0.4s">Experience the serenity of backwaters with luxury and comfort in the heart
                of Dapoli.</p>
        </div>

        <!-- Arrows -->
        <button onclick="prevSlide()"
            class="absolute left-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition p-2">
            <i data-lucide="chevron-left" class="w-10 h-10"></i>
        </button>
        <button onclick="nextSlide()"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition p-2">
            <i data-lucide="chevron-right" class="w-10 h-10"></i>
        </button>
    </section>

    <!-- Booking Widget -->
    <div class="relative -mt-24 z-40 px-4 mb-20">
        <div
            class="max-w-6xl mx-auto bg-white rounded-xl shadow-2xl p-6 md:p-8 transform hover:-translate-y-1 transition duration-300">
            <form action="{{ route('houseboat.index') }}" method="GET"
                class="flex flex-col lg:flex-row gap-6 items-end relative">

                <!-- Date Selection (Custom Trigger) -->
                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
                    <div class="md:col-span-2 relative">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Check In -
                            Check Out</label>
                        <div id="date-range-trigger"
                            class="bg-gray-50 border border-gray-200 rounded-lg p-3 flex items-center cursor-pointer hover:bg-gray-100 transition">
                            <i data-lucide="calendar" class="w-5 h-5 text-gray-400 mr-3"></i>
                            <div class="flex-1 flex gap-4">
                                <div>
                                    <span class="text-xs text-gray-500 block">Check In</span>
                                    <span id="check-in-display"
                                        class="font-bold text-gray-900">{{ $checkIn ?? date('Y-m-d') }}</span>
                                </div>
                                <div class="border-l border-gray-300 mx-2"></div>
                                <div>
                                    <span class="text-xs text-gray-500 block">Check Out</span>
                                    <span id="check-out-display"
                                        class="font-bold text-gray-900">{{ $checkOut ?? date('Y-m-d', strtotime('+1 day')) }}</span>
                                </div>
                            </div>
                            <input type="text" id="flatpickr-range" name="date_range" class="hidden">
                            <input type="hidden" name="check_in" id="check_in_input"
                                value="{{ $checkIn ?? date('Y-m-d') }}">
                            <input type="hidden" name="check_out" id="check_out_input"
                                value="{{ $checkOut ?? date('Y-m-d', strtotime('+1 day')) }}">
                        </div>
                    </div>

                    <!-- Occupancy Selection (Custom Trigger) -->
                    <div class="relative">
                        <label
                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Guests</label>
                        <div id="occupancy-trigger"
                            class="bg-gray-50 border border-gray-200 rounded-lg p-3 flex items-center cursor-pointer hover:bg-gray-100 transition">
                            <i data-lucide="users" class="w-5 h-5 text-gray-400 mr-3"></i>
                            <div class="flex-1 text-left">
                                <span class="text-xs text-gray-500 block">Occupancy</span>
                                <span id="occupancy-display" class="font-bold text-gray-900 truncate">1 Room, 2
                                    Guests</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                        </div>

                        <!-- Popover -->
                        <div id="occupancy-popover"
                            class="hidden absolute top-full right-0 mt-2 w-72 bg-white rounded-xl shadow-2xl border border-gray-100 p-4 z-50 animate-in fade-in zoom-in-95 duration-200">
                            <!-- Room Row -->
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <p class="font-bold text-sm text-gray-900">Rooms</p>
                                    <p class="text-[10px] text-gray-400">Min 1, Max 5</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="button" onclick="updateCounter('rooms', -1)"
                                        class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:border-gray-300 transition-colors">
                                        <i data-lucide="minus" class="w-3 h-3"></i>
                                    </button>
                                    <span id="count-rooms" class="font-bold text-gray-900 w-4 text-center">1</span>
                                    <button type="button" onclick="updateCounter('rooms', 1)"
                                        class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:border-gray-300 transition-colors">
                                        <i data-lucide="plus" class="w-3 h-3"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Adults Row -->
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <p class="font-bold text-sm text-gray-900">Adults</p>
                                    <p class="text-[10px] text-gray-400">12+ years</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="button" onclick="updateCounter('guests', -1)"
                                        class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:border-gray-300 transition-colors">
                                        <i data-lucide="minus" class="w-3 h-3"></i>
                                    </button>
                                    <span id="count-guests"
                                        class="font-bold text-gray-900 w-4 text-center">{{ $guests ?? 2 }}</span>
                                    <button type="button" onclick="updateCounter('guests', 1)"
                                        class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:border-gray-300 transition-colors">
                                        <i data-lucide="plus" class="w-3 h-3"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Children Row -->
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <p class="font-bold text-sm text-gray-900">Children</p>
                                    <p class="text-[10px] text-gray-400">5-12 years</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="button" onclick="updateCounter('children', -1)"
                                        class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:border-gray-300 transition-colors">
                                        <i data-lucide="minus" class="w-3 h-3"></i>
                                    </button>
                                    <span id="count-children" class="font-bold text-gray-900 w-4 text-center">0</span>
                                    <button type="button" onclick="updateCounter('children', 1)"
                                        class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:border-gray-300 transition-colors">
                                        <i data-lucide="plus" class="w-3 h-3"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="button" onclick="toggleOccupancy()"
                                class="w-full bg-gray-900 text-white text-xs font-bold py-3 rounded-lg hover:bg-black transition">Done</button>

                            <!-- Hidden Inputs -->
                            <input type="hidden" name="rooms" id="rooms_input" value="1">
                            <input type="hidden" name="guests" id="guests_input" value="{{ $guests ?? 2 }}">
                            <input type="hidden" name="children" id="children_input" value="0">
                        </div>
                    </div>
                </div>
                <button type="submit"
                    class="w-full lg:w-auto bg-gray-900 hover:bg-black text-white font-bold py-4 px-10 rounded-lg transition-all shadow-lg hover:shadow-xl active:scale-95 flex items-center justify-center gap-2">
                    <span>Search</span>
                    <i data-lucide="search" class="w-5 h-5"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">

        <!-- Room Cards -->
        <div class="space-y-12">
            @foreach($rooms as $room)
                <div
                    class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 md:flex group border border-gray-100">
                    <!-- Image Side -->
                    <div class="md:w-5/12 relative h-72 md:h-auto overflow-hidden">
                        <img src="{{ asset($room->image_url) }}" alt="{{ $room->name }}"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div
                            class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <button
                                onclick="openRoomDetails('{{ $room->name }}', {{ $room->price }}, {{ json_encode($room->gallery_images ?? [$room->image_url]) }}, '{{ addslashes($room->description) }}', '{{ json_encode($room->amenities) }}')"
                                class="text-white border border-white px-6 py-2 rounded-full font-bold uppercase tracking-widest text-sm hover:bg-white hover:text-black transition">
                                View Details
                            </button>
                        </div>

                        @if($room->total_rooms <= 5)
                            <div
                                class="absolute top-4 right-4 bg-red-600 text-white text-[10px] font-bold px-3 py-1.5 rounded shadow-lg animate-pulse">
                                ⚠️ Only {{ $room->total_rooms }} Rooms Left!
                            </div>
                        @endif
                    </div>

                    <!-- Content Side -->
                    <div class="md:w-7/12 p-8 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <h2
                                    class="text-3xl font-serif font-bold text-gray-900 group-hover:text-blue-600 transition">
                                    {{ $room->name }}
                                </h2>
                                <div
                                    class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded border border-green-200">
                                    AVAILABLE TODAY
                                </div>
                            </div>

                            <!-- Amenities Chips -->
                            <div class="flex flex-wrap gap-3 mb-6">
                                @if($room->amenities)
                                    @foreach($room->amenities as $amenity)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 text-gray-600 text-xs font-semibold rounded-full border border-gray-100">
                                            <i data-lucide="check" class="w-3 h-3 text-blue-500"></i> {{ $amenity }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>

                            <p class="text-gray-500 text-sm leading-relaxed mb-8 line-clamp-3">
                                {{ $room->description }}
                            </p>
                        </div>

                        <div class="flex items-end justify-between border-t border-gray-100 pt-6">
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase mb-1">Total Price</p>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-3xl font-bold text-gray-900">₹{{ number_format($room->price) }}</span>
                                    <span class="text-xs text-gray-500 font-medium">/ night</span>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1">Excludes GST</p>
                            </div>
                            <!-- Room Actions Container -->
                            <div id="room-action-{{ $room->id }}" class="flex flex-col items-end">
                                <!-- Initial Add Button -->
                                <button type="button"
                                    onclick="addToCart({{ $room->id }}, '{{ $room->name }}', {{ $room->price }})"
                                    class="add-btn bg-gradient-to-r from-gray-900 to-black hover:from-blue-600 hover:to-blue-700 text-white px-8 py-3.5 rounded-lg font-bold shadow-lg hover:shadow-blue-500/30 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center gap-2">
                                    Add Room <i data-lucide="plus" class="w-4 h-4"></i>
                                </button>

                                <!-- Stepper (Initially Hidden) -->
                                <div
                                    class="stepper hidden flex items-center gap-4 bg-white border-2 border-blue-600 rounded-lg p-1.5 shadow-lg">
                                    <button type="button" onclick="updateCartItem({{ $room->id }}, -1)"
                                        class="w-8 h-8 rounded bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-700 font-bold transition">-</button>
                                    <span id="qty-{{ $room->id }}"
                                        class="font-bold text-lg text-blue-900 w-6 text-center">1</span>
                                    <button type="button" onclick="updateCartItem({{ $room->id }}, 1)"
                                        class="w-8 h-8 rounded bg-blue-600 hover:bg-blue-700 flex items-center justify-center text-white font-bold transition">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- About Section -->
        <div class="mt-32 mb-20">
            <div class="bg-blue-50 rounded-3xl p-8 md:p-16 grid md:grid-cols-2 gap-16 items-center">
                <div class="order-2 md:order-1">
                    <span class="text-blue-600 font-bold uppercase tracking-widest text-xs mb-3 block">Discover</span>
                    <h3 class="text-4xl font-serif font-bold text-gray-900 mb-6">A Floating Paradise</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Immerse yourself in the tranquility of our premium houseboat. Designed to offer a perfect blend
                        of modern luxury and traditional charm, we ensure your stay is nothing short of magical.
                    </p>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center gap-3 text-gray-700">
                            <div
                                class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-green-500">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </div>
                            <span>Complimentary Authentic Breakfast</span>
                        </li>
                        <li class="flex items-center gap-3 text-gray-700">
                            <div
                                class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-green-500">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </div>
                            <span>Panoramic Deck Views</span>
                        </li>
                        <li class="flex items-center gap-3 text-gray-700">
                            <div
                                class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-green-500">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </div>
                            <span>24/7 On-board Assistance</span>
                        </li>
                    </ul>
                </div>
                <div class="order-1 md:order-2 relative">
                    <img src="{{ asset('images/houseboat/about_deck.jpg') }}"
                        class="rounded-2xl shadow-2xl transform rotate-2 hover:rotate-0 transition duration-500 w-full object-cover h-96">
                    <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-xl shadow-xl flex items-center gap-3">
                        <div class="bg-yellow-100 p-2 rounded-full text-yellow-600">
                            <i data-lucide="award" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase">Rated</p>
                            <p class="font-bold text-gray-900">#1 in Dapoli</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 mt-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 pb-12">
            <div class="grid md:grid-cols-4 gap-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="https://d3ki85qs1zca4t.cloudfront.net/bookingEngine/uploads/1763102420667451.jpg"
                            class="w-12 h-12 rounded-full border border-gray-700">
                        <div>
                            <h4 class="font-bold text-lg">Supriya</h4>
                            <p class="text-xs text-gray-400 uppercase tracking-widest">Houseboat</p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">
                        Experience the finest hospitality on the waters of Dapoli. Book your stay with us today.
                    </p>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-6">Contact</h4>
                    <ul class="space-y-4 text-sm text-gray-400">
                        <li class="flex items-start gap-3">
                            <i data-lucide="map-pin" class="w-5 h-5 text-gray-500 shrink-0"></i>
                            <span>Dapoli, Dist. Ratnagiri, Maharashtra 415712</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i data-lucide="phone" class="w-5 h-5 text-gray-500 shrink-0"></i>
                            <span>+91 9422431371</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i data-lucide="mail" class="w-5 h-5 text-gray-500 shrink-0"></i>
                            <span>booking@supriyahouseboat.com</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-6">Facilities</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li>• Restaurant</li>
                        <li>• Ferry Service</li>
                        <li>• Free Parking</li>
                        <li>• Room Service</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-6">Payment Methods</h4>
                    <div class="flex gap-2 mb-4">
                        <div class="bg-white p-1 rounded w-10 h-6 flex items-center justify-center"><img
                                src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png"
                                class="h-full object-contain"></div>
                        <div class="bg-white p-1 rounded w-10 h-6 flex items-center justify-center"><img
                                src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg"
                                class="h-full object-contain"></div>
                        <div class="bg-white p-1 rounded w-10 h-6 flex items-center justify-center"><img
                                src="https://upload.wikimedia.org/wikipedia/commons/e/e1/UPI-Logo-vector.svg"
                                class="h-full object-contain"></div>
                    </div>
                    <p class="text-xs text-gray-500">
                        <i data-lucide="shield-check" class="w-3 h-3 inline mr-1"></i> Secure Payment Gateway
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-gray-950 py-6 text-center border-t border-gray-800">
            <p class="text-xs text-gray-500">&copy; {{ date('Y') }} Supriya Houseboat (Jetty Services). <span
                    class="hidden md:inline">Powered by <span class="text-blue-500 font-bold">Bookingjini
                        Labs</span></span></p>
        </div>
    </footer>

    <!-- Sticky Bottom Checkout Bar -->
    <div id="sticky-checkout-bar"
        class="fixed bottom-0 left-0 right-0 glass-panel border-t border-gray-200/50 shadow-[0_-5px_30px_rgba(0,0,0,0.08)] transform translate-y-full transition-transform duration-500 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 md:py-3 flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Summary Info -->
            <div class="flex items-center gap-6 w-full md:w-auto overflow-x-auto pb-2 md:pb-0">
                <div class="flex items-center gap-3 shrink-0">
                    <div class="bg-blue-50 p-2 rounded-lg text-blue-600">
                        <i data-lucide="calendar-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase">Dates</p>
                        <p id="bar-date-range" class="text-sm font-bold text-gray-900 whitespace-nowrap">--</p>
                    </div>
                </div>
                <div class="w-px h-8 bg-gray-200 hidden md:block"></div>
                <div class="flex items-center gap-3 shrink-0">
                    <div class="bg-blue-50 p-2 rounded-lg text-blue-600">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase">Guests</p>
                        <p id="bar-guests" class="text-sm font-bold text-gray-900 whitespace-nowrap">--</p>
                    </div>
                </div>
                <div class="w-px h-8 bg-gray-200 hidden md:block"></div>
                <div class="flex items-center gap-3 shrink-0">
                    <div class="bg-blue-50 p-2 rounded-lg text-blue-600">
                        <i data-lucide="home" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase">Selection</p>
                        <p id="bar-room-summary" class="text-sm font-bold text-gray-900 whitespace-nowrap">--</p>
                    </div>
                </div>
            </div>

            <!-- Total & Action -->
            <div class="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end">
                <div class="text-right">
                    <p class="text-[10px] text-gray-500 font-bold uppercase">Total (Excl. Tax)</p>
                    <p id="bar-total-price" class="text-2xl font-serif font-bold text-gray-900">₹0</p>
                </div>

                <form action="{{ route('houseboat.checkout') }}" method="GET">
                    <input type="hidden" name="cart_data" id="cart-data-input">
                    <input type="hidden" name="check_in" id="bar-check-in">
                    <input type="hidden" name="check_out" id="bar-check-out">
                    <button type="submit"
                        class="bg-gray-900 hover:bg-black text-white text-sm font-bold py-3 px-8 rounded-lg shadow-lg hover:shadow-xl transition flex items-center gap-2">
                        Proceed <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Room Details Side Drawer -->
    <div id="room-drawer" class="fixed inset-0 z-[60] overflow-hidden hidden" aria-labelledby="slide-over-title"
        role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <div id="drawer-backdrop" onclick="closeDrawer()"
                class="absolute inset-0 bg-gray-900/70 transition-opacity opacity-0 backdrop-blur-sm"
                aria-hidden="true"></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-0 md:pl-10">
                <div id="drawer-panel"
                    class="pointer-events-auto w-screen max-w-md transform transition ease-in-out duration-500 translate-x-full">
                    <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-2xl">
                        <!-- Drawer Header -->
                        <div class="relative h-72 group">
                            <!-- Carousel Container -->
                            <div id="drawer-carousel" class="w-full h-full relative">
                                <!-- Images injected by JS -->
                            </div>

                            <!-- Navigation Arrows -->
                            <button onclick="changeDrawerSlide(-1)"
                                class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/30 hover:bg-white/50 text-white p-2 rounded-full backdrop-blur-sm transition opacity-0 group-hover:opacity-100 focus:outline-none">
                                <i data-lucide="chevron-left" class="w-6 h-6"></i>
                            </button>
                            <button onclick="changeDrawerSlide(1)"
                                class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/30 hover:bg-white/50 text-white p-2 rounded-full backdrop-blur-sm transition opacity-0 group-hover:opacity-100 focus:outline-none">
                                <i data-lucide="chevron-right" class="w-6 h-6"></i>
                            </button>

                            <div
                                class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/80 to-transparent pointer-events-none">
                            </div>
                            <button type="button" onclick="closeDrawer()"
                                class="absolute top-4 right-4 rounded-full bg-white/20 backdrop-blur p-2 text-white hover:bg-white hover:text-black transition focus:outline-none">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                            <div class="absolute bottom-4 left-6 text-white">
                                <h2 id="drawer-title" class="text-3xl font-serif font-bold mb-1"></h2>
                                <span id="drawer-price" class="text-xl font-bold text-yellow-400"></span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6 md:p-8 space-y-8">
                            <!-- Icons -->
                            <div class="grid grid-cols-4 gap-3">
                                <div
                                    class="flex flex-col items-center justify-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <i data-lucide="bed" class="w-5 h-5 text-gray-900 mb-2"></i>
                                    <span class="text-[10px] uppercase font-bold text-gray-500">King</span>
                                </div>
                                <div
                                    class="flex flex-col items-center justify-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <i data-lucide="users" class="w-5 h-5 text-gray-900 mb-2"></i>
                                    <span class="text-[10px] uppercase font-bold text-gray-500">4 Pax</span>
                                </div>
                                <div
                                    class="flex flex-col items-center justify-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <i data-lucide="eye" class="w-5 h-5 text-gray-900 mb-2"></i>
                                    <span class="text-[10px] uppercase font-bold text-gray-500">View</span>
                                </div>
                                <div
                                    class="flex flex-col items-center justify-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <i data-lucide="wind" class="w-5 h-5 text-gray-900 mb-2"></i>
                                    <span class="text-[10px] uppercase font-bold text-gray-500">AC</span>
                                </div>
                            </div>

                            <div>
                                <h3 class="font-bold text-xs uppercase tracking-widest text-gray-400 mb-4">Amenities
                                </h3>
                                <div id="drawer-amenities" class="flex flex-wrap gap-2"></div>
                            </div>

                            <div>
                                <h3 class="font-bold text-xs uppercase tracking-widest text-gray-400 mb-3">Description
                                </h3>
                                <p id="drawer-description" class="text-gray-600 text-sm leading-relaxed"></p>
                            </div>

                            <div class="pt-6 border-t border-gray-100">
                                <button onclick="closeDrawer()"
                                    class="w-full bg-gray-900 text-white font-bold py-4 rounded-xl hover:bg-black transition shadow-lg">
                                    Close Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global Loader Logic
        window.addEventListener('load', () => {
            const loader = document.getElementById('global-loader');
            setTimeout(() => {
                loader.classList.add('opacity-0');
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 700);
            }, 800);
        });

        // Mobile Menu Logic
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const panel = document.getElementById('mobile-menu-panel');

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                // slight delay to allow display:block to apply
                setTimeout(() => {
                    menu.classList.remove('opacity-0');
                    panel.classList.remove('translate-x-full');
                }, 10);
            } else {
                menu.classList.add('opacity-0');
                panel.classList.add('translate-x-full');
                setTimeout(() => {
                    menu.classList.add('hidden');
                }, 300);
            }
        }

        lucide.createIcons();

        // ... (Rest of existing scripts)

        // --- Custom Booking Engine Logic ---

        // 1. Flatpickr Dual-Month Calendar
        const checkInDisplay = document.getElementById('check-in-display');
        const checkOutDisplay = document.getElementById('check-out-display');
        const checkInInput = document.getElementById('check_in_input');
        const checkOutInput = document.getElementById('check_out_input');

        flatpickr("#flatpickr-range", {
            mode: "range",
            minDate: "today",
            showMonths: 2,
            dateFormat: "Y-m-d",
            defaultDate: ["{{ $checkIn ?? date('Y-m-d') }}", "{{ $checkOut ?? date('Y-m-d', strtotime('+1 day')) }}"],
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const start = selectedDates[0];
                    const end = selectedDates[1];

                    // Format for Display (e.g., 07 Jan)
                    const options = { day: 'numeric', month: 'short', year: 'numeric' };
                    checkInDisplay.innerText = start.toLocaleDateString('en-GB', options);
                    checkOutDisplay.innerText = end.toLocaleDateString('en-GB', options);

                    // Update Hidden Inputs (Y-m-d)
                    // Helper to format YYYY-MM-DD manually to avoid timezone issues
                    const formatYMD = (date) => {
                        const offset = date.getTimezoneOffset();
                        date = new Date(date.getTime() - (offset * 60 * 1000));
                        return date.toISOString().split('T')[0];
                    };

                    checkInInput.value = formatYMD(start);
                    checkOutInput.value = formatYMD(end);
                }
            },
            onDayCreate: function (dObj, dStr, fp, dayElem) {
                // Mock Daily Price Injection (Phase 3 requirement)
                // In a real implementation, this would come from an API
                const date = dayElem.dateObj;
                const price = date.getDay() === 0 || date.getDay() === 6 ? '₹7500' : '₹6000'; // Weekend pricing mock

                // Add price element
                const priceSpan = document.createElement("span");
                priceSpan.className = "price-tag block text-[10px] text-gray-500 font-medium -mt-1";

                // Add Sold Out mock (Random deterministic based on date to avoid flickering)
                // specific dates for demo
                const isSoldOut = (date.getDate() % 7 === 0) && (date > new Date()); // Every 7th day is sold out

                if (isSoldOut) {
                    dayElem.classList.add('sold-out');
                    priceSpan.innerHTML = "Sold Out";
                } else {
                    priceSpan.innerHTML = price;
                }

                dayElem.appendChild(priceSpan);
            }
        });

        // Trigger calendar when clicking the customized input
        document.getElementById('date-range-trigger').addEventListener('click', () => {
            document.getElementById('flatpickr-range')._flatpickr.open();
        });


        // 2. Occupancy Popover Logic
        const occupancyTrigger = document.getElementById('occupancy-trigger');
        const occupancyPopover = document.getElementById('occupancy-popover');
        const occupancyDisplay = document.getElementById('occupancy-display');

        let counts = {
            rooms: 1,
            guests: {{ $guests ?? 2 }},
            children: 0
        };

        function toggleOccupancy() {
            occupancyPopover.classList.toggle('hidden');
        }

        occupancyTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleOccupancy();
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!occupancyTrigger.contains(e.target) && !occupancyPopover.contains(e.target)) {
                occupancyPopover.classList.add('hidden');
            }
        });

        function updateCounter(type, change) {
            // Limits
            if (type === 'rooms') {
                if (counts.rooms + change < 1) return;
                if (counts.rooms + change > 5) return;
            }
            if (type === 'guests') {
                if (counts.guests + change < 1) return; // Min 1 guest
            }
            if (type === 'children') {
                if (counts.children + change < 0) return;
            }

            counts[type] += change;

            // Logic Validation
            // Ensure at least 1 guest per room
            if (counts.guests < counts.rooms) {
                counts.guests = counts.rooms;
            }

            // Update UI
            document.getElementById(`count-${type}`).innerText = counts[type];
            document.getElementById(`${type}_input`).value = counts[type];

            // Update Display Text
            occupancyDisplay.innerText = `${counts.rooms} Room${counts.rooms > 1 ? 's' : ''}, ${counts.guests} Guest${counts.guests > 1 ? '(s)' : ''}`;
        }

        // Update Display Text
        occupancyDisplay.innerText = `${counts.rooms} Room${counts.rooms > 1 ? 's' : ''}, ${counts.guests} Guest${counts.guests > 1 ? '(s)' : ''}`;
        }

        // Initialize logic once
        lucide.createIcons();

        // --- End Custom Engine ---

        // Header Scroll Effect
        const header = document.getElementById('main-header');
        const headerText = document.getElementById('header-text');
        const navLinks = document.getElementById('nav-links');
        const headerRating = document.getElementById('header-rating');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.remove('header-transparent');
                header.classList.add('header-scrolled');

                // Change text colors for white background
                headerText.classList.remove('text-white');
                headerText.classList.add('text-gray-900');

                navLinks.querySelectorAll('a').forEach(link => {
                    link.classList.remove('text-white');
                    link.classList.add('text-gray-800');
                });
            } else {
                header.classList.add('header-transparent');
                header.classList.remove('header-scrolled');

                // Revert text colors for transparent background
                headerText.classList.add('text-white');
                headerText.classList.remove('text-gray-900');

                navLinks.querySelectorAll('a').forEach(link => {
                    link.classList.add('text-white');
                    link.classList.remove('text-gray-800');
                });
            }
        });

        // Hero Carousel Logic
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-slide');

        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            slides[index].classList.add('active');
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
        }

        // Auto play
        setInterval(nextSlide, 5000);
        // --- Cart State Management ---
        let cart = {}; // { roomId: { qty: 0, price: 0, name: '' } }
        const stickyBar = document.getElementById('sticky-checkout-bar');

        function addToCart(roomId, name, price) {
            // Initial Add
            if (!cart[roomId]) {
                cart[roomId] = { qty: 1, price: price, name: name };
            }

            // Toggle UI
            const container = document.getElementById(`room-action-${roomId}`);
            container.querySelector('.add-btn').classList.add('hidden');
            container.querySelector('.stepper').classList.remove('hidden');

            updateCartUI();
        }

        function updateCartItem(roomId, change) {
            if (!cart[roomId]) return;

            cart[roomId].qty += change;

            const container = document.getElementById(`room-action-${roomId}`);

            if (cart[roomId].qty <= 0) {
                // Remove from cart
                delete cart[roomId];
                // Revert UI
                container.querySelector('.add-btn').classList.remove('hidden');
                container.querySelector('.stepper').classList.add('hidden');
            } else {
                // Update Number
                document.getElementById(`qty-${roomId}`).innerText = cart[roomId].qty;
            }

            updateCartUI();
        }

        function updateCartUI() {
            // Calculate Totals
            let totalRooms = 0;
            let totalPrice = 0;
            const cartItems = [];

            for (const [id, item] of Object.entries(cart)) {
                totalRooms += item.qty;
                totalPrice += (item.qty * item.price);
                cartItems.push({ id, ...item });
            }

            // Update Floating Bar
            if (totalRooms > 0) {
                stickyBar.classList.remove('translate-y-full');
            } else {
                stickyBar.classList.add('translate-y-full');
            }

            document.getElementById('bar-room-summary').innerText = `${totalRooms} Room${totalRooms > 1 ? 's' : ''} Selected`;
            document.getElementById('bar-total-price').innerText = `₹${totalPrice.toLocaleString()}`;

            // Update Inputs for Form
            document.getElementById('cart-data-input').value = JSON.stringify(cart);
            document.getElementById('bar-check-in').value = document.getElementById('check_in_input').value;
            document.getElementById('bar-check-out').value = document.getElementById('check_out_input').value; // check_out_input from previous step

            // Update Display Text in Bar
            const cin = document.getElementById('check-in-display').innerText;
            const cout = document.getElementById('check-out-display').innerText;
            const guests = document.getElementById('occupancy-display').innerText;

            document.getElementById('bar-date-range').innerText = `${cin} - ${cout}`;
            document.getElementById('bar-guests').innerText = guests;
        }

        // --- Room Drawer Logic ---
        function openRoomDetails(name, price, imageOrGallery, description, amenitiesJson) {
            const amenities = typeof amenitiesJson === 'string' ? JSON.parse(amenitiesJson) : amenitiesJson;

            document.getElementById('drawer-title').innerText = name;
            document.getElementById('drawer-price').innerText = '₹' + price.toLocaleString() + ' / night';
            // Image handling moved to initDrawerCarousel below
            document.getElementById('drawer-description').innerText = description;

            const amenitiesContainer = document.getElementById('drawer-amenities');
            amenitiesContainer.innerHTML = '';
            if (amenities) {
            });
        }
        lucide.createIcons();

        // --- Drawer Carousel Logic ---
        const carouselContainer = document.getElementById('drawer-carousel');
        let currentDrawerSlide = 0;
        let drawerImages = [];

        // Reset and Populate
        function initDrawerCarousel(images) {
            drawerImages = images.length > 0 ? images : ['/images/placeholder.jpg'];
            currentDrawerSlide = 0;
            renderDrawerSlide();
        }

        window.renderDrawerSlide = function () {
            carouselContainer.innerHTML = '';

            drawerImages.forEach((src, index) => {
                const img = document.createElement('img');
                img.src = src; // Assuming src is already full URL or asset path
                // Ensure local asset path is handled if it doesn't start with http
                // In this context, the blade passed encoded paths like ["\/images\/..."] which are valid

                img.className = `absolute inset-0 w-full h-full object-cover transition-opacity duration-500 ${index === currentDrawerSlide ? 'opacity-100' : 'opacity-0'}`;
                carouselContainer.appendChild(img);
            });
        };

        window.changeDrawerSlide = function (direction) {
            if (drawerImages.length <= 1) return;
            currentDrawerSlide = (currentDrawerSlide + direction + drawerImages.length) % drawerImages.length;
            renderDrawerSlide();
        };


        const drawer = document.getElementById('room-drawer');
        const backdrop = document.getElementById('drawer-backdrop');
        const panel = document.getElementById('drawer-panel');

        // Initialize Carousel
        initDrawerCarousel(Array.isArray(imageOrGallery) ? imageOrGallery : [imageOrGallery]);

        drawer.classList.remove('hidden');
            }
        lucide.createIcons();

        const drawer = document.getElementById('room-drawer');
        const backdrop = document.getElementById('drawer-backdrop');
        const panel = document.getElementById('drawer-panel');

        drawer.classList.remove('hidden');
        // Minimal timeout to allow display:block to apply before transition
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('translate-x-full');
        }, 10);
        }

        function closeDrawer() {
            const drawer = document.getElementById('room-drawer');
            const backdrop = document.getElementById('drawer-backdrop');
            const panel = document.getElementById('drawer-panel');

            backdrop.classList.add('opacity-0');
            panel.classList.add('translate-x-full');

            setTimeout(() => {
                drawer.classList.add('hidden');
            }, 500);
        }
    </script>
</body>

</html>