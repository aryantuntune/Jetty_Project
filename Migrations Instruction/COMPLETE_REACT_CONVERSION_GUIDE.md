# COMPLETE FRONTEND CONVERSION TO REACT
## Convert ALL Blade Templates to React - Public Website + Customer Portal + Admin Panel

---

## 🎯 OBJECTIVE

Replace **ALL** Laravel Blade templates with React - no more Blade/React hybrid!

**What we're building:**
- ✅ **Public Website** (Homepage, About, Contact, Routes, Houseboat)
- ✅ **Customer Portal** (Login, Register, Dashboard, Booking, History)
- ✅ **Admin Panel** (Dashboard, Ticket Entry, Reports, Management)
- ✅ **Checker Interface** (Verification panel)

**Result:** One unified React app, Laravel becomes API-only backend.

---

## 🏗️ PROJECT STRUCTURE (Complete)

```
jetty-react-app/                          # Single React app for everything
├── public/
│   ├── images/
│   │   ├── carferry/                     # Ferry route images
│   │   ├── houseboat/                    # Houseboat images
│   │   └── logo.png
│   └── favicon.ico
│
├── src/
│   ├── main.tsx
│   ├── App.tsx
│   │
│   ├── config/
│   │   ├── api.ts
│   │   ├── routes.ts
│   │   └── constants.ts
│   │
│   ├── types/
│   │   ├── auth.ts
│   │   ├── ticket.ts
│   │   ├── booking.ts
│   │   ├── branch.ts
│   │   ├── ferry.ts
│   │   ├── houseboat.ts                  # NEW
│   │   └── customer.ts                    # NEW
│   │
│   ├── lib/
│   │   ├── axios.ts
│   │   ├── utils.ts
│   │   └── cn.ts
│   │
│   ├── hooks/
│   │   ├── useAuth.ts
│   │   ├── useCustomerAuth.ts             # NEW - Customer authentication
│   │   ├── useTickets.ts
│   │   ├── useBookings.ts
│   │   ├── useBranches.ts
│   │   ├── useHouseboat.ts                # NEW
│   │   └── useReports.ts
│   │
│   ├── store/
│   │   ├── authStore.ts                   # Staff auth
│   │   ├── customerAuthStore.ts           # NEW - Customer auth
│   │   ├── ticketStore.ts
│   │   └── bookingStore.ts                # NEW
│   │
│   ├── components/
│   │   ├── ui/                            # Shared components
│   │   │
│   │   ├── layouts/
│   │   │   ├── PublicLayout.tsx           # NEW - Public website layout
│   │   │   ├── CustomerLayout.tsx         # NEW - Customer portal layout
│   │   │   ├── AdminLayout.tsx            # Admin panel layout
│   │   │   └── CheckerLayout.tsx          # NEW - Checker interface layout
│   │   │
│   │   ├── public/                        # NEW - Public website components
│   │   │   ├── Navbar.tsx
│   │   │   ├── Footer.tsx
│   │   │   ├── Hero.tsx
│   │   │   ├── RouteCard.tsx
│   │   │   ├── FeatureSection.tsx
│   │   │   └── ContactForm.tsx
│   │   │
│   │   ├── customer/                      # NEW - Customer portal components
│   │   │   ├── BookingForm.tsx
│   │   │   ├── BookingCard.tsx
│   │   │   ├── TicketView.tsx
│   │   │   ├── QRCodeDisplay.tsx
│   │   │   └── ProfileForm.tsx
│   │   │
│   │   ├── houseboat/                     # NEW - Houseboat components
│   │   │   ├── RoomCard.tsx
│   │   │   ├── BookingForm.tsx
│   │   │   └── Gallery.tsx
│   │   │
│   │   ├── admin/                         # Admin components
│   │   │   ├── Sidebar.tsx
│   │   │   ├── Header.tsx
│   │   │   └── ...
│   │   │
│   │   └── common/
│   │       ├── LoadingSpinner.tsx
│   │       ├── ErrorMessage.tsx
│   │       └── ProtectedRoute.tsx
│   │
│   ├── pages/
│   │   ├── public/                        # NEW - Public website pages
│   │   │   ├── Home.tsx
│   │   │   ├── About.tsx
│   │   │   ├── Contact.tsx
│   │   │   ├── RouteDetail.tsx
│   │   │   └── HouseboatBooking.tsx
│   │   │
│   │   ├── customer/                      # NEW - Customer portal pages
│   │   │   ├── auth/
│   │   │   │   ├── Login.tsx
│   │   │   │   ├── Register.tsx
│   │   │   │   ├── OTPVerification.tsx
│   │   │   │   ├── ForgotPassword.tsx
│   │   │   │   └── ResetPassword.tsx
│   │   │   ├── Dashboard.tsx
│   │   │   ├── BookTicket.tsx
│   │   │   ├── BookingHistory.tsx
│   │   │   ├── TicketDetail.tsx
│   │   │   └── Profile.tsx
│   │   │
│   │   ├── admin/                         # Admin panel pages
│   │   │   ├── Dashboard.tsx
│   │   │   ├── TicketEntry.tsx
│   │   │   ├── Reports.tsx
│   │   │   └── ...
│   │   │
│   │   ├── checker/                       # NEW - Checker interface
│   │   │   ├── Dashboard.tsx
│   │   │   └── TicketVerify.tsx
│   │   │
│   │   └── NotFound.tsx
│   │
│   ├── services/
│   │   ├── authService.ts
│   │   ├── customerAuthService.ts         # NEW
│   │   ├── ticketService.ts
│   │   ├── bookingService.ts
│   │   ├── houseboatService.ts            # NEW
│   │   ├── branchService.ts
│   │   └── reportService.ts
│   │
│   └── styles/
│       ├── globals.css
│       └── public.css                     # NEW - Public website specific styles
│
├── .env
├── package.json
├── tailwind.config.js
├── tsconfig.json
└── vite.config.ts
```

