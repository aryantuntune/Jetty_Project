import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Button, Input, Select, Card, CardHeader, CardTitle, CardContent, Badge } from '@/components/ui';
import { Plus, Pencil, Trash2, Search, RefreshCw, Clock } from 'lucide-react';
import { toast } from 'sonner';
import apiClient from '@/lib/axios';

interface Schedule {
    id: number;
    schedule_time: string;
    hour: number;
    minute: number;
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

export function Schedules() {
    const queryClient = useQueryClient();
    const [filterBranch, setFilterBranch] = useState<string>('');
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingSchedule, setEditingSchedule] = useState<Schedule | null>(null);
    const [formData, setFormData] = useState({ hour: '', minute: '', branch_id: '' });

    // Fetch schedules
    const { data: schedules = [], isLoading, refetch } = useQuery({
        queryKey: ['ferry-schedules', filterBranch],
        queryFn: async () => {
            const params = filterBranch ? `?branch_id=${filterBranch}` : '';
            const res = await apiClient.get(`/api/admin/schedules${params}`);
            return extractData<Schedule[]>(res);
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
        mutationFn: (data: any) => apiClient.post('/api/admin/schedules', data),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['ferry-schedules'] });
            toast.success('Schedule created successfully');
            closeModal();
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to create'),
    });

    const updateMutation = useMutation({
        mutationFn: ({ id, data }: { id: number; data: any }) =>
            apiClient.put(`/api/admin/schedules/${id}`, data),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['ferry-schedules'] });
            toast.success('Schedule updated successfully');
            closeModal();
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to update'),
    });

    const deleteMutation = useMutation({
        mutationFn: (id: number) => apiClient.delete(`/api/admin/schedules/${id}`),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['ferry-schedules'] });
            toast.success('Schedule deleted successfully');
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Failed to delete'),
    });

    const openModal = (schedule?: Schedule) => {
        if (schedule) {
            setEditingSchedule(schedule);
            setFormData({
                hour: schedule.hour?.toString() || '',
                minute: schedule.minute?.toString() || '',
                branch_id: schedule.branch_id?.toString() || '',
            });
        } else {
            setEditingSchedule(null);
            setFormData({ hour: '', minute: '0', branch_id: branches[0]?.id?.toString() || '' });
        }
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setEditingSchedule(null);
        setFormData({ hour: '', minute: '', branch_id: '' });
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        const hour = parseInt(formData.hour);
        const minute = parseInt(formData.minute) || 0;
        const payload = {
            hour,
            minute,
            schedule_time: `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}:00`,
            branch_id: parseInt(formData.branch_id),
        };
        if (editingSchedule) {
            updateMutation.mutate({ id: editingSchedule.id, data: payload });
        } else {
            createMutation.mutate(payload);
        }
    };

    const handleDelete = (id: number) => {
        if (confirm('Are you sure you want to delete this schedule?')) {
            deleteMutation.mutate(id);
        }
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Ferry Schedules</h1>
                    <p className="text-gray-500">Manage ferry departure times</p>
                </div>
                <div className="flex gap-2">
                    <Button variant="outline" onClick={() => refetch()}>
                        <RefreshCw className="w-4 h-4" />
                        Refresh
                    </Button>
                    <Button onClick={() => openModal()}>
                        <Plus className="w-4 h-4" />
                        Add Schedule
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
                        <Clock className="w-5 h-5 text-emerald-600" />
                        All Schedules ({schedules.length})
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-0">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50 border-b">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Time</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Hour</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Minute</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Branch</th>
                                    <th className="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {isLoading ? (
                                    <tr><td colSpan={6} className="px-4 py-8 text-center text-gray-500">Loading...</td></tr>
                                ) : schedules.length === 0 ? (
                                    <tr><td colSpan={6} className="px-4 py-8 text-center text-gray-500">No schedules found</td></tr>
                                ) : (
                                    schedules.map((schedule: Schedule) => (
                                        <tr key={schedule.id} className="hover:bg-gray-50">
                                            <td className="px-4 py-3 text-gray-500">#{schedule.id}</td>
                                            <td className="px-4 py-3">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white">
                                                        <Clock className="w-4 h-4" />
                                                    </div>
                                                    <span className="font-semibold text-gray-800">{schedule.schedule_time}</span>
                                                </div>
                                            </td>
                                            <td className="px-4 py-3 text-gray-600">{schedule.hour?.toString().padStart(2, '0')}</td>
                                            <td className="px-4 py-3 text-gray-600">{schedule.minute?.toString().padStart(2, '0')}</td>
                                            <td className="px-4 py-3">
                                                <Badge variant="default">{schedule.branch_name || '-'}</Badge>
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex justify-center gap-1">
                                                    <Button variant="ghost" size="icon" onClick={() => openModal(schedule)}>
                                                        <Pencil className="w-4 h-4" />
                                                    </Button>
                                                    <Button variant="ghost" size="icon" onClick={() => handleDelete(schedule.id)} className="text-red-500 hover:text-red-700">
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
                        <div className="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white p-4 flex justify-between items-center">
                            <span className="font-semibold">{editingSchedule ? 'Edit Schedule' : 'Add Schedule'}</span>
                            <button onClick={closeModal} className="p-1 hover:bg-white/20 rounded">✕</button>
                        </div>
                        <form onSubmit={handleSubmit} className="p-6 space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Hour (0-23) *</label>
                                    <Input
                                        type="number"
                                        min="0"
                                        max="23"
                                        value={formData.hour}
                                        onChange={(e) => setFormData({ ...formData, hour: e.target.value })}
                                        placeholder="0-23"
                                        required
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Minute (0-59)</label>
                                    <Input
                                        type="number"
                                        min="0"
                                        max="59"
                                        value={formData.minute}
                                        onChange={(e) => setFormData({ ...formData, minute: e.target.value })}
                                        placeholder="0-59"
                                    />
                                </div>
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
                                    {editingSchedule ? 'Update' : 'Save Schedule'}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
