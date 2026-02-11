import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';
import { useAuthStore } from '@/store';
import { authService } from '@/services/authService';
import { LoginRequest } from '@/types/auth';
import { toast } from 'sonner';

export function useAuth() {
    const navigate = useNavigate();
    const queryClient = useQueryClient();
    const { setAuth, clearAuth, user, isAuthenticated } = useAuthStore();

    // Login mutation
    const loginMutation = useMutation({
        mutationFn: (credentials: LoginRequest) => authService.login(credentials),
        onSuccess: (data) => {
            setAuth(data.user, data.token);
            queryClient.invalidateQueries({ queryKey: ['user'] });
            toast.success('Login successful!');
            navigate('/dashboard');
        },
        onError: (error: any) => {
            const message = error.response?.data?.message || 'Invalid credentials';
            toast.error(message);
        },
    });

    // Logout mutation
    const logoutMutation = useMutation({
        mutationFn: () => authService.logout(),
        onSuccess: () => {
            clearAuth();
            queryClient.clear();
            navigate('/login');
            toast.success('Logged out successfully');
        },
        onError: () => {
            // Force logout even on error
            clearAuth();
            queryClient.clear();
            navigate('/login');
        },
    });

    // Current user query
    const userQuery = useQuery({
        queryKey: ['user'],
        queryFn: () => authService.getCurrentUser(),
        enabled: isAuthenticated,
        staleTime: 5 * 60 * 1000, // 5 minutes
    });

    return {
        user: user || userQuery.data,
        isAuthenticated,
        login: loginMutation.mutate,
        logout: logoutMutation.mutate,
        isLoggingIn: loginMutation.isPending,
        isLoggingOut: logoutMutation.isPending,
        loginError: loginMutation.error,
    };
}
