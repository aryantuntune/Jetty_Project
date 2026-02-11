import { useForm, Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import { ArrowLeft } from 'lucide-react';

export default function Edit({ guestCategory }) {
    const { data, setData, put, processing, errors } = useForm({
        name: guestCategory?.name || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('guest_categories.update', guestCategory.id));
    };

    return (
        <div className="max-w-xl mx-auto space-y-6">
            <Link
                href={route('guest_categories.index')}
                className="inline-flex items-center gap-2 text-slate-600 hover:text-indigo-600"
            >
                <ArrowLeft className="w-4 h-4" />
                Back to Categories
            </Link>

            <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h1 className="text-xl font-bold text-slate-800 mb-6">Edit Guest Category</h1>

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Category Name</label>
                        <input
                            type="text"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            className="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        />
                        {errors.name && <p className="text-red-500 text-sm mt-1">{errors.name}</p>}
                    </div>

                    <div className="flex justify-end gap-3 pt-4 border-t">
                        <Link
                            href={route('guest_categories.index')}
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

Edit.layout = (page) => <Layout title="Edit Guest Category">{page}</Layout>;
