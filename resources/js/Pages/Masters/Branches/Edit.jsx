import { Head, Link, useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { Building2, ArrowLeft, Save, AlertCircle } from 'lucide-react';
import Layout from '@/Layouts/Layout';

export default function BranchEdit({ branch }) {
    const form = useForm({
        branch_id: branch.branch_id || '',
        branch_name: branch.branch_name || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        form.put(route('branches.update', branch.id));
    };

    return (
        <>
            <Head title="Edit Branch" />

            {/* Back Link */}
            <div className="mb-8">
                <Link
                    href={route('branches.index')}
                    className="inline-flex items-center space-x-2 text-slate-600 hover:text-slate-800 transition-colors"
                >
                    <ArrowLeft className="w-4 h-4" />
                    <span>Back to Branches</span>
                </Link>
            </div>

            {/* Form Card */}
            <div className="max-w-2xl">
                <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                    {/* Card Header */}
                    <div className="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-indigo-600 to-indigo-700">
                        <div className="flex items-center space-x-3">
                            <div className="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                <Building2 className="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <h2 className="font-semibold text-white">Edit Branch</h2>
                                <p className="text-sm text-indigo-100">
                                    Update the details below
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Form */}
                    <form onSubmit={handleSubmit} className="p-6 space-y-6">
                        {/* Error Summary */}
                        {Object.keys(form.errors).length > 0 && (
                            <div className="p-4 rounded-xl bg-red-50 border border-red-200">
                                <div className="flex items-start space-x-2 text-red-700">
                                    <AlertCircle className="w-5 h-5 flex-shrink-0 mt-0.5" />
                                    <div className="text-sm font-medium space-y-1">
                                        {form.errors.branch_id && (
                                            <p>{form.errors.branch_id}</p>
                                        )}
                                        {form.errors.branch_name && (
                                            <p>{form.errors.branch_name}</p>
                                        )}
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Branch ID */}
                        <div>
                            <label
                                htmlFor="branch_id"
                                className="block text-sm font-medium text-slate-700 mb-2"
                            >
                                Branch ID <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="branch_id"
                                value={form.data.branch_id}
                                onChange={(e) =>
                                    form.setData('branch_id', e.target.value)
                                }
                                className={`w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 ${
                                    form.errors.branch_id
                                        ? 'border-red-500'
                                        : 'border-slate-200'
                                }`}
                                placeholder="Enter branch ID (e.g., BR001)"
                                required
                                autoFocus
                            />
                        </div>

                        {/* Branch Name */}
                        <div>
                            <label
                                htmlFor="branch_name"
                                className="block text-sm font-medium text-slate-700 mb-2"
                            >
                                Branch Name <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="branch_name"
                                value={form.data.branch_name}
                                onChange={(e) =>
                                    form.setData('branch_name', e.target.value)
                                }
                                className={`w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 ${
                                    form.errors.branch_name
                                        ? 'border-red-500'
                                        : 'border-slate-200'
                                }`}
                                placeholder="Enter branch name (e.g., Dabhol Jetty)"
                                required
                            />
                        </div>

                        {/* Form Actions */}
                        <div className="flex items-center justify-end space-x-4 pt-4 border-t border-slate-200">
                            <Link
                                href={route('branches.index')}
                                className="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium transition-colors"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                disabled={form.processing}
                                className="inline-flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-medium transition-colors disabled:opacity-70 disabled:cursor-not-allowed"
                            >
                                {form.processing ? (
                                    <>
                                        <svg
                                            className="animate-spin h-4 w-4"
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
                                        <span>Updating...</span>
                                    </>
                                ) : (
                                    <>
                                        <Save className="w-4 h-4" />
                                        <span>Update Branch</span>
                                    </>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}

BranchEdit.layout = (page) => <Layout children={page} />;
