import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Button, Input, Select, Card, CardHeader, CardTitle, CardContent, Badge } from '@/components/ui';
import { Plus, Pencil, Trash2, Search, RefreshCw, ScanLine } from 'lucide-react';
import { toast } from 'sonner';
import apiClient from '@/lib/axios';

interface Checker {
    id: number;
    name: string;
    email: string;
    mobile?: string;
    branch_id: number;
    branch_name?: string;
}

interface Branch {
    id: number;
    branch_name: string;
}

function extractData<T>(response: any): T {
    if (response.data?.data) return response.data.data;
    return response.data;
}

export function Checkers() {
    const queryClient = useQueryClient();
    const [searchQuery, setSearchQuery] = useState('');
    const [filterBranch, setFilterBranch] = useState('');
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingChecker, setEditingChecker] = useState<Checker | null>(null);
    const [formData, setFormData] = useState({ name: '', email: '', password: '', mobile: '', branch_id: '' });

    // Fetch checkers (role_id = 5)
    const { data: checkers = [], isLoading, refetch } = useQuery({
        queryKey: ['checkers', filterBranch],
        queryFn: async () => {
            const params = filterBranch ? `?branch_id=${filterBranch}&role_id=5` : '?role_id=5';
            const res = await apiClient.get(`/api/admin/users${params}`);
            return extractData<Checker[]>(res);
        },
    });

    const { data: branches = [] } = useQuery({
        queryKey: ['branches-dropdown'],
        queryFn: async () => {
            const res = await apiClient.get('/api/admin/branches');
            return extractData<Branch[]>(res);
        },
    });

    const createMutation = useMutation({
        mutationFn: (data: any) => apiClient.post('/api/admin/users', { ...data, role_id: 5 }),
        onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['checkers'] }); toast.success('Checker created'); closeModal(); },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed'),
    });

    const updateMutation = useMutation({
        mutationFn: ({ id, data }: { id: number; data: any }) => apiClient.put(`/api/admin/users/${id}`, { ...data, role_id: 5 }),
        onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['checkers'] }); toast.success('Checker updated'); closeModal(); },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed'),
    });

    const deleteMutation = useMutation({
        mutationFn: (id: number) => apiClient.delete(`/api/admin/users/${id}`),
        onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['checkers'] }); toast.success('Checker deleted'); },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed'),
    });

    const filtered = checkers.filter((c: Checker) => c.name?.toLowerCase().includes(searchQuery.toLowerCase()) || c.email?.toLowerCase().includes(searchQuery.toLowerCase()));

    const openModal = (c?: Checker) => {
        if (c) { setEditingChecker(c); setFormData({ name: c.name, email: c.email, password: '', mobile: c.mobile || '', branch_id: c.branch_id?.toString() || '' }); }
        else { setEditingChecker(null); setFormData({ name: '', email: '', password: '', mobile: '', branch_id: '' }); }
        setIsModalOpen(true);
    };

    const closeModal = () => { setIsModalOpen(false); setEditingChecker(null); setFormData({ name: '', email: '', password: '', mobile: '', branch_id: '' }); };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        const payload = { name: formData.name, email: formData.email, mobile: formData.mobile, branch_id: formData.branch_id ? parseInt(formData.branch_id) : null, ...(formData.password ? { password: formData.password } : {}) };
        editingChecker ? updateMutation.mutate({ id: editingChecker.id, data: payload }) : createMutation.mutate({ ...payload, password: formData.password });
    };

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <div><h1 className="text-2xl font-bold text-gray-900">Checkers</h1><p className="text-gray-500">Manage ticket checker accounts</p></div>
                <div className="flex gap-2"><Button variant="outline" onClick={() => refetch()}><RefreshCw className="w-4 h-4" />Refresh</Button><Button onClick={() => openModal()}><Plus className="w-4 h-4" />Add Checker</Button></div>
            </div>

            <Card><CardContent className="p-4"><div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="relative"><Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" /><Input placeholder="Search..." value={searchQuery} onChange={(e) => setSearchQuery(e.target.value)} className="pl-10" /></div>
                <Select value={filterBranch} onChange={(e) => setFilterBranch(e.target.value)}><option value="">All Branches</option>{branches.map((b: Branch) => <option key={b.id} value={b.id}>{b.branch_name}</option>)}</Select>
            </div></CardContent></Card>

            <Card>
                <CardHeader><CardTitle className="flex items-center gap-2"><ScanLine className="w-5 h-5 text-cyan-600" />All Checkers ({filtered.length})</CardTitle></CardHeader>
                <CardContent className="p-0"><div className="overflow-x-auto"><table className="w-full">
                    <thead className="bg-gray-50 border-b"><tr>
                        <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                        <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                        <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mobile</th>
                        <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Branch</th>
                        <th className="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr></thead>
                    <tbody className="divide-y">
                        {isLoading ? <tr><td colSpan={5} className="px-4 py-8 text-center text-gray-500">Loading...</td></tr>
                            : filtered.length === 0 ? <tr><td colSpan={5} className="px-4 py-8 text-center text-gray-500">No checkers</td></tr>
                                : filtered.map((c: Checker) => (
                                    <tr key={c.id} className="hover:bg-gray-50">
                                        <td className="px-4 py-3"><div className="flex items-center gap-3"><div className="w-9 h-9 rounded-full bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center text-white font-semibold">{c.name?.charAt(0).toUpperCase()}</div><span className="font-medium">{c.name}</span></div></td>
                                        <td className="px-4 py-3 text-gray-600">{c.email}</td>
                                        <td className="px-4 py-3 text-gray-600">{c.mobile || 'N/A'}</td>
                                        <td className="px-4 py-3"><Badge variant="default">{c.branch_name || 'N/A'}</Badge></td>
                                        <td className="px-4 py-3"><div className="flex justify-center gap-1"><Button variant="ghost" size="icon" onClick={() => openModal(c)}><Pencil className="w-4 h-4" /></Button><Button variant="ghost" size="icon" onClick={() => confirm('Delete?') && deleteMutation.mutate(c.id)} className="text-red-500"><Trash2 className="w-4 h-4" /></Button></div></td>
                                    </tr>
                                ))}
                    </tbody>
                </table></div></CardContent>
            </Card>

            {isModalOpen && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                    <div className="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                        <div className="bg-gradient-to-r from-cyan-600 to-cyan-700 text-white p-4 flex justify-between items-center"><span className="font-semibold">{editingChecker ? 'Edit Checker' : 'Add Checker'}</span><button onClick={closeModal} className="p-1 hover:bg-white/20 rounded">✕</button></div>
                        <form onSubmit={handleSubmit} className="p-6 space-y-4">
                            <div><label className="block text-sm font-medium text-gray-700 mb-1">Name *</label><Input value={formData.name} onChange={(e) => setFormData({ ...formData, name: e.target.value })} required /></div>
                            <div><label className="block text-sm font-medium text-gray-700 mb-1">Email *</label><Input type="email" value={formData.email} onChange={(e) => setFormData({ ...formData, email: e.target.value })} required /></div>
                            <div><label className="block text-sm font-medium text-gray-700 mb-1">Password {editingChecker ? '' : '*'}</label><Input type="password" value={formData.password} onChange={(e) => setFormData({ ...formData, password: e.target.value })} {...(!editingChecker && { required: true })} /></div>
                            <div><label className="block text-sm font-medium text-gray-700 mb-1">Mobile</label><Input value={formData.mobile} onChange={(e) => setFormData({ ...formData, mobile: e.target.value })} /></div>
                            <div><label className="block text-sm font-medium text-gray-700 mb-1">Branch</label><Select value={formData.branch_id} onChange={(e) => setFormData({ ...formData, branch_id: e.target.value })}><option value="">-- Select --</option>{branches.map((b: Branch) => <option key={b.id} value={b.id}>{b.branch_name}</option>)}</Select></div>
                            <div className="flex justify-end gap-3 pt-4"><Button type="button" variant="outline" onClick={closeModal}>Cancel</Button><Button type="submit" loading={createMutation.isPending || updateMutation.isPending}>{editingChecker ? 'Update' : 'Save'}</Button></div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
