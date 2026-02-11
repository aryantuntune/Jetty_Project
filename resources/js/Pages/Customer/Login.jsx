import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import {
    Ship,
    Mail,
    Lock,
    Eye,
    EyeOff,
    ArrowRight,
    ArrowLeft,
    UserPlus,
    AlertCircle,
} from 'lucide-react';

export default function CustomerLogin({ errors, status }) {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [remember, setRemember] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    const [loading, setLoading] = useState(false);

    const handleSubmit = (e) => {
        e.preventDefault();
        setLoading(true);

        router.post(route('customer.login.submit'), {
            email,
            password,
            remember,
        }, {
            onFinish: () => setLoading(false),
        });
    };

    const errorMessage = errors?.email || errors?.password || Object.values(errors || {})[0];

    return (
        <>
            <Head>
                <title>Login - Jetty Ferry Booking</title>
                <meta name="description" content="Sign in to your Jetty account to book ferry tickets online across Maharashtra's Konkan coast." />
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
                        {/* Logo & Brand */}
                        <div className="text-center mb-8 animate-fade-in-up">
                            <Link href={route('public.home')} className="inline-flex items-center gap-3 group">
                                <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center shadow-xl group-hover:shadow-sky-500/50 transition-all duration-300">
                                    <Ship className="w-7 h-7 text-white" />
                                </div>
                                <span className="text-3xl font-bold text-white tracking-tight">Jetty</span>
                            </Link>
                        </div>

                        {/* Login Card */}
                        <div className="glass-card rounded-3xl p-8 md:p-10 shadow-2xl animate-fade-in-up-delayed">
                            {/* Header */}
                            <div className="text-center mb-8">
                                <h1 className="text-2xl md:text-3xl font-bold text-white mb-2">Welcome Back</h1>
                                <p className="text-white/60">Sign in to book your next ferry journey</p>
                            </div>

                            {/* Status Message */}
                            {status && (
                                <div className="mb-6 p-4 rounded-xl bg-green-500/20 border border-green-500/30">
                                    <p className="text-green-300 text-sm font-medium">{status}</p>
                                </div>
                            )}

                            {/* Error Alert */}
                            {errorMessage && (
                                <div className="mb-6 p-4 rounded-xl bg-red-500/20 border border-red-500/30 flex items-center gap-2">
                                    <AlertCircle className="w-5 h-5 text-red-300" />
                                    <span className="text-red-300 text-sm font-medium">{errorMessage}</span>
                                </div>
                            )}

                            {/* Login Form */}
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
                                            value={email}
                                            onChange={(e) => setEmail(e.target.value)}
                                            className="input-glass w-full pl-12 pr-4 py-3.5 rounded-xl text-white placeholder-white/50 focus:outline-none"
                                            placeholder="you@example.com"
                                            required
                                            autoFocus
                                        />
                                    </div>
                                </div>

                                {/* Password Field */}
                                <div>
                                    <div className="flex items-center justify-between mb-2">
                                        <label className="block text-sm font-medium text-white/80">
                                            Password
                                        </label>
                                        <Link
                                            href={route('customer.password.request')}
                                            className="text-sm text-amber-400 hover:text-amber-300 transition-colors"
                                        >
                                            Forgot password?
                                        </Link>
                                    </div>
                                    <div className="relative">
                                        <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <Lock className="w-5 h-5 text-white/40" />
                                        </div>
                                        <input
                                            type={showPassword ? 'text' : 'password'}
                                            value={password}
                                            onChange={(e) => setPassword(e.target.value)}
                                            className="input-glass w-full pl-12 pr-12 py-3.5 rounded-xl text-white placeholder-white/50 focus:outline-none"
                                            placeholder="Enter your password"
                                            required
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

                                {/* Remember Me */}
                                <div className="flex items-center">
                                    <input
                                        type="checkbox"
                                        id="remember"
                                        checked={remember}
                                        onChange={(e) => setRemember(e.target.checked)}
                                        className="w-4 h-4 rounded border-white/30 bg-white/10 text-sky-500 focus:ring-sky-500 focus:ring-offset-0"
                                    />
                                    <label htmlFor="remember" className="ml-2 text-sm text-white/70">
                                        Remember me for 30 days
                                    </label>
                                </div>

                                {/* Submit Button */}
                                <button
                                    type="submit"
                                    disabled={loading}
                                    className="btn-gradient w-full py-4 rounded-xl font-bold text-slate-900 text-lg flex items-center justify-center gap-2 group disabled:opacity-70"
                                >
                                    {loading ? (
                                        <div className="w-5 h-5 border-2 border-slate-900/30 border-t-slate-900 rounded-full animate-spin" />
                                    ) : (
                                        <>
                                            <span>Sign In</span>
                                            <ArrowRight className="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                                        </>
                                    )}
                                </button>
                            </form>

                            {/* Divider */}
                            <div className="relative my-8">
                                <div className="absolute inset-0 flex items-center">
                                    <div className="w-full border-t border-white/10"></div>
                                </div>
                                <div className="relative flex justify-center text-sm">
                                    <span className="px-4 text-white/40">New to Jetty?</span>
                                </div>
                            </div>

                            {/* Register Link */}
                            <Link
                                href={route('customer.register')}
                                className="inline-flex items-center justify-center gap-2 w-full py-3.5 rounded-xl border border-white/20 text-white font-medium hover:bg-white/10 transition-all duration-300"
                            >
                                <UserPlus className="w-5 h-5" />
                                <span>Create an Account</span>
                            </Link>
                        </div>

                        {/* Back to Home */}
                        <div className="text-center mt-6">
                            <Link
                                href={route('public.home')}
                                className="inline-flex items-center gap-2 text-white/60 hover:text-white transition-colors"
                            >
                                <ArrowLeft className="w-4 h-4" />
                                <span>Back to Home</span>
                            </Link>
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

// Full-page layout (no sidebar)
CustomerLogin.layout = (page) => page;
