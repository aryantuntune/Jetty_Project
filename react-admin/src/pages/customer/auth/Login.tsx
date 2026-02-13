import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useMutation } from '@tanstack/react-query';
import { Button, Input, Card } from '@/components/ui';
import { useCustomerAuthStore } from '@/store/customerAuthStore';
import { Mail, Lock, Eye, EyeOff } from 'lucide-react';
import { toast } from 'sonner';
import apiClient from '@/lib/axios';

// Import background
import waterRipplesImg from '@/assets/water_ripples.jpg';

export function CustomerLogin() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [showPassword, setShowPassword] = useState(false);
    const navigate = useNavigate();
    const { setAuth } = useCustomerAuthStore();

    const loginMutation = useMutation({
        mutationFn: async (credentials: { email: string; password: string }) => {
            const response = await apiClient.post('/api/customer/login', credentials);
            return response.data;
        },
        onSuccess: (data) => {
            if (data.success && data.data?.customer) {
                setAuth(data.data.customer, data.data.token);
                toast.success('Login successful!');
                navigate('/customer/dashboard');
            } else if (data.success && data.customer) {
                // Alternative response format
                setAuth(data.customer, data.token);
                toast.success('Login successful!');
                navigate('/customer/dashboard');
            } else {
                toast.error(data.message || 'Login failed');
            }
        },
        onError: (error: any) => {
            toast.error(error.response?.data?.message || 'Invalid email or password');
        },
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        loginMutation.mutate({ email, password });
    };

    return (
        <div className="min-h-screen flex items-center justify-center relative p-4">
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
                        <div className="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-500 to-cyan-600 mb-4 shadow-lg shadow-cyan-500/30 p-3">
                            <img src="/images/carferry/logo-white.png" alt="Jetty Ferry" className="w-full h-full object-contain" />
                        </div>
                        <h1 className="text-2xl font-bold text-slate-800 mb-2">Welcome Back!</h1>
                        <p className="text-slate-500">Login to book your ferry tickets</p>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-5">
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                            <div className="relative">
                                <Mail className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" />
                                <Input
                                    type="email"
                                    value={email}
                                    onChange={(e) => setEmail(e.target.value)}
                                    placeholder="your@email.com"
                                    className="pl-10 border-slate-200 focus:border-cyan-500 focus:ring-cyan-500"
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
                                    value={password}
                                    onChange={(e) => setPassword(e.target.value)}
                                    placeholder="••••••••"
                                    className="pl-10 pr-10 border-slate-200 focus:border-cyan-500 focus:ring-cyan-500"
                                    required
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

                        <div className="flex items-center justify-between text-sm">
                            <label className="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" className="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" />
                                <span className="text-slate-600">Remember me</span>
                            </label>
                            <Link to="/customer/forgot-password" className="text-cyan-600 hover:underline font-medium">
                                Forgot password?
                            </Link>
                        </div>

                        <Button
                            type="submit"
                            className="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 shadow-lg shadow-orange-500/30"
                            size="lg"
                            disabled={loginMutation.isPending}
                        >
                            {loginMutation.isPending ? 'Signing in...' : 'Sign In'}
                        </Button>

                        <div className="relative my-6">
                            <div className="absolute inset-0 flex items-center">
                                <div className="w-full border-t border-slate-200"></div>
                            </div>
                            <div className="relative flex justify-center text-sm">
                                <span className="px-4 bg-white text-slate-500">Or continue with</span>
                            </div>
                        </div>

                        <Button type="button" variant="outline" className="w-full border-slate-300 hover:border-cyan-500" size="lg">
                            <img src="/images/google-logo.svg" className="w-5 h-5 mr-2" alt="Google" />
                            Sign in with Google
                        </Button>

                        <p className="text-center text-sm text-slate-600">
                            Don't have an account?{' '}
                            <Link to="/customer/register" className="text-orange-600 hover:underline font-medium">
                                Register now
                            </Link>
                        </p>
                    </form>
                </Card>
            </div>
        </div>
    );
}
