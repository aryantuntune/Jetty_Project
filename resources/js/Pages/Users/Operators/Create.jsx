import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    User,
    Mail,
    Lock,
    Phone,
    MapPin,
    Ship,
    Eye,
    EyeOff,
    Save,
    AlertCircle,
} from 'lucide-react';

export default function Create({ branches, ferryboats }) {
    const [showPassword, setShowPassword] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        password: '',
        mobile: '',
        branch_id: '',
        ferry_boat_id: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('operator.store'));
    };

    return (
        <>
            <Head title="Add Operator" />

            {/* Header */}
            <div className="mb-6">
                <Link
                    href={route('operator.index')}
                    className="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition-colors mb-4"
                >
                    <ArrowLeft className="w-4 h-4" />
                    <span>Back to Operators</span>
                </Link>
                <h1 className="text-2xl font-bold text-gray-900">Add New Operator</h1>
                <p className="text-sm text-gray-500 mt-1">
                    Create a new ferry ticket counter operator account
                </p>
            </div>

            {/* Form Card */}
            <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden max-w-2xl">
                <form onSubmit={handleSubmit}>
                    <div className="p-6 space-y-6">
                        {/* General Error */}
                        {Object.keys(errors).length > 0 && (
                            <div className="p-4 rounded-xl bg-red-50 border border-red-200">
                                <div className="flex items-start gap-3">
                                    <AlertCircle className="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <p className="text-sm font-medium text-red-800">
                                            Please fix the following errors:
                                        </p>
                                        <ul className="mt-2 text-sm text-red-700 list-disc list-inside">
                                            {Object.values(errors).map((error, index) => (
                                                <li key={index}>{error}</li>
                                            ))}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Name Field */}
                        <div>
                            <label
                                htmlFor="name"
                                className="block text-sm font-medium text-slate-700 mb-2"
                            >
                                Full Name <span className="text-red-500">*</span>
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <User className="w-5 h-5 text-slate-400" />
                                </div>
                                <input
                                    type="text"
                                    id="name"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    className={`w-full pl-12 pr-4 py-3 rounded-xl border transition-all focus:outline-none focus:ring-4 focus:ring-indigo-500/10 ${errors.name
                                        ? 'border-red-500 focus:border-red-500'
                                        : 'border-slate-200 focus:border-indigo-500'
                                        }`}
                                    placeholder="Enter operator's full name"
                                    required
                                />
                            </div>
                        </div>

                        {/* Email Field */}
                        <div>
                            <label
                                htmlFor="email"
                                className="block text-sm font-medium text-slate-700 mb-2"
                            >
                                Email Address <span className="text-red-500">*</span>
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <Mail className="w-5 h-5 text-slate-400" />
                                </div>
                                <input
                                    type="email"
                                    id="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    className={`w-full pl-12 pr-4 py-3 rounded-xl border transition-all focus:outline-none focus:ring-4 focus:ring-indigo-500/10 ${errors.email
                                        ? 'border-red-500 focus:border-red-500'
                                        : 'border-slate-200 focus:border-indigo-500'
                                        }`}
                                    placeholder="operator@example.com"
                                    required
                                />
                            </div>
                        </div>

                        {/* Password Field */}
                        <div>
                            <label
                                htmlFor="password"
                                className="block text-sm font-medium text-slate-700 mb-2"
                            >
                                Password <span className="text-red-500">*</span>
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <Lock className="w-5 h-5 text-slate-400" />
                                </div>
                                <input
                                    type={showPassword ? 'text' : 'password'}
                                    id="password"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    className={`w-full pl-12 pr-12 py-3 rounded-xl border transition-all focus:outline-none focus:ring-4 focus:ring-indigo-500/10 ${errors.password
                                        ? 'border-red-500 focus:border-red-500'
                                        : 'border-slate-200 focus:border-indigo-500'
                                        }`}
                                    placeholder="Create a secure password"
                                    required
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPassword(!showPassword)}
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

                        {/* Mobile Field */}
                        <div>
                            <label
                                htmlFor="mobile"
                                className="block text-sm font-medium text-slate-700 mb-2"
                            >
                                Mobile Number
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <Phone className="w-5 h-5 text-slate-400" />
                                </div>
                                <input
                                    type="tel"
                                    id="mobile"
                                    value={data.mobile}
                                    onChange={(e) => setData('mobile', e.target.value)}
                                    className={`w-full pl-12 pr-4 py-3 rounded-xl border transition-all focus:outline-none focus:ring-4 focus:ring-indigo-500/10 ${errors.mobile
                                        ? 'border-red-500 focus:border-red-500'
                                        : 'border-slate-200 focus:border-indigo-500'
                                        }`}
                                    placeholder="Enter mobile number"
                                />
                            </div>
                        </div>

                        {/* Branch Select */}
                        <div>
                            <label
                                htmlFor="branch_id"
                                className="block text-sm font-medium text-slate-700 mb-2"
                            >
                                Branch
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <MapPin className="w-5 h-5 text-slate-400" />
                                </div>
                                <select
                                    id="branch_id"
                                    value={data.branch_id}
                                    onChange={(e) => setData('branch_id', e.target.value)}
                                    className={`w-full pl-12 pr-4 py-3 rounded-xl border transition-all focus:outline-none focus:ring-4 focus:ring-indigo-500/10 appearance-none bg-white ${errors.branch_id
                                        ? 'border-red-500 focus:border-red-500'
                                        : 'border-slate-200 focus:border-indigo-500'
                                        }`}
                                >
                                    <option value="">Select a branch</option>
                                    {branches?.map((branch) => (
                                        <option key={branch.id || branch.branch_id} value={branch.id || branch.branch_id}>
                                            {branch.name || branch.branch_name}
                                        </option>
                                    ))}
                                </select>
                                <div className="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <svg
                                        className="w-5 h-5 text-slate-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {/* Ferry Boat Select */}
                        <div>
                            <label
                                htmlFor="ferry_boat_id"
                                className="block text-sm font-medium text-slate-700 mb-2"
                            >
                                Ferry Boat
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <Ship className="w-5 h-5 text-slate-400" />
                                </div>
                                <select
                                    id="ferry_boat_id"
                                    value={data.ferry_boat_id}
                                    onChange={(e) => setData('ferry_boat_id', e.target.value)}
                                    className={`w-full pl-12 pr-4 py-3 rounded-xl border transition-all focus:outline-none focus:ring-4 focus:ring-indigo-500/10 appearance-none bg-white ${errors.ferry_boat_id
                                        ? 'border-red-500 focus:border-red-500'
                                        : 'border-slate-200 focus:border-indigo-500'
                                        }`}
                                >
                                    <option value="">Select a ferry boat</option>
                                    {ferryboats?.map((ferry) => (
                                        <option key={ferry.id || ferry.ferry_boat_id} value={ferry.id || ferry.ferry_boat_id}>
                                            {ferry.name || ferry.ferry_name}
                                        </option>
                                    ))}
                                </select>
                                <div className="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <svg
                                        className="w-5 h-5 text-slate-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Form Actions */}
                    <div className="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-3">
                        <Link
                            href={route('operator.index')}
                            className="px-4 py-2.5 text-gray-700 font-medium rounded-xl hover:bg-gray-100 transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {processing ? (
                                <>
                                    <svg
                                        className="animate-spin w-5 h-5"
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
                                    <span>Creating...</span>
                                </>
                            ) : (
                                <>
                                    <Save className="w-5 h-5" />
                                    <span>Create Operator</span>
                                </>
                            )}
                        </button>
                    </div>
                </form>
            </div>
        </>
    );
}
