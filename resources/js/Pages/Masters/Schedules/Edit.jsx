import { useForm, Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import { Clock, ArrowLeft, Save } from 'lucide-react';

export default function SchedulesEdit({ schedule, branches }) {
    const { data, setData, put, processing, errors } = useForm({
        hour: schedule?.hour ?? '',
        minute: schedule?.minute ?? '',
        branch_id: schedule?.branch_id || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('schedules.update', schedule.id));
    };

    // Generate hour options (0-23)
    const hours = Array.from({ length: 24 }, (_, i) => i);
    // Generate minute options (0-59)
    const minutes = Array.from({ length: 60 }, (_, i) => i);

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center space-x-4">
                <Link
                    href={route('ferry_schedules.index')}
                    className="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors"
                >
                    <ArrowLeft className="w-5 h-5 text-slate-600" />
                </Link>
                <div>
                    <h1 className="text-2xl font-bold text-slate-800 tracking-tight">
                        Edit Schedule
                    </h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Update departure time details
                    </p>
                </div>
            </div>

            {/* Form Card */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div className="px-6 py-4 border-b border-slate-200 flex items-center space-x-3">
                    <div className="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <Clock className="w-5 h-5 text-emerald-600" />
                    </div>
                    <div>
                        <h2 className="font-semibold text-slate-800">Schedule Details</h2>
                        <p className="text-sm text-slate-500">Modify the departure time</p>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="p-6 space-y-6">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {/* Hour */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Hour <span className="text-red-500">*</span>
                            </label>
                            <select
                                value={data.hour}
                                onChange={(e) => setData('hour', e.target.value)}
                                className={`w-full px-4 py-2.5 rounded-xl border ${errors.hour ? 'border-red-300' : 'border-slate-200'
                                    } focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm`}
                            >
                                <option value="">Select Hour</option>
                                {hours.map((h) => (
                                    <option key={h} value={h}>
                                        {String(h).padStart(2, '0')}
                                    </option>
                                ))}
                            </select>
                            {errors.hour && (
                                <p className="mt-1 text-sm text-red-500">{errors.hour}</p>
                            )}
                        </div>

                        {/* Minute */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Minute <span className="text-red-500">*</span>
                            </label>
                            <select
                                value={data.minute}
                                onChange={(e) => setData('minute', e.target.value)}
                                className={`w-full px-4 py-2.5 rounded-xl border ${errors.minute ? 'border-red-300' : 'border-slate-200'
                                    } focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm`}
                            >
                                <option value="">Select Minute</option>
                                {minutes.map((m) => (
                                    <option key={m} value={m}>
                                        {String(m).padStart(2, '0')}
                                    </option>
                                ))}
                            </select>
                            {errors.minute && (
                                <p className="mt-1 text-sm text-red-500">{errors.minute}</p>
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

                    {/* Preview */}
                    {data.hour !== '' && data.minute !== '' && (
                        <div className="p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                            <p className="text-sm text-emerald-700">
                                Preview: <span className="font-semibold">{String(data.hour).padStart(2, '0')}:{String(data.minute).padStart(2, '0')}</span>
                            </p>
                        </div>
                    )}

                    {/* Actions */}
                    <div className="flex items-center justify-end space-x-3 pt-6 border-t border-slate-200">
                        <Link
                            href={route('ferry_schedules.index')}
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
                            <span>{processing ? 'Saving...' : 'Update Schedule'}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

SchedulesEdit.layout = (page) => <Layout children={page} title="Edit Schedule" />;
