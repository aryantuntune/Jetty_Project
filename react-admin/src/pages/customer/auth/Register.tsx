import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useMutation } from '@tanstack/react-query';
import { Button, Input, Card } from '@/components/ui';
import { Mail, Lock, User, Phone, Eye, EyeOff } from 'lucide-react';
import { toast } from 'sonner';
import apiClient from '@/lib/axios';

// Import background
import waterRipplesImg from '@/assets/water_ripples.jpg';

export function CustomerRegister() {
    const [formData, setFormData] = useState({
        first_name: '',
        last_name: '',
        email: '',
        mobile: '',
        password: '',
        password_confirmation: '',
    });
    const [showPassword, setShowPassword] = useState(false);
    const navigate = useNavigate();

    const registerMutation = useMutation({
        mutationFn: async (data: typeof formData) => {
            const response = await apiClient.post('/api/customer/register', data);
            return response.data;
        },
        onSuccess: (data) => {
            if (data.success) {
                toast.success('Registration successful! Please login.');
                navigate('/customer/login');
            } else {
                toast.error(data.message || 'Registration failed');
            }
        },
        onError: (error: any) => {
            const errors = error.response?.data?.errors;
            if (errors) {
                Object.values(errors).forEach((err: any) => {
                    toast.error(err[0]);
                });
            } else {
                toast.error(error.response?.data?.message || 'Registration failed');
            }
        },
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (formData.password !== formData.password_confirmation) {
            toast.error('Passwords do not match');
            return;
        }
        registerMutation.mutate(formData);
    };

    const updateField = (field: string, value: string) => {
        setFormData(prev => ({ ...prev, [field]: value }));
    };

    return (
        <div className="min-h-screen flex items-center justify-center relative p-4 py-12">
            {/* Background */}
            <div
                className="absolute inset-0 bg-cover bg-center"
                style={{ backgroundImage: `url(${waterRipplesImg})` }}
            >
                <div className="absolute inset-0 bg-gradient-to-br from-cyan-600/30 via-slate-700/50 to-slate-900/80"></div>
            </div>

            <div className="w-full max-w-md relative z-10">
                {/* Back to Home */}
                <Link to="/" className="inline-flex items-center text-white/80 hover:text-white mb-6 transition-colors">
                    ← Back to Home
                </Link>

                <Card className="p-8 shadow-2xl border-0">
                    <div className="text-center mb-8">
                        <div className="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 mb-4 shadow-lg shadow-orange-500/30 p-3">
                            <img src="/images/carferry/logo-white.png" alt="Jetty Ferry" className="w-full h-full object-contain" />
                        </div>
                        <h1 className="text-2xl font-bold text-slate-800 mb-2">Create Account</h1>
                        <p className="text-slate-500">Register to book ferry tickets easily</p>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-2">First Name</label>
                                <div className="relative">
                                    <User className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" />
                                    <Input
                                        value={formData.first_name}
                                        onChange={(e) => updateField('first_name', e.target.value)}
                                        placeholder="John"
                                        className="pl-10 border-slate-200 focus:border-cyan-500"
                                        required
                                    />
                                </div>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-2">Last Name</label>
                                <Input
                                    value={formData.last_name}
                                    onChange={(e) => updateField('last_name', e.target.value)}
                                    placeholder="Doe"
                                    className="border-slate-200 focus:border-cyan-500"
                                    required
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                            <div className="relative">
                                <Mail className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" />
                                <Input
                                    type="email"
                                    value={formData.email}
                                    onChange={(e) => updateField('email', e.target.value)}
                                    placeholder="your@email.com"
                                    className="pl-10 border-slate-200 focus:border-cyan-500"
                                    required
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">Mobile Number</label>
                            <div className="relative">
                                <Phone className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" />
                                <Input
                                    type="tel"
                                    value={formData.mobile}
                                    onChange={(e) => updateField('mobile', e.target.value)}
                                    placeholder="+91 98765 43210"
                                    className="pl-10 border-slate-200 focus:border-cyan-500"
                                    required
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">Password</label>
                            <div className="relative">
                                <Lock className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" />
                                <Input
                                    type={showPassword ? 'text' : 'password'}
                                    value={formData.password}
                                    onChange={(e) => updateField('password', e.target.value)}
                                    placeholder="••••••••"
                                    className="pl-10 pr-10 border-slate-200 focus:border-cyan-500"
                                    required
                                    minLength={8}
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPassword(!showPassword)}
                                    className="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600"
                                >
                                    {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                                </button>
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">Confirm Password</label>
                            <div className="relative">
                                <Lock className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" />
                                <Input
                                    type={showPassword ? 'text' : 'password'}
                                    value={formData.password_confirmation}
                                    onChange={(e) => updateField('password_confirmation', e.target.value)}
                                    placeholder="••••••••"
                                    className="pl-10 border-slate-200 focus:border-cyan-500"
                                    required
                                />
                            </div>
                        </div>

                        <div className="flex items-start gap-2 pt-2">
                            <input type="checkbox" className="mt-1 rounded border-slate-300 text-cyan-600" required />
                            <span className="text-sm text-slate-600">
                                I agree to the{' '}
                                <Link to="/terms" className="text-cyan-600 hover:underline">Terms of Service</Link>
                                {' '}and{' '}
                                <Link to="/privacy" className="text-cyan-600 hover:underline">Privacy Policy</Link>
                            </span>
                        </div>

                        <Button
                            type="submit"
                            className="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 shadow-lg shadow-orange-500/30"
                            size="lg"
                            disabled={registerMutation.isPending}
                        >
                            {registerMutation.isPending ? 'Creating Account...' : 'Create Account'}
                        </Button>

                        <div className="relative my-6">
                            <div className="absolute inset-0 flex items-center">
                                <div className="w-full border-t border-slate-200"></div>
                            </div>
                            <div className="relative flex justify-center text-sm">
                                <span className="px-4 bg-white text-slate-500">Or register with</span>
                            </div>
                        </div>

                        <Button type="button" variant="outline" className="w-full border-slate-300" size="lg">
                            <img src="/images/google-logo.svg" className="w-5 h-5 mr-2" alt="Google" />
                            Sign up with Google
                        </Button>

                        <p className="text-center text-sm text-slate-600">
                            Already have an account?{' '}
                            <Link to="/customer/login" className="text-cyan-600 hover:underline font-medium">
                                Sign in
                            </Link>
                        </p>
                    </form>
                </Card>
            </div>
        </div>
    );
}
