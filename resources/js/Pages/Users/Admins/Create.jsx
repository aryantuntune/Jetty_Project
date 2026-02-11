import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { Shield, ArrowLeft, Eye, EyeOff, AlertCircle } from 'lucide-react';
import Layout from '@/Layouts/Layout';

export default function AdminsCreate({ branches, ferryboats }) {
    const [showPassword, setShowPassword] = useState(false);

    const form = useForm({
        name: '',
        email: '',
        password: '',
        mobile: '',
        branch_id: '',
        ferry_boat_id: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        form.post(route('admin.store'));
    };

    return (
        <>
            <Head title="Add Administrator" />

            {/* Header */}
            <div className="mb-8">
                <Link
                    href={route('admin.index')}
                    className="inline-flex items-center space-x-2 text-slate-600 hover:text-slate-800 mb-4"
                >
                    <ArrowLeft className="w-4 h-4" />
                    <span>Back to Administrators</span>
                </Link>
                <h1 className="text-2xl font-bold text-slate-800">Add Administrator</h1>
                <p className="mt-1 text-sm text-slate-500">Create a new system administrator</p>
            </div>

            {/* Form Card */}
            <div className="max-w-2xl">
                <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                    <div className="px-6 py-4 border-b border-slate-200 flex items-center space-x-3">
                        <div className="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                            <Shield className="w-5 h-5 text-red-600" />
                        </div>
                        <div>
                            <h2 className="font-semibold text-slate-800">Administrator Details</h2>
                            <p className="text-sm text-slate-500">Fill in the information below</p>
                        </div>
                    </div>

                    <form onSubmit={handleSubmit} className="p-6 space-y-6">
                        {/* Error Summary */}
                        {Object.keys(form.errors).length > 0 && (
                            <div className="p-4 rounded-xl bg-red-50 border border-red-200">
                                <div className="flex items-start space-x-2 text-red-700">
                                    <AlertCircle className="w-5 h-5 flex-shrink-0 mt-0.5" />
                                    <div className="text-sm">
                                        {Object.values(form.errors).map((error, i) => (
                                            <p key={i}>{error}</p>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Name */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Full Name <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                value={form.data.name}
                                onChange={(e) => form.setData('name', e.target.value)}
                                className={`w-full px-4 py-3 rounded-xl border transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 ${
                                    form.errors.name ? 'border-red-500' : 'border-slate-200'
                                }`}
                                placeholder="John Doe"
                                required
                            />
                        </div>

                        {/* Email */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Email Address <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                value={form.data.email}
                                onChange={(e) => form.setData('email', e.target.value)}
                                className={`w-full px-4 py-3 rounded-xl border transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 ${
                                    form.errors.email ? 'border-red-500' : 'border-slate-200'
                                }`}
                                placeholder="admin@example.com"
                                required
                            />
                        </div>

                        {/* Password */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Password <span className="text-red-500">*</span>
                            </label>
                            <div className="relative">
                                <input
                                    type={showPassword ? 'text' : 'password'}
                                    value={form.data.password}
                                    onChange={(e) => form.setData('password', e.target.value)}
                                    className={`w-full px-4 py-3 pr-12 rounded-xl border transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 ${
                                        form.errors.password ? 'border-red-500' : 'border-slate-200'
                                    }`}
                                    placeholder="Minimum 6 characters"
                                    required
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPassword(!showPassword)}
                                    className="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600"
                                    tabIndex={-1}
                                >
                                    {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                                </button>
                            </div>
                        </div>

                        {/* Mobile */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Mobile Number
                            </label>
                            <input
                                type="tel"
                                value={form.data.mobile}
                                onChange={(e) => form.setData('mobile', e.target.value)}
                                className="w-full px-4 py-3 rounded-xl border border-slate-200 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                                placeholder="+91 9876543210"
                            />
                        </div>

                        {/* Branch */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Branch
                            </label>
                            <select
                                value={form.data.branch_id}
                                onChange={(e) => form.setData('branch_id', e.target.value)}
                                className="w-full px-4 py-3 rounded-xl border border-slate-200 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                            >
                                <option value="">Select Branch (Optional)</option>
                                {branches.map((branch) => (
                                    <option key={branch.id} value={branch.id}>
                                        {branch.branch_name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Ferry Boat */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Ferry Boat
                            </label>
                            <select
                                value={form.data.ferry_boat_id}
                                onChange={(e) => form.setData('ferry_boat_id', e.target.value)}
                                className="w-full px-4 py-3 rounded-xl border border-slate-200 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                            >
                                <option value="">Select Ferry Boat (Optional)</option>
                                {ferryboats.map((ferry) => (
                                    <option key={ferry.id} value={ferry.id}>
                                        {ferry.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Actions */}
                        <div className="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
                            <Link
                                href={route('admin.index')}
                                className="px-6 py-3 text-slate-700 font-medium rounded-xl border border-slate-300 hover:bg-slate-50 transition-colors"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                disabled={form.processing}
                                className="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors disabled:opacity-50"
                            >
                                {form.processing ? 'Creating...' : 'Create Administrator'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}

AdminsCreate.layout = (page) => <Layout children={page} />;