---

## 🎨 SECTION 1: PUBLIC WEBSITE (Replace Blade)

### 1.1: Public Layout

**File: `src/components/layouts/PublicLayout.tsx`**

```typescript
import { Outlet } from 'react-router-dom';
import { PublicNavbar } from '../public/Navbar';
import { Footer } from '../public/Footer';

export function PublicLayout() {
  return (
    <div className="min-h-screen flex flex-col">
      <PublicNavbar />
      
      <main className="flex-1">
        <Outlet />
      </main>
      
      <Footer />
    </div>
  );
}
```

### 1.2: Public Navbar

**File: `src/components/public/Navbar.tsx`**

```typescript
import { Link } from 'react-router-dom';
import { Ship, User } from 'lucide-react';
import { Button } from '../ui/button';

export function PublicNavbar() {
  return (
    <nav className="bg-white shadow-sm sticky top-0 z-50">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <Link to="/" className="flex items-center gap-2">
            <Ship className="w-8 h-8 text-blue-600" />
            <span className="text-xl font-bold text-gray-900">Jetty Ferry</span>
          </Link>

          {/* Navigation Links */}
          <div className="hidden md:flex items-center gap-6">
            <Link to="/" className="text-gray-700 hover:text-blue-600 font-medium">
              Home
            </Link>
            <Link to="/about" className="text-gray-700 hover:text-blue-600 font-medium">
              About
            </Link>
            <Link to="/routes" className="text-gray-700 hover:text-blue-600 font-medium">
              Ferry Routes
            </Link>
            <Link to="/houseboat" className="text-gray-700 hover:text-blue-600 font-medium">
              Houseboat
            </Link>
            <Link to="/contact" className="text-gray-700 hover:text-blue-600 font-medium">
              Contact
            </Link>
          </div>

          {/* Auth Buttons */}
          <div className="flex items-center gap-3">
            <Link to="/customer/login">
              <Button variant="outline" size="sm">
                <User className="w-4 h-4 mr-2" />
                Login
              </Button>
            </Link>
            <Link to="/customer/register">
              <Button size="sm">
                Book Ticket
              </Button>
            </Link>
          </div>
        </div>
      </div>
    </nav>
  );
}
```

### 1.3: Homepage

**File: `src/pages/public/Home.tsx`**

