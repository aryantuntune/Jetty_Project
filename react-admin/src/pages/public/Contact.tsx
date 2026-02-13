import { useState } from 'react';
import { Card, Button, Input } from '@/components/ui';
import { Phone, Mail, MapPin, Clock, Send, MessageCircle } from 'lucide-react';
import { toast } from 'sonner';

// Import background image
import waterRipplesImg from '@/assets/water_ripples.jpg';

export function Contact() {
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        phone: '',
        subject: '',
        message: '',
    });
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);

        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000));

        toast.success('Message sent successfully! We will get back to you soon.');
        setFormData({ name: '', email: '', phone: '', subject: '', message: '' });
        setIsSubmitting(false);
    };

    const contactInfo = [
        {
            icon: Phone,
            title: 'Phone',
            details: ['+91 02348-248900', '+91 9767248900'],
            color: 'bg-cyan-500',
        },
        {
            icon: Mail,
            title: 'Email',
            details: ['ssmsdapoli@rediffmail.com'],
            color: 'bg-orange-500',
        },
        {
            icon: MapPin,
            title: 'Head Office',
            details: ['Dabhol FerryBoat Jetty, Dapoli', 'Dist. Ratnagiri, Maharashtra - 415712'],
            color: 'bg-cyan-600',
        },
        {
            icon: Clock,
            title: 'Working Hours',
            details: ['Monday - Sunday', '6:00 AM - 8:00 PM'],
            color: 'bg-orange-600',
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
                            <MessageCircle className="w-4 h-4" />
                            Contact Us
                        </span>
                        <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">Get in Touch</h1>
                        <p className="text-xl text-slate-200 max-w-2xl mx-auto">
                            Have questions about our ferry services? We're here to help!
                        </p>
                    </div>
                </div>
            </section>

            {/* Contact Cards */}
            <section className="relative -mt-8 z-20 pb-12">
                <div className="container mx-auto px-4">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        {contactInfo.map((info) => {
                            const Icon = info.icon;
                            return (
                                <div key={info.title} className="bg-white rounded-2xl p-6 shadow-xl shadow-slate-900/10 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                                    <div className={`w-12 h-12 rounded-xl ${info.color} flex items-center justify-center mb-4`}>
                                        <Icon className="w-6 h-6 text-white" />
                                    </div>
                                    <h3 className="font-bold text-lg mb-2 text-slate-800">{info.title}</h3>
                                    {info.details.map((detail, idx) => (
                                        <p key={idx} className="text-slate-600">{detail}</p>
                                    ))}
                                </div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* Form & Map Section */}
            <section className="py-12 bg-slate-50">
                <div className="container mx-auto px-4">
                    <div className="grid lg:grid-cols-2 gap-8">
                        {/* Contact Form */}
                        <Card className="p-8 border-0 shadow-xl">
                            <h2 className="text-2xl font-bold mb-6 text-slate-800">Send us a Message</h2>

                            <form onSubmit={handleSubmit} className="space-y-5">
                                <div className="grid md:grid-cols-2 gap-5">
                                    <div>
                                        <label className="block text-sm font-medium mb-2 text-slate-700">
                                            Your Name <span className="text-orange-500">*</span>
                                        </label>
                                        <Input
                                            value={formData.name}
                                            onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                                            placeholder="John Doe"
                                            required
                                            className="border-slate-200 focus:border-cyan-500 focus:ring-cyan-500"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium mb-2 text-slate-700">
                                            Email Address <span className="text-orange-500">*</span>
                                        </label>
                                        <Input
                                            type="email"
                                            value={formData.email}
                                            onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                                            placeholder="john@example.com"
                                            required
                                            className="border-slate-200 focus:border-cyan-500 focus:ring-cyan-500"
                                        />
                                    </div>
                                </div>

                                <div className="grid md:grid-cols-2 gap-5">
                                    <div>
                                        <label className="block text-sm font-medium mb-2 text-slate-700">Phone Number</label>
                                        <Input
                                            type="tel"
                                            value={formData.phone}
                                            onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                                            placeholder="+91 98765 43210"
                                            className="border-slate-200 focus:border-cyan-500 focus:ring-cyan-500"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium mb-2 text-slate-700">Subject</label>
                                        <Input
                                            value={formData.subject}
                                            onChange={(e) => setFormData({ ...formData, subject: e.target.value })}
                                            placeholder="Booking inquiry"
                                            className="border-slate-200 focus:border-cyan-500 focus:ring-cyan-500"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-sm font-medium mb-2 text-slate-700">
                                        Message <span className="text-orange-500">*</span>
                                    </label>
                                    <textarea
                                        value={formData.message}
                                        onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                                        placeholder="How can we help you?"
                                        rows={5}
                                        required
                                        className="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none resize-none"
                                    />
                                </div>

                                <Button
                                    type="submit"
                                    size="lg"
                                    className="w-full bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-600 hover:to-cyan-700 text-white shadow-lg shadow-cyan-500/30"
                                    disabled={isSubmitting}
                                >
                                    {isSubmitting ? (
                                        'Sending...'
                                    ) : (
                                        <>
                                            <Send className="w-4 h-4 mr-2" />
                                            Send Message
                                        </>
                                    )}
                                </Button>
                            </form>
                        </Card>

                        {/* Map & Additional Info */}
                        <div className="space-y-6">
                            {/* Map Placeholder */}
                            <Card className="h-80 overflow-hidden border-0 shadow-xl">
                                <div className="w-full h-full bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center">
                                    <div className="text-center text-slate-500">
                                        <div className="w-16 h-16 mx-auto mb-3 bg-slate-400/30 rounded-full flex items-center justify-center">
                                            <MapPin className="w-8 h-8 text-slate-500" />
                                        </div>
                                        <p className="font-medium text-slate-600">Map will be displayed here</p>
                                        <p className="text-sm">Dabhol FerryBoat Jetty, Dapoli</p>
                                    </div>
                                </div>
                            </Card>

                            {/* WhatsApp Contact */}
                            <Card className="p-6 border-0 shadow-xl bg-gradient-to-r from-green-500 to-green-600 text-white">
                                <div className="flex items-center gap-4">
                                    <div className="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                                        <MessageCircle className="w-7 h-7" />
                                    </div>
                                    <div className="flex-1">
                                        <h3 className="font-bold text-lg">WhatsApp Support</h3>
                                        <p className="text-green-100">Quick response via WhatsApp</p>
                                    </div>
                                    <Button className="bg-white text-green-600 hover:bg-green-50">
                                        Chat Now
                                    </Button>
                                </div>
                            </Card>

                            {/* Emergency Contact */}
                            <Card className="p-6 border-0 shadow-xl bg-gradient-to-r from-orange-500 to-orange-600 text-white">
                                <h3 className="font-bold text-lg mb-2">Emergency Contact</h3>
                                <p className="text-orange-100 mb-2">
                                    For urgent matters or emergencies:
                                </p>
                                <p className="text-2xl font-bold">+91 9767248900</p>
                            </Card>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    );
}
