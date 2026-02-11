import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Button, Input, Select, Card, CardHeader, CardTitle, CardContent, Badge } from '@/components/ui';
import { Plus, Pencil, Trash2, RefreshCw, BadgeDollarSign } from 'lucide-react';
import { toast } from 'sonner';
import apiClient from '@/lib/axios';
import { formatCurrency } from '@/lib/utils';

interface Charge {
    id: number;
    special_charge: number;
    branch_id: number;
    branch_name?: string;
    created_at?: string;
}

interface Branch {
    id: number;
    branch_name: string;
}

function extractData<T>(response: any): T {
    if (response.data?.data) return response.data.data;
    return response.data;
}

export function SpecialCharges() {
    const queryClient = useQueryClient();
    const [filterBranch, setFilterBranch] = useState<string>('');
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingCharge, setEditingCharge] = useState<Charge | null>(null);
    const [formData, setFormData] = useState({ special_charge: '', branch_id: '' });

    // Fetch charges
    const { data: charges = [], isLoading, refetch } = useQuery({
        queryKey: ['special-charges', filterBranch],
        queryFn: async () => {
            const params = filterBranch ? `?branch_id=${filterBranch}` : '';
            const res = await apiClient.get(`/api/admin/special-charges${params}`);
            return extractData<Charge[]>(res);
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
        mutationFn: (data: any) => apiClient.post('/api/admin/special-charges', data),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['special-charges'] });
            toast.success('Special charge created');
            closeModal();
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to create'),
    });

    const updateMutation = useMutation({
        mutationFn: ({ id, data }: { id: number; data: any }) =>
            apiClient.put(`/api/admin/special-charges/${id}`, data),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['special-charges'] });
            toast.success('Special charge updated');
            closeModal();
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to update'),
    });

    const deleteMutation = useMutation({
        mutationFn: (id: number) => apiClient.delete(`/api/admin/special-charges/${id}`),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['special-charges'] });
            toast.success('Charge deleted');
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to delete'),
    });

    const openModal = (charge?: Charge) => {
        if (charge) {
            setEditingCharge(charge);
            setFormData({
                special_charge: charge.special_charge?.toString() || '',
                branch_id: charge.branch_id?.toString() || '',
            });
        } else {
            setEditingCharge(null);
            setFormData({ special_charge: '', branch_id: branches[0]?.id?.toString() || '' });
        }
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setEditingCharge(null);
        setFormData({ special_charge: '', branch_id: '' });
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        const payload = {
            special_charge: parseFloat(formData.special_charge),
            branch_id: parseInt(formData.branch_id),
        };
        if (editingCharge) {
            updateMutation.mutate({ id: editingCharge.id, data: payload });
        } else {
            createMutation.mutate(payload);
        }
    };

    const handleDelete = (id: number) => {
        if (confirm('Delete this special charge?')) {
            deleteMutation.mutate(id);
        }
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Special Charges</h1>
                    <p className="text-gray-500">Manage branch-specific special charges</p>
                </div>
                <div className="flex gap-2">
                    <Button variant="outline" onClick={() => refetch()}>
                        <RefreshCw className="w-4 h-4" />
                        Refresh
                    </Button>
                    <Button onClick={() => openModal()}>
                        <Plus className="w-4 h-4" />
                        Add Special Charge
                    </Button>
                </div>
            </div>

            {/* Filters */}
            <Card>
                <CardContent className="p-4">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Select value={filterBranch} onChange={(e) => setFilterBranch(e.target.value)}>
                            <option value="">All Branches</option>
                            {branches.map((b: Branch) => (
                                <option key={b.id} value={b.id}>{b.branch_name}</option>
                            ))}
                        </Select>
                        <Button variant="outline" onClick={() => setFilterBranch('')}>
                            Reset Filter
                        </Button>
                    </div>
                </CardContent>
            </Card>

            {/* Table */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <BadgeDollarSign className="w-5 h-5 text-blue-600" />
                        All Charges ({charges.length})
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-0">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50 border-b">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Branch</th>
                                    <th className="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Special Charge</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created At</th>
                                    <th className="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {isLoading ? (
                                    <tr><td colSpan={5} className="px-4 py-8 text-center text-gray-500">Loading...</td></tr>
                                ) : charges.length === 0 ? (
                                    <tr><td colSpan={5} className="px-4 py-8 text-center text-gray-500">No charges found</td></tr>
                                ) : (
                                    charges.map((charge: Charge) => (
                                        <tr key={charge.id} className="hover:bg-gray-50">
                                            <td className="px-4 py-3 text-gray-500">#{charge.id}</td>
                                            <td className="px-4 py-3">
                                                <Badge variant="default">{charge.branch_name || '-'}</Badge>
                                            </td>
                                            <td className="px-4 py-3 text-right font-semibold text-gray-800">
                                                {formatCurrency(charge.special_charge)}
                                            </td>
                                            <td className="px-4 py-3 text-gray-600 text-sm">{charge.created_at || '-'}</td>
                                            <td className="px-4 py-3">
                                                <div className="flex justify-center gap-1">
                                                    <Button variant="ghost" size="icon" onClick={() => openModal(charge)}>
                                                        <Pencil className="w-4 h-4" />
                                                    </Button>
                                                    <Button variant="ghost" size="icon" onClick={() => handleDelete(charge.id)} className="text-red-500 hover:text-red-700">
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
                        <div className="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 flex justify-between items-center">
                            <span className="font-semibold">{editingCharge ? 'Edit Charge' : 'Add Charge'}</span>
                            <button onClick={closeModal} className="p-1 hover:bg-white/20 rounded">✕</button>
                        </div>
                        <form onSubmit={handleSubmit} className="p-6 space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Branch *</label>
                                <Select
                                    value={formData.branch_id}
                                    onChange={(e) => setFormData({ ...formData, branch_id: e.target.value })}
                                    required
                                >
                                    <option value="">-- Select Branch --</option>
                                    {branches.map((b: Branch) => (
                                        <option key={b.id} value={b.id}>{b.branch_name}</option>
                                    ))}
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Special Charge (₹) *</label>
                                <Input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value={formData.special_charge}
                                    onChange={(e) => setFormData({ ...formData, special_charge: e.target.value })}
                                    placeholder="0.00"
                                    required
                                />
                            </div>
                            <div className="flex justify-end gap-3 pt-4">
                                <Button type="button" variant="outline" onClick={closeModal}>Cancel</Button>
                                <Button type="submit" loading={createMutation.isPending || updateMutation.isPending}>
                                    {editingCharge ? 'Update' : 'Save Charge'}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
