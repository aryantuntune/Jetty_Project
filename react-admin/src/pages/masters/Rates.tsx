import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Button, Input, Select, Card, CardHeader, CardTitle, CardContent, Badge } from '@/components/ui';
import { Plus, Pencil, Trash2, Search, RefreshCw, DollarSign } from 'lucide-react';
import { toast } from 'sonner';
import apiClient from '@/lib/axios';
import { formatCurrency } from '@/lib/utils';

interface Rate {
    id: number;
    item_name: string;
    item_rate: number;
    item_lavy: number;
    branch_id: number;
    branch?: { branch_name: string };
}

interface Branch {
    id: number;
    branch_name: string;
}

function extractData<T>(response: any): T {
    if (response.data?.data) return response.data.data;
    return response.data;
}

export function Rates() {
    const queryClient = useQueryClient();
    const [searchQuery, setSearchQuery] = useState('');
    const [filterBranch, setFilterBranch] = useState<string>('');
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingRate, setEditingRate] = useState<Rate | null>(null);
    const [formData, setFormData] = useState({ item_name: '', item_rate: '', item_lavy: '', branch_id: '' });

    // Fetch rates
    const { data: rates = [], isLoading, refetch } = useQuery({
        queryKey: ['admin-rates', filterBranch],
        queryFn: async () => {
            const params = filterBranch ? `?branch_id=${filterBranch}` : '';
            const res = await apiClient.get(`/api/admin/rates${params}`);
            return extractData<Rate[]>(res);
        },
    });

    // Fetch branches for dropdown
    const { data: branches = [] } = useQuery({
        queryKey: ['branches-dropdown'],
        queryFn: async () => {
            const res = await apiClient.get('/api/admin/branches');
            return extractData<Branch[]>(res);
        },
    });

    const createMutation = useMutation({
        mutationFn: (data: any) => apiClient.post('/api/admin/rates', data),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['admin-rates'] });
            toast.success('Rate created successfully');
            closeModal();
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to create'),
    });

    const updateMutation = useMutation({
        mutationFn: ({ id, data }: { id: number; data: any }) =>
            apiClient.put(`/api/admin/rates/${id}`, data),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['admin-rates'] });
            toast.success('Rate updated successfully');
            closeModal();
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to update'),
    });

    const deleteMutation = useMutation({
        mutationFn: (id: number) => apiClient.delete(`/api/admin/rates/${id}`),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['admin-rates'] });
            toast.success('Rate deleted successfully');
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to delete'),
    });

    const filteredRates = rates.filter((r: Rate) =>
        r.item_name?.toLowerCase().includes(searchQuery.toLowerCase())
    );

    const openModal = (rate?: Rate) => {
        if (rate) {
            setEditingRate(rate);
            setFormData({
                item_name: rate.item_name,
                item_rate: rate.item_rate.toString(),
                item_lavy: (rate.item_lavy || 0).toString(),
                branch_id: rate.branch_id.toString(),
            });
        } else {
            setEditingRate(null);
            setFormData({ item_name: '', item_rate: '', item_lavy: '0', branch_id: branches[0]?.id?.toString() || '' });
        }
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setEditingRate(null);
        setFormData({ item_name: '', item_rate: '', item_lavy: '', branch_id: '' });
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        const payload = {
            item_name: formData.item_name,
            item_rate: parseFloat(formData.item_rate),
            item_lavy: parseFloat(formData.item_lavy) || 0,
            branch_id: parseInt(formData.branch_id),
        };
        if (editingRate) {
            updateMutation.mutate({ id: editingRate.id, data: payload });
        } else {
            createMutation.mutate(payload);
        }
    };

    const handleDelete = (id: number) => {
        if (confirm('Are you sure you want to delete this rate?')) {
            deleteMutation.mutate(id);
        }
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Item Rates</h1>
                    <p className="text-gray-500">Manage ticket item rates and levies</p>
                </div>
                <div className="flex gap-2">
                    <Button variant="outline" onClick={() => refetch()}>
                        <RefreshCw className="w-4 h-4" />
                        Refresh
                    </Button>
                    <Button onClick={() => openModal()}>
                        <Plus className="w-4 h-4" />
                        Add Rate
                    </Button>
                </div>
            </div>

            {/* Filters */}
            <Card>
                <CardContent className="p-4">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div className="relative">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <Input
                                placeholder="Search rates..."
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                className="pl-10"
                            />
                        </div>
                        <Select value={filterBranch} onChange={(e) => setFilterBranch(e.target.value)}>
                            <option value="">All Branches</option>
                            {branches.map((b: Branch) => (
                                <option key={b.id} value={b.id}>{b.branch_name}</option>
                            ))}
                        </Select>
                    </div>
                </CardContent>
            </Card>

            {/* Table */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <DollarSign className="w-5 h-5 text-green-600" />
                        All Rates ({filteredRates.length})
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-0">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50 border-b">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Item Name</th>
                                    <th className="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Rate</th>
                                    <th className="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Levy</th>
                                    <th className="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Branch</th>
                                    <th className="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {isLoading ? (
                                    <tr><td colSpan={7} className="px-4 py-8 text-center text-gray-500">Loading...</td></tr>
                                ) : filteredRates.length === 0 ? (
                                    <tr><td colSpan={7} className="px-4 py-8 text-center text-gray-500">No rates found</td></tr>
                                ) : (
                                    filteredRates.map((rate: Rate) => (
                                        <tr key={rate.id} className="hover:bg-gray-50">
                                            <td className="px-4 py-3 text-gray-500">#{rate.id}</td>
                                            <td className="px-4 py-3 font-medium text-gray-900">{rate.item_name}</td>
                                            <td className="px-4 py-3 text-right text-gray-700">{formatCurrency(parseFloat(String(rate.item_rate)) || 0)}</td>
                                            <td className="px-4 py-3 text-right text-gray-500">{formatCurrency(parseFloat(String(rate.item_lavy)) || 0)}</td>
                                            <td className="px-4 py-3 text-right font-semibold text-green-600">
                                                {formatCurrency((parseFloat(String(rate.item_rate)) || 0) + (parseFloat(String(rate.item_lavy)) || 0))}
                                            </td>
                                            <td className="px-4 py-3">
                                                <Badge variant="default">{rate.branch?.branch_name || '-'}</Badge>
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex justify-center gap-1">
                                                    <Button variant="ghost" size="icon" onClick={() => openModal(rate)}>
                                                        <Pencil className="w-4 h-4" />
                                                    </Button>
                                                    <Button variant="ghost" size="icon" onClick={() => handleDelete(rate.id)} className="text-red-500 hover:text-red-700">
                                                        <Trash2 className="w-4 h-4" />
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            {/* Modal */}
            {isModalOpen && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                    <div className="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                        <div className="bg-gradient-to-r from-green-600 to-emerald-600 text-white p-4 flex justify-between items-center">
                            <span className="font-semibold">{editingRate ? 'Edit Rate' : 'Add Rate'}</span>
                            <button onClick={closeModal} className="p-1 hover:bg-white/20 rounded">✕</button>
                        </div>
                        <form onSubmit={handleSubmit} className="p-6 space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                                <Input
                                    value={formData.item_name}
                                    onChange={(e) => setFormData({ ...formData, item_name: e.target.value })}
                                    placeholder="e.g., Adult Passenger"
                                    required
                                />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Rate (₹)</label>
                                    <Input
                                        type="number"
                                        step="0.01"
                                        value={formData.item_rate}
                                        onChange={(e) => setFormData({ ...formData, item_rate: e.target.value })}
                                        placeholder="0.00"
                                        required
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Levy (₹)</label>
                                    <Input
                                        type="number"
                                        step="0.01"
                                        value={formData.item_lavy}
                                        onChange={(e) => setFormData({ ...formData, item_lavy: e.target.value })}
                                        placeholder="0.00"
                                    />
                                </div>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                                <Select
                                    value={formData.branch_id}
                                    onChange={(e) => setFormData({ ...formData, branch_id: e.target.value })}
                                    required
                                >
                                    <option value="">Select Branch</option>
                                    {branches.map((b: Branch) => (
                                        <option key={b.id} value={b.id}>{b.branch_name}</option>
                                    ))}
                                </Select>
                            </div>
                            <div className="flex justify-end gap-3 pt-4">
                                <Button type="button" variant="outline" onClick={closeModal}>Cancel</Button>
                                <Button type="submit" loading={createMutation.isPending || updateMutation.isPending}>
                                    {editingRate ? 'Update' : 'Create'}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
