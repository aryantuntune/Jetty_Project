import { useState } from 'react';
import { router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import { Search, Package, Building } from 'lucide-react';

export default function FromRatesIndex({ items, branches }) {
    const [filters, setFilters] = useState({
        id: '',
        name: '',
        branch_id: '',
    });

    const handleFilter = (e) => {
        e.preventDefault();
        router.get(route('items.from_rates'), filters);
    };

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold text-slate-800">Items from Rates</h1>
                <p className="text-slate-500 mt-1">View items derived from rate configurations</p>
            </div>

            {/* Filters */}
            <form onSubmit={handleFilter} className="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div className="flex items-center gap-2 mb-4">
                    <Search className="w-5 h-5 text-slate-400" />
                    <span className="font-semibold text-slate-700">Search & Filter</span>
                </div>
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input
                        type="text"
                        value={filters.id}
                        onChange={(e) => setFilters({ ...filters, id: e.target.value })}
                        placeholder="Item ID"
                        className="px-3 py-2 border border-slate-300 rounded-lg"
                    />
                    <input
                        type="text"
                        value={filters.name}
                        onChange={(e) => setFilters({ ...filters, name: e.target.value })}
                        placeholder="Item Name"
                        className="px-3 py-2 border border-slate-300 rounded-lg"
                    />
                    <select
                        value={filters.branch_id}
                        onChange={(e) => setFilters({ ...filters, branch_id: e.target.value })}
                        className="px-3 py-2 border border-slate-300 rounded-lg"
                    >
                        <option value="">All Branches</option>
                        {branches?.map((b) => (
                            <option key={b.id} value={b.id}>{b.branch_name}</option>
                        ))}
                    </select>
                    <button
                        type="submit"
                        className="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                    >
                        Search
                    </button>
                </div>
            </form>

            {/* Table */}
            <div className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <table className="w-full">
                    <thead className="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th className="text-left px-6 py-3 text-sm font-semibold text-slate-600">ID</th>
                            <th className="text-left px-6 py-3 text-sm font-semibold text-slate-600">Item Name</th>
                            <th className="text-left px-6 py-3 text-sm font-semibold text-slate-600">Category</th>
                            <th className="text-left px-6 py-3 text-sm font-semibold text-slate-600">Branch</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                        {items?.data?.map((item) => (
                            <tr key={item.id} className="hover:bg-slate-50">
                                <td className="px-6 py-4 text-slate-600">{item.id}</td>
                                <td className="px-6 py-4">
                                    <div className="flex items-center gap-2">
                                        <Package className="w-4 h-4 text-indigo-500" />
                                        <span className="font-medium text-slate-800">{item.item_name}</span>
                                    </div>
                                </td>
                                <td className="px-6 py-4 text-slate-600">{item.category_name || '-'}</td>
                                <td className="px-6 py-4">
                                    <div className="flex items-center gap-2">
                                        <Building className="w-4 h-4 text-slate-400" />
                                        <span className="text-slate-700">{item.branch_name || '-'}</span>
                                    </div>
                                </td>
                            </tr>
                        ))}
                        {(!items?.data || items.data.length === 0) && (
                            <tr>
                                <td colSpan="4" className="px-6 py-12 text-center text-slate-500">
                                    No items found.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {/* Pagination */}
            {items?.links && (
                <div className="flex justify-center gap-2">
                    {items.links.map((link, idx) => (
                        <button
                            key={idx}
                            onClick={() => link.url && router.get(link.url)}
                            disabled={!link.url}
                            className={`px-3 py-1 rounded ${link.active
                                ? 'bg-indigo-600 text-white'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                                } ${!link.url && 'opacity-50 cursor-not-allowed'}`}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ))}
                </div>
            )}
        </div>
    );
}

FromRatesIndex.layout = (page) => <Layout title="Items from Rates">{page}</Layout>;
