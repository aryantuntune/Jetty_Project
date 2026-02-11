import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import {
    Ship,
    User,
    Mail,
    Lock,
    Eye,
    EyeOff,
    ArrowRight,
    ArrowLeft,
    AlertCircle,
} from 'lucide-react';

export default function Register() {
    const [showPassword, setShowPassword] = useState(false);
    const [showPasswordConfirmation, setShowPasswordConfirmation] = useState(false);

    const form = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        form.post(route('register.submit'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    };

    const togglePasswordVisibility = () => {
        setShowPassword(!showPassword);
    };

    const togglePasswordConfirmationVisibility = () => {
        setShowPasswordConfirmation(!showPasswordConfirmation);
    };

    return (
        <>
            <Head title="Create Account" />

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
                .animate-slide-up-delayed-25 { animation: slideUp 0.5s ease-out 0.25s forwards; opacity: 0; }
                .animate-slide-up-delayed-3 { animation: slideUp 0.5s ease-out 0.3s forwards; opacity: 0; }
                .animate-slide-up-delayed-35 { animation: slideUp 0.5s ease-out 0.35s forwards; opacity: 0; }
                .animate-slide-up-delayed-4 { animation: slideUp 0.5s ease-out 0.4s forwards; opacity: 0; }
                .animate-slide-up-delayed-45 { animation: slideUp 0.5s ease-out 0.45s forwards; opacity: 0; }
                .animate-slide-up-delayed-5 { animation: slideUp 0.5s ease-out 0.5s forwards; opacity: 0; }
                .animate-fade-in-delayed { animation: fadeIn 0.5s ease-out 0.5s forwards; opacity: 0; }
            `}</style>

            {/* Full page container with gradient background */}
            <div className="font-sans antialiased min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-indigo-500 via-purple-500 to-purple-700 bg-[length:400%_400%] animate-gradient">
                {/* Background Shapes */}
                <div className="fixed inset-0 overflow-hidden pointer-events-none">
                    <div className="absolute -top-40 -right-40 w-80 h-80 rounded-full bg-white/10 animate-float" />
                    <div className="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-white/5 animate-float-delayed-2" />
                    <div className="absolute top-1/2 left-1/4 w-64 h-64 rounded-full bg-white/5 animate-float-delayed-4" />
                </div>

                {/* Register Card Container */}
                <div className="relative w-full max-w-md">
                    {/* Logo */}
                    <div className="text-center mb-8 animate-slide-up">
                        <Link href="/" className="inline-flex items-center space-x-3 group">
                            <div className="w-14 h-14 rounded-2xl bg-white shadow-xl flex items-center justify-center group-hover:shadow-2xl transition-shadow">
                                <Ship className="w-8 h-8 text-indigo-600" />
                            </div>
                            <span className="text-3xl font-bold text-white">Jetty</span>
                        </Link>
                        <p className="mt-2 text-white/70 text-sm">Ferry Booking System</p>
                    </div>

                    {/* Card */}
                    <div className="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-8 md:p-10 animate-slide-up-delayed-1">
                        {/* Header */}
                        <div className="text-center mb-8">
                            <h1 className="text-2xl font-bold text-slate-800 mb-2">Create Account</h1>
                            <p className="text-slate-500">Join Jetty to book your ferry tickets</p>
                        </div>

                        {/* Error Messages */}
                        {(form.errors.name || form.errors.email || form.errors.password || form.errors.password_confirmation) && (
                            <div className="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
                                <div className="flex items-start space-x-2 text-red-700">
                                    <AlertCircle className="w-5 h-5 flex-shrink-0 mt-0.5" />
                                    <div className="text-sm font-medium space-y-1">
                                        {form.errors.name && <p>{form.errors.name}</p>}
                                        {form.errors.email && <p>{form.errors.email}</p>}
                                        {form.errors.password && <p>{form.errors.password}</p>}
                                        {form.errors.password_confirmation && <p>{form.errors.password_confirmation}</p>}
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Register Form */}
                        <form onSubmit={handleSubmit} className="space-y-5">
                            {/* Name Field */}
                            <div className="animate-slide-up-delayed-2">
                                <label
                                    htmlFor="name"
                                    className="block text-sm font-medium text-slate-700 mb-2"
                                >
                                    Full Name
                                </label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <User className="w-5 h-5 text-slate-400" />
                                    </div>
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        value={form.data.name}
                                        onChange={(e) => form.setData('name', e.target.value)}
                                        className={`w-full pl-12 pr-4 py-3.5 rounded-xl border transition-all duration-300 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-slate-800 placeholder-slate-400 ${
                                            form.errors.name
                                                ? 'border-red-500'
                                                : 'border-slate-200'
                                        }`}
                                        placeholder="John Doe"
                                        required
                                        autoFocus
                                        autoComplete="name"
                                    />
                                </div>
                            </div>

                            {/* Email Field */}
                            <div className="animate-slide-up-delayed-25">
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
                                        className={`w-full pl-12 pr-4 py-3.5 rounded-xl border transition-all duration-300 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-slate-800 placeholder-slate-400 ${
                                            form.errors.email
                                                ? 'border-red-500'
                                                : 'border-slate-200'
                                        }`}
                                        placeholder="you@example.com"
                                        required
                                        autoComplete="email"
                                    />
                                </div>
                            </div>

                            {/* Password Field */}
                            <div className="animate-slide-up-delayed-3">
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
                                        className={`w-full pl-12 pr-12 py-3.5 rounded-xl border transition-all duration-300 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-slate-800 placeholder-slate-400 ${
                                            form.errors.password
                                                ? 'border-red-500'
                                                : 'border-slate-200'
                                        }`}
                                        placeholder="Create a password"
                                        required
                                        autoComplete="new-password"
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

                            {/* Password Confirmation Field */}
                            <div className="animate-slide-up-delayed-35">
                                <label
                                    htmlFor="password_confirmation"
                                    className="block text-sm font-medium text-slate-700 mb-2"
                                >
                                    Confirm Password
                                </label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <Lock className="w-5 h-5 text-slate-400" />
                                    </div>
                                    <input
                                        type={showPasswordConfirmation ? 'text' : 'password'}
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        value={form.data.password_confirmation}
                                        onChange={(e) => form.setData('password_confirmation', e.target.value)}
                                        className={`w-full pl-12 pr-12 py-3.5 rounded-xl border transition-all duration-300 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-slate-800 placeholder-slate-400 ${
                                            form.errors.password_confirmation
                                                ? 'border-red-500'
                                                : 'border-slate-200'
                                        }`}
                                        placeholder="Confirm your password"
                                        required
                                        autoComplete="new-password"
                                    />
                                    <button
                                        type="button"
                                        onClick={togglePasswordConfirmationVisibility}
                                        className="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                                        tabIndex={-1}
                                    >
                                        {showPasswordConfirmation ? (
                                            <EyeOff className="w-5 h-5" />
                                        ) : (
                                            <Eye className="w-5 h-5" />
                                        )}
                                    </button>
                                </div>
                            </div>

                            {/* Submit Button */}
                            <div className="animate-slide-up-delayed-4 pt-2">
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
                                            <span>Creating Account...</span>
                                        </>
                                    ) : (
                                        <>
                                            <span>Create Account</span>
                                            <ArrowRight className="w-5 h-5" />
                                        </>
                                    )}
                                </button>
                            </div>
                        </form>

                        {/* Sign In Link */}
                        <div className="mt-6 text-center animate-slide-up-delayed-45">
                            <p className="text-slate-600">
                                Already have an account?{' '}
                                <Link
                                    href={route('login')}
                                    className="font-semibold text-indigo-600 hover:text-indigo-700 transition-colors"
                                >
                                    Sign In
                                </Link>
                            </p>
                        </div>
                    </div>

                    {/* Back to Home */}
                    <div className="text-center mt-8 animate-slide-up-delayed-5">
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
Register.layout = (page) => page;
