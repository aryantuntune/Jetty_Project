import { useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import {
    Ship,
    Mail,
    ArrowLeft,
    Send,
    CheckCircle,
    AlertCircle,
} from 'lucide-react';

export default function CustomerForgotPassword() {
    const { flash, errors: pageErrors } = usePage().props;
    const [email, setEmail] = useState('');
    const [loading, setLoading] = useState(false);
    const [showModal, setShowModal] = useState(false);
    const [modalType, setModalType] = useState(''); // 'success' or 'error'

    const handleSubmit = (e) => {
        e.preventDefault();
        setLoading(true);

        router.post(route('customer.password.email'), { email }, {
            onSuccess: () => {
                setModalType('success');
                setShowModal(true);
                setEmail('');
            },
            onError: () => {
                setModalType('error');
                setShowModal(true);
            },
            onFinish: () => setLoading(false),
        });
    };

    const errorMessage = pageErrors?.email || Object.values(pageErrors || {})[0];

    return (
        <>
            <Head>
                <title>Forgot Password - Jetty Customer</title>
                <meta name="description" content="Reset your Jetty customer account password" />
            </Head>

            <div className="min-h-screen relative overflow-hidden">
                {/* Video Background */}
                <div className="fixed inset-0 z-0">
                    <video
                        autoPlay
                        muted
                        loop
                        playsInline
                        className="w-full h-full object-cover"
                        style={{ filter: 'brightness(0.5)' }}
                    >
                        <source src="/videos/1.mp4" type="video/mp4" />
                    </video>
                </div>

                {/* Content */}
                <div className="relative z-10 min-h-screen flex items-center justify-center px-4 py-12">
                    <div className="w-full max-w-md">
                        {/* Logo */}
                        <div className="text-center mb-8 animate-fade-in-up">
                            <Link href={route('public.home')} className="inline-flex items-center gap-3 group">
                                <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center shadow-xl group-hover:shadow-sky-500/50 transition-all">
                                    <Ship className="w-7 h-7 text-white" />
                                </div>
                                <span className="text-3xl font-bold text-white tracking-tight">Jetty</span>
                            </Link>
                        </div>

                        {/* Card */}
                        <div className="glass-card rounded-3xl p-8 md:p-10 shadow-2xl animate-fade-in-up-delayed">
                            <div className="text-center mb-8">
                                <h1 className="text-2xl md:text-3xl font-bold text-white mb-2">Reset Password</h1>
                                <p className="text-white/60">
                                    Enter your email to receive a password reset link
                                </p>
                            </div>

                            {/* Success/Error inline message */}
                            {flash?.success && (
                                <div className="mb-6 p-4 rounded-xl bg-green-500/20 border border-green-500/30 flex items-center gap-3">
                                    <CheckCircle className="w-5 h-5 text-green-400" />
                                    <span className="text-green-300 text-sm">{flash.success}</span>
                                </div>
                            )}

                            {errorMessage && (
                                <div className="mb-6 p-4 rounded-xl bg-red-500/20 border border-red-500/30 flex items-center gap-3">
                                    <AlertCircle className="w-5 h-5 text-red-400" />
                                    <span className="text-red-300 text-sm">{errorMessage}</span>
                                </div>
                            )}

                            <form onSubmit={handleSubmit} className="space-y-6">
                                {/* Email Field */}
                                <div>
                                    <label className="block text-sm font-medium text-white/80 mb-2">
                                        Email Address
                                    </label>
                                    <div className="relative">
                                        <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <Mail className="w-5 h-5 text-white/40" />
                                        </div>
                                        <input
                                            type="email"
                                            value={email}
                                            onChange={(e) => setEmail(e.target.value)}
                                            className="input-glass w-full pl-12 pr-4 py-3.5 rounded-xl text-white placeholder-white/50 focus:outline-none"
                                            placeholder="Enter your email"
                                            required
                                            autoFocus
                                        />
                                    </div>
                                </div>

                                {/* Submit Button */}
                                <button
                                    type="submit"
                                    disabled={loading}
                                    className="btn-gradient w-full py-4 rounded-xl font-bold text-slate-900 text-lg flex items-center justify-center gap-2 disabled:opacity-70"
                                >
                                    {loading ? (
                                        <div className="w-5 h-5 border-2 border-slate-900/30 border-t-slate-900 rounded-full animate-spin" />
                                    ) : (
                                        <>
                                            <Send className="w-5 h-5" />
                                            <span>Send Reset Link</span>
                                        </>
                                    )}
                                </button>
                            </form>

                            {/* Back to Login */}
                            <div className="mt-8 text-center">
                                <Link
                                    href={route('customer.login')}
                                    className="inline-flex items-center gap-2 text-white/60 hover:text-white transition-colors"
                                >
                                    <ArrowLeft className="w-4 h-4" />
                                    <span>Back to Login</span>
                                </Link>
                            </div>
                        </div>

                        {/* Footer */}
                        <p className="text-center text-white/30 text-sm mt-8">
                            &copy; {new Date().getFullYear()} Jetty. All rights reserved.
                        </p>
                    </div>
                </div>

                {/* Modal */}
                {showModal && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4" onClick={() => setShowModal(false)}>
                        <div className="absolute inset-0 bg-black/60 backdrop-blur-sm" />
                        <div
                            className="relative bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl"
                            onClick={(e) => e.stopPropagation()}
                        >
                            <div className={`w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4 ${
                                modalType === 'success' ? 'bg-green-100' : 'bg-red-100'
                            }`}>
                                {modalType === 'success' ? (
                                    <CheckCircle className="w-8 h-8 text-green-600" />
                                ) : (
                                    <AlertCircle className="w-8 h-8 text-red-600" />
                                )}
                            </div>
                            <h3 className={`text-xl font-bold text-center mb-2 ${
                                modalType === 'success' ? 'text-green-800' : 'text-red-800'
                            }`}>
                                {modalType === 'success' ? 'Success!' : 'Error'}
                            </h3>
                            <p className="text-center text-slate-600 mb-6">
                                {modalType === 'success'
                                    ? 'Password reset link sent successfully. Please check your inbox.'
                                    : errorMessage || 'Something went wrong. Please try again.'}
                            </p>
                            <button
                                onClick={() => setShowModal(false)}
                                className="w-full py-3 rounded-xl bg-slate-800 text-white font-semibold hover:bg-slate-900 transition-colors"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                )}
            </div>

            <style>{`
                .glass-card {
                    background: rgba(255, 255, 255, 0.08);
                    backdrop-filter: blur(24px);
                    -webkit-backdrop-filter: blur(24px);
                    border: 1px solid rgba(255, 255, 255, 0.15);
                }

                .input-glass {
                    background: rgba(255, 255, 255, 0.1);
                    border: 1px solid rgba(255, 255, 255, 0.2);
                    transition: all 0.3s ease;
                }

                .input-glass:focus {
                    background: rgba(255, 255, 255, 0.15);
                    border-color: rgba(14, 165, 233, 0.5);
                    box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
                }

                .btn-gradient {
                    background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%);
                    transition: all 0.3s ease;
                }

                .btn-gradient:hover:not(:disabled) {
                    transform: translateY(-2px);
                    box-shadow: 0 15px 30px rgba(251, 191, 36, 0.35);
                }

                .animate-fade-in-up {
                    animation: fadeInUp 0.6s ease-out forwards;
                }

                .animate-fade-in-up-delayed {
                    animation: fadeInUp 0.6s ease-out 0.1s forwards;
                    opacity: 0;
                }

                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
            `}</style>
        </>
    );
}

// Full-page layout
CustomerForgotPassword.layout = (page) => page;
