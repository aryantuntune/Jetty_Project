import { useForm, Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import { Ship, ArrowLeft, Save } from 'lucide-react';

export default function FerryBoatsCreate({ branches }) {
    const { data, setData, post, processing, errors } = useForm({
        number: '',
        name: '',
        branch_id: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('ferryboats.store'));
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center space-x-4">
                <Link
                    href={route('ferryboats.index')}
                    className="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors"
                >
                    <ArrowLeft className="w-5 h-5 text-slate-600" />
                </Link>
                <div>
                    <h1 className="text-2xl font-bold text-slate-800 tracking-tight">
                        Add Ferry Boat
                    </h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Create a new ferry boat in your fleet
                    </p>
                </div>
            </div>

            {/* Form Card */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div className="px-6 py-4 border-b border-slate-200 flex items-center space-x-3">
                    <div className="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                        <Ship className="w-5 h-5 text-purple-600" />
                    </div>
                    <div>
                        <h2 className="font-semibold text-slate-800">Ferry Boat Details</h2>
                        <p className="text-sm text-slate-500">Enter the boat information</p>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="p-6 space-y-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {/* Boat Number */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Boat Number <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                value={data.number}
                                onChange={(e) => setData('number', e.target.value)}
                                className={`w-full px-4 py-2.5 rounded-xl border ${errors.number ? 'border-red-300' : 'border-slate-200'
                                    } focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm`}
                                placeholder="e.g., FB-001"
                            />
                            {errors.number && (
                                <p className="mt-1 text-sm text-red-500">{errors.number}</p>
                            )}
                        </div>

                        {/* Boat Name */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Boat Name <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                className={`w-full px-4 py-2.5 rounded-xl border ${errors.name ? 'border-red-300' : 'border-slate-200'
                                    } focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm`}
                                placeholder="e.g., Sea Star"
                            />
                            {errors.name && (
                                <p className="mt-1 text-sm text-red-500">{errors.name}</p>
                            )}
                        </div>

                        {/* Branch */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Branch <span className="text-red-500">*</span>
                            </label>
                            <select
                                value={data.branch_id}
                                onChange={(e) => setData('branch_id', e.target.value)}
                                className={`w-full px-4 py-2.5 rounded-xl border ${errors.branch_id ? 'border-red-300' : 'border-slate-200'
                                    } focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm`}
                            >
                                <option value="">Select Branch</option>
                                {branches?.map((branch) => (
                                    <option key={branch.id} value={branch.id}>
                                        {branch.branch_name}
                                    </option>
                                ))}
                            </select>
                            {errors.branch_id && (
                                <p className="mt-1 text-sm text-red-500">{errors.branch_id}</p>
                            )}
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-end space-x-3 pt-6 border-t border-slate-200">
                        <Link
                            href={route('ferryboats.index')}
                            className="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white px-5 py-2.5 rounded-xl font-medium transition-colors"
                        >
                            <Save className="w-4 h-4" />
                            <span>{processing ? 'Saving...' : 'Save Ferry Boat'}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

FerryBoatsCreate.layout = (page) => <Layout children={page} title="Add Ferry Boat" />;
