import { Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import { Plus, Pencil, Trash2, Search, MapPin } from 'lucide-react';

export default function Index({ routeGroups }) {
    const handleDelete = (routeId) => {
        if (confirm('Are you sure you want to delete this route?')) {
            router.delete(route('routes.destroy', routeId));
        }
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-slate-800">Routes</h1>
                    <p className="text-slate-500 mt-1">Manage ferry routes between branches</p>
                </div>
                <Link
                    href={route('routes.create')}
                    className="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                >
                    <Plus className="w-4 h-4" />
                    Add Route
                </Link>
            </div>

            {/* Routes Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {Object.entries(routeGroups || {}).map(([routeId, entries]) => (
                    <div key={routeId} className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div className="bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-3">
                            <div className="flex items-center justify-between">
                                <span className="text-white font-semibold">Route #{routeId}</span>
                                <div className="flex gap-2">
                                    <Link
                                        href={route('routes.edit', routeId)}
                                        className="p-1.5 bg-white/20 rounded-lg text-white hover:bg-white/30 transition-colors"
                                    >
                                        <Pencil className="w-4 h-4" />
                                    </Link>
                                    <button
                                        onClick={() => handleDelete(routeId)}
                                        className="p-1.5 bg-white/20 rounded-lg text-white hover:bg-red-500 transition-colors"
                                    >
                                        <Trash2 className="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div className="p-4">
                            <div className="space-y-2">
                                {entries.map((entry, idx) => (
                                    <div key={entry.id} className="flex items-center gap-3">
                                        <div className="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                            {entry.sequence}
                                        </div>
                                        <MapPin className="w-4 h-4 text-slate-400" />
                                        <span className="text-slate-700">{entry.branch?.branch_name || 'Unknown'}</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                ))}

                {Object.keys(routeGroups || {}).length === 0 && (
                    <div className="col-span-full text-center py-12 text-slate-500">
                        No routes found. Create your first route to get started.
                    </div>
                )}
            </div>
        </div>
    );
}

Index.layout = (page) => <Layout title="Routes">{page}</Layout>;