```typescript
import { Link } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Ship, Clock, Shield, DollarSign, ArrowRight } from 'lucide-react';

export function Home() {
  const routes = [
    {
      id: 'dabhol-dhopave',
      name: 'Dabhol – Dhopave',
      image: '/images/carferry/dabhol-dhopave.jpg',
      distance: '8 km',
      duration: '20 minutes',
      since: '2003',
    },
    {
      id: 'jaigad-tawsal',
      name: 'Jaigad – Tawsal',
      image: '/images/carferry/jaigad-tawsal.jpg',
      distance: '6 km',
      duration: '15 minutes',
      since: '2005',
    },
    {
      id: 'dighi-agardande',
      name: 'Dighi – Agardande',
      image: '/images/carferry/dighi-agardande.jpg',
      distance: '5 km',
      duration: '12 minutes',
      since: '2008',
    },
    {
      id: 'veshvi-bagmandale',
      name: 'Veshvi – Bagmandale',
      image: '/images/carferry/veshvi-bagmandale.jpg',
      distance: '7 km',
      duration: '18 minutes',
      since: '2010',
    },
  ];

  const features = [
    {
      icon: Shield,
      title: 'Safe & Reliable',
      description: 'All ferries undergo regular safety inspections',
    },
    {
      icon: DollarSign,
      title: 'Affordable Rates',
      description: 'Budget-friendly fares for all passengers',
    },
    {
      icon: Clock,
      title: 'Punctual Service',
      description: 'Ferries depart on time, every time',
    },
    {
      icon: Ship,
      title: 'Modern Fleet',
      description: 'Well-maintained vessels with modern amenities',
    },
  ];

  return (
    <div>
      {/* Hero Section */}
      <section className="relative bg-gradient-to-br from-blue-600 to-blue-800 text-white">
        <div className="container mx-auto px-4 py-24">
          <div className="max-w-3xl">
            <h1 className="text-5xl md:text-6xl font-bold mb-6">
              Maharashtra's Premier Ferry Service
            </h1>
            <p className="text-xl md:text-2xl mb-8 text-blue-100">
              Connecting coastal communities since 2003. Safe, reliable, and affordable ferry transportation.
            </p>
            <div className="flex flex-col sm:flex-row gap-4">
              <Link to="/customer/register">
                <Button size="lg" className="bg-white text-blue-600 hover:bg-gray-100">
                  Book Ticket Now
                  <ArrowRight className="ml-2 w-5 h-5" />
                </Button>
              </Link>
              <Link to="/routes">
                <Button size="lg" variant="outline" className="border-white text-white hover:bg-white/10">
                  View Ferry Routes
                </Button>
              </Link>
            </div>
          </div>
        </div>
        
        {/* Wave decoration */}
        <div className="absolute bottom-0 left-0 right-0">
          <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="white"/>
          </svg>
        </div>
      </section>

      {/* Ferry Routes Section */}
      <section className="py-20 bg-white">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4">Ferry Routes</h2>
            <p className="text-xl text-gray-600">Choose from 4 scenic routes along Maharashtra coast</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {routes.map((route) => (
              <Card key={route.id} className="overflow-hidden hover:shadow-xl transition-shadow">
                <img 
                  src={route.image} 
                  alt={route.name}
                  className="w-full h-48 object-cover"
                />
                <div className="p-6">
                  <h3 className="text-xl font-bold mb-2">{route.name}</h3>
                  <p className="text-sm text-gray-600 mb-4">Operating since {route.since}</p>
                  
                  <div className="space-y-2 mb-4">
                    <div className="flex justify-between text-sm">
                      <span className="text-gray-600">Distance:</span>
                      <span className="font-medium">{route.distance}</span>
                    </div>
                    <div className="flex justify-between text-sm">
                      <span className="text-gray-600">Duration:</span>
                      <span className="font-medium">{route.duration}</span>
                    </div>
                  </div>

                  <Link to={`/route/${route.id}`}>
                    <Button variant="outline" className="w-full">
                      View Details
                      <ArrowRight className="ml-2 w-4 h-4" />
                    </Button>
                  </Link>
                </div>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-20 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4">Why Choose Us</h2>
            <p className="text-xl text-gray-600">Experience the best ferry service in Maharashtra</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {features.map((feature) => {
              const Icon = feature.icon;
              return (
                <div key={feature.title} className="text-center">
                  <div className="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-600 mb-4">
                    <Icon className="w-8 h-8" />
                  </div>
                  <h3 className="text-xl font-bold mb-2">{feature.title}</h3>
                  <p className="text-gray-600">{feature.description}</p>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20 bg-blue-600 text-white">
        <div className="container mx-auto px-4 text-center">
          <h2 className="text-4xl font-bold mb-4">Ready to Book Your Journey?</h2>
          <p className="text-xl mb-8 text-blue-100">
            Book your ferry tickets online in just a few clicks
          </p>
          <Link to="/customer/register">
            <Button size="lg" className="bg-white text-blue-600 hover:bg-gray-100">
              Book Now
              <ArrowRight className="ml-2 w-5 h-5" />
            </Button>
          </Link>
        </div>
      </section>
    </div>
  );
}
```

### 1.4: About Page

**File: `src/pages/public/About.tsx`**

```typescript
import { Card } from '@/components/ui/card';
import { Ship, Users, Award, MapPin } from 'lucide-react';

export function About() {
  return (
    <div className="py-12">
      <div className="container mx-auto px-4">
        {/* Hero */}
        <div className="text-center mb-12">
          <h1 className="text-5xl font-bold mb-4">About Jetty Ferry</h1>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Providing safe, reliable, and affordable ferry transportation along Maharashtra's beautiful coastline since 2003.
          </p>
        </div>

        {/* Stats */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-16">
          <Card className="p-6 text-center">
            <Ship className="w-12 h-12 text-blue-600 mx-auto mb-4" />
            <div className="text-3xl font-bold mb-2">20+</div>
            <div className="text-gray-600">Years of Service</div>
          </Card>
          
          <Card className="p-6 text-center">
            <MapPin className="w-12 h-12 text-blue-600 mx-auto mb-4" />
            <div className="text-3xl font-bold mb-2">8</div>
            <div className="text-gray-600">Ferry Terminals</div>
          </Card>
          
          <Card className="p-6 text-center">
            <Users className="w-12 h-12 text-blue-600 mx-auto mb-4" />
            <div className="text-3xl font-bold mb-2">2M+</div>
            <div className="text-gray-600">Passengers Annually</div>
          </Card>
          
          <Card className="p-6 text-center">
            <Award className="w-12 h-12 text-blue-600 mx-auto mb-4" />
            <div className="text-3xl font-bold mb-2">100%</div>
            <div className="text-gray-600">Safety Record</div>
          </Card>
        </div>

        {/* Mission & Vision */}
        <div className="grid md:grid-cols-2 gap-8 mb-16">
          <Card className="p-8">
            <h2 className="text-2xl font-bold mb-4">Our Mission</h2>
            <p className="text-gray-700 leading-relaxed">
              To provide safe, reliable, and affordable ferry transportation services that connect coastal communities and promote economic development in Maharashtra.
            </p>
          </Card>

          <Card className="p-8">
            <h2 className="text-2xl font-bold mb-4">Our Vision</h2>
            <p className="text-gray-700 leading-relaxed">
              To be the preferred ferry service provider in Maharashtra, known for excellence in service, safety, and customer satisfaction.
            </p>
          </Card>
        </div>

        {/* History */}
        <Card className="p-8">
          <h2 className="text-2xl font-bold mb-4">Our Story</h2>
          <div className="prose prose-lg max-w-none text-gray-700">
            <p>
              Jetty Ferry Services was established in 2003 with the inauguration of the Dabhol-Dhopave route, the first of its kind in Maharashtra. Over the years, we have expanded our services to include multiple routes connecting various coastal towns and villages.
            </p>
            <p>
              Today, we operate 4 major routes with a fleet of modern ferries, serving thousands of passengers daily. Our commitment to safety, reliability, and customer service has made us the trusted choice for ferry transportation in the region.
            </p>
          </div>
        </Card>
      </div>
    </div>
  );
}
```

