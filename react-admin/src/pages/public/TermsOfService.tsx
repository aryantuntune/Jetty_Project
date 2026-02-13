import { Link } from 'react-router-dom';
import { Ship, FileText, UserCheck, Ticket, CreditCard, XCircle, ShieldCheck, Anchor, Scale, Globe, Gavel, Mail, Phone, MapPin } from 'lucide-react';

const LAST_UPDATED = 'February 13, 2026';

interface Section {
    id: string;
    icon: React.ElementType;
    title: string;
    content: React.ReactNode;
}

export function TermsOfService() {
    const sections: Section[] = [
        {
            id: 'acceptance',
            icon: FileText,
            title: '1. Acceptance of Terms',
            content: (
                <div className="space-y-3">
                    <p>
                        By accessing or using the website{' '}
                        <a
                            href="https://carferry.online"
                            target="_blank"
                            rel="noopener noreferrer"
                            className="text-cyan-600 hover:text-cyan-700 underline underline-offset-2"
                        >
                            carferry.online
                        </a>{' '}
                        and its associated services (collectively, the "Platform"), you agree to be bound by these Terms of Service
                        ("Terms"). These Terms constitute a legally binding agreement between you ("User", "Passenger", or "you")
                        and Suvarnadurga Shipping &amp; Marine Services Pvt. Ltd. ("Company", "we", "us", or "our").
                    </p>
                    <p>
                        If you do not agree to these Terms, you must not access or use the Platform. Your continued use of the
                        Platform after any modifications to these Terms constitutes your acceptance of the revised Terms. We
                        reserve the right to update or modify these Terms at any time without prior notice. It is your
                        responsibility to review these Terms periodically.
                    </p>
                </div>
            ),
        },
        {
            id: 'service-description',
            icon: Ship,
            title: '2. Service Description',
            content: (
                <div className="space-y-3">
                    <p>
                        Suvarnadurga Shipping &amp; Marine Services Pvt. Ltd. operates ferry and boat transportation services
                        along the Maharashtra coastline, approved and regulated by the Maharashtra Maritime Board. The Platform
                        provides an online ticket booking system for our ferry services.
                    </p>
                    <p className="font-medium text-slate-800">Our currently operated routes include:</p>
                    <ul className="grid sm:grid-cols-2 gap-2">
                        {[
                            'Dabhol \u2013 Dhopave (operational since 2003)',
                            'Veshvi \u2013 Bagmandale (operational since 2007)',
                            'Jaigad \u2013 Tawsal',
                            'Dighi \u2013 Agardande',
                            'Ambet \u2013 Mahpral',
                            'Vasai \u2013 Bhayander (Sagarmala Project)',
                            'Virar \u2013 Saphale (Jalsar) RORO Service',
                        ].map((route) => (
                            <li key={route} className="flex items-start gap-2">
                                <Anchor className="w-4 h-4 text-cyan-500 mt-1 flex-shrink-0" />
                                <span>{route}</span>
                            </li>
                        ))}
                    </ul>
                    <p>
                        The Platform allows users to search for available ferry schedules, book tickets online, make payments,
                        and receive digital tickets with QR codes for boarding verification. Services may be expanded, modified,
                        or discontinued at the sole discretion of the Company.
                    </p>
                </div>
            ),
        },
        {
            id: 'user-accounts',
            icon: UserCheck,
            title: '3. User Accounts',
            content: (
                <div className="space-y-3">
                    <p>
                        To access certain features of the Platform, including ticket booking, you may be required to create a
                        user account. By creating an account, you agree to the following:
                    </p>
                    <ul className="space-y-2">
                        {[
                            'You must provide accurate, current, and complete information during registration, including your full legal name, valid email address, and phone number.',
                            'You are responsible for maintaining the confidentiality of your account credentials (username and password) and for all activities that occur under your account.',
                            'You must immediately notify us of any unauthorized use of your account or any other breach of security.',
                            'You must not create multiple accounts for fraudulent purposes or share your account with others.',
                            'The Company reserves the right to suspend or terminate your account if any information provided is found to be inaccurate, misleading, or in violation of these Terms.',
                            'You must be at least 18 years of age to create an account. Minors may use the Platform only under the supervision of a parent or legal guardian.',
                        ].map((item, idx) => (
                            <li key={idx} className="flex items-start gap-2">
                                <span className="w-5 h-5 rounded-full bg-cyan-100 text-cyan-700 flex items-center justify-center flex-shrink-0 text-xs font-bold mt-0.5">
                                    {idx + 1}
                                </span>
                                <span>{item}</span>
                            </li>
                        ))}
                    </ul>
                </div>
            ),
        },
        {
            id: 'booking-tickets',
            icon: Ticket,
            title: '4. Booking & Tickets',
            content: (
                <div className="space-y-3">
                    <p>
                        All bookings made through the Platform are subject to availability and confirmation. The following
                        conditions apply to all ticket bookings:
                    </p>
                    <ul className="space-y-2">
                        {[
                            'Tickets are valid only for the specific date, time, route, and vessel indicated on the booking confirmation.',
                            'Each ticket includes a unique QR code that must be presented at the time of boarding for verification. Failure to produce a valid QR code may result in denial of boarding.',
                            'Tickets are non-transferable and are issued in the name of the passenger. The name on the ticket must match a valid government-issued photo identification document.',
                            'Duplicate or counterfeit tickets will be confiscated, and the holder may be subject to legal action.',
                            'A booking confirmation will be sent to your registered email address and phone number. It is your responsibility to verify the accuracy of all booking details upon receipt.',
                            'The Company reserves the right to cancel or modify bookings in cases of operational necessity, including but not limited to adverse weather conditions, vessel maintenance, or regulatory directives.',
                        ].map((item, idx) => (
                            <li key={idx} className="flex items-start gap-2">
                                <span className="w-1.5 h-1.5 rounded-full bg-orange-500 mt-2.5 flex-shrink-0" />
                                <span>{item}</span>
                            </li>
                        ))}
                    </ul>
                </div>
            ),
        },
        {
            id: 'payment-terms',
            icon: CreditCard,
            title: '5. Payment Terms',
            content: (
                <div className="space-y-3">
                    <p>
                        All payments for ticket bookings on the Platform are processed securely through Razorpay, a
                        PCI-DSS compliant payment gateway. By making a payment, you agree to the following:
                    </p>
                    <ul className="space-y-2">
                        {[
                            'All prices displayed on the Platform are in Indian Rupees (INR) and include applicable taxes (GST) unless explicitly stated otherwise.',
                            'The Company accepts payments via UPI, debit cards, credit cards, net banking, and digital wallets as supported by Razorpay.',
                            'Payment must be completed at the time of booking. A booking is confirmed only upon successful receipt of payment.',
                            'The Company is not responsible for any transaction failures, delays, or errors caused by the payment gateway, your bank, or network issues.',
                            'Transaction receipts and invoices will be sent to your registered email address. All invoices will bear the Company\'s GST number: 27AAICS4116Q1ZU.',
                            'Pricing is subject to change without prior notice. The price applicable at the time of booking confirmation shall prevail.',
                        ].map((item, idx) => (
                            <li key={idx} className="flex items-start gap-2">
                                <span className="w-1.5 h-1.5 rounded-full bg-orange-500 mt-2.5 flex-shrink-0" />
                                <span>{item}</span>
                            </li>
                        ))}
                    </ul>
                </div>
            ),
        },
        {
            id: 'cancellation-policy',
            icon: XCircle,
            title: '6. Cancellation & Refund Policy',
            content: (
                <div className="space-y-3">
                    <p>
                        Cancellations and refund requests are governed by our dedicated Cancellation &amp; Refund Policy.
                        Passengers are encouraged to review the full policy before making a booking.
                    </p>
                    <p>
                        For complete details regarding cancellation timelines, applicable charges, and refund processing,
                        please visit our{' '}
                        <Link
                            to="/refund-policy"
                            className="text-cyan-600 hover:text-cyan-700 underline underline-offset-2 font-medium"
                        >
                            Cancellation &amp; Refund Policy
                        </Link>{' '}
                        page.
                    </p>
                    <div className="bg-orange-50 border border-orange-200 rounded-lg p-4 mt-2">
                        <p className="text-orange-800 text-sm">
                            <strong>Please note:</strong> Refunds for cancellations made by the Company due to operational
                            reasons (weather, mechanical issues, regulatory orders) will be processed in full without any
                            deduction. Passenger-initiated cancellations may be subject to cancellation fees as outlined in the
                            Refund Policy.
                        </p>
                    </div>
                </div>
            ),
        },
        {
            id: 'passenger-responsibilities',
            icon: ShieldCheck,
            title: '7. Passenger Responsibilities',
            content: (
                <div className="space-y-3">
                    <p>
                        All passengers using our ferry services are expected to comply with the following responsibilities to
                        ensure a safe and pleasant journey for everyone:
                    </p>
                    <ul className="space-y-2">
                        {[
                            'Carry a valid government-issued photo identification document (Aadhaar Card, PAN Card, Driving License, Passport, Voter ID, or any other government-issued ID) at all times during the journey.',
                            'Arrive at the departure jetty at least 15 minutes before the scheduled departure time. The Company is not obligated to delay departures for late-arriving passengers.',
                            'Follow all safety instructions given by the crew, including the wearing of life jackets when directed, remaining seated during the voyage, and adhering to designated passenger areas.',
                            'Do not carry prohibited or dangerous items on board, including but not limited to: explosives, flammable substances, firearms, sharp weapons, and any items restricted by Indian maritime law.',
                            'Do not consume alcohol or any intoxicating substances on board. Passengers found to be under the influence may be denied boarding or disembarked.',
                            'Supervise minor children at all times. The Company is not responsible for unattended minors.',
                            'Do not engage in any behaviour that may endanger the safety of the vessel, crew, or fellow passengers. The Captain reserves the right to refuse service or disembark any passenger whose conduct is deemed unsafe or disruptive.',
                        ].map((item, idx) => (
                            <li key={idx} className="flex items-start gap-2">
                                <span className="w-1.5 h-1.5 rounded-full bg-cyan-500 mt-2.5 flex-shrink-0" />
                                <span>{item}</span>
                            </li>
                        ))}
                    </ul>
                </div>
            ),
        },
        {
            id: 'ferry-operations',
            icon: Anchor,
            title: '8. Ferry Operations & Schedules',
            content: (
                <div className="space-y-3">
                    <p>
                        While the Company endeavors to maintain published schedules, ferry operations are inherently subject to
                        factors beyond our control. By using our services, you acknowledge and accept the following:
                    </p>
                    <ul className="space-y-2">
                        {[
                            'Ferry schedules, routes, and frequencies are subject to change without prior notice due to weather conditions (monsoons, storms, high winds, poor visibility), tidal variations, sea conditions, or other natural causes.',
                            'The Company shall not be held liable for any delays, cancellations, or diversions caused by adverse weather, natural disasters, government orders, regulatory directives, port authority instructions, or any other force majeure events.',
                            'The Captain of the vessel has absolute authority and discretion regarding the safe operation of the ferry, including decisions to delay departure, alter the route, or cancel a trip.',
                            'During the monsoon season (typically June to September), ferry services may be suspended on certain routes as per Maharashtra Maritime Board directives. Seasonal schedule changes will be communicated on the Platform where possible.',
                            'Vehicle ferry (RORO) services are subject to vessel capacity limitations. Vehicle bookings are on a first-come, first-served basis, and overbooking of vehicle slots is not guaranteed even with advance booking.',
                        ].map((item, idx) => (
                            <li key={idx} className="flex items-start gap-2">
                                <span className="w-1.5 h-1.5 rounded-full bg-cyan-500 mt-2.5 flex-shrink-0" />
                                <span>{item}</span>
                            </li>
                        ))}
                    </ul>
                </div>
            ),
        },
        {
            id: 'liability',
            icon: Scale,
            title: '9. Limitation of Liability',
            content: (
                <div className="space-y-3">
                    <p>
                        Suvarnadurga Shipping &amp; Marine Services Pvt. Ltd. operates all ferry services in compliance with the
                        regulations of the Maharashtra Maritime Board and maintains proper insurance coverage for its vessels
                        and operations. However, the following limitations of liability apply:
                    </p>
                    <ul className="space-y-2">
                        {[
                            'The Company shall not be liable for any loss, damage, or theft of personal belongings, luggage, or valuables belonging to passengers during the voyage. Passengers are advised to keep their belongings secure at all times.',
                            'The Company shall not be liable for any indirect, incidental, consequential, or punitive damages arising from the use of the Platform or our ferry services, including but not limited to missed connections, business losses, or travel disruptions.',
                            'The Company\'s total liability for any claim arising out of or related to the use of the Platform or services shall not exceed the amount paid by the passenger for the specific ticket in question.',
                            'The Company shall not be liable for any damages resulting from unauthorized access to or alteration of your data, transmissions, or content.',
                            'All vessels are subject to regular safety inspections as mandated by the Maharashtra Maritime Board. The Company maintains all required certifications and insurance policies.',
                        ].map((item, idx) => (
                            <li key={idx} className="flex items-start gap-2">
                                <span className="w-1.5 h-1.5 rounded-full bg-orange-500 mt-2.5 flex-shrink-0" />
                                <span>{item}</span>
                            </li>
                        ))}
                    </ul>
                </div>
            ),
        },
        {
            id: 'intellectual-property',
            icon: Globe,
            title: '10. Intellectual Property',
            content: (
                <div className="space-y-3">
                    <p>
                        All content on the Platform, including but not limited to text, graphics, logos, images, icons, audio
                        clips, digital downloads, data compilations, software, and the overall design and layout of the website,
                        is the exclusive property of Suvarnadurga Shipping &amp; Marine Services Pvt. Ltd. and is protected by
                        Indian and international copyright, trademark, and intellectual property laws.
                    </p>
                    <ul className="space-y-2">
                        {[
                            'You may not reproduce, distribute, modify, create derivative works from, publicly display, or commercially exploit any content from the Platform without prior written consent from the Company.',
                            'The Company name, logo, and all related trademarks, service marks, and trade names are the property of Suvarnadurga Shipping & Marine Services Pvt. Ltd.',
                            'Any unauthorized use of the Platform\'s content may violate copyright laws, trademark laws, privacy laws, and other applicable regulations and could result in legal action.',
                        ].map((item, idx) => (
                            <li key={idx} className="flex items-start gap-2">
                                <span className="w-1.5 h-1.5 rounded-full bg-orange-500 mt-2.5 flex-shrink-0" />
                                <span>{item}</span>
                            </li>
                        ))}
                    </ul>
                </div>
            ),
        },
        {
            id: 'governing-law',
            icon: Gavel,
            title: '11. Governing Law & Jurisdiction',
            content: (
                <div className="space-y-3">
                    <p>
                        These Terms shall be governed by and construed in accordance with the laws of the Republic of India,
                        without regard to its conflict of law principles. The following provisions apply:
                    </p>
                    <ul className="space-y-2">
                        {[
                            'Any disputes, claims, or controversies arising out of or relating to these Terms or the use of the Platform shall be subject to the exclusive jurisdiction of the courts located in Ratnagiri District, Maharashtra, India.',
                            'The Company operates in compliance with all applicable Indian laws, including but not limited to the Information Technology Act, 2000, the Consumer Protection Act, 2019, the Indian Contract Act, 1872, and the Merchant Shipping Act, 1958.',
                            'These Terms are additionally governed by the rules and regulations of the Maharashtra Maritime Board as applicable to passenger and vehicle ferry services.',
                            'If any provision of these Terms is found to be invalid or unenforceable by a court of competent jurisdiction, the remaining provisions shall continue in full force and effect.',
                        ].map((item, idx) => (
                            <li key={idx} className="flex items-start gap-2">
                                <span className="w-1.5 h-1.5 rounded-full bg-cyan-500 mt-2.5 flex-shrink-0" />
                                <span>{item}</span>
                            </li>
                        ))}
                    </ul>
                </div>
            ),
        },
    ];

    return (
        <div className="overflow-hidden">
            {/* Hero Section */}
            <section className="relative py-20 bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800">
                <div className="absolute inset-0 opacity-10">
                    <div className="absolute inset-0" style={{
                        backgroundImage: 'radial-gradient(circle at 25% 25%, rgba(6,182,212,0.3) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(249,115,22,0.2) 0%, transparent 50%)',
                    }} />
                </div>

                <div className="container mx-auto px-4 relative z-10">
                    <div className="text-center text-white">
                        <span className="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium mb-6">
                            <Ship className="w-4 h-4" />
                            Legal
                        </span>
                        <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">Terms of Service</h1>
                        <p className="text-xl text-slate-300 max-w-2xl mx-auto">
                            Please read these terms carefully before using our ferry booking services.
                        </p>
                        <p className="text-sm text-slate-400 mt-4">
                            Last updated: {LAST_UPDATED}
                        </p>
                    </div>
                </div>
            </section>

            {/* Company Info Bar */}
            <section className="relative -mt-6 z-20 pb-8">
                <div className="container mx-auto px-4">
                    <div className="bg-white rounded-2xl shadow-xl shadow-slate-900/10 p-6">
                        <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span className="text-slate-500 block">Company</span>
                                <span className="font-semibold text-slate-800">Suvarnadurga Shipping &amp; Marine Services Pvt. Ltd.</span>
                            </div>
                            <div>
                                <span className="text-slate-500 block">CIN</span>
                                <span className="font-semibold text-slate-800">U61100MH2000PTC124043</span>
                            </div>
                            <div>
                                <span className="text-slate-500 block">GST</span>
                                <span className="font-semibold text-slate-800">27AAICS4116Q1ZU</span>
                            </div>
                            <div>
                                <span className="text-slate-500 block">Website</span>
                                <a
                                    href="https://carferry.online"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="font-semibold text-cyan-600 hover:text-cyan-700"
                                >
                                    carferry.online
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Table of Contents */}
            <section className="pb-8">
                <div className="container mx-auto px-4">
                    <div className="max-w-4xl mx-auto">
                        <div className="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                            <h2 className="text-lg font-bold text-slate-800 mb-4">Table of Contents</h2>
                            <nav className="grid sm:grid-cols-2 gap-2">
                                {sections.map((section) => {
                                    const Icon = section.icon;
                                    return (
                                        <a
                                            key={section.id}
                                            href={`#${section.id}`}
                                            className="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-600 hover:text-cyan-700 hover:bg-cyan-50 transition-colors text-sm"
                                        >
                                            <Icon className="w-4 h-4 text-cyan-500 flex-shrink-0" />
                                            <span>{section.title}</span>
                                        </a>
                                    );
                                })}
                                <a
                                    href="#contact"
                                    className="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-600 hover:text-cyan-700 hover:bg-cyan-50 transition-colors text-sm"
                                >
                                    <Mail className="w-4 h-4 text-cyan-500 flex-shrink-0" />
                                    <span>12. Contact Information</span>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
            </section>

            {/* Sections */}
            <section className="pb-16">
                <div className="container mx-auto px-4">
                    <div className="max-w-4xl mx-auto space-y-8">
                        {sections.map((section) => {
                            const Icon = section.icon;
                            return (
                                <article
                                    key={section.id}
                                    id={section.id}
                                    className="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow scroll-mt-8"
                                >
                                    <div className="p-6 sm:p-8">
                                        <div className="flex items-center gap-3 mb-5">
                                            <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-cyan-600 flex items-center justify-center flex-shrink-0">
                                                <Icon className="w-5 h-5 text-white" />
                                            </div>
                                            <h2 className="text-xl sm:text-2xl font-bold text-slate-800">{section.title}</h2>
                                        </div>
                                        <div className="text-slate-600 leading-relaxed">
                                            {section.content}
                                        </div>
                                    </div>
                                </article>
                            );
                        })}

                        {/* Contact Information Section */}
                        <article
                            id="contact"
                            className="bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 rounded-2xl shadow-xl text-white scroll-mt-8"
                        >
                            <div className="p-6 sm:p-8">
                                <div className="flex items-center gap-3 mb-5">
                                    <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0">
                                        <Mail className="w-5 h-5 text-white" />
                                    </div>
                                    <h2 className="text-xl sm:text-2xl font-bold">12. Contact Information</h2>
                                </div>
                                <p className="text-slate-300 mb-6 leading-relaxed">
                                    If you have any questions, concerns, or grievances regarding these Terms of Service, please
                                    contact us using the information below:
                                </p>
                                <div className="grid sm:grid-cols-2 gap-6">
                                    <div className="space-y-4">
                                        <div className="flex items-start gap-3">
                                            <div className="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <Ship className="w-4 h-4 text-cyan-400" />
                                            </div>
                                            <div>
                                                <p className="text-sm text-slate-400">Company</p>
                                                <p className="font-medium">Suvarnadurga Shipping &amp; Marine Services Pvt. Ltd.</p>
                                            </div>
                                        </div>
                                        <div className="flex items-start gap-3">
                                            <div className="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <MapPin className="w-4 h-4 text-cyan-400" />
                                            </div>
                                            <div>
                                                <p className="text-sm text-slate-400">Registered Address</p>
                                                <p className="font-medium">Dabhol FerryBoat Jetty, Dapoli,</p>
                                                <p className="font-medium">Dist. Ratnagiri, Maharashtra - 415712</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="space-y-4">
                                        <div className="flex items-start gap-3">
                                            <div className="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <Mail className="w-4 h-4 text-orange-400" />
                                            </div>
                                            <div>
                                                <p className="text-sm text-slate-400">Email</p>
                                                <a
                                                    href="mailto:ssmsdapoli@rediffmail.com"
                                                    className="font-medium text-cyan-400 hover:text-cyan-300"
                                                >
                                                    ssmsdapoli@rediffmail.com
                                                </a>
                                            </div>
                                        </div>
                                        <div className="flex items-start gap-3">
                                            <div className="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <Phone className="w-4 h-4 text-orange-400" />
                                            </div>
                                            <div>
                                                <p className="text-sm text-slate-400">Phone</p>
                                                <p className="font-medium">02348-248900</p>
                                                <p className="font-medium">9767248900</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>

                        {/* Bottom Notice */}
                        <div className="bg-cyan-50 border border-cyan-200 rounded-2xl p-6 text-center">
                            <p className="text-slate-700">
                                By using our Platform and services, you acknowledge that you have read, understood, and agree
                                to be bound by these Terms of Service.
                            </p>
                            <div className="flex flex-wrap items-center justify-center gap-4 mt-4">
                                <Link
                                    to="/refund-policy"
                                    className="text-cyan-600 hover:text-cyan-700 underline underline-offset-2 font-medium text-sm"
                                >
                                    Refund Policy
                                </Link>
                                <span className="text-slate-300">|</span>
                                <Link
                                    to="/privacy-policy"
                                    className="text-cyan-600 hover:text-cyan-700 underline underline-offset-2 font-medium text-sm"
                                >
                                    Privacy Policy
                                </Link>
                                <span className="text-slate-300">|</span>
                                <Link
                                    to="/contact"
                                    className="text-cyan-600 hover:text-cyan-700 underline underline-offset-2 font-medium text-sm"
                                >
                                    Contact Us
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    );
}
