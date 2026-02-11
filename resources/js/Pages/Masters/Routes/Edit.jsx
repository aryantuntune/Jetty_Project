import { useForm, Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import { ArrowLeft, Plus, Trash2 } from 'lucide-react';

export default function Edit({ branches, routeId, routeEntries }) {
    const { data, setData, put, processing, errors } = useForm({
        branches: routeEntries?.map(e => e.branch_id.toString()) || ['', ''],
    });

    const addBranch = () => {
        setData('branches', [...data.branches, '']);
    };

    const removeBranch = (index) => {
        if (data.branches.length > 2) {
            const newBranches = [...data.branches];
            newBranches.splice(index, 1);
            setData('branches', newBranches);
        }
    };

    const updateBranch = (index, value) => {
        const newBranches = [...data.branches];
        newBranches[index] = value;
        setData('branches', newBranches);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('routes.update', routeId));
    };

    return (
        <div className="max-w-2xl mx-auto space-y-6">
            <Link
                href={route('routes.index')}
                className="inline-flex items-center gap-2 text-slate-600 hover:text-indigo-600 transition-colors"
            >
                <ArrowLeft className="w-4 h-4" />
                Back to Routes
            </Link>

            <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h1 className="text-xl font-bold text-slate-800 mb-6">Edit Route #{routeId}</h1>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-2">Route Stops (in sequence)</label>
                        <div className="space-y-3">
                            {data.branches.map((branchId, idx) => (
                                <div key={idx} className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm">
                                        {idx + 1}
                                    </div>
                                    <select
                                        value={branchId}
                                        onChange={(e) => updateBranch(idx, e.target.value)}
                                        className="flex-1 px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <option value="">Select Branch</option>
                                        {branches.map((b) => (
                                            <option key={b.id} value={b.id}>{b.branch_name}</option>
                                        ))}
                                    </select>
                                    {data.branches.length > 2 && (
                                        <button
                                            type="button"
                                            onClick={() => removeBranch(idx)}
                                            className="p-2 text-red-500 hover:bg-red-50 rounded-lg"
                                        >
                                            <Trash2 className="w-4 h-4" />
                                        </button>
                                    )}
                                </div>
                            ))}
                        </div>
                        {errors.branches && <p className="text-red-500 text-sm mt-1">{errors.branches}</p>}

                        <button
                            type="button"
                            onClick={addBranch}
                            className="mt-3 inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700"
                        >
                            <Plus className="w-4 h-4" />
                            Add Stop
                        </button>
                    </div>

                    <div className="flex justify-end gap-3 pt-4 border-t">
                        <Link
                            href={route('routes.index')}
                            className="px-4 py-2 text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {processing ? 'Saving...' : 'Save Changes'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

Edit.layout = (page) => <Layout title="Edit Route">{page}</Layout>;
