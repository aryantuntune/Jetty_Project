import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import {
    Ship,
    Mail,
    Lock,
    Eye,
    EyeOff,
    ArrowRight,
    ArrowLeft,
    AlertCircle,
} from 'lucide-react';

export default function Login({ status }) {
    const [showPassword, setShowPassword] = useState(false);

    const form = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        form.post(route('login'), {
            onFinish: () => form.reset('password'),
        });
    };

    const togglePasswordVisibility = () => {
        setShowPassword(!showPassword);
    };

    return (
        <>
            <Head title="Admin Login" />

            <style>{`
                @keyframes gradientShift {
                    0% { background-position: 0% 50%; }
                    50% { background-position: 100% 50%; }
                    100% { background-position: 0% 50%; }
                }
                @keyframes float {
                    0%, 100% { transform: translateY(0px); }
                    50% { transform: translateY(-10px); }
                }
                @keyframes slideUp {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                .animate-gradient { animation: gradientShift 15s ease infinite; }
                .animate-float { animation: float 6s ease-in-out infinite; }
                .animate-float-delayed-2 { animation: float 6s ease-in-out infinite 2s; }
                .animate-float-delayed-4 { animation: float 6s ease-in-out infinite 4s; }
                .animate-slide-up { animation: slideUp 0.5s ease-out forwards; }
                .animate-slide-up-delayed-1 { animation: slideUp 0.5s ease-out 0.1s forwards; opacity: 0; }
                .animate-slide-up-delayed-2 { animation: slideUp 0.5s ease-out 0.2s forwards; opacity: 0; }
                .animate-slide-up-delayed-3 { animation: slideUp 0.5s ease-out 0.3s forwards; opacity: 0; }
                .animate-fade-in-delayed { animation: fadeIn 0.5s ease-out 0.3s forwards; opacity: 0; }
            `}</style>

            {/* Full page container with gradient background */}
            <div className="font-sans antialiased min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-indigo-500 via-purple-500 to-purple-700 bg-[length:400%_400%] animate-gradient">
                {/* Background Shapes */}
                <div className="fixed inset-0 overflow-hidden pointer-events-none">
                    <div className="absolute -top-40 -right-40 w-80 h-80 rounded-full bg-white/10 animate-float" />
                    <div className="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-white/5 animate-float-delayed-2" />
                    <div className="absolute top-1/2 left-1/4 w-64 h-64 rounded-full bg-white/5 animate-float-delayed-4" />
                </div>

                {/* Login Card Container */}
                <div className="relative w-full max-w-md">
                    {/* Logo */}
                    <div className="text-center mb-8 animate-slide-up">
                        <Link href="/" className="inline-flex items-center space-x-3 group">
                            <div className="w-14 h-14 rounded-2xl bg-white shadow-xl flex items-center justify-center group-hover:shadow-2xl transition-shadow">
                                <Ship className="w-8 h-8 text-indigo-600" />
                            </div>
                            <span className="text-3xl font-bold text-white">Jetty</span>
                        </Link>
                        <p className="mt-2 text-white/70 text-sm">Admin Portal</p>
                    </div>

                    {/* Card */}
                    <div className="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-8 md:p-10 animate-slide-up-delayed-1">
                        {/* Header */}
                        <div className="text-center mb-8">
                            <h1 className="text-2xl font-bold text-slate-800 mb-2">Welcome Back</h1>
                            <p className="text-slate-500">Sign in to access the admin dashboard</p>
                        </div>

                        {/* Status Message */}
                        {status && (
                            <div className="mb-6 p-4 rounded-xl bg-green-50 border border-green-200">
                                <div className="flex items-center space-x-2 text-green-700">
                                    <span className="text-sm font-medium">{status}</span>
                                </div>
                            </div>
                        )}

                        {/* Error Messages */}
                        {(form.errors.email || form.errors.password) && (
                            <div className="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
                                <div className="flex items-center space-x-2 text-red-700">
                                    <AlertCircle className="w-5 h-5 flex-shrink-0" />
                                    <span className="text-sm font-medium">
                                        {form.errors.email || form.errors.password}
                                    </span>
                                </div>
                            </div>
                        )}

                        {/* Login Form */}
                        <form onSubmit={handleSubmit} className="space-y-6">
                            {/* Email Field */}
                            <div className="animate-slide-up-delayed-2">
                                <label
                                    htmlFor="email"
                                    className="block text-sm font-medium text-slate-700 mb-2"
                                >
                                    Email Address
                                </label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <Mail className="w-5 h-5 text-slate-400" />
                                    </div>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value={form.data.email}
                                        onChange={(e) => form.setData('email', e.target.value)}
                                        className={`w-full pl-12 pr-4 py-3.5 rounded-xl border transition-all duration-300 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-slate-800 placeholder-slate-400 ${form.errors.email
                                                ? 'border-red-500'
                                                : 'border-slate-200'
                                            }`}
                                        placeholder="admin@example.com"
                                        required
                                        autoFocus
                                        autoComplete="email"
                                    />
                                </div>
                            </div>

                            {/* Password Field */}
                            <div className="animate-slide-up-delayed-2">
                                <label
                                    htmlFor="password"
                                    className="block text-sm font-medium text-slate-700 mb-2"
                                >
                                    Password
                                </label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <Lock className="w-5 h-5 text-slate-400" />
                                    </div>
                                    <input
                                        type={showPassword ? 'text' : 'password'}
                                        id="password"
                                        name="password"
                                        value={form.data.password}
                                        onChange={(e) => form.setData('password', e.target.value)}
                                        className={`w-full pl-12 pr-12 py-3.5 rounded-xl border transition-all duration-300 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-slate-800 placeholder-slate-400 ${form.errors.password
                                                ? 'border-red-500'
                                                : 'border-slate-200'
                                            }`}
                                        placeholder="Enter your password"
                                        required
                                        autoComplete="current-password"
                                    />
                                    <button
                                        type="button"
                                        onClick={togglePasswordVisibility}
                                        className="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                                        tabIndex={-1}
                                    >
                                        {showPassword ? (
                                            <EyeOff className="w-5 h-5" />
                                        ) : (
                                            <Eye className="w-5 h-5" />
                                        )}
                                    </button>
                                </div>
                            </div>

                            {/* Remember Me */}
                            <div className="flex items-center justify-between animate-slide-up-delayed-3">
                                <label className="flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="remember"
                                        checked={form.data.remember}
                                        onChange={(e) => form.setData('remember', e.target.checked)}
                                        className="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span className="ml-2 text-sm text-slate-600">Remember me</span>
                                </label>
                            </div>

                            {/* Submit Button */}
                            <div className="animate-slide-up-delayed-3">
                                <button
                                    type="submit"
                                    disabled={form.processing}
                                    className="w-full py-4 rounded-xl font-semibold text-white text-lg flex items-center justify-center space-x-2 bg-gradient-to-br from-indigo-500 to-indigo-700 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-500/30 active:translate-y-0 disabled:opacity-70 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none"
                                >
                                    {form.processing ? (
                                        <>
                                            <svg
                                                className="animate-spin h-5 w-5 text-white"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                            >
                                                <circle
                                                    className="opacity-25"
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                    stroke="currentColor"
                                                    strokeWidth="4"
                                                />
                                                <path
                                                    className="opacity-75"
                                                    fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                />
                                            </svg>
                                            <span>Signing In...</span>
                                        </>
                                    ) : (
                                        <>
                                            <span>Sign In</span>
                                            <ArrowRight className="w-5 h-5" />
                                        </>
                                    )}
                                </button>
                            </div>
                        </form>
                    </div>

                    {/* Back to Home */}
                    <div className="text-center mt-8 animate-slide-up-delayed-3">
                        <Link
                            href="/"
                            className="inline-flex items-center space-x-2 text-white/80 hover:text-white transition-colors"
                        >
                            <ArrowLeft className="w-4 h-4" />
                            <span>Back to Home</span>
                        </Link>
                    </div>

                    {/* Footer */}
                    <p className="text-center text-white/50 text-sm mt-6 animate-fade-in-delayed">
                        &copy; {new Date().getFullYear()} Jetty. All rights reserved.
                    </p>
                </div>
            </div>
        </>
    );
}

// Disable default layout - this is a full-page design
Login.layout = (page) => page;
