import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Button, Input, Card, CardHeader, CardTitle, CardContent } from '@/components/ui';
import { Plus, Pencil, Trash2, Search, RefreshCw, Tag } from 'lucide-react';
import { toast } from 'sonner';
import apiClient from '@/lib/axios';

interface ItemCategory {
    id: number;
    category_name: string;
}

function extractData<T>(response: any): T {
    if (response.data?.data) return response.data.data;
    return response.data;
}

export function ItemCategories() {
    const queryClient = useQueryClient();
    const [searchQuery, setSearchQuery] = useState('');
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingCategory, setEditingCategory] = useState<ItemCategory | null>(null);
    const [formData, setFormData] = useState({ category_name: '' });

    // Fetch categories
    const { data: categories = [], isLoading, refetch } = useQuery({
        queryKey: ['item-categories'],
        queryFn: async () => {
            const res = await apiClient.get('/api/admin/item-categories');
            return extractData<ItemCategory[]>(res);
        },
    });

    const createMutation = useMutation({
        mutationFn: (data: any) => apiClient.post('/api/admin/item-categories', data),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['item-categories'] });
            toast.success('Category created');
            closeModal();
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to create'),
    });

    const updateMutation = useMutation({
        mutationFn: ({ id, data }: { id: number; data: any }) =>
            apiClient.put(`/api/admin/item-categories/${id}`, data),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['item-categories'] });
            toast.success('Category updated');
            closeModal();
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to update'),
    });

    const deleteMutation = useMutation({
        mutationFn: (id: number) => apiClient.delete(`/api/admin/item-categories/${id}`),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['item-categories'] });
            toast.success('Category deleted');
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to delete'),
    });

    const filteredCategories = categories.filter((c: ItemCategory) =>
        c.category_name?.toLowerCase().includes(searchQuery.toLowerCase())
    );

    const openModal = (category?: ItemCategory) => {
        if (category) {
            setEditingCategory(category);
            setFormData({ category_name: category.category_name });
        } else {
            setEditingCategory(null);
            setFormData({ category_name: '' });
        }
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setEditingCategory(null);
        setFormData({ category_name: '' });
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editingCategory) {
            updateMutation.mutate({ id: editingCategory.id, data: formData });
        } else {
            createMutation.mutate(formData);
        }
    };

    const handleDelete = (id: number) => {
        if (confirm('Delete this category?')) {
            deleteMutation.mutate(id);
        }
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Item Categories</h1>
                    <p className="text-gray-500">Manage ticket item categories</p>
                </div>
                <div className="flex gap-2">
                    <Button variant="outline" onClick={() => refetch()}>
                        <RefreshCw className="w-4 h-4" />
                        Refresh
                    </Button>
                    <Button onClick={() => openModal()}>
                        <Plus className="w-4 h-4" />
                        Add Category
                    </Button>
                </div>
            </div>

            {/* Search */}
            <Card>
                <CardContent className="p-4">
                    <div className="relative max-w-md">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <Input
                            placeholder="Search categories..."
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="pl-10"
                        />
                    </div>
                </CardContent>
            </Card>

            {/* Table */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Tag className="w-5 h-5 text-indigo-600" />
                        All Categories ({filteredCategories.length})
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-0">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50 border-b">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Category Name</th>
                                    <th className="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {isLoading ? (
                                    <tr><td colSpan={3} className="px-4 py-8 text-center text-gray-500">Loading...</td></tr>
                                ) : filteredCategories.length === 0 ? (
                                    <tr><td colSpan={3} className="px-4 py-8 text-center text-gray-500">No categories found</td></tr>
                                ) : (
                                    filteredCategories.map((cat: ItemCategory) => (
                                        <tr key={cat.id} className="hover:bg-gray-50">
                                            <td className="px-4 py-3 text-gray-500">#{cat.id}</td>
                                            <td className="px-4 py-3">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white">
                                                        <Tag className="w-4 h-4" />
                                                    </div>
                                                    <span className="font-medium text-gray-900">{cat.category_name}</span>
                                                </div>
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex justify-center gap-1">
                                                    <Button variant="ghost" size="icon" onClick={() => openModal(cat)}>
                                                        <Pencil className="w-4 h-4" />
                                                    </Button>
                                                    <Button variant="ghost" size="icon" onClick={() => handleDelete(cat.id)} className="text-red-500 hover:text-red-700">
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
                        <div className="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white p-4 flex justify-between items-center">
                            <span className="font-semibold">{editingCategory ? 'Edit Category' : 'Add Category'}</span>
                            <button onClick={closeModal} className="p-1 hover:bg-white/20 rounded">✕</button>
                        </div>
                        <form onSubmit={handleSubmit} className="p-6 space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Category Name *</label>
                                <Input
                                    value={formData.category_name}
                                    onChange={(e) => setFormData({ category_name: e.target.value })}
                                    placeholder="Enter category name"
                                    required
                                />
                            </div>
                            <div className="flex justify-end gap-3 pt-4">
                                <Button type="button" variant="outline" onClick={closeModal}>Cancel</Button>
                                <Button type="submit" loading={createMutation.isPending || updateMutation.isPending}>
                                    {editingCategory ? 'Update' : 'Save Category'}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
