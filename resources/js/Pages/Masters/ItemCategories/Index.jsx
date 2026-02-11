import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import {
    Tags,
    Tag,
    Plus,
    Search,
    X,
    Pencil,
    Trash2,
    Filter,
    ChevronDown,
    ChevronRight,
    Package,
    IndianRupee,
} from 'lucide-react';

export default function ItemCategoriesIndex({ categories, total, filters }) {
    const [searchFilters, setSearchFilters] = useState({
        id: filters?.id || '',
        category_name: filters?.category_name || '',
    });
    const [expandedCategories, setExpandedCategories] = useState({});

    const toggleCategory = (categoryId) => {
        setExpandedCategories(prev => ({
            ...prev,
            [categoryId]: !prev[categoryId]
        }));
    };

    const expandAll = () => {
        const allExpanded = {};
        categories?.forEach(cat => {
            allExpanded[cat.id] = true;
        });
        setExpandedCategories(allExpanded);
    };

    const collapseAll = () => {
        setExpandedCategories({});
    };

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('item_categories.index'), searchFilters, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const clearFilters = () => {
        setSearchFilters({ id: '', category_name: '' });
        router.get(route('item_categories.index'));
    };

    const handleDelete = (category) => {
        if (confirm(`Delete category "${category.category_name}"?`)) {
            router.delete(route('item_categories.destroy', category.id));
        }
    };

    return (
        <>
            <Head>
                <title>Item Categories - Jetty Admin</title>
            </Head>

            {/* Page Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-slate-800">Item Categories</h1>
                    <p className="mt-1 text-sm text-slate-500">Manage ticket item categories with nested items</p>
                </div>
                <div className="mt-4 sm:mt-0 flex gap-2">
                    <button
                        onClick={expandAll}
                        className="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-medium transition-colors"
                    >
                        Expand All
                    </button>
                    <button
                        onClick={collapseAll}
                        className="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-medium transition-colors"
                    >
                        Collapse All
                    </button>
                    <Link
                        href={route('item_categories.create')}
                        className="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors shadow-sm"
                    >
                        <Plus className="w-5 h-5" />
                        <span>Add Category</span>
                    </Link>
                </div>
            </div>

            {/* Filters Card */}
            <div className="bg-white rounded-2xl border border-slate-200 mb-6 overflow-hidden">
                <div className="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 className="font-semibold text-slate-700 flex items-center gap-2">
                        <Filter className="w-4 h-4" />
                        <span>Filters</span>
                    </h2>
                </div>
                <form onSubmit={handleSearch} className="p-6">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Category ID
                            </label>
                            <input
                                type="text"
                                value={searchFilters.id}
                                onChange={(e) => setSearchFilters({ ...searchFilters, id: e.target.value })}
                                placeholder="Search by ID..."
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Category Name
                            </label>
                            <input
                                type="text"
                                value={searchFilters.category_name}
                                onChange={(e) => setSearchFilters({ ...searchFilters, category_name: e.target.value })}
                                placeholder="Search by name..."
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                            />
                        </div>
                        <div className="flex items-end gap-2">
                            <button
                                type="submit"
                                className="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-xl font-medium transition-colors"
                            >
                                <Search className="w-4 h-4" />
                                <span>Search</span>
                            </button>
                            <button
                                type="button"
                                onClick={clearFilters}
                                className="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors"
                            >
                                <X className="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {/* Nested Category View */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div className="px-6 py-4 border-b border-slate-200 flex items-center gap-3">
                    <div className="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <Tags className="w-5 h-5 text-indigo-600" />
                    </div>
                    <div>
                        <h2 className="font-semibold text-slate-800">All Categories</h2>
                        <p className="text-sm text-slate-500">
                            Total: {total} categories • Click a category to view items
                        </p>
                    </div>
                </div>

                <div className="divide-y divide-slate-100">
                    {categories?.length > 0 ? (
                        categories.map((cat) => (
                            <div key={cat.id} className="bg-white">
                                {/* Category Row */}
                                <div
                                    className="flex items-center justify-between px-6 py-4 hover:bg-slate-50/50 transition-colors cursor-pointer"
                                    onClick={() => toggleCategory(cat.id)}
                                >
                                    <div className="flex items-center gap-4">
                                        <button className="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center transition-colors">
                                            {expandedCategories[cat.id] ? (
                                                <ChevronDown className="w-5 h-5 text-slate-500" />
                                            ) : (
                                                <ChevronRight className="w-5 h-5 text-slate-500" />
                                            )}
                                        </button>
                                        <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white">
                                            <Tag className="w-5 h-5" />
                                        </div>
                                        <div>
                                            <h3 className="font-semibold text-slate-800">
                                                {cat.category_name}
                                            </h3>
                                            <p className="text-sm text-slate-500">
                                                {cat.items?.length || 0} items
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-2" onClick={(e) => e.stopPropagation()}>
                                        <span className="px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                            #{cat.id}
                                        </span>
                                        <Link
                                            href={route('item_categories.edit', cat.id)}
                                            className="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors flex items-center justify-center"
                                            title="Edit Category"
                                        >
                                            <Pencil className="w-4 h-4" />
                                        </Link>
                                        <button
                                            onClick={() => handleDelete(cat)}
                                            className="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors flex items-center justify-center"
                                            title="Delete Category"
                                        >
                                            <Trash2 className="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>

                                {/* Nested Items */}
                                {expandedCategories[cat.id] && (
                                    <div className="bg-slate-50/50 border-t border-slate-100">
                                        {cat.items?.length > 0 ? (
                                            <div className="divide-y divide-slate-100">
                                                {cat.items.map((item) => (
                                                    <div
                                                        key={item.id}
                                                        className="flex items-center justify-between pl-20 pr-6 py-3 hover:bg-slate-100/50 transition-colors"
                                                    >
                                                        <div className="flex items-center gap-3">
                                                            <div className="w-8 h-8 rounded-lg bg-slate-200 flex items-center justify-center">
                                                                <Package className="w-4 h-4 text-slate-600" />
                                                            </div>
                                                            <div>
                                                                <p className="font-medium text-slate-700 text-sm">
                                                                    {item.item_name}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div className="flex items-center gap-4">
                                                            <div className="flex items-center gap-1 text-green-600 font-semibold">
                                                                <IndianRupee className="w-3.5 h-3.5" />
                                                                <span>{parseFloat(item.item_rate || 0).toFixed(2)}</span>
                                                            </div>
                                                            {item.item_lavy > 0 && (
                                                                <span className="px-2 py-0.5 rounded text-xs bg-amber-100 text-amber-700">
                                                                    Levy: ₹{parseFloat(item.item_lavy).toFixed(2)}
                                                                </span>
                                                            )}
                                                            <span className={`px-2 py-0.5 rounded text-xs ${item.is_active
                                                                    ? 'bg-green-100 text-green-700'
                                                                    : 'bg-red-100 text-red-700'
                                                                }`}>
                                                                {item.is_active ? 'Active' : 'Inactive'}
                                                            </span>
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        ) : (
                                            <div className="pl-20 pr-6 py-6 text-center text-slate-500 text-sm">
                                                No items in this category
                                            </div>
                                        )}

                                        {/* Add Item Link */}
                                        <div className="pl-20 pr-6 py-3 border-t border-slate-100">
                                            <Link
                                                href={route('item_rates.create') + `?category_id=${cat.id}`}
                                                className="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 text-sm font-medium"
                                            >
                                                <Plus className="w-4 h-4" />
                                                Add Item to {cat.category_name}
                                            </Link>
                                        </div>
                                    </div>
                                )}
                            </div>
                        ))
                    ) : (
                        <div className="px-6 py-12 text-center">
                            <div className="flex flex-col items-center">
                                <div className="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                    <Tags className="w-8 h-8 text-slate-400" />
                                </div>
                                <h3 className="text-lg font-medium text-slate-800 mb-1">
                                    No categories found
                                </h3>
                                <p className="text-sm text-slate-500">
                                    Try adjusting your search or add a new category.
                                </p>
                            </div>
                        </div>
                    )}
                </div>

                <div className="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    <p className="text-sm text-slate-600">
                        Showing <span className="font-medium">{categories?.length || 0}</span> of{' '}
                        <span className="font-medium">{total}</span> categories
                    </p>
                </div>
            </div>
        </>
    );
}

ItemCategoriesIndex.layout = (page) => <Layout>{page}</Layout>;
