import { Ship, Mail, Phone, Globe, MapPin } from 'lucide-react';

export function RefundPolicy() {
    const sections = [
        {
            number: '1',
            title: 'Overview',
            content: (
                <p className="text-slate-600 leading-relaxed">
                    Suvarnadurga Shipping & Marine Services Pvt. Ltd. ("the Company") strives to provide a seamless booking experience. Bookings made through our platform may be cancelled subject to the following guidelines. Please read this policy carefully before making a booking.
                </p>
            ),
        },
        {
            number: '2',
            title: 'Cancellation by Passenger',
            content: (
                <ul className="space-y-2 text-slate-600">
                    <li className="flex items-start gap-2">
                        <span className="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-cyan-500" />
                        Cancellation requests must be made before the scheduled departure time.
                    </li>
                    <li className="flex items-start gap-2">
                        <span className="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-cyan-500" />
                        The refund amount will depend on how far in advance the cancellation is made relative to the departure time.
                    </li>
                    <li className="flex items-start gap-2">
                        <span className="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-cyan-500" />
                        Applicable processing or administrative fees may be deducted from the refund.
                    </li>
                    <li className="flex items-start gap-2">
                        <span className="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-cyan-500" />
                        Approved refunds will be credited to the original payment method within 7-10 business days.
                    </li>
                </ul>
            ),
        },
        {
            number: '3',
            title: 'Cancellation by Company',
            content: (
                <ul className="space-y-2 text-slate-600">
                    <li className="flex items-start gap-2">
                        <span className="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-orange-500" />
                        If a ferry service is cancelled due to adverse weather, safety concerns, or mechanical issues, passengers will be offered a full refund or the option to reschedule at no additional cost.
                    </li>
                    <li className="flex items-start gap-2">
                        <span className="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-orange-500" />
                        The Company reserves the right to cancel or delay any sailing for safety reasons in compliance with Maharashtra Maritime Board regulations.
                    </li>
                </ul>
            ),
        },
        {
            number: '4',
            title: 'No-Show Policy',
            content: (
                <p className="text-slate-600 leading-relaxed">
                    Passengers who fail to board the ferry at the scheduled departure time without prior cancellation will be considered a no-show and are not eligible for a refund.
                </p>
            ),
        },
        {
            number: '5',
            title: 'Refund Process',
            content: (
                <ul className="space-y-2 text-slate-600">
                    <li className="flex items-start gap-2">
                        <span className="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-cyan-500" />
                        Submit your refund request via email or phone using the contact details provided below.
                    </li>
                    <li className="flex items-start gap-2">
                        <span className="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-cyan-500" />
                        Please include your Booking ID, passenger name, and reason for cancellation.
                    </li>
                    <li className="flex items-start gap-2">
                        <span className="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-cyan-500" />
                        Refunds are typically processed within 7-10 business days from the date of approval.
                    </li>
                </ul>
            ),
        },
        {
            number: '6',
            title: 'Non-Refundable Items',
            content: (
                <p className="text-slate-600 leading-relaxed">
                    Convenience fees and payment gateway charges incurred during the booking process may not be refundable. Any applicable taxes will be refunded in accordance with prevailing regulations.
                </p>
            ),
        },
    ];

    return (
        <div className="overflow-hidden">
            {/* Hero Section */}
            <section className="relative py-20 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-700">
                <div className="container mx-auto px-4 relative z-10">
                    <div className="text-center text-white">
                        <span className="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium mb-6">
                            <Ship className="w-4 h-4" />
                            Policy
                        </span>
                        <h1 className="text-4xl md:text-5xl font-bold mb-4">Refund & Cancellation Policy</h1>
                        <p className="text-lg text-slate-300 max-w-2xl mx-auto">
                            Suvarnadurga Shipping & Marine Services Pvt. Ltd.
                        </p>
                    </div>
                </div>
            </section>

            {/* Policy Content */}
            <section className="py-16 bg-slate-50">
                <div className="container mx-auto px-4">
                    <div className="max-w-3xl mx-auto space-y-6">
                        {sections.map((section) => (
                            <div
                                key={section.number}
                                className="bg-white rounded-2xl p-6 shadow-md shadow-slate-900/5 border border-slate-100"
                            >
                                <div className="flex items-center gap-3 mb-4">
                                    <span className="flex-shrink-0 w-8 h-8 rounded-lg bg-cyan-500 text-white text-sm font-bold flex items-center justify-center">
                                        {section.number}
                                    </span>
                                    <h2 className="text-xl font-bold text-slate-800">{section.title}</h2>
                                </div>
                                {section.content}
                            </div>
                        ))}

                        {/* Contact for Refunds */}
                        <div className="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-xl">
                            <h2 className="text-xl font-bold mb-4">Contact for Refunds</h2>
                            <p className="text-slate-300 mb-5 text-sm">
                                For refund requests or queries regarding this policy, please reach out to us:
                            </p>
                            <div className="space-y-3 text-sm">
                                <div className="flex items-start gap-3">
                                    <MapPin className="w-4 h-4 text-orange-400 mt-0.5 flex-shrink-0" />
                                    <span className="text-slate-300">Dabhol FerryBoat Jetty, Dapoli, Dist. Ratnagiri, Maharashtra - 415712</span>
                                </div>
                                <div className="flex items-center gap-3">
                                    <Mail className="w-4 h-4 text-orange-400 flex-shrink-0" />
                                    <a href="mailto:ssmsdapoli@rediffmail.com" className="text-cyan-400 hover:text-cyan-300 transition-colors">
                                        ssmsdapoli@rediffmail.com
                                    </a>
                                </div>
                                <div className="flex items-center gap-3">
                                    <Phone className="w-4 h-4 text-orange-400 flex-shrink-0" />
                                    <span className="text-slate-300">02348-248900 / 9767248900</span>
                                </div>
                                <div className="flex items-center gap-3">
                                    <Globe className="w-4 h-4 text-orange-400 flex-shrink-0" />
                                    <a href="https://carferry.online" target="_blank" rel="noopener noreferrer" className="text-cyan-400 hover:text-cyan-300 transition-colors">
                                        carferry.online
                                    </a>
                                </div>
                            </div>
                        </div>

                        {/* Last Updated Note */}
                        <p className="text-center text-sm text-slate-400 pt-2">
                            This policy is subject to change. Please check this page periodically for updates.
                        </p>
                    </div>
                </div>
            </section>
        </div>
    );
}
