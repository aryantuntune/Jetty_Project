import { useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import {
    UserPlus,
    ArrowLeft,
    Save,
    AlertCircle,
    User,
    Tag,
    Building2,
} from 'lucide-react';

export default function GuestsCreate({ categories, branches, errors }) {
    const { auth } = usePage().props;
    const user = auth?.user;
    const canSelectBranch = user && [1, 2].includes(user.role_id);

    const [formData, setFormData] = useState({
        name: '',
        category_id: '',
        branch_id: '',
    });
    const [loading, setLoading] = useState(false);

    const handleSubmit = (e) => {
        e.preventDefault();
        setLoading(true);

        router.post(route('guests.store'), formData, {
            onFinish: () => setLoading(false),
        });
    };

    return (
        <>
            <Head>
                <title>Add Guest - Jetty Admin</title>
            </Head>

            {/* Back Button */}
            <div className="mb-8">
                <Link
                    href={route('guests.index')}
                    className="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition-colors"
                >
                    <ArrowLeft className="w-4 h-4" />
                    <span>Back to Guests</span>
                </Link>
            </div>

            {/* Form Card */}
            <div className="max-w-2xl">
                <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                    {/* Header */}
                    <div className="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-orange-600 to-orange-700">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                <UserPlus className="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <h2 className="font-semibold text-white">Add New Guest</h2>
                                <p className="text-sm text-orange-100">Fill in the details below</p>
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

                        {/* Guest Name */}
                        <div>
                            <label
                                htmlFor="name"
                                className="block text-sm font-medium text-slate-700 mb-2"
                            >
                                Guest Name <span className="text-red-500">*</span>
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <User className="w-5 h-5 text-slate-400" />
                                </div>
                                <input
                                    type="text"
                                    id="name"
                                    value={formData.name}
                                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                                    className={`w-full pl-12 pr-4 py-3 rounded-xl border ${
                                        errors?.name
                                            ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20'
                                            : 'border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20'
                                    } focus:ring-2 outline-none transition-all`}
                                    placeholder="Enter guest name"
                                    required
                                    autoFocus
                                />
                            </div>
                            {errors?.name && (
                                <p className="mt-1 text-sm text-red-600">{errors.name}</p>
                            )}
                        </div>

                        {/* Guest Category */}
                        <div>
                            <label
                                htmlFor="category_id"
                                className="block text-sm font-medium text-slate-700 mb-2"
                            >
                                Guest Category <span className="text-red-500">*</span>
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <Tag className="w-5 h-5 text-slate-400" />
                                </div>
                                <select
                                    id="category_id"
                                    value={formData.category_id}
                                    onChange={(e) => setFormData({ ...formData, category_id: e.target.value })}
                                    className={`w-full pl-12 pr-4 py-3 rounded-xl border ${
                                        errors?.category_id
                                            ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20'
                                            : 'border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20'
                                    } focus:ring-2 outline-none transition-all appearance-none bg-white`}
                                    required
                                >
                                    <option value="">-- Select Category --</option>
                                    {categories?.map((category) => (
                                        <option key={category.id} value={category.id}>
                                            {category.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            {errors?.category_id && (
                                <p className="mt-1 text-sm text-red-600">{errors.category_id}</p>
                            )}
                        </div>

                        {/* Branch Selection (Admin only) */}
                        {canSelectBranch && (
                            <div>
                                <label
                                    htmlFor="branch_id"
                                    className="block text-sm font-medium text-slate-700 mb-2"
                                >
                                    Select Branch <span className="text-red-500">*</span>
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
                        )}

                        {/* Actions */}
                        <div className="flex items-center justify-end gap-4 pt-4 border-t border-slate-200">
                            <Link
                                href={route('guests.index')}
                                className="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium transition-colors"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                disabled={loading}
                                className="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-xl font-medium transition-colors disabled:opacity-70"
                            >
                                {loading ? (
                                    <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                                ) : (
                                    <Save className="w-4 h-4" />
                                )}
                                <span>Save Guest</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}

GuestsCreate.layout = (page) => <Layout>{page}</Layout>;
