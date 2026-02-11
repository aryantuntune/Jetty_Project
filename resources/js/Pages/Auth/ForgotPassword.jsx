import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import {
    Ship,
    Mail,
    ArrowLeft,
    Send,
    CheckCircle,
    AlertCircle,
} from 'lucide-react';

export default function ForgotPassword({ status, errors }) {
    const [email, setEmail] = useState('');
    const [loading, setLoading] = useState(false);

    const handleSubmit = (e) => {
        e.preventDefault();
        setLoading(true);

        router.post(route('password.email'), { email }, {
            onFinish: () => setLoading(false),
        });
    };

    const errorMessage = errors?.email || Object.values(errors || {})[0];

    return (
        <>
            <Head>
                <title>Forgot Password - Jetty Admin</title>
                <meta name="description" content="Reset your Jetty admin password" />
            </Head>

            <div className="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 flex items-center justify-center px-4 py-12">
                {/* Background decoration */}
                <div className="absolute inset-0 overflow-hidden">
                    <div className="absolute top-0 left-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl" />
                    <div className="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl" />
                </div>

                <div className="relative w-full max-w-md">
                    {/* Logo */}
                    <div className="text-center mb-8">
                        <Link href={route('login')} className="inline-flex items-center gap-3 group">
                            <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center shadow-xl group-hover:shadow-indigo-500/30 transition-all">
                                <Ship className="w-7 h-7 text-white" />
                            </div>
                            <span className="text-3xl font-bold text-white tracking-tight">Jetty</span>
                        </Link>
                    </div>

                    {/* Card */}
                    <div className="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl">
                        <div className="text-center mb-8">
                            <h1 className="text-2xl font-bold text-white mb-2">Forgot Password?</h1>
                            <p className="text-white/60">
                                Enter your email address and we'll send you a link to reset your password.
                            </p>
                        </div>

                        {/* Success Message */}
                        {status && (
                            <div className="mb-6 p-4 rounded-xl bg-green-500/20 border border-green-500/30 flex items-center gap-3">
                                <CheckCircle className="w-5 h-5 text-green-400" />
                                <span className="text-green-300 text-sm">{status}</span>
                            </div>
                        )}

                        {/* Error Message */}
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
                                        className="w-full pl-12 pr-4 py-3.5 rounded-xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"
                                        placeholder="you@example.com"
                                        required
                                        autoFocus
                                    />
                                </div>
                            </div>

                            {/* Submit Button */}
                            <button
                                type="submit"
                                disabled={loading}
                                className="w-full py-4 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-700 text-white font-bold text-lg flex items-center justify-center gap-2 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-500/30 transition-all disabled:opacity-70 disabled:cursor-not-allowed"
                            >
                                {loading ? (
                                    <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
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
                                href={route('login')}
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
        </>
    );
}

// Full-page layout
ForgotPassword.layout = (page) => page;
