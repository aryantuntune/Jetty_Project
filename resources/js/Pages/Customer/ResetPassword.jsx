import { useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import {
    Ship,
    Mail,
    Lock,
    Eye,
    EyeOff,
    ArrowLeft,
    KeyRound,
    AlertCircle,
} from 'lucide-react';

export default function CustomerResetPassword({ token, email }) {
    const { errors: pageErrors } = usePage().props;
    const [formData, setFormData] = useState({
        email: email || '',
        password: '',
        password_confirmation: '',
    });
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const [loading, setLoading] = useState(false);

    const handleSubmit = (e) => {
        e.preventDefault();
        setLoading(true);

        router.post(route('customer.password.update'), {
            token,
            ...formData,
        }, {
            onFinish: () => setLoading(false),
        });
    };

    const errorMessage = pageErrors?.email || pageErrors?.password || Object.values(pageErrors || {})[0];

    return (
        <>
            <Head>
                <title>Reset Password - Jetty Customer</title>
                <meta name="description" content="Set a new password for your Jetty account" />
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
                                    Enter your new password below
                                </p>
                            </div>

                            {/* Error Message */}
                            {errorMessage && (
                                <div className="mb-6 p-4 rounded-xl bg-red-500/20 border border-red-500/30 flex items-center gap-3">
                                    <AlertCircle className="w-5 h-5 text-red-400" />
                                    <span className="text-red-300 text-sm">{errorMessage}</span>
                                </div>
                            )}

                            <form onSubmit={handleSubmit} className="space-y-5">
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
                                            value={formData.email}
                                            onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                                            className="input-glass w-full pl-12 pr-4 py-3.5 rounded-xl text-white placeholder-white/50 focus:outline-none"
                                            placeholder="Enter your email"
                                            required
                                        />
                                    </div>
                                </div>

                                {/* New Password Field */}
                                <div>
                                    <label className="block text-sm font-medium text-white/80 mb-2">
                                        New Password
                                    </label>
                                    <div className="relative">
                                        <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <Lock className="w-5 h-5 text-white/40" />
                                        </div>
                                        <input
                                            type={showPassword ? 'text' : 'password'}
                                            value={formData.password}
                                            onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                                            className="input-glass w-full pl-12 pr-12 py-3.5 rounded-xl text-white placeholder-white/50 focus:outline-none"
                                            placeholder="New password"
                                            required
                                            minLength={8}
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowPassword(!showPassword)}
                                            className="absolute inset-y-0 right-0 pr-4 flex items-center text-white/40 hover:text-white/70 transition-colors"
                                        >
                                            {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                                        </button>
                                    </div>
                                </div>

                                {/* Confirm Password Field */}
                                <div>
                                    <label className="block text-sm font-medium text-white/80 mb-2">
                                        Confirm Password
                                    </label>
                                    <div className="relative">
                                        <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <Lock className="w-5 h-5 text-white/40" />
                                        </div>
                                        <input
                                            type={showConfirmPassword ? 'text' : 'password'}
                                            value={formData.password_confirmation}
                                            onChange={(e) => setFormData({ ...formData, password_confirmation: e.target.value })}
                                            className="input-glass w-full pl-12 pr-12 py-3.5 rounded-xl text-white placeholder-white/50 focus:outline-none"
                                            placeholder="Confirm password"
                                            required
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                                            className="absolute inset-y-0 right-0 pr-4 flex items-center text-white/40 hover:text-white/70 transition-colors"
                                        >
                                            {showConfirmPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                                        </button>
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
                                            <KeyRound className="w-5 h-5" />
                                            <span>Reset Password</span>
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
CustomerResetPassword.layout = (page) => page;
