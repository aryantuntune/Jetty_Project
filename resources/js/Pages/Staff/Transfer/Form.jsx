import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import {
    User,
    ArrowLeft,
    ArrowRightLeft,
    AlertCircle,
    Building2,
} from 'lucide-react';

export default function StaffTransferForm({ user, branches, errors }) {
    const [formData, setFormData] = useState({
        to_branch_id: '',
    });
    const [loading, setLoading] = useState(false);

    const handleSubmit = (e) => {
        e.preventDefault();
        setLoading(true);

        router.put(route('employees.transfer.update', user.id), formData, {
            onFinish: () => setLoading(false),
        });
    };

    return (
        <>
            <Head>
                <title>Transfer Employee - Jetty Admin</title>
            </Head>

            {/* Back Button */}
            <div className="mb-8">
                <Link
                    href={route('employees.transfer.index')}
                    className="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition-colors"
                >
                    <ArrowLeft className="w-4 h-4" />
                    <span>Back to Employee List</span>
                </Link>
            </div>

            <div className="max-w-2xl mx-auto space-y-6">
                {/* Employee Info Card */}
                <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                    <div className="px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <div className="flex items-center gap-2">
                            <User className="w-5 h-5 text-slate-400" />
                            <span className="font-semibold text-slate-700">Employee Details</span>
                        </div>
                    </div>
                    <div className="p-6">
                        <div className="flex items-center gap-4">
                            <div className="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center">
                                <User className="w-8 h-8 text-indigo-600" />
                            </div>
                            <div>
                                <h3 className="text-lg font-semibold text-slate-800">{user?.name}</h3>
                                <p className="text-slate-500">
                                    Current Branch:{' '}
                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700">
                                        {user?.branch?.branch_name?.toUpperCase() || 'N/A'}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Transfer Form Card */}
                <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                    <div className="px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <div className="flex items-center gap-2">
                            <ArrowRightLeft className="w-5 h-5 text-slate-400" />
                            <span className="font-semibold text-slate-700">Transfer Details</span>
                        </div>
                    </div>

                    {/* Form */}
                    <form onSubmit={handleSubmit} className="p-6 space-y-6">
                        {/* Global Error */}
                        {errors?.general && (
                            <div className="p-4 rounded-xl bg-red-50 border border-red-200 flex items-center gap-3">
                                <AlertCircle className="w-5 h-5 text-red-600" />
                                <span className="text-red-700 text-sm">{errors.general}</span>
                            </div>
                        )}

                        {/* New Branch Selection */}
                        <div>
                            <label
                                htmlFor="to_branch_id"
                                className="block text-sm font-medium text-slate-700 mb-2"
                            >
                                New Branch <span className="text-red-500">*</span>
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <Building2 className="w-5 h-5 text-slate-400" />
                                </div>
                                <select
                                    id="to_branch_id"
                                    value={formData.to_branch_id}
                                    onChange={(e) => setFormData({ ...formData, to_branch_id: e.target.value })}
                                    className={`w-full pl-12 pr-4 py-3 rounded-xl border ${
                                        errors?.to_branch_id
                                            ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20'
                                            : 'border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20'
                                    } focus:ring-2 outline-none transition-all appearance-none bg-white`}
                                    required
                                >
                                    <option value="">-- Choose Branch --</option>
                                    {branches?.map((branch) => (
                                        <option key={branch.id} value={branch.id}>
                                            {branch.branch_name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            {errors?.to_branch_id && (
                                <p className="mt-1 text-sm text-red-600">{errors.to_branch_id}</p>
                            )}
                            <p className="mt-2 text-sm text-slate-500">
                                Select the branch to transfer the employee to
                            </p>
                        </div>

                        {/* Actions */}
                        <div className="flex items-center justify-end gap-4 pt-4 border-t border-slate-200">
                            <Link
                                href={route('employees.transfer.index')}
                                className="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium transition-colors"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                disabled={loading}
                                className="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-medium transition-colors disabled:opacity-70"
                            >
                                {loading ? (
                                    <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                                ) : (
                                    <ArrowRightLeft className="w-4 h-4" />
                                )}
                                <span>Transfer Employee</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}

StaffTransferForm.layout = (page) => <Layout>{page}</Layout>;
