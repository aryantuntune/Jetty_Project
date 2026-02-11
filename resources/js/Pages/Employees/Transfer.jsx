import { useForm, Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import { ArrowLeft, ArrowRight, User, Building } from 'lucide-react';

export default function Transfer({ user, branches }) {
    const { data, setData, post, processing, errors } = useForm({
        to_branch_id: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('employees.transfer', user.id));
    };

    return (
        <div className="max-w-xl mx-auto space-y-6">
            <Link
                href={route('employees.transfer.index')}
                className="inline-flex items-center gap-2 text-slate-600 hover:text-indigo-600"
            >
                <ArrowLeft className="w-4 h-4" />
                Back to Employees
            </Link>

            <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h1 className="text-xl font-bold text-slate-800 mb-6">Transfer Employee</h1>

                {/* Employee Info */}
                <div className="bg-slate-50 rounded-lg p-4 mb-6">
                    <div className="flex items-center gap-4">
                        <div className="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                            <User className="w-6 h-6 text-indigo-600" />
                        </div>
                        <div>
                            <p className="font-semibold text-slate-800">{user?.name}</p>
                            <p className="text-slate-500 text-sm">{user?.email}</p>
                        </div>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Current Branch */}
                    <div className="flex items-center gap-4">
                        <div className="flex-1">
                            <label className="block text-sm font-medium text-slate-500 mb-1">Current Branch</label>
                            <div className="flex items-center gap-2 px-4 py-3 bg-slate-100 rounded-lg">
                                <Building className="w-5 h-5 text-slate-400" />
                                <span className="font-medium text-slate-700">{user?.branch?.branch_name || 'Not Assigned'}</span>
                            </div>
                        </div>

                        <ArrowRight className="w-5 h-5 text-indigo-500 mt-6" />

                        <div className="flex-1">
                            <label className="block text-sm font-medium text-slate-700 mb-1">New Branch</label>
                            <select
                                value={data.to_branch_id}
                                onChange={(e) => setData('to_branch_id', e.target.value)}
                                className="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="">Select Branch</option>
                                {branches?.filter((b) => b.id !== user?.branch_id).map((b) => (
                                    <option key={b.id} value={b.id}>{b.branch_name}</option>
                                ))}
                            </select>
                            {errors.to_branch_id && <p className="text-red-500 text-sm mt-1">{errors.to_branch_id}</p>}
                        </div>
                    </div>

                    <div className="flex justify-end gap-3 pt-4 border-t">
                        <Link
                            href={route('employees.transfer.index')}
                            className="px-4 py-2 text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing || !data.to_branch_id}
                            className="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {processing ? 'Transferring...' : 'Confirm Transfer'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

Transfer.layout = (page) => <Layout title="Transfer Employee">{page}</Layout>;