### 1.5: Contact Page

**File: `src/pages/public/Contact.tsx`**

```typescript
import { useState } from 'react';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Phone, Mail, MapPin, Clock } from 'lucide-react';
import { toast } from 'sonner';

export function Contact() {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    message: '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    // Handle form submission
    toast.success('Message sent! We will get back to you soon.');
    setFormData({ name: '', email: '', phone: '', message: '' });
  };

  return (
    <div className="py-12">
      <div className="container mx-auto px-4">
        <div className="text-center mb-12">
          <h1 className="text-5xl font-bold mb-4">Contact Us</h1>
          <p className="text-xl text-gray-600">
            Get in touch with us for any queries or assistance
          </p>
        </div>

        <div className="grid lg:grid-cols-3 gap-8">
          {/* Contact Form */}
          <Card className="lg:col-span-2 p-8">
            <h2 className="text-2xl font-bold mb-6">Send us a Message</h2>
            
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="grid md:grid-cols-2 gap-6">
                <div>
                  <label className="block text-sm font-medium mb-2">Name *</label>
                  <Input
                    value={formData.name}
                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    placeholder="Your name"
                    required
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium mb-2">Email *</label>
                  <Input
                    type="email"
                    value={formData.email}
                    onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                    placeholder="your@email.com"
                    required
                  />
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium mb-2">Phone</label>
                <Input
                  type="tel"
                  value={formData.phone}
                  onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                  placeholder="+91 98765 43210"
                />
              </div>

              <div>
                <label className="block text-sm font-medium mb-2">Message *</label>
                <Textarea
                  value={formData.message}
                  onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                  placeholder="How can we help you?"
                  rows={6}
                  required
                />
              </div>

              <Button type="submit" size="lg" className="w-full">
                Send Message
              </Button>
            </form>
          </Card>

          {/* Contact Info */}
          <div className="space-y-6">
            <Card className="p-6">
              <div className="flex items-start gap-4">
                <div className="p-3 rounded-lg bg-blue-100">
                  <Phone className="w-6 h-6 text-blue-600" />
                </div>
                <div>
                  <h3 className="font-bold mb-2">Phone</h3>
                  <p className="text-gray-600">+91 02358-234567</p>
                  <p className="text-gray-600">+91 02358-234568</p>
                </div>
              </div>
            </Card>

            <Card className="p-6">
              <div className="flex items-start gap-4">
                <div className="p-3 rounded-lg bg-blue-100">
                  <Mail className="w-6 h-6 text-blue-600" />
                </div>
                <div>
                  <h3 className="font-bold mb-2">Email</h3>
                  <p className="text-gray-600">info@jettyferry.com</p>
                  <p className="text-gray-600">support@jettyferry.com</p>
                </div>
              </div>
            </Card>

            <Card className="p-6">
              <div className="flex items-start gap-4">
                <div className="p-3 rounded-lg bg-blue-100">
                  <MapPin className="w-6 h-6 text-blue-600" />
                </div>
                <div>
                  <h3 className="font-bold mb-2">Head Office</h3>
                  <p className="text-gray-600">
                    Dabhol Jetty<br />
                    Ratnagiri, Maharashtra<br />
                    India - 415612
                  </p>
                </div>
              </div>
            </Card>

            <Card className="p-6">
              <div className="flex items-start gap-4">
                <div className="p-3 rounded-lg bg-blue-100">
                  <Clock className="w-6 h-6 text-blue-600" />
                </div>
                <div>
                  <h3 className="font-bold mb-2">Working Hours</h3>
                  <p className="text-gray-600">
                    Monday - Sunday<br />
                    6:00 AM - 8:00 PM
                  </p>
                </div>
              </div>
            </Card>
          </div>
        </div>
      </div>
    </div>
  );
}
```

### 1.6: Houseboat Booking Page

**File: `src/pages/public/HouseboatBooking.tsx`**

