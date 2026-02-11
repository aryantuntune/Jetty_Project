import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Button, Input, Select, Card, CardHeader, CardTitle, CardContent, Badge } from '@/components/ui';
import { Plus, Pencil, Trash2, Search, RefreshCw, Users as UsersIcon, Shield } from 'lucide-react';
import { toast } from 'sonner';
import apiClient from '@/lib/axios';

interface User {
    id: number;
    name: string;
    email: string;
    role_id: number;
    role_name: string;
    branch_id?: number;
    branch_name?: string;
    created_at: string;
}

interface Branch {
    id: number;
    branch_name: string;
}

function extractData<T>(response: any): T {
    if (response.data?.data) return response.data.data;
    return response.data;
}

const ROLES = [
    { id: 1, name: 'Super Admin', color: 'bg-purple-100 text-purple-800' },
    { id: 2, name: 'Admin', color: 'bg-blue-100 text-blue-800' },
    { id: 3, name: 'User', color: 'bg-green-100 text-green-800' },
];

export function Users() {
    const queryClient = useQueryClient();
    const [searchQuery, setSearchQuery] = useState('');
    const [filterRole, setFilterRole] = useState<string>('');
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingUser, setEditingUser] = useState<User | null>(null);
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        role_id: '3',
        branch_id: '',
    });

    // Fetch users
    const { data: users = [], isLoading, refetch } = useQuery({
        queryKey: ['admin-users'],
        queryFn: async () => {
            const res = await apiClient.get('/api/admin/users');
            return extractData<User[]>(res);
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
        mutationFn: (data: any) => apiClient.post('/api/admin/users', data),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['admin-users'] });
            toast.success('User created successfully');
            closeModal();
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to create'),
    });

    const updateMutation = useMutation({
        mutationFn: ({ id, data }: { id: number; data: any }) =>
            apiClient.put(`/api/admin/users/${id}`, data),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['admin-users'] });
            toast.success('User updated successfully');
            closeModal();
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to update'),
    });

    const deleteMutation = useMutation({
        mutationFn: (id: number) => apiClient.delete(`/api/admin/users/${id}`),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['admin-users'] });
            toast.success('User deleted successfully');
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to delete'),
    });

    const filteredUsers = users.filter((u: User) => {
        const matchesSearch =
            u.name?.toLowerCase().includes(searchQuery.toLowerCase()) ||
            u.email?.toLowerCase().includes(searchQuery.toLowerCase());
        const matchesRole = !filterRole || u.role_id.toString() === filterRole;
        return matchesSearch && matchesRole;
    });

    const openModal = (user?: User) => {
        if (user) {
            setEditingUser(user);
            setFormData({
                name: user.name,
                email: user.email,
                password: '',
                role_id: user.role_id.toString(),
                branch_id: user.branch_id?.toString() || '',
            });
        } else {
            setEditingUser(null);
            setFormData({ name: '', email: '', password: '', role_id: '3', branch_id: '' });
        }
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setEditingUser(null);
        setFormData({ name: '', email: '', password: '', role_id: '3', branch_id: '' });
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        const payload: any = {
            name: formData.name,
            email: formData.email,
            role_id: parseInt(formData.role_id),
            branch_id: formData.branch_id ? parseInt(formData.branch_id) : null,
        };
        if (formData.password) {
            payload.password = formData.password;
        }
        if (editingUser) {
            updateMutation.mutate({ id: editingUser.id, data: payload });
        } else {
            if (!formData.password) {
                toast.error('Password is required for new users');
                return;
            }
            createMutation.mutate(payload);
        }
    };

    const handleDelete = (id: number) => {
        if (confirm('Are you sure you want to delete this user?')) {
            deleteMutation.mutate(id);
        }
    };

    const getRoleBadge = (roleId: number) => {
        const role = ROLES.find(r => r.id === roleId);
        return (
            <span className={`px-2 py-1 rounded-full text-xs font-medium ${role?.color || 'bg-gray-100 text-gray-800'}`}>
                {role?.name || 'Unknown'}
            </span>
        );
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Users</h1>
                    <p className="text-gray-500">Manage admin users and operators</p>
                </div>
                <div className="flex gap-2">
                    <Button variant="outline" onClick={() => refetch()}>
                        <RefreshCw className="w-4 h-4" />
                        Refresh
                    </Button>
                    <Button onClick={() => openModal()}>
                        <Plus className="w-4 h-4" />
                        Add User
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
                                placeholder="Search users..."
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                className="pl-10"
                            />
                        </div>
                        <Select value={filterRole} onChange={(e) => setFilterRole(e.target.value)}>
                            <option value="">All Roles</option>
                            {ROLES.map((role) => (
                                <option key={role.id} value={role.id}>{role.name}</option>
                            ))}
                        </Select>
                    </div>
                </CardContent>
            </Card>

            {/* Table */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <UsersIcon className="w-5 h-5 text-purple-600" />
                        All Users ({filteredUsers.length})
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-0">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50 border-b">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Branch</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created</th>
                                    <th className="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {isLoading ? (
                                    <tr><td colSpan={7} className="px-4 py-8 text-center text-gray-500">Loading...</td></tr>
                                ) : filteredUsers.length === 0 ? (
                                    <tr><td colSpan={7} className="px-4 py-8 text-center text-gray-500">No users found</td></tr>
                                ) : (
                                    filteredUsers.map((user: User) => (
                                        <tr key={user.id} className="hover:bg-gray-50">
                                            <td className="px-4 py-3 text-gray-500">#{user.id}</td>
                                            <td className="px-4 py-3">
                                                <div className="flex items-center gap-2">
                                                    <div className="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                        {user.name?.charAt(0).toUpperCase()}
                                                    </div>
                                                    <span className="font-medium text-gray-900">{user.name}</span>
                                                </div>
                                            </td>
                                            <td className="px-4 py-3 text-gray-600">{user.email}</td>
                                            <td className="px-4 py-3">{getRoleBadge(user.role_id)}</td>
                                            <td className="px-4 py-3 text-gray-600">{user.branch_name || '-'}</td>
                                            <td className="px-4 py-3 text-gray-500 text-sm">{user.created_at}</td>
                                            <td className="px-4 py-3">
                                                <div className="flex justify-center gap-1">
                                                    <Button variant="ghost" size="icon" onClick={() => openModal(user)}>
                                                        <Pencil className="w-4 h-4" />
                                                    </Button>
                                                    <Button variant="ghost" size="icon" onClick={() => handleDelete(user.id)} className="text-red-500 hover:text-red-700">
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
                        <div className="bg-gradient-to-r from-purple-600 to-indigo-600 text-white p-4 flex justify-between items-center">
                            <span className="font-semibold flex items-center gap-2">
                                <Shield className="w-5 h-5" />
                                {editingUser ? 'Edit User' : 'Add User'}
                            </span>
                            <button onClick={closeModal} className="p-1 hover:bg-white/20 rounded">✕</button>
                        </div>
                        <form onSubmit={handleSubmit} className="p-6 space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <Input
                                    value={formData.name}
                                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                                    placeholder="John Doe"
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <Input
                                    type="email"
                                    value={formData.email}
                                    onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                                    placeholder="john@example.com"
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Password {editingUser && <span className="text-gray-400">(leave blank to keep current)</span>}
                                </label>
                                <Input
                                    type="password"
                                    value={formData.password}
                                    onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                                    placeholder="••••••••"
                                    required={!editingUser}
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                <Select
                                    value={formData.role_id}
                                    onChange={(e) => setFormData({ ...formData, role_id: e.target.value })}
                                    required
                                >
                                    {ROLES.map((role) => (
                                        <option key={role.id} value={role.id}>{role.name}</option>
                                    ))}
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Branch (Optional)</label>
                                <Select
                                    value={formData.branch_id}
                                    onChange={(e) => setFormData({ ...formData, branch_id: e.target.value })}
                                >
                                    <option value="">No Branch</option>
                                    {branches.map((b: Branch) => (
                                        <option key={b.id} value={b.id}>{b.branch_name}</option>
                                    ))}
                                </Select>
                            </div>
                            <div className="flex justify-end gap-3 pt-4">
                                <Button type="button" variant="outline" onClick={closeModal}>Cancel</Button>
                                <Button type="submit" loading={createMutation.isPending || updateMutation.isPending}>
                                    {editingUser ? 'Update' : 'Create'}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
