import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Button, Select, Card, CardHeader, CardTitle, CardContent, Badge } from '@/components/ui';
import { ArrowRightLeft, RefreshCw } from 'lucide-react';
import { toast } from 'sonner';
import apiClient from '@/lib/axios';

interface User {
    id: number;
    name: string;
    email: string;
    role_id: number;
    role_name?: string;
    branch_id: number;
    branch_name?: string;
}

interface Branch { id: number; branch_name: string; }

function extractData<T>(response: any): T {
    if (response.data?.data) return response.data.data;
    return response.data;
}

export function EmployeeTransfer() {
    const queryClient = useQueryClient();
    const [selectedEmployee, setSelectedEmployee] = useState('');
    const [targetBranch, setTargetBranch] = useState('');

    // Fetch employees (operators & managers)
    const { data: employees = [], isLoading, refetch } = useQuery({
        queryKey: ['transfer-employees'],
        queryFn: async () => {
            const res = await apiClient.get('/api/admin/users');
            const all = extractData<User[]>(res);
            return all.filter((u: User) => [3, 4, 5].includes(u.role_id)); // Managers, Operators, Checkers
        },
    });

    const { data: branches = [] } = useQuery({
        queryKey: ['branches-dropdown'],
        queryFn: async () => {
            const res = await apiClient.get('/api/admin/branches');
            return extractData<Branch[]>(res);
        },
    });

    const transferMutation = useMutation({
        mutationFn: async ({ userId, branchId }: { userId: number; branchId: number }) => {
            return apiClient.put(`/api/admin/users/${userId}`, { branch_id: branchId });
        },
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['transfer-employees'] });
            toast.success('Employee transferred successfully!');
            setSelectedEmployee('');
            setTargetBranch('');
        },
        onError: (err: any) => toast.error(err.response?.data?.message || 'Transfer failed'),
    });

    const handleTransfer = () => {
        if (!selectedEmployee || !targetBranch) {
            toast.error('Select both employee and target branch');
            return;
        }
        const emp = employees.find((e: User) => e.id === parseInt(selectedEmployee));
        if (emp?.branch_id === parseInt(targetBranch)) {
            toast.error('Employee is already in this branch');
            return;
        }
        if (confirm(`Transfer ${emp?.name} to new branch?`)) {
            transferMutation.mutate({ userId: parseInt(selectedEmployee), branchId: parseInt(targetBranch) });
        }
    };

    const selectedEmp = employees.find((e: User) => e.id === parseInt(selectedEmployee));

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Employee Transfer</h1>
                    <p className="text-gray-500">Transfer employees between branches</p>
                </div>
                <Button variant="outline" onClick={() => refetch()}>
                    <RefreshCw className="w-4 h-4" />
                    Refresh
                </Button>
            </div>

            {/* Transfer Form */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <ArrowRightLeft className="w-5 h-5 text-blue-600" />
                        Transfer Employee
                    </CardTitle>
                </CardHeader>
                <CardContent className="space-y-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">Select Employee *</label>
                            <Select value={selectedEmployee} onChange={(e) => setSelectedEmployee(e.target.value)}>
                                <option value="">-- Select Employee --</option>
                                {employees.map((e: User) => (
                                    <option key={e.id} value={e.id}>
                                        {e.name} ({e.role_name || 'Staff'}) - {e.branch_name || 'No Branch'}
                                    </option>
                                ))}
                            </Select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">Target Branch *</label>
                            <Select value={targetBranch} onChange={(e) => setTargetBranch(e.target.value)}>
                                <option value="">-- Select Target Branch --</option>
                                {branches.map((b: Branch) => (
                                    <option key={b.id} value={b.id}>{b.branch_name}</option>
                                ))}
                            </Select>
                        </div>
                    </div>

                    {selectedEmp && (
                        <div className="p-4 bg-blue-50 rounded-xl border border-blue-200">
                            <div className="flex items-center gap-4">
                                <div className="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-lg">
                                    {selectedEmp.name?.charAt(0).toUpperCase()}
                                </div>
                                <div className="flex-1">
                                    <p className="font-semibold text-gray-900">{selectedEmp.name}</p>
                                    <p className="text-sm text-gray-600">{selectedEmp.email}</p>
                                </div>
                                <div className="text-right">
                                    <Badge variant="default">{selectedEmp.role_name || 'Staff'}</Badge>
                                    <p className="text-sm text-gray-500 mt-1">Current: {selectedEmp.branch_name || 'None'}</p>
                                </div>
                                {targetBranch && (
                                    <>
                                        <ArrowRightLeft className="w-6 h-6 text-blue-600" />
                                        <div className="text-right">
                                            <p className="text-sm text-gray-500">New Branch:</p>
                                            <Badge variant="success">{branches.find((b: Branch) => b.id === parseInt(targetBranch))?.branch_name}</Badge>
                                        </div>
                                    </>
                                )}
                            </div>
                        </div>
                    )}

                    <div className="flex justify-end">
                        <Button
                            onClick={handleTransfer}
                            disabled={!selectedEmployee || !targetBranch}
                            loading={transferMutation.isPending}
                            className="px-8"
                        >
                            <ArrowRightLeft className="w-4 h-4 mr-2" />
                            Transfer Employee
                        </Button>
                    </div>
                </CardContent>
            </Card>

            {/* Employees List */}
            <Card>
                <CardHeader>
                    <CardTitle>All Employees ({employees.length})</CardTitle>
                </CardHeader>
                <CardContent className="p-0">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50 border-b">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Branch</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {isLoading ? (
                                    <tr><td colSpan={4} className="px-4 py-8 text-center text-gray-500">Loading...</td></tr>
                                ) : employees.length === 0 ? (
                                    <tr><td colSpan={4} className="px-4 py-8 text-center text-gray-500">No employees</td></tr>
                                ) : (
                                    employees.map((e: User) => (
                                        <tr key={e.id} className="hover:bg-gray-50">
                                            <td className="px-4 py-3 font-medium text-gray-900">{e.name}</td>
                                            <td className="px-4 py-3 text-gray-600">{e.email}</td>
                                            <td className="px-4 py-3"><Badge variant={e.role_id === 3 ? 'success' : e.role_id === 4 ? 'warning' : 'default'}>{e.role_name || 'Staff'}</Badge></td>
                                            <td className="px-4 py-3"><Badge variant="default">{e.branch_name || 'None'}</Badge></td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    );
}
