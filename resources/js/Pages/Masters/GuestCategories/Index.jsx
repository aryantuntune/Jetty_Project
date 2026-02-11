import { Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import { Plus, Pencil, Trash2, Tag } from 'lucide-react';

export default function Index({ categories }) {
    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this category?')) {
            router.delete(route('guest_categories.destroy', id));
        }
    };

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-slate-800">Guest Categories</h1>
                    <p className="text-slate-500 mt-1">Manage guest type categories</p>
                </div>
                <Link
                    href={route('guest_categories.create')}
                    className="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                >
                    <Plus className="w-4 h-4" />
                    Add Category
                </Link>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <table className="w-full">
                    <thead className="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th className="text-left px-6 py-3 text-sm font-semibold text-slate-600">ID</th>
                            <th className="text-left px-6 py-3 text-sm font-semibold text-slate-600">Name</th>
                            <th className="text-right px-6 py-3 text-sm font-semibold text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                        {categories?.map((cat) => (
                            <tr key={cat.id} className="hover:bg-slate-50">
                                <td className="px-6 py-4 text-slate-600">{cat.id}</td>
                                <td className="px-6 py-4">
                                    <div className="flex items-center gap-2">
                                        <Tag className="w-4 h-4 text-indigo-500" />
                                        <span className="font-medium text-slate-800">{cat.name}</span>
                                    </div>
                                </td>
                                <td className="px-6 py-4">
                                    <div className="flex justify-end gap-2">
                                        <Link
                                            href={route('guest_categories.edit', cat.id)}
                                            className="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg"
                                        >
                                            <Pencil className="w-4 h-4" />
                                        </Link>
                                        <button
                                            onClick={() => handleDelete(cat.id)}
                                            className="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg"
                                        >
                                            <Trash2 className="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                        {(!categories || categories.length === 0) && (
                            <tr>
                                <td colSpan="3" className="px-6 py-12 text-center text-slate-500">
                                    No guest categories found.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
}

Index.layout = (page) => <Layout title="Guest Categories">{page}</Layout>;