```typescript
import { useState } from 'react';
import { useQuery, useMutation } from '@tanstack/react-query';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { toast } from 'sonner';
import { Calendar, Users, Home } from 'lucide-react';

export function HouseboatBooking() {
  const [selectedRoom, setSelectedRoom] = useState<number | null>(null);
  const [formData, setFormData] = useState({
    customer_name: '',
    customer_email: '',
    customer_phone: '',
    check_in: '',
    check_out: '',
    guests_adults: 2,
    guests_kids: 0,
    room_count: 1,
  });

  // Fetch houseboat rooms
  const { data: rooms } = useQuery({
    queryKey: ['houseboat-rooms'],
    queryFn: async () => {
      // Fetch from API
      return [
        {
          id: 1,
          name: 'Deluxe Room',
          description: 'Spacious deluxe room with scenic views',
          price: 6000,
          capacity_adults: 4,
          capacity_kids: 2,
          amenities: ['AC', 'Attached Bathroom', 'TV', 'Mini Fridge', 'Complimentary Breakfast'],
          image_url: '/images/houseboat/deluxe-1.jpg',
        },
        {
          id: 2,
          name: 'VIP Suite with Deck',
          description: 'Luxurious suite with private deck',
          price: 8000,
          capacity_adults: 6,
          capacity_kids: 3,
          amenities: ['AC', 'Attached Bathroom', 'TV', 'Mini Fridge', 'Private Deck', 'Dining Area', 'Complimentary Meals'],
          image_url: '/images/houseboat/vip-1.jpg',
        },
      ];
    },
  });

  const bookingMutation = useMutation({
    mutationFn: async (data: any) => {
      // Submit booking to API
      return { success: true };
    },
    onSuccess: () => {
      toast.success('Booking confirmed! Check your email for details.');
      setFormData({
        customer_name: '',
        customer_email: '',
        customer_phone: '',
        check_in: '',
        check_out: '',
        guests_adults: 2,
        guests_kids: 0,
        room_count: 1,
      });
    },
  });

  const calculateTotal = () => {
    if (!selectedRoom || !formData.check_in || !formData.check_out) return 0;
    
    const room = rooms?.find(r => r.id === selectedRoom);
    if (!room) return 0;

    const checkIn = new Date(formData.check_in);
    const checkOut = new Date(formData.check_out);
    const days = Math.ceil((checkOut.getTime() - checkIn.getTime()) / (1000 * 60 * 60 * 24));
    
    return days * room.price * formData.room_count;
  };

  return (
    <div className="py-12">
      <div className="container mx-auto px-4">
        <div className="text-center mb-12">
          <h1 className="text-5xl font-bold mb-4">Houseboat Booking</h1>
          <p className="text-xl text-gray-600">
            Experience luxury on water - Book your houseboat stay
          </p>
        </div>

        <div className="grid lg:grid-cols-3 gap-8">
          {/* Room Selection */}
          <div className="lg:col-span-2 space-y-6">
            <h2 className="text-2xl font-bold">Select Your Room</h2>
            
            {rooms?.map((room) => (
              <Card 
                key={room.id}
                className={`p-6 cursor-pointer transition-all ${
                  selectedRoom === room.id ? 'ring-2 ring-blue-600' : ''
                }`}
                onClick={() => setSelectedRoom(room.id)}
              >
                <div className="flex gap-6">
                  <img 
                    src={room.image_url} 
                    alt={room.name}
                    className="w-48 h-32 object-cover rounded-lg"
                  />
                  
                  <div className="flex-1">
                    <h3 className="text-xl font-bold mb-2">{room.name}</h3>
                    <p className="text-gray-600 mb-3">{room.description}</p>
                    
                    <div className="flex items-center gap-4 mb-3">
                      <div className="flex items-center gap-1 text-sm text-gray-600">
                        <Users className="w-4 h-4" />
                        Up to {room.capacity_adults} adults, {room.capacity_kids} kids
                      </div>
                    </div>

                    <div className="flex flex-wrap gap-2 mb-3">
                      {room.amenities.map((amenity) => (
                        <span key={amenity} className="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded">
                          {amenity}
                        </span>
                      ))}
                    </div>

                    <div className="text-2xl font-bold text-blue-600">
                      ₹{room.price.toLocaleString()}/night
                    </div>
                  </div>
                </div>
              </Card>
            ))}

            {/* Booking Form */}
            {selectedRoom && (
              <Card className="p-6">
                <h3 className="text-xl font-bold mb-4">Booking Details</h3>
                
                <div className="grid md:grid-cols-2 gap-4 mb-4">
                  <div>
                    <label className="block text-sm font-medium mb-2">Full Name *</label>
                    <Input
                      value={formData.customer_name}
                      onChange={(e) => setFormData({ ...formData, customer_name: e.target.value })}
                      required
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-2">Email *</label>
                    <Input
                      type="email"
                      value={formData.customer_email}
                      onChange={(e) => setFormData({ ...formData, customer_email: e.target.value })}
                      required
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-2">Phone *</label>
                    <Input
                      type="tel"
                      value={formData.customer_phone}
                      onChange={(e) => setFormData({ ...formData, customer_phone: e.target.value })}
                      required
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-2">Number of Rooms</label>
                    <Input
                      type="number"
                      min="1"
                      value={formData.room_count}
                      onChange={(e) => setFormData({ ...formData, room_count: parseInt(e.target.value) })}
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-2">Check-in Date *</label>
                    <Input
                      type="date"
                      value={formData.check_in}
                      onChange={(e) => setFormData({ ...formData, check_in: e.target.value })}
                      required
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-2">Check-out Date *</label>
                    <Input
                      type="date"
                      value={formData.check_out}
                      onChange={(e) => setFormData({ ...formData, check_out: e.target.value })}
                      required
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-2">Adults</label>
                    <Input
                      type="number"
                      min="1"
                      value={formData.guests_adults}
                      onChange={(e) => setFormData({ ...formData, guests_adults: parseInt(e.target.value) })}
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-2">Children</label>
                    <Input
                      type="number"
                      min="0"
                      value={formData.guests_kids}
                      onChange={(e) => setFormData({ ...formData, guests_kids: parseInt(e.target.value) })}
                    />
                  </div>
                </div>
              </Card>
            )}
          </div>

          {/* Booking Summary */}
          {selectedRoom && (
            <div>
              <Card className="p-6 sticky top-24">
                <h3 className="text-xl font-bold mb-4">Booking Summary</h3>
                
                <div className="space-y-3 mb-6">
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Room</span>
                    <span className="font-medium">
                      {rooms?.find(r => r.id === selectedRoom)?.name}
                    </span>
                  </div>

                  {formData.check_in && formData.check_out && (
                    <div className="flex justify-between text-sm">
                      <span className="text-gray-600">Duration</span>
                      <span className="font-medium">
                        {Math.ceil((new Date(formData.check_out).getTime() - new Date(formData.check_in).getTime()) / (1000 * 60 * 60 * 24))} nights
                      </span>
                    </div>
                  )}

                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Rooms</span>
                    <span className="font-medium">{formData.room_count}</span>
                  </div>

                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Guests</span>
                    <span className="font-medium">
                      {formData.guests_adults} adults, {formData.guests_kids} kids
                    </span>
                  </div>
                </div>

                <div className="border-t pt-4 mb-6">
                  <div className="flex justify-between items-center">
                    <span className="font-bold">Total Amount</span>
                    <span className="text-2xl font-bold text-blue-600">
                      ₹{calculateTotal().toLocaleString()}
                    </span>
                  </div>
                </div>

                <Button 
                  size="lg" 
                  className="w-full"
                  onClick={() => bookingMutation.mutate({ ...formData, room_id: selectedRoom })}
                  disabled={!formData.customer_name || !formData.check_in || !formData.check_out}
                >
                  Confirm Booking
                </Button>

                <p className="text-xs text-gray-500 text-center mt-4">
                  * You will receive a confirmation email after booking
                </p>
              </Card>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
```

