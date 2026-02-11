import { useState, useEffect } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import PublicLayout from '@/Layouts/PublicLayout';
import {
    ChevronRight,
    Home,
    MapPin,
    Phone,
    Mail,
    Clock,
    Send,
    CheckCircle,
    AlertCircle,
} from 'lucide-react';

const officeContacts = [
    { name: 'Dabhol – Dhopave', phone1: '02348-248900', phone2: '9767248900' },
    { name: 'Jaigad – Tawsal', phone1: '02354-242500', phone2: '8550999884' },
    { name: 'Dighi – Agardande', phone1: '9156546700', phone2: '8550999887' },
    { name: 'Veshvi – Bagmandale', phone1: '02350-223300', phone2: '9322819161' },
    { name: 'Vasai – Bhayander', phone1: '8624063900', phone2: '8600314710' },
];

export default function Contact() {
    const { flash } = usePage().props;
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        phone: '',
        message: '',
    });
    const [captcha, setCaptcha] = useState({ num1: 0, num2: 0, answer: 0 });
    const [captchaInput, setCaptchaInput] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    // Generate captcha
    useEffect(() => {
        generateCaptcha();
    }, []);

    const generateCaptcha = () => {
        const num1 = Math.floor(Math.random() * 10);
        const num2 = Math.floor(Math.random() * 10);
        setCaptcha({ num1, num2, answer: num1 + num2 });
        setCaptchaInput('');
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        if (parseInt(captchaInput) !== captcha.answer) {
            setError('Please enter the correct answer to the math question.');
            generateCaptcha();
            return;
        }

        setLoading(true);
        setError('');

        router.post(route('public.contact.submit'), formData, {
            onSuccess: () => {
                setFormData({ name: '', email: '', phone: '', message: '' });
                generateCaptcha();
            },
            onError: (errors) => {
                setError(Object.values(errors)[0] || 'Please correct the errors and try again.');
            },
            onFinish: () => setLoading(false),
        });
    };

    return (
        <>
            <Head>
                <title>Contact Us - Suvarnadurga Shipping & Marine Services</title>
                <meta
                    name="description"
                    content="Contact Suvarnadurga Shipping & Marine Services for ferry bookings and inquiries. Call us at +91 9422431371 or email ssmsdapoli@rediffmail.com."
                />
            </Head>

            {/* Page Hero */}
            <section className="relative bg-gradient-to-br from-sky-600 to-sky-800 py-24 md:py-32">
                <div className="absolute inset-0 overflow-hidden">
                    <div className="absolute top-0 left-0 w-96 h-96 bg-sky-400/20 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2" />
                    <div className="absolute bottom-0 right-0 w-96 h-96 bg-amber-400/10 rounded-full blur-3xl translate-x-1/2 translate-y-1/2" />
                </div>

                <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h1 className="text-4xl md:text-5xl font-bold text-white mb-4">Contact Us</h1>
                    <p className="text-lg text-sky-100">We value your opinion. Please give your feedback.</p>

                    {/* Breadcrumb */}
                    <nav className="flex items-center justify-center gap-2 mt-6 text-sm text-sky-200">
                        <Link href={route('public.home')} className="hover:text-white transition-colors flex items-center gap-1">
                            <Home className="w-4 h-4" />
                            Home
                        </Link>
                        <ChevronRight className="w-4 h-4" />
                        <span className="text-amber-300 font-medium">Contact Us</span>
                    </nav>
                </div>
            </section>

            {/* Contact Section */}
            <section className="py-16 md:py-24">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Intro */}
                    <div className="max-w-2xl mx-auto text-center mb-16">
                        <h2 className="text-3xl font-bold text-sky-800 mb-4">Get In Touch</h2>
                        <p className="text-lg text-slate-600">
                            Have questions about our ferry services? Need help with a booking?
                            We're here to help. Fill out the form below or contact us directly.
                        </p>
                    </div>

                    <div className="grid lg:grid-cols-2 gap-12">
                        {/* Contact Form */}
                        <div className="bg-white p-8 md:p-10 rounded-3xl shadow-xl border border-sky-100">
                            <h3 className="text-2xl font-bold text-slate-800 mb-8">Send Us a Message</h3>

                            {/* Success Message */}
                            {flash?.success && (
                                <div className="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-center gap-3">
                                    <CheckCircle className="w-5 h-5 text-green-600" />
                                    <span className="text-green-700">{flash.success}</span>
                                </div>
                            )}

                            {/* Error Message */}
                            {error && (
                                <div className="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 flex items-center gap-3">
                                    <AlertCircle className="w-5 h-5 text-red-600" />
                                    <span className="text-red-700">{error}</span>
                                </div>
                            )}

                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div>
                                    <label className="block text-sm font-medium text-slate-700 mb-2">
                                        Your Name *
                                    </label>
                                    <input
                                        type="text"
                                        value={formData.name}
                                        onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                                        className="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-sky-500 focus:ring-0 transition-colors"
                                        placeholder="Enter your full name"
                                        required
                                    />
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-slate-700 mb-2">
                                        Email Address *
                                    </label>
                                    <input
                                        type="email"
                                        value={formData.email}
                                        onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                                        className="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-sky-500 focus:ring-0 transition-colors"
                                        placeholder="Enter your email address"
                                        required
                                    />
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-slate-700 mb-2">
                                        Phone Number
                                    </label>
                                    <input
                                        type="tel"
                                        value={formData.phone}
                                        onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                                        className="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-sky-500 focus:ring-0 transition-colors"
                                        placeholder="Enter your phone number"
                                    />
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-slate-700 mb-2">
                                        Your Message *
                                    </label>
                                    <textarea
                                        value={formData.message}
                                        onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                                        rows={5}
                                        className="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-sky-500 focus:ring-0 transition-colors resize-none"
                                        placeholder="How can we help you?"
                                        required
                                    />
                                </div>

                                {/* Captcha */}
                                <div className="flex items-center gap-4">
                                    <div className="px-5 py-3 rounded-xl bg-sky-50 font-bold text-sky-700">
                                        {captcha.num1} + {captcha.num2} = ?
                                    </div>
                                    <input
                                        type="text"
                                        value={captchaInput}
                                        onChange={(e) => setCaptchaInput(e.target.value)}
                                        className="w-20 px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-sky-500 focus:ring-0 text-center font-bold"
                                        placeholder="?"
                                        required
                                    />
                                </div>

                                <button
                                    type="submit"
                                    disabled={loading}
                                    className="w-full py-4 rounded-xl bg-gradient-to-r from-amber-400 to-orange-500 text-slate-900 font-bold text-lg flex items-center justify-center gap-2 hover:-translate-y-0.5 hover:shadow-lg transition-all disabled:opacity-70"
                                >
                                    {loading ? (
                                        <div className="w-5 h-5 border-2 border-slate-900/30 border-t-slate-900 rounded-full animate-spin" />
                                    ) : (
                                        <>
                                            <Send className="w-5 h-5" />
                                            <span>Send Message</span>
                                        </>
                                    )}
                                </button>
                            </form>
                        </div>

                        {/* Contact Info */}
                        <div className="space-y-6">
                            <div className="p-6 bg-sky-50 rounded-2xl flex items-start gap-5">
                                <div className="w-14 h-14 rounded-xl bg-sky-600 flex items-center justify-center flex-shrink-0">
                                    <MapPin className="w-7 h-7 text-white" />
                                </div>
                                <div>
                                    <h4 className="font-bold text-slate-800 text-lg mb-2">Head Office Address</h4>
                                    <p className="text-slate-600">Dabhol FerryBoat Jetty,</p>
                                    <p className="text-slate-600">Dapoli, Dist. Ratnagiri,</p>
                                    <p className="text-slate-600">Maharashtra - 415712</p>
                                </div>
                            </div>

                            <div className="p-6 bg-sky-50 rounded-2xl flex items-start gap-5">
                                <div className="w-14 h-14 rounded-xl bg-sky-600 flex items-center justify-center flex-shrink-0">
                                    <Phone className="w-7 h-7 text-white" />
                                </div>
                                <div>
                                    <h4 className="font-bold text-slate-800 text-lg mb-2">Phone Numbers</h4>
                                    <p className="text-slate-600">
                                        Dabhol: <a href="tel:02348248900" className="text-sky-600 hover:underline">02348-248900</a>,{' '}
                                        <a href="tel:+919767248900" className="text-sky-600 hover:underline">9767248900</a>
                                    </p>
                                    <p className="text-slate-600">
                                        Veshvi: <a href="tel:02350223300" className="text-sky-600 hover:underline">02350-223300</a>,{' '}
                                        <a href="tel:+918767980300" className="text-sky-600 hover:underline">8767980300</a>
                                    </p>
                                </div>
                            </div>

                            <div className="p-6 bg-sky-50 rounded-2xl flex items-start gap-5">
                                <div className="w-14 h-14 rounded-xl bg-sky-600 flex items-center justify-center flex-shrink-0">
                                    <Mail className="w-7 h-7 text-white" />
                                </div>
                                <div>
                                    <h4 className="font-bold text-slate-800 text-lg mb-2">Email Addresses</h4>
                                    <p>
                                        <a href="mailto:ssmsdapoli@rediffmail.com" className="text-sky-600 hover:underline">
                                            ssmsdapoli@rediffmail.com
                                        </a>
                                    </p>
                                    <p>
                                        <a href="mailto:y.mokal@rediffmail.com" className="text-sky-600 hover:underline">
                                            y.mokal@rediffmail.com
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div className="p-6 bg-sky-50 rounded-2xl flex items-start gap-5">
                                <div className="w-14 h-14 rounded-xl bg-sky-600 flex items-center justify-center flex-shrink-0">
                                    <Clock className="w-7 h-7 text-white" />
                                </div>
                                <div>
                                    <h4 className="font-bold text-slate-800 text-lg mb-2">Operating Hours</h4>
                                    <p className="text-slate-600">Monday - Sunday: 9:00 AM - 5:00 PM</p>
                                    <p className="text-slate-600">Open all 7 days of the week</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Office Contacts */}
            <section className="py-16 bg-white">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 className="text-3xl font-bold text-sky-800 text-center mb-12">
                        Route-wise Contact Numbers
                    </h2>

                    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {officeContacts.map((office) => (
                            <div
                                key={office.name}
                                className="p-6 bg-sky-50 rounded-2xl text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-300"
                            >
                                <h4 className="font-bold text-sky-700 text-lg mb-4">{office.name}</h4>
                                <p className="text-slate-600">
                                    <a href={`tel:${office.phone1.replace(/[^0-9+]/g, '')}`} className="hover:text-sky-600">
                                        {office.phone1}
                                    </a>
                                </p>
                                <p className="text-slate-600">
                                    <a href={`tel:+91${office.phone2}`} className="hover:text-sky-600">
                                        {office.phone2}
                                    </a>
                                </p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Map Section */}
            <section className="py-16 bg-sky-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 className="text-3xl font-bold text-sky-800 text-center mb-12">Our Location</h2>
                    <div className="max-w-4xl mx-auto rounded-3xl overflow-hidden shadow-xl">
                        <img
                            src="/images/carferry/misc/map.jpg"
                            alt="Ferry Service Locations Map"
                            className="w-full"
                            onError={(e) => {
                                e.target.parentElement.innerHTML = '<div class="bg-slate-200 h-96 flex items-center justify-center text-slate-500">Map image not available</div>';
                            }}
                        />
                    </div>
                </div>
            </section>
        </>
    );
}

Contact.layout = (page) => <PublicLayout>{page}</PublicLayout>;
