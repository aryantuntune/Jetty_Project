import { useState } from 'react';
import { useMutation } from '@tanstack/react-query';
import { Card, CardHeader, CardTitle, CardContent, Button, Input } from '@/components/ui';
import { useCustomerAuthStore } from '@/store/customerAuthStore';
import { User, Mail, Phone, Camera, Save, LogOut } from 'lucide-react';
import { toast } from 'sonner';
import { useNavigate } from 'react-router-dom';
import apiClient from '@/lib/axios';

export function CustomerProfile() {
    const { customer, updateCustomer, clearAuth } = useCustomerAuthStore();
    const navigate = useNavigate();

    const [formData, setFormData] = useState({
        first_name: customer?.first_name || '',
        last_name: customer?.last_name || '',
        email: customer?.email || '',
        mobile: customer?.mobile || '',
    });
    const [isEditing, setIsEditing] = useState(false);

    const updateMutation = useMutation({
        mutationFn: async (data: typeof formData) => {
            const res = await apiClient.put('/api/customer/profile', data);
            return res.data;
        },
        onSuccess: (data) => {
            if (data.success) {
                updateCustomer(formData);
                toast.success('Profile updated successfully');
                setIsEditing(false);
            } else {
                toast.error(data.message || 'Failed to update profile');
            }
        },
        onError: (error: any) => {
            toast.error(error.response?.data?.message || 'Failed to update profile');
        },
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        updateMutation.mutate(formData);
    };

    const handleLogout = () => {
        clearAuth();
        toast.success('Logged out successfully');
        navigate('/');
    };

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold">My Profile</h1>
                    <p className="text-gray-600">Manage your account settings</p>
                </div>
                <Button variant="outline" onClick={handleLogout} className="text-red-600 hover:bg-red-50">
                    <LogOut className="w-4 h-4 mr-2" />
                    Logout
                </Button>
            </div>

            {/* Profile Picture */}
            <Card className="overflow-hidden">
                <div className="h-32 bg-gradient-to-r from-blue-600 to-blue-700"></div>
                <CardContent className="relative pt-16 pb-6">
                    <div className="absolute -top-12 left-6">
                        <div className="w-24 h-24 rounded-full bg-white border-4 border-white shadow-lg overflow-hidden flex items-center justify-center bg-blue-100">
                            {customer?.profile_image ? (
                                <img
                                    src={customer.profile_image}
                                    alt="Profile"
                                    className="w-full h-full object-cover"
                                />
                            ) : (
                                <span className="text-4xl font-bold text-blue-600">
                                    {customer?.first_name?.charAt(0) || 'U'}
                                </span>
                            )}
                        </div>
                        <button className="absolute bottom-0 right-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-blue-700">
                            <Camera className="w-4 h-4" />
                        </button>
                    </div>
                    <div className="pl-32">
                        <h2 className="text-xl font-bold">
                            {customer?.first_name} {customer?.last_name}
                        </h2>
                        <p className="text-gray-600">{customer?.email}</p>
                    </div>
                </CardContent>
            </Card>

            {/* Profile Form */}
            <Card>
                <CardHeader className="flex flex-row items-center justify-between">
                    <CardTitle>Personal Information</CardTitle>
                    {!isEditing && (
                        <Button variant="outline" onClick={() => setIsEditing(true)}>
                            Edit Profile
                        </Button>
                    )}
                </CardHeader>
                <CardContent>
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div className="grid md:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium mb-2">First Name</label>
                                <div className="relative">
                                    <User className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <Input
                                        value={formData.first_name}
                                        onChange={(e) => setFormData({ ...formData, first_name: e.target.value })}
                                        placeholder="First Name"
                                        className="pl-10"
                                        disabled={!isEditing}
                                    />
                                </div>
                            </div>

                            <div>
                                <label className="block text-sm font-medium mb-2">Last Name</label>
                                <Input
                                    value={formData.last_name}
                                    onChange={(e) => setFormData({ ...formData, last_name: e.target.value })}
                                    placeholder="Last Name"
                                    disabled={!isEditing}
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-2">Email Address</label>
                            <div className="relative">
                                <Mail className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                                <Input
                                    type="email"
                                    value={formData.email}
                                    onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                                    placeholder="your@email.com"
                                    className="pl-10"
                                    disabled={!isEditing}
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-2">Mobile Number</label>
                            <div className="relative">
                                <Phone className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                                <Input
                                    type="tel"
                                    value={formData.mobile}
                                    onChange={(e) => setFormData({ ...formData, mobile: e.target.value })}
                                    placeholder="+91 98765 43210"
                                    className="pl-10"
                                    disabled={!isEditing}
                                />
                            </div>
                        </div>

                        {isEditing && (
                            <div className="flex gap-3 pt-4">
                                <Button
                                    type="submit"
                                    className="bg-blue-600 hover:bg-blue-700"
                                    disabled={updateMutation.isPending}
                                >
                                    <Save className="w-4 h-4 mr-2" />
                                    {updateMutation.isPending ? 'Saving...' : 'Save Changes'}
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => {
                                        setIsEditing(false);
                                        setFormData({
                                            first_name: customer?.first_name || '',
                                            last_name: customer?.last_name || '',
                                            email: customer?.email || '',
                                            mobile: customer?.mobile || '',
                                        });
                                    }}
                                >
                                    Cancel
                                </Button>
                            </div>
                        )}
                    </form>
                </CardContent>
            </Card>

            {/* Change Password */}
            <Card>
                <CardHeader>
                    <CardTitle>Security</CardTitle>
                </CardHeader>
                <CardContent>
                    <p className="text-gray-600 mb-4">Change your password to keep your account secure</p>
                    <Button variant="outline">Change Password</Button>
                </CardContent>
            </Card>
        </div>
    );
}