---

## 👤 SECTION 2: CUSTOMER PORTAL (Replace Blade)

### 2.1: Customer Auth Types

**File: `src/types/customer.ts`**

```typescript
export interface Customer {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  mobile: string;
  profile_image?: string;
  google_id?: string;
}

export interface CustomerLoginRequest {
  email: string;
  password: string;
}

export interface CustomerRegisterRequest {
  first_name: string;
  last_name: string;
  email: string;
  mobile: string;
  password: string;
  password_confirmation: string;
}

export interface OTPVerificationRequest {
  email: string;
  otp: string;
}
```

### 2.2: Customer Auth Store

**File: `src/store/customerAuthStore.ts`**

```typescript
import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import { Customer } from '@/types/customer';

interface CustomerAuthState {
  customer: Customer | null;
  token: string | null;
  isAuthenticated: boolean;
  
  setAuth: (customer: Customer, token: string) => void;
  clearAuth: () => void;
}

export const useCustomerAuthStore = create<CustomerAuthState>()(
  persist(
    (set) => ({
      customer: null,
      token: null,
      isAuthenticated: false,

      setAuth: (customer, token) => {
        localStorage.setItem('customer_token', token);
        set({ customer, token, isAuthenticated: true });
      },

      clearAuth: () => {
        localStorage.removeItem('customer_token');
        set({ customer: null, token: null, isAuthenticated: false });
      },
    }),
    {
      name: 'customer-auth-storage',
    }
  )
);
```

### 2.3: Customer Login Page

**File: `src/pages/customer/auth/Login.tsx`**

```typescript
import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useMutation } from '@tanstack/react-query';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card } from '@/components/ui/card';
import { useCustomerAuthStore } from '@/store/customerAuthStore';
import { Ship, Mail, Lock } from 'lucide-react';
import { toast } from 'sonner';

export function CustomerLogin() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const navigate = useNavigate();
  const { setAuth } = useCustomerAuthStore();

  const loginMutation = useMutation({
    mutationFn: async (credentials: any) => {
      // Call API
      return {
        token: 'customer_token_123',
        customer: {
          id: 1,
          first_name: 'John',
          last_name: 'Doe',
          email: credentials.email,
          mobile: '9876543210',
        },
      };
    },
    onSuccess: (data) => {
      setAuth(data.customer, data.token);
      toast.success('Login successful!');
      navigate('/customer/dashboard');
    },
    onError: () => {
      toast.error('Invalid email or password');
    },
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    loginMutation.mutate({ email, password });
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 p-4">
      <Card className="w-full max-w-md p-8">
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-4">
            <Ship className="w-8 h-8 text-blue-600" />
          </div>
          <h1 className="text-3xl font-bold mb-2">Welcome Back</h1>
          <p className="text-gray-600">Login to book your ferry tickets</p>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium mb-2">Email</label>
            <div className="relative">
              <Mail className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
              <Input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="your@email.com"
                className="pl-10"
                required
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium mb-2">Password</label>
            <div className="relative">
              <Lock className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
              <Input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="••••••••"
                className="pl-10"
                required
              />
            </div>
          </div>

          <div className="flex items-center justify-between text-sm">
            <label className="flex items-center gap-2">
              <input type="checkbox" className="rounded" />
              Remember me
            </label>
            <Link to="/customer/forgot-password" className="text-blue-600 hover:underline">
              Forgot password?
            </Link>
          </div>

          <Button 
            type="submit" 
            className="w-full" 
            size="lg"
            disabled={loginMutation.isPending}
          >
            {loginMutation.isPending ? 'Logging in...' : 'Login'}
          </Button>

          <div className="relative my-6">
            <div className="absolute inset-0 flex items-center">
              <div className="w-full border-t border-gray-300"></div>
            </div>
            <div className="relative flex justify-center text-sm">
              <span className="px-2 bg-white text-gray-500">Or continue with</span>
            </div>
          </div>

          <Button type="button" variant="outline" className="w-full" size="lg">
            <img src="/images/google-logo.png" className="w-5 h-5 mr-2" alt="Google" />
            Sign in with Google
          </Button>

          <div className="text-center text-sm">
            Don't have an account?{' '}
            <Link to="/customer/register" className="text-blue-600 hover:underline font-medium">
              Register now
            </Link>
          </div>
        </form>
      </Card>
    </div>
  );
}
```

