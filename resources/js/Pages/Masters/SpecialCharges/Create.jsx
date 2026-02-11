import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import {
    BadgeDollarSign,
    ArrowLeft,
    Save,
    AlertCircle,
    Building2,
    IndianRupee,
} from 'lucide-react';

export default function SpecialChargesCreate({ branches, errors }) {
    const [formData, setFormData] = useState({
        branch_id: '',
        special_charge: '',
    });
    const [loading, setLoading] = useState(false);

    const handleSubmit = (e) => {
        e.preventDefault();
        setLoading(true);

        router.post(route('special-charges.store'), formData, {
            onFinish: () => setLoading(false),
        });
    };

    return (
        <>
            <Head>
                <title>Add Special Charge - Jetty Admin</title>
            </Head>

            {/* Back Button */}
            <div className="mb-8">
                <Link
                    href={route('special-charges.index')}
                    className="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition-colors"
                >
                    <ArrowLeft className="w-4 h-4" />
                    <span>Back to Special Charges</span>
                </Link>
            </div>

            {/* Form Card */}
            <div className="max-w-2xl">
                <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                    {/* Header */}
                    <div className="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-emerald-600 to-emerald-700">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                <BadgeDollarSign className="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <h2 className="font-semibold text-white">Add Special Charge</h2>
                                <p className="text-sm text-emerald-100">Create a new branch-specific special charge</p>
                            </div>
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

                        {/* Branch Selection */}
                        <div>
                            <label
                                htmlFor="branch_id"
                                className="block text-sm font-medium text-slate-700 mb-2"
                            >
                                Branch <span className="text-red-500">*</span>
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <Building2 className="w-5 h-5 text-slate-400" />
                                </div>
                                <select
                                    id="branch_id"
                                    value={formData.branch_id}
                                    onChange={(e) => setFormData({ ...formData, branch_id: e.target.value })}
                                    className={`w-full pl-12 pr-4 py-3 rounded-xl border ${
                                        errors?.branch_id
                                            ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20'
                                            : 'border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20'
                                    } focus:ring-2 outline-none transition-all appearance-none bg-white`}
                                    required
                                >
                                    <option value="">-- Select Branch --</option>
                                    {branches?.map((branch) => (
                                        <option key={branch.id} value={branch.id}>
                                            {branch.branch_name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            {errors?.branch_id && (
                                <p className="mt-1 text-sm text-red-600">{errors.branch_id}</p>
                            )}
                        </div>

                        {/* Special Charge Amount */}
                        <div>
                            <label
                                htmlFor="special_charge"
                                className="block text-sm font-medium text-slate-700 mb-2"
                            >
                                Special Charge Amount <span className="text-red-500">*</span>
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <IndianRupee className="w-5 h-5 text-slate-400" />
                                </div>
                                <input
                                    type="number"
                                    id="special_charge"
                                    step="0.01"
                                    min="0"
                                    value={formData.special_charge}
                                    onChange={(e) => setFormData({ ...formData, special_charge: e.target.value })}
                                    className={`w-full pl-12 pr-4 py-3 rounded-xl border ${
                                        errors?.special_charge
                                            ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20'
                                            : 'border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20'
                                    } focus:ring-2 outline-none transition-all`}
                                    placeholder="0.00"
                                    required
                                />
                            </div>
                            {errors?.special_charge && (
                                <p className="mt-1 text-sm text-red-600">{errors.special_charge}</p>
                            )}
                            <p className="mt-2 text-sm text-slate-500">
                                Enter the special charge amount for this branch
                            </p>
                        </div>

                        {/* Actions */}
                        <div className="flex items-center justify-end gap-4 pt-4 border-t border-slate-200">
                            <Link
                                href={route('special-charges.index')}
                                className="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium transition-colors"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                disabled={loading}
                                className="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-medium transition-colors disabled:opacity-70"
                            >
                                {loading ? (
                                    <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                                ) : (
                                    <Save className="w-4 h-4" />
                                )}
                                <span>Save Special Charge</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}

SpecialChargesCreate.layout = (page) => <Layout>{page}</Layout>;
