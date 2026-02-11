import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Button, Input, Select, Card, CardHeader, CardTitle, CardContent, Badge } from '@/components/ui';
import { Plus, Pencil, Trash2, Search, RefreshCw, Users as UsersIcon } from 'lucide-react';
import { toast } from 'sonner';
import apiClient from '@/lib/axios';

interface Guest {
    id: number;
    name: string;
    category_id: number;
    category_name?: string;
    branch_id: number;
    branch_name?: string;
}

interface Category {
    id: number;
    name: string;
}

interface Branch {
    id: number;
    branch_name: string;
}

function extractData<T>(response: any): T {
    if (response.data?.data) return response.data.data;
    return response.data;
}

export function Guests() {
    const queryClient = useQueryClient();
    const [searchQuery, setSearchQuery] = useState('');
    const [filterBranch, setFilterBranch] = useState<string>('');
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingGuest, setEditingGuest] = useState<Guest | null>(null);
    const [formData, setFormData] = useState({ name: '', category_id: '', branch_id: '' });

    // Fetch guests
    const { data: guests = [], isLoading, refetch } = useQuery({
        queryKey: ['guests', filterBranch],
        queryFn: async () => {
            const params = filterBranch ? `?branch_id=${filterBranch}` : '';
            const res = await apiClient.get(`/api/admin/guests${params}`);
            return extractData<Guest[]>(res);
        },
    });

    // Fetch categories for dropdown
    const { data: categories = [] } = useQuery({
        queryKey: ['guest-categories'],
        queryFn: async () => {
            const res = await apiClient.get('/api/admin/guest-categories');
            return extractData<Category[]>(res);
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
        mutationFn: (data: any) => apiClient.post('/api/admin/guests', data),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['guests'] });
            toast.success('Guest created successfully');
            closeModal();
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to create'),
    });

    const updateMutation = useMutation({
        mutationFn: ({ id, data }: { id: number; data: any }) =>
            apiClient.put(`/api/admin/guests/${id}`, data),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['guests'] });
            toast.success('Guest updated successfully');
            closeModal();
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to update'),
    });

    const deleteMutation = useMutation({
        mutationFn: (id: number) => apiClient.delete(`/api/admin/guests/${id}`),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['guests'] });
            toast.success('Guest deleted successfully');
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to delete'),
    });

    const filteredGuests = guests.filter((g: Guest) =>
        g.name?.toLowerCase().includes(searchQuery.toLowerCase())
    );

    const openModal = (guest?: Guest) => {
        if (guest) {
            setEditingGuest(guest);
            setFormData({
                name: guest.name,
                category_id: guest.category_id?.toString() || '',
                branch_id: guest.branch_id?.toString() || '',
            });
        } else {
            setEditingGuest(null);
            setFormData({ name: '', category_id: '', branch_id: branches[0]?.id?.toString() || '' });
        }
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setEditingGuest(null);
        setFormData({ name: '', category_id: '', branch_id: '' });
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        const payload = {
            name: formData.name,
            category_id: parseInt(formData.category_id),
            branch_id: parseInt(formData.branch_id),
        };
        if (editingGuest) {
            updateMutation.mutate({ id: editingGuest.id, data: payload });
        } else {
            createMutation.mutate(payload);
        }
    };

    const handleDelete = (id: number) => {
        if (confirm('Are you sure you want to delete this guest?')) {
            deleteMutation.mutate(id);
        }
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Guests</h1>
                    <p className="text-gray-500">Manage registered guests</p>
                </div>
                <div className="flex gap-2">
                    <Button variant="outline" onClick={() => refetch()}>
                        <RefreshCw className="w-4 h-4" />
                        Refresh
                    </Button>
                    <Button onClick={() => openModal()}>
                        <Plus className="w-4 h-4" />
                        Add Guest
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
                                placeholder="Search guests..."
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
                        <UsersIcon className="w-5 h-5 text-orange-600" />
                        All Guests ({filteredGuests.length})
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-0">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50 border-b">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Category</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Branch</th>
                                    <th className="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {isLoading ? (
                                    <tr><td colSpan={5} className="px-4 py-8 text-center text-gray-500">Loading...</td></tr>
                                ) : filteredGuests.length === 0 ? (
                                    <tr><td colSpan={5} className="px-4 py-8 text-center text-gray-500">No guests found</td></tr>
                                ) : (
                                    filteredGuests.map((guest: Guest, idx: number) => (
                                        <tr key={guest.id} className="hover:bg-gray-50">
                                            <td className="px-4 py-3 text-gray-500">{idx + 1}</td>
                                            <td className="px-4 py-3">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-semibold text-sm">
                                                        {guest.name?.charAt(0).toUpperCase()}
                                                    </div>
                                                    <span className="font-medium text-gray-900">{guest.name}</span>
                                                </div>
                                            </td>
                                            <td className="px-4 py-3">
                                                <Badge variant="default" className="bg-purple-50 text-purple-700">
                                                    {guest.category_name || 'N/A'}
                                                </Badge>
                                            </td>
                                            <td className="px-4 py-3">
                                                <Badge variant="default">{guest.branch_name || '-'}</Badge>
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex justify-center gap-1">
                                                    <Button variant="ghost" size="icon" onClick={() => openModal(guest)}>
                                                        <Pencil className="w-4 h-4" />
                                                    </Button>
                                                    <Button variant="ghost" size="icon" onClick={() => handleDelete(guest.id)} className="text-red-500 hover:text-red-700">
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
                        <div className="bg-gradient-to-r from-orange-600 to-orange-700 text-white p-4 flex justify-between items-center">
                            <span className="font-semibold">{editingGuest ? 'Edit Guest' : 'Add Guest'}</span>
                            <button onClick={closeModal} className="p-1 hover:bg-white/20 rounded">✕</button>
                        </div>
                        <form onSubmit={handleSubmit} className="p-6 space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Guest Name *</label>
                                <Input
                                    value={formData.name}
                                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                                    placeholder="Enter guest name"
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Guest Category *</label>
                                <Select
                                    value={formData.category_id}
                                    onChange={(e) => setFormData({ ...formData, category_id: e.target.value })}
                                    required
                                >
                                    <option value="">-- Select Category --</option>
                                    {categories.map((c: Category) => (
                                        <option key={c.id} value={c.id}>{c.name}</option>
                                    ))}
                                </Select>
                            </div>
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
                            <div className="flex justify-end gap-3 pt-4">
                                <Button type="button" variant="outline" onClick={closeModal}>Cancel</Button>
                                <Button type="submit" loading={createMutation.isPending || updateMutation.isPending}>
                                    {editingGuest ? 'Update' : 'Save Guest'}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