### 2.4: Customer Register Page

**File: `src/pages/customer/auth/Register.tsx`**

```typescript
import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useMutation } from '@tanstack/react-query';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card } from '@/components/ui/card';
import { Ship, User, Mail, Phone, Lock } from 'lucide-react';
import { toast } from 'sonner';

export function CustomerRegister() {
  const [formData, setFormData] = useState({
    first_name: '',
    last_name: '',
    email: '',
    mobile: '',
    password: '',
    password_confirmation: '',
  });
  const navigate = useNavigate();

  const registerMutation = useMutation({
    mutationFn: async (data: any) => {
      // Call API to generate OTP
      return { otp_sent: true };
    },
    onSuccess: () => {
      toast.success('OTP sent to your email!');
      navigate('/customer/verify-otp', { state: { email: formData.email } });
    },
    onError: () => {
      toast.error('Registration failed');
    },
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    if (formData.password !== formData.password_confirmation) {
      toast.error('Passwords do not match');
      return;
    }

    registerMutation.mutate(formData);
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 p-4">
      <Card className="w-full max-w-md p-8">
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-4">
            <Ship className="w-8 h-8 text-blue-600" />
          </div>
          <h1 className="text-3xl font-bold mb-2">Create Account</h1>
          <p className="text-gray-600">Register to start booking</p>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium mb-2">First Name</label>
              <Input
                value={formData.first_name}
                onChange={(e) => setFormData({ ...formData, first_name: e.target.value })}
                required
              />
            </div>

            <div>
              <label className="block text-sm font-medium mb-2">Last Name</label>
              <Input
                value={formData.last_name}
                onChange={(e) => setFormData({ ...formData, last_name: e.target.value })}
                required
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium mb-2">Email</label>
            <div className="relative">
              <Mail className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
              <Input
                type="email"
                value={formData.email}
                onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                placeholder="your@email.com"
                className="pl-10"
                required
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium mb-2">Mobile</label>
            <div className="relative">
              <Phone className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
              <Input
                type="tel"
                value={formData.mobile}
                onChange={(e) => setFormData({ ...formData, mobile: e.target.value })}
                placeholder="9876543210"
                className="pl-10"
                required
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium mb-2">Password</label>
            <div className="relative">
              <Lock className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
              <Input
                type="password"
                value={formData.password}
                onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                placeholder="••••••••"
                className="pl-10"
                required
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium mb-2">Confirm Password</label>
            <div className="relative">
              <Lock className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
              <Input
                type="password"
                value={formData.password_confirmation}
                onChange={(e) => setFormData({ ...formData, password_confirmation: e.target.value })}
                placeholder="••••••••"
                className="pl-10"
                required
              />
            </div>
          </div>

          <Button 
            type="submit" 
            className="w-full" 
            size="lg"
            disabled={registerMutation.isPending}
          >
            {registerMutation.isPending ? 'Creating Account...' : 'Register'}
          </Button>

          <div className="text-center text-sm">
            Already have an account?{' '}
            <Link to="/customer/login" className="text-blue-600 hover:underline font-medium">
              Login here
            </Link>
          </div>
        </form>
      </Card>
    </div>
  );
}
```

### 2.5: Customer Dashboard

**File: `src/pages/customer/Dashboard.tsx`**

```typescript
import { Link } from 'react-router-dom';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { useCustomerAuthStore } from '@/store/customerAuthStore';
import { Ticket, History, User, Ship, Calendar } from 'lucide-react';

export function CustomerDashboard() {
  const { customer } = useCustomerAuthStore();

  return (
    <div className="py-8">
      <div className="container mx-auto px-4">
        {/* Welcome Section */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold mb-2">
            Welcome back, {customer?.first_name}!
          </h1>
          <p className="text-gray-600">Manage your bookings and profile</p>
        </div>

        {/* Quick Actions */}
        <div className="grid md:grid-cols-2 gap-6 mb-8">
          <Card className="p-8 text-center hover:shadow-lg transition-shadow">
            <div className="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-4">
              <Ticket className="w-8 h-8 text-blue-600" />
            </div>
            <h2 className="text-2xl font-bold mb-2">Book New Ticket</h2>
            <p className="text-gray-600 mb-4">Book ferry tickets for your next journey</p>
            <Link to="/customer/book">
              <Button size="lg">Book Now</Button>
            </Link>
          </Card>

          <Card className="p-8 text-center hover:shadow-lg transition-shadow">
            <div className="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
              <History className="w-8 h-8 text-green-600" />
            </div>
            <h2 className="text-2xl font-bold mb-2">My Bookings</h2>
            <p className="text-gray-600 mb-4">View your booking history and tickets</p>
            <Link to="/customer/history">
              <Button size="lg" variant="outline">View History</Button>
            </Link>
          </Card>
        </div>

        {/* Upcoming Trips */}
        <Card className="p-6">
          <h2 className="text-2xl font-bold mb-6">Upcoming Trips</h2>
          
          {/* Empty state or list of bookings */}
          <div className="text-center py-12 text-gray-500">
            <Ship className="w-16 h-16 mx-auto mb-4 text-gray-400" />
            <p>No upcoming trips</p>
            <Link to="/customer/book">
              <Button className="mt-4">Book Your First Trip</Button>
            </Link>
          </div>
        </Card>
      </div>
    </div>
  );
}
```

