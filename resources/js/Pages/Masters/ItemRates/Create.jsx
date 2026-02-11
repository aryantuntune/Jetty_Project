import { useForm, Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import { Tag, ArrowLeft, Save, Calculator } from 'lucide-react';

export default function ItemRatesCreate({ branches, categories }) {
    const { data, setData, post, processing, errors } = useForm({
        item_name: '',
        item_short_name: '',
        item_category_id: '',
        item_rate: '',
        item_lavy: '',
        branch_id: '',
        starting_date: '',
        ending_date: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('item-rates.store'));
    };

    const calculateTotal = () => {
        const rate = parseFloat(data.item_rate) || 0;
        const levy = parseFloat(data.item_lavy) || 0;
        return (rate + levy).toFixed(2);
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center space-x-4">
                <Link
                    href={route('item-rates.index')}
                    className="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors"
                >
                    <ArrowLeft className="w-5 h-5 text-slate-600" />
                </Link>
                <div>
                    <h1 className="text-2xl font-bold text-slate-800 tracking-tight">
                        Add Rate Slab
                    </h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Create a new pricing slab for ferry items
                    </p>
                </div>
            </div>

            {/* Form Card */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div className="px-6 py-4 border-b border-slate-200 flex items-center space-x-3">
                    <div className="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                        <Tag className="w-5 h-5 text-amber-600" />
                    </div>
                    <div>
                        <h2 className="font-semibold text-slate-800">Rate Slab Details</h2>
                        <p className="text-sm text-slate-500">Enter the item pricing information</p>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="p-6 space-y-6">
                    {/* Basic Info */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {/* Item Name */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Item Name <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                value={data.item_name}
                                onChange={(e) => setData('item_name', e.target.value)}
                                className={`w-full px-4 py-2.5 rounded-xl border ${errors.item_name ? 'border-red-300' : 'border-slate-200'
                                    } focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm`}
                                placeholder="e.g., Adult Passenger"
                            />
                            {errors.item_name && (
                                <p className="mt-1 text-sm text-red-500">{errors.item_name}</p>
                            )}
                        </div>

                        {/* Short Name */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Short Name
                            </label>
                            <input
                                type="text"
                                value={data.item_short_name}
                                onChange={(e) => setData('item_short_name', e.target.value)}
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                                placeholder="e.g., ADULT"
                            />
                        </div>

                        {/* Category */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Category
                            </label>
                            <select
                                value={data.item_category_id}
                                onChange={(e) => setData('item_category_id', e.target.value)}
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                            >
                                <option value="">Select Category</option>
                                {categories?.map((cat) => (
                                    <option key={cat.id} value={cat.id}>
                                        {cat.category_name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Branch */}
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Branch
                            </label>
                            <select
                                value={data.branch_id}
                                onChange={(e) => setData('branch_id', e.target.value)}
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                            >
                                <option value="">All Branches (Global)</option>
                                {branches?.map((branch) => (
                                    <option key={branch.id} value={branch.id}>
                                        {branch.branch_name}
                                    </option>
                                ))}
                            </select>
                            <p className="mt-1 text-xs text-slate-500">Leave empty for items available at all branches</p>
                        </div>
                    </div>

                    {/* Pricing */}
                    <div className="pt-6 border-t border-slate-200">
                        <h3 className="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                            <Calculator className="w-5 h-5 mr-2 text-indigo-600" />
                            Pricing
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {/* Rate */}
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-2">
                                    Rate (INR) <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value={data.item_rate}
                                    onChange={(e) => setData('item_rate', e.target.value)}
                                    className={`w-full px-4 py-2.5 rounded-xl border ${errors.item_rate ? 'border-red-300' : 'border-slate-200'
                                        } focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm`}
                                    placeholder="0.00"
                                />
                                {errors.item_rate && (
                                    <p className="mt-1 text-sm text-red-500">{errors.item_rate}</p>
                                )}
                            </div>

                            {/* Levy */}
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-2">
                                    Levy (INR)
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value={data.item_lavy}
                                    onChange={(e) => setData('item_lavy', e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                                    placeholder="0.00"
                                />
                            </div>

                            {/* Total Preview */}
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-2">
                                    Total
                                </label>
                                <div className="w-full px-4 py-2.5 rounded-xl border border-green-200 bg-green-50 text-sm">
                                    <span className="font-bold text-green-600 text-lg">
                                        INR {calculateTotal()}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Validity */}
                    <div className="pt-6 border-t border-slate-200">
                        <h3 className="text-lg font-semibold text-slate-800 mb-4">Validity Period</h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {/* Starting Date */}
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-2">
                                    Valid From <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    value={data.starting_date}
                                    onChange={(e) => setData('starting_date', e.target.value)}
                                    className={`w-full px-4 py-2.5 rounded-xl border ${errors.starting_date ? 'border-red-300' : 'border-slate-200'
                                        } focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm`}
                                />
                                {errors.starting_date && (
                                    <p className="mt-1 text-sm text-red-500">{errors.starting_date}</p>
                                )}
                            </div>

                            {/* Ending Date */}
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-2">
                                    Valid Until
                                </label>
                                <input
                                    type="date"
                                    value={data.ending_date}
                                    onChange={(e) => setData('ending_date', e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm"
                                />
                                <p className="mt-1 text-xs text-slate-500">Leave empty for no end date</p>
                            </div>
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-end space-x-3 pt-6 border-t border-slate-200">
                        <Link
                            href={route('item-rates.index')}
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
                            <span>{processing ? 'Saving...' : 'Save Rate Slab'}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

ItemRatesCreate.layout = (page) => <Layout children={page} title="Add Rate Slab" />;
