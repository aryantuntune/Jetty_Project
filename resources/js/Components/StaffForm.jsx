import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { ArrowLeft, Eye, EyeOff, AlertCircle, Save } from 'lucide-react';
import { roleConfig } from './StaffTable';

/**
 * Reusable Staff Form Component for Create/Edit operations
 *
 * @param {Object} props
 * @param {string} props.role - Role key from roleConfig (admin, manager, operator, checker)
 * @param {React.ComponentType} props.Icon - Lucide icon component
 * @param {Object} props.staff - Existing staff data for edit mode (null for create)
 * @param {Array} props.branches - List of branches
 * @param {Array} props.ferryboats - List of ferry boats
 * @param {boolean} props.showBranch - Whether to show branch field
 * @param {boolean} props.showFerry - Whether to show ferry field
 * @param {boolean} props.branchRequired - Whether branch is required
 * @param {boolean} props.ferryRequired - Whether ferry is required
 */
export default function StaffForm({
    role,
    Icon,
    staff = null,
    branches = [],
    ferryboats = [],
    showBranch = true,
    showFerry = true,
    branchRequired = false,
    ferryRequired = false,
}) {
    const [showPassword, setShowPassword] = useState(false);
    const config = roleConfig[role];
    const isEdit = !!staff;

    const form = useForm({
        name: staff?.name || '',
        email: staff?.email || '',
        password: '',
        mobile: staff?.mobile || '',
        branch_id: staff?.branch_id || '',
        ferry_boat_id: staff?.ferry_boat_id || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEdit) {
            form.put(route(config.routes.edit.replace('.edit', '.update'), staff.id));
        } else {
            form.post(route(config.routes.create.replace('.create', '.store')));
        }
    };

    return (
        <>
            <Head title={`${isEdit ? 'Edit' : 'Add'} ${config.singular}`} />

            {/* Header */}
            <div className="mb-8">
                <Link
                    href={route(config.routes.index)}
                    className="inline-flex items-center space-x-2 text-slate-600 hover:text-slate-800 mb-4 transition-colors"
                >
                    <ArrowLeft className="w-4 h-4" />
                    <span>Back to {config.title}</span>
                </Link>
                <h1 className="text-2xl font-bold text-slate-800 tracking-tight">
                    {isEdit ? 'Edit' : 'Add'} {config.singular}
                </h1>
                <p className="mt-1 text-sm text-slate-500">
                    {isEdit ? `Update ${config.singular.toLowerCase()} information` : `Create a new ${config.singular.toLowerCase()}`}
                </p>
            </div>

            {/* Form Card */}
            <div className="max-w-2xl">
                <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <div className="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-indigo-600 to-indigo-700">
                        <div className="flex items-center space-x-3">
                            <div className="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                <Icon className="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <h2 className="font-semibold text-white">{config.singular} Details</h2>
                                <p className="text-sm text-indigo-100">Fill in the information below</p>
                            </div>
                        </div>
                    </div>

                    <form onSubmit={handleSubmit} className="p-6 space-y-6">
                        {/* Error Summary */}
                        {Object.keys(form.errors).length > 0 && (
                            <div className="p-4 rounded-xl bg-red-50 border border-red-200">
                                <div className="flex items-start space-x-2 text-red-700">
                                    <AlertCircle className="w-5 h-5 flex-shrink-0 mt-0.5" />
                                    <div className="text-sm font-medium space-y-1">
                                        {Object.values(form.errors).map((error, i) => (
                                            <p key={i}>{error}</p>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Name */}
                        <div>
                            <label htmlFor="name" className="block text-sm font-medium text-slate-700 mb-2">
                                Full Name <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                value={form.data.name}
                                onChange={(e) => form.setData('name', e.target.value)}
                                className={`w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 ${
                                    form.errors.name ? 'border-red-500' : 'border-slate-200'
                                }`}
                                placeholder="John Doe"
                                required
                                autoFocus
                            />
                        </div>

                        {/* Email */}
                        <div>
                            <label htmlFor="email" className="block text-sm font-medium text-slate-700 mb-2">
                                Email Address <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                value={form.data.email}
                                onChange={(e) => form.setData('email', e.target.value)}
                                className={`w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 ${
                                    form.errors.email ? 'border-red-500' : 'border-slate-200'
                                }`}
                                placeholder={`${role}@example.com`}
                                required
                            />
                        </div>

                        {/* Password */}
                        <div>
                            <label htmlFor="password" className="block text-sm font-medium text-slate-700 mb-2">
                                Password {!isEdit && <span className="text-red-500">*</span>}
                                {isEdit && <span className="text-slate-400 font-normal">(leave blank to keep current)</span>}
                            </label>
                            <div className="relative">
                                <input
                                    type={showPassword ? 'text' : 'password'}
                                    id="password"
                                    value={form.data.password}
                                    onChange={(e) => form.setData('password', e.target.value)}
                                    className={`w-full px-4 py-3 pr-12 rounded-xl border transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 ${
                                        form.errors.password ? 'border-red-500' : 'border-slate-200'
                                    }`}
                                    placeholder="Minimum 6 characters"
                                    required={!isEdit}
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPassword(!showPassword)}
                                    className="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                                    tabIndex={-1}
                                    aria-label={showPassword ? 'Hide password' : 'Show password'}
                                >
                                    {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                                </button>
                            </div>
                        </div>

                        {/* Mobile */}
                        <div>
                            <label htmlFor="mobile" className="block text-sm font-medium text-slate-700 mb-2">
                                Mobile Number
                            </label>
                            <input
                                type="tel"
                                id="mobile"
                                value={form.data.mobile}
                                onChange={(e) => form.setData('mobile', e.target.value)}
                                className="w-full px-4 py-3 rounded-xl border border-slate-200 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                                placeholder="+91 9876543210"
                            />
                        </div>

                        {/* Branch */}
                        {showBranch && (
                            <div>
                                <label htmlFor="branch_id" className="block text-sm font-medium text-slate-700 mb-2">
                                    Branch {branchRequired && <span className="text-red-500">*</span>}
                                </label>
                                <select
                                    id="branch_id"
                                    value={form.data.branch_id}
                                    onChange={(e) => form.setData('branch_id', e.target.value)}
                                    className={`w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 ${
                                        form.errors.branch_id ? 'border-red-500' : 'border-slate-200'
                                    }`}
                                    required={branchRequired}
                                >
                                    <option value="">Select Branch {branchRequired ? '' : '(Optional)'}</option>
                                    {branches.map((branch) => (
                                        <option key={branch.id} value={branch.id}>
                                            {branch.branch_name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        )}

                        {/* Ferry Boat */}
                        {showFerry && (
                            <div>
                                <label htmlFor="ferry_boat_id" className="block text-sm font-medium text-slate-700 mb-2">
                                    Ferry Boat {ferryRequired && <span className="text-red-500">*</span>}
                                </label>
                                <select
                                    id="ferry_boat_id"
                                    value={form.data.ferry_boat_id}
                                    onChange={(e) => form.setData('ferry_boat_id', e.target.value)}
                                    className={`w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 ${
                                        form.errors.ferry_boat_id ? 'border-red-500' : 'border-slate-200'
                                    }`}
                                    required={ferryRequired}
                                >
                                    <option value="">Select Ferry Boat {ferryRequired ? '' : '(Optional)'}</option>
                                    {ferryboats.map((ferry) => (
                                        <option key={ferry.id} value={ferry.id}>
                                            {ferry.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        )}

                        {/* Actions */}
                        <div className="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
                            <Link
                                href={route(config.routes.index)}
                                className="px-6 py-2.5 text-slate-700 font-medium rounded-xl border border-slate-300 hover:bg-slate-50 transition-colors"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                disabled={form.processing}
                                className="inline-flex items-center space-x-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {form.processing ? (
                                    <>
                                        <svg className="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                        </svg>
                                        <span>{isEdit ? 'Updating...' : 'Creating...'}</span>
                                    </>
                                ) : (
                                    <>
                                        <Save className="w-4 h-4" />
                                        <span>{isEdit ? 'Update' : 'Create'} {config.singular}</span>
                                    </>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}