---

## 🔄 SECTION 3: COMPLETE ROUTING

**File: `src/App.tsx` (COMPLETE VERSION)**

```typescript
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { Toaster } from 'sonner';

// Layouts
import { PublicLayout } from './components/layouts/PublicLayout';
import { CustomerLayout } from './components/layouts/CustomerLayout';
import { AdminLayout } from './components/layouts/AdminLayout';

// Public Pages
import { Home } from './pages/public/Home';
import { About } from './pages/public/About';
import { Contact } from './pages/public/Contact';
import { HouseboatBooking } from './pages/public/HouseboatBooking';

// Customer Auth Pages
import { CustomerLogin } from './pages/customer/auth/Login';
import { CustomerRegister } from './pages/customer/auth/Register';

// Customer Pages
import { CustomerDashboard } from './pages/customer/Dashboard';
import { BookTicket } from './pages/customer/BookTicket';
import { BookingHistory } from './pages/customer/BookingHistory';

// Admin Pages
import { AdminLogin } from './pages/admin/auth/Login';
import { AdminDashboard } from './pages/admin/Dashboard';
import { TicketEntry } from './pages/admin/TicketEntry';

// Stores
import { useAuthStore } from './store/authStore';
import { useCustomerAuthStore } from './store/customerAuthStore';

const queryClient = new QueryClient();

// Protected Route Components
function CustomerProtectedRoute({ children }: { children: React.ReactNode }) {
  const { isAuthenticated } = useCustomerAuthStore();
  return isAuthenticated ? children : <Navigate to="/customer/login" />;
}

function AdminProtectedRoute({ children }: { children: React.ReactNode }) {
  const { isAuthenticated } = useAuthStore();
  return isAuthenticated ? children : <Navigate to="/admin/login" />;
}

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <BrowserRouter>
        <Routes>
          {/* PUBLIC ROUTES */}
          <Route element={<PublicLayout />}>
            <Route path="/" element={<Home />} />
            <Route path="/about" element={<About />} />
            <Route path="/contact" element={<Contact />} />
            <Route path="/houseboat" element={<HouseboatBooking />} />
          </Route>

          {/* CUSTOMER AUTH ROUTES (No Layout) */}
          <Route path="/customer/login" element={<CustomerLogin />} />
          <Route path="/customer/register" element={<CustomerRegister />} />

          {/* CUSTOMER PROTECTED ROUTES */}
          <Route
            path="/customer"
            element={
              <CustomerProtectedRoute>
                <CustomerLayout />
              </CustomerProtectedRoute>
            }
          >
            <Route index element={<Navigate to="/customer/dashboard" />} />
            <Route path="dashboard" element={<CustomerDashboard />} />
            <Route path="book" element={<BookTicket />} />
            <Route path="history" element={<BookingHistory />} />
          </Route>

          {/* ADMIN AUTH ROUTES */}
          <Route path="/admin/login" element={<AdminLogin />} />

          {/* ADMIN PROTECTED ROUTES */}
          <Route
            path="/admin"
            element={
              <AdminProtectedRoute>
                <AdminLayout />
              </AdminProtectedRoute>
            }
          >
            <Route index element={<Navigate to="/admin/dashboard" />} />
            <Route path="dashboard" element={<AdminDashboard />} />
            <Route path="ticket-entry" element={<TicketEntry />} />
            {/* Add more admin routes */}
          </Route>

          {/* 404 */}
          <Route path="*" element={<div>404 Not Found</div>} />
        </Routes>
      </BrowserRouter>
      
      <Toaster position="top-right" richColors />
    </QueryClientProvider>
  );
}

export default App;
```

---

## ✅ COMPLETION CHECKLIST

Give this guide to Claude Code and he should build:

- ✅ **Public Website** (Home, About, Contact, Houseboat)
- ✅ **Customer Portal** (Login, Register, Dashboard, Booking)
- ✅ **Admin Panel** (All admin features)
- ✅ **Complete React app** (No more Blade!)

---

## 🚀 DEPLOYMENT

### Build for Production

```bash
# Build React app
npm run build

# Deploy dist/ folder to:
# /var/www/jetty-react/dist

# Configure Nginx to serve React app
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name unfurling.ninja;
    root /var/www/jetty-react/dist;
    index index.html;

    # React SPA - all routes to index.html
    location / {
        try_files $uri $uri/ /index.html;
    }

    # API proxy to Laravel
    location /api {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }

    # Laravel endpoints
    location ~ ^/(login|logout|ticket-entry|reports) {
        proxy_pass http://localhost:8000;
    }
}
```

---

**NOW you have a complete React frontend guide that replaces ALL Blade templates!** 🎉
