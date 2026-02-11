import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import {
    Tag,
    ArrowLeft,
    Save,
    AlertCircle,
} from 'lucide-react';

export default function ItemCategoriesEdit({ itemCategory, errors }) {
    const [formData, setFormData] = useState({
        category_name: itemCategory?.category_name || '',
    });
    const [loading, setLoading] = useState(false);

    const handleSubmit = (e) => {
        e.preventDefault();
        setLoading(true);

        router.put(route('item_categories.update', itemCategory.id), formData, {
            onFinish: () => setLoading(false),
        });
    };

    return (
        <>
            <Head>
                <title>Edit Item Category - Jetty Admin</title>
            </Head>

            {/* Back Button */}
            <div className="mb-8">
                <Link
                    href={route('item_categories.index')}
                    className="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition-colors"
                >
                    <ArrowLeft className="w-4 h-4" />
                    <span>Back to Categories</span>
                </Link>
            </div>

            {/* Form Card */}
            <div className="max-w-2xl">
                <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                    {/* Header */}
                    <div className="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-indigo-600 to-indigo-700">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                <Tag className="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <h2 className="font-semibold text-white">Edit Item Category</h2>
                                <p className="text-sm text-indigo-100">Update the details below</p>
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

                        {/* Category ID (read-only) */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Category ID
                            </label>
                            <div className="px-4 py-3 rounded-xl bg-slate-100 text-slate-600 font-medium">
                                #{itemCategory?.id}
                            </div>
                        </div>

                        {/* Category Name */}
                        <div>
                            <label
                                htmlFor="category_name"
                                className="block text-sm font-medium text-slate-700 mb-2"
                            >
                                Category Name <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="category_name"
                                value={formData.category_name}
                                onChange={(e) => setFormData({ ...formData, category_name: e.target.value })}
                                className={`w-full px-4 py-3 rounded-xl border ${errors?.category_name
                                        ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20'
                                        : 'border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20'
                                    } focus:ring-2 outline-none transition-all`}
                                placeholder="Enter category name"
                                required
                                autoFocus
                            />
                            {errors?.category_name && (
                                <p className="mt-1 text-sm text-red-600">{errors.category_name}</p>
                            )}
                            <p className="mt-2 text-sm text-slate-500">
                                Changing the category name will affect all associated item rates
                            </p>
                        </div>

                        {/* Actions */}
                        <div className="flex items-center justify-end gap-4 pt-4 border-t border-slate-200">
                            <Link
                                href={route('item_categories.index')}
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
                                    <Save className="w-4 h-4" />
                                )}
                                <span>Update Category</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}

ItemCategoriesEdit.layout = (page) => <Layout>{page}</Layout>;
