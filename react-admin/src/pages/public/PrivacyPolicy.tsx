import { Ship, Shield, Lock, Eye, Cookie, UserCheck, Baby, Bell, Mail, Phone, MapPin, Globe } from 'lucide-react';

export function PrivacyPolicy() {
    const lastUpdated = 'February 13, 2026';

    const sections = [
        {
            id: 'information-we-collect',
            icon: Eye,
            iconColor: 'bg-cyan-500',
            title: '1. Information We Collect',
            content: (
                <div className="space-y-4">
                    <p>
                        We collect the following categories of personal information when you use our ferry booking
                        services, visit our website, or interact with us:
                    </p>
                    <div className="space-y-3">
                        <div>
                            <h4 className="font-semibold text-slate-800 mb-1">a) Personal Identification Information</h4>
                            <ul className="list-disc list-inside space-y-1 text-slate-600 ml-2">
                                <li>Full name as provided during booking</li>
                                <li>Email address</li>
                                <li>Mobile phone number</li>
                                <li>Gender and age (for passenger manifest requirements)</li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-semibold text-slate-800 mb-1">b) Booking and Transaction Information</h4>
                            <ul className="list-disc list-inside space-y-1 text-slate-600 ml-2">
                                <li>Ferry route and travel date selections</li>
                                <li>Passenger and vehicle details</li>
                                <li>Booking reference numbers</li>
                                <li>Payment transaction identifiers (processed securely via Razorpay)</li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-semibold text-slate-800 mb-1">c) Technical Information</h4>
                            <ul className="list-disc list-inside space-y-1 text-slate-600 ml-2">
                                <li>IP address and browser type</li>
                                <li>Device information and operating system</li>
                                <li>Pages visited and time spent on our website</li>
                            </ul>
                        </div>
                    </div>
                    <p className="text-sm text-slate-500 italic">
                        Note: We do not store your complete credit/debit card numbers or bank account details.
                        All payment information is handled directly by our payment processor, Razorpay.
                    </p>
                </div>
            ),
        },
        {
            id: 'how-we-use',
            icon: Shield,
            iconColor: 'bg-orange-500',
            title: '2. How We Use Your Information',
            content: (
                <div className="space-y-4">
                    <p>
                        We use your personal information for the following lawful purposes under the Information
                        Technology Act, 2000 and the Information Technology (Reasonable Security Practices and
                        Procedures and Sensitive Personal Data or Information) Rules, 2011:
                    </p>
                    <ul className="list-disc list-inside space-y-2 text-slate-600 ml-2">
                        <li>
                            <span className="font-medium text-slate-700">Booking Processing:</span> To confirm, manage, and
                            fulfill your ferry ticket reservations and vehicle bookings
                        </li>
                        <li>
                            <span className="font-medium text-slate-700">Communication:</span> To send booking confirmations,
                            travel updates, schedule changes, cancellation notices, and respond to your enquiries
                        </li>
                        <li>
                            <span className="font-medium text-slate-700">Payment Processing:</span> To process payments and
                            refunds through our payment partner Razorpay
                        </li>
                        <li>
                            <span className="font-medium text-slate-700">Service Improvement:</span> To analyse usage patterns
                            and improve our ferry services, website functionality, and customer experience
                        </li>
                        <li>
                            <span className="font-medium text-slate-700">Safety and Compliance:</span> To maintain passenger
                            manifests as required by maritime safety regulations and government authorities
                        </li>
                        <li>
                            <span className="font-medium text-slate-700">Legal Obligations:</span> To comply with applicable
                            Indian laws, regulations, and lawful government requests
                        </li>
                    </ul>
                </div>
            ),
        },
        {
            id: 'information-sharing',
            icon: UserCheck,
            iconColor: 'bg-cyan-600',
            title: '3. Information Sharing and Disclosure',
            content: (
                <div className="space-y-4">
                    <p className="font-medium text-slate-800">
                        We do not sell, rent, or trade your personal information to third parties for marketing
                        purposes.
                    </p>
                    <p>We may share your information only in the following limited circumstances:</p>
                    <ul className="list-disc list-inside space-y-2 text-slate-600 ml-2">
                        <li>
                            <span className="font-medium text-slate-700">Payment Processor:</span> Transaction details are
                            shared with Razorpay Software Private Limited for secure payment processing. Razorpay's
                            handling of your data is governed by their own privacy policy
                        </li>
                        <li>
                            <span className="font-medium text-slate-700">Government Authorities:</span> We may disclose your
                            information when required by law, court order, or government directive, including requests
                            from maritime authorities, law enforcement agencies, or regulatory bodies under the
                            Information Technology Act, 2000
                        </li>
                        <li>
                            <span className="font-medium text-slate-700">Maritime Safety:</span> Passenger manifest information
                            may be shared with port authorities and maritime safety bodies as mandated by applicable
                            regulations
                        </li>
                        <li>
                            <span className="font-medium text-slate-700">Service Providers:</span> With trusted service
                            providers who assist us in operating our website and services, subject to strict
                            confidentiality obligations
                        </li>
                    </ul>
                </div>
            ),
        },
        {
            id: 'data-security',
            icon: Lock,
            iconColor: 'bg-orange-600',
            title: '4. Data Security',
            content: (
                <div className="space-y-4">
                    <p>
                        We implement reasonable security practices and procedures as required under the Information
                        Technology (Reasonable Security Practices and Procedures and Sensitive Personal Data or
                        Information) Rules, 2011, including:
                    </p>
                    <ul className="list-disc list-inside space-y-2 text-slate-600 ml-2">
                        <li>Encrypted data transmission using SSL/TLS protocols across our website</li>
                        <li>Secure payment processing through Razorpay's PCI-DSS compliant infrastructure</li>
                        <li>Access controls restricting personal data access to authorised personnel only</li>
                        <li>Regular security assessments and updates to our systems</li>
                        <li>Secure storage of personal data with appropriate technical safeguards</li>
                    </ul>
                    <p className="text-sm text-slate-500 italic">
                        While we strive to protect your personal information, no method of transmission over the
                        Internet or electronic storage is completely secure. We cannot guarantee absolute security
                        but are committed to maintaining industry-standard protections.
                    </p>
                </div>
            ),
        },
        {
            id: 'cookies',
            icon: Cookie,
            iconColor: 'bg-cyan-500',
            title: '5. Cookies and Tracking Technologies',
            content: (
                <div className="space-y-4">
                    <p>
                        Our website uses cookies and similar tracking technologies to enhance your browsing
                        experience. These include:
                    </p>
                    <ul className="list-disc list-inside space-y-2 text-slate-600 ml-2">
                        <li>
                            <span className="font-medium text-slate-700">Essential Cookies:</span> Required for the website
                            to function properly, including session management and booking functionality
                        </li>
                        <li>
                            <span className="font-medium text-slate-700">Analytics Cookies:</span> Used to understand how
                            visitors interact with our website, helping us improve our services. We may use third-party
                            analytics tools such as Google Analytics
                        </li>
                        <li>
                            <span className="font-medium text-slate-700">Preference Cookies:</span> Remember your
                            preferences such as language and route selections for a better experience
                        </li>
                    </ul>
                    <p>
                        You can control cookie settings through your browser preferences. Disabling certain cookies
                        may affect the functionality of our booking system.
                    </p>
                </div>
            ),
        },
        {
            id: 'your-rights',
            icon: UserCheck,
            iconColor: 'bg-orange-500',
            title: '6. Your Rights',
            content: (
                <div className="space-y-4">
                    <p>
                        Under the Information Technology Act, 2000 and the SPDI Rules, 2011, you have the following
                        rights regarding your personal information:
                    </p>
                    <ul className="list-disc list-inside space-y-2 text-slate-600 ml-2">
                        <li>
                            <span className="font-medium text-slate-700">Right to Access:</span> You may request a copy
                            of the personal information we hold about you
                        </li>
                        <li>
                            <span className="font-medium text-slate-700">Right to Correction:</span> You may request
                            correction of any inaccurate or incomplete personal information
                        </li>
                        <li>
                            <span className="font-medium text-slate-700">Right to Withdrawal of Consent:</span> You may
                            withdraw your consent for the collection and use of your personal information at any time.
                            Please note that withdrawal of consent may affect our ability to provide services to you
                        </li>
                        <li>
                            <span className="font-medium text-slate-700">Right to Grievance Redressal:</span> You may
                            raise a grievance regarding the handling of your personal information by contacting our
                            Grievance Officer (details provided below)
                        </li>
                    </ul>
                    <p>
                        To exercise any of these rights, please contact us using the details provided in the
                        "Contact Us" section below. We will respond to your request within a reasonable timeframe,
                        not exceeding 30 days.
                    </p>
                </div>
            ),
        },
        {
            id: 'data-retention',
            icon: Shield,
            iconColor: 'bg-cyan-600',
            title: '7. Data Retention',
            content: (
                <div className="space-y-4">
                    <p>
                        We retain your personal information only for as long as necessary to fulfill the purposes for
                        which it was collected, including:
                    </p>
                    <ul className="list-disc list-inside space-y-2 text-slate-600 ml-2">
                        <li>Booking and transaction records are retained for a minimum of 8 years as required
                            under the Income Tax Act and GST regulations</li>
                        <li>Passenger manifest data is retained as per maritime safety regulations</li>
                        <li>Account information is retained until you request deletion or account closure</li>
                        <li>Analytics data is retained in anonymised form and does not identify individual users</li>
                    </ul>
                </div>
            ),
        },
        {
            id: 'children',
            icon: Baby,
            iconColor: 'bg-orange-600',
            title: '8. Children\'s Privacy',
            content: (
                <div className="space-y-4">
                    <p>
                        Our online booking services are not directed to children under the age of 13. We do not
                        knowingly collect personal information from children under 13 without verifiable parental
                        consent.
                    </p>
                    <p>
                        Ferry bookings for minors must be made by a parent or legal guardian. If you believe we
                        have inadvertently collected information from a child under 13 without appropriate consent,
                        please contact us immediately and we will take steps to delete such information.
                    </p>
                </div>
            ),
        },
        {
            id: 'changes',
            icon: Bell,
            iconColor: 'bg-cyan-500',
            title: '9. Changes to This Privacy Policy',
            content: (
                <div className="space-y-4">
                    <p>
                        We may update this Privacy Policy from time to time to reflect changes in our practices,
                        technology, legal requirements, or other factors. When we make material changes:
                    </p>
                    <ul className="list-disc list-inside space-y-2 text-slate-600 ml-2">
                        <li>The updated policy will be posted on this page with a revised "Last Updated" date</li>
                        <li>For significant changes, we may notify you via email or a prominent notice on our website</li>
                        <li>Your continued use of our services after the changes take effect constitutes your
                            acceptance of the revised policy</li>
                    </ul>
                    <p>
                        We encourage you to review this Privacy Policy periodically to stay informed about how we
                        protect your information.
                    </p>
                </div>
            ),
        },
    ];

    return (
        <div className="overflow-hidden">
            {/* Hero Section */}
            <section className="relative py-20 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-700">
                <div className="absolute inset-0 opacity-10">
                    <div className="absolute inset-0 bg-[radial-gradient(circle_at_30%_50%,rgba(6,182,212,0.3),transparent_50%)]"></div>
                    <div className="absolute inset-0 bg-[radial-gradient(circle_at_70%_50%,rgba(249,115,22,0.2),transparent_50%)]"></div>
                </div>

                <div className="container mx-auto px-4 relative z-10">
                    <div className="text-center text-white">
                        <span className="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium mb-6">
                            <Ship className="w-4 h-4" />
                            Legal
                        </span>
                        <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">Privacy Policy</h1>
                        <p className="text-xl text-slate-200 max-w-2xl mx-auto">
                            Your privacy is important to us. This policy explains how we collect, use, and protect
                            your personal information.
                        </p>
                        <p className="text-sm text-slate-400 mt-4">Last Updated: {lastUpdated}</p>
                    </div>
                </div>
            </section>

            {/* Introduction */}
            <section className="py-12 bg-white">
                <div className="container mx-auto px-4">
                    <div className="max-w-4xl mx-auto">
                        <div className="bg-gradient-to-br from-cyan-50 to-white border border-cyan-100 rounded-2xl p-8">
                            <p className="text-slate-700 leading-relaxed text-lg">
                                <span className="font-semibold text-slate-800">Suvarnadurga Shipping & Marine Services Pvt. Ltd.</span>{' '}
                                (CIN: U61100MH2000PTC124043), operating under the brand names "Jetty Ferry" and "carferry.in",
                                is committed to protecting the privacy of its users. This Privacy Policy describes our
                                practices regarding the collection, use, storage, and disclosure of personal information
                                provided by users of our website{' '}
                                <a href="https://carferry.online" className="text-cyan-600 hover:text-cyan-700 underline" target="_blank" rel="noopener noreferrer">
                                    carferry.online
                                </a>{' '}
                                and related services.
                            </p>
                            <p className="text-slate-600 leading-relaxed mt-4">
                                This policy is published in compliance with the Information Technology Act, 2000,
                                the Information Technology (Reasonable Security Practices and Procedures and Sensitive
                                Personal Data or Information) Rules, 2011 ("SPDI Rules"), and other applicable laws of India.
                            </p>
                            <p className="text-slate-600 leading-relaxed mt-4">
                                By using our services, you consent to the collection and use of your information as
                                described in this Privacy Policy. If you do not agree with the terms of this policy,
                                please do not use our services.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {/* Policy Sections */}
            <section className="py-12 bg-slate-50">
                <div className="container mx-auto px-4">
                    <div className="max-w-4xl mx-auto space-y-8">
                        {sections.map((section) => {
                            const Icon = section.icon;
                            return (
                                <div
                                    key={section.id}
                                    id={section.id}
                                    className="bg-white rounded-2xl p-8 shadow-lg shadow-slate-900/5 border border-slate-100"
                                >
                                    <div className="flex items-center gap-3 mb-6">
                                        <div className={`p-3 ${section.iconColor} rounded-xl flex-shrink-0`}>
                                            <Icon className="w-5 h-5 text-white" />
                                        </div>
                                        <h2 className="text-xl md:text-2xl font-bold text-slate-800">
                                            {section.title}
                                        </h2>
                                    </div>
                                    <div className="text-slate-600 leading-relaxed">
                                        {section.content}
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* Contact Section */}
            <section className="py-16 bg-white">
                <div className="container mx-auto px-4">
                    <div className="max-w-4xl mx-auto">
                        <div className="text-center mb-10">
                            <span className="inline-block px-4 py-1.5 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold mb-4">
                                Contact Us
                            </span>
                            <h2 className="text-3xl md:text-4xl font-bold text-slate-800">
                                Questions About Your Privacy?
                            </h2>
                            <p className="text-slate-600 mt-3 text-lg">
                                If you have any questions, concerns, or requests regarding this Privacy Policy or
                                our data practices, please contact our Grievance Officer:
                            </p>
                        </div>

                        <div className="bg-gradient-to-br from-slate-700 via-slate-800 to-slate-900 rounded-2xl p-8 md:p-10 text-white">
                            <h3 className="text-xl font-bold mb-6 text-cyan-300">
                                Suvarnadurga Shipping & Marine Services Pvt. Ltd.
                            </h3>

                            <div className="grid md:grid-cols-2 gap-6">
                                <div className="space-y-4">
                                    <div className="flex items-start gap-3">
                                        <div className="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <MapPin className="w-5 h-5 text-cyan-400" />
                                        </div>
                                        <div>
                                            <p className="font-medium text-slate-200">Registered Office</p>
                                            <p className="text-slate-400 text-sm leading-relaxed">
                                                Dabhol FerryBoat Jetty, Dapoli,<br />
                                                Dist. Ratnagiri, Maharashtra - 415712
                                            </p>
                                        </div>
                                    </div>

                                    <div className="flex items-start gap-3">
                                        <div className="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <Mail className="w-5 h-5 text-orange-400" />
                                        </div>
                                        <div>
                                            <p className="font-medium text-slate-200">Email</p>
                                            <a
                                                href="mailto:ssmsdapoli@rediffmail.com"
                                                className="text-cyan-400 hover:text-cyan-300 text-sm transition-colors"
                                            >
                                                ssmsdapoli@rediffmail.com
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div className="space-y-4">
                                    <div className="flex items-start gap-3">
                                        <div className="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <Phone className="w-5 h-5 text-cyan-400" />
                                        </div>
                                        <div>
                                            <p className="font-medium text-slate-200">Phone</p>
                                            <p className="text-slate-400 text-sm">02348-248900</p>
                                            <p className="text-slate-400 text-sm">9767248900</p>
                                        </div>
                                    </div>

                                    <div className="flex items-start gap-3">
                                        <div className="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <Globe className="w-5 h-5 text-orange-400" />
                                        </div>
                                        <div>
                                            <p className="font-medium text-slate-200">Website</p>
                                            <a
                                                href="https://carferry.online"
                                                className="text-cyan-400 hover:text-cyan-300 text-sm transition-colors"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                carferry.online
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="mt-6 pt-6 border-t border-white/10">
                                <div className="grid md:grid-cols-2 gap-4 text-sm text-slate-400">
                                    <p><span className="text-slate-300 font-medium">CIN:</span> U61100MH2000PTC124043</p>
                                    <p><span className="text-slate-300 font-medium">GST:</span> 27AAICS4116Q1ZU</p>
                                </div>
                            </div>
                        </div>

                        <div className="mt-8 text-center text-sm text-slate-500">
                            <p>
                                This Privacy Policy is governed by and construed in accordance with the laws of India.
                                Any disputes arising under this policy shall be subject to the exclusive jurisdiction of
                                the courts in Ratnagiri, Maharashtra.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    );
}
