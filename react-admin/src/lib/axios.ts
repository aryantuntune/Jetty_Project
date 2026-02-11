import axios, { AxiosError, InternalAxiosRequestConfig } from 'axios';

const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000',
    timeout: 30000,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    withCredentials: true, // Important for Sanctum cookies
});

// Function to get XSRF token from cookie
function getXsrfToken(): string | null {
    const cookies = document.cookie.split(';');
    for (const cookie of cookies) {
        const [name, value] = cookie.trim().split('=');
        if (name === 'XSRF-TOKEN') {
            return decodeURIComponent(value);
        }
    }
    return null;
}

// Request interceptor - add auth token and XSRF token
apiClient.interceptors.request.use(
    (config: InternalAxiosRequestConfig) => {
        // Add Bearer token if available
        const token = localStorage.getItem('auth_token');
        if (token && config.headers) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        // Add XSRF token for non-GET requests
        if (config.method !== 'get') {
            const xsrfToken = getXsrfToken();
            if (xsrfToken && config.headers) {
                config.headers['X-XSRF-TOKEN'] = xsrfToken;
            }
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor - handle errors
apiClient.interceptors.response.use(
    (response) => response,
    (error: AxiosError) => {
        const requestUrl = error.config?.url || '';

        // Only redirect to login for actual auth failures on admin endpoints
        if (error.response?.status === 401) {
            const isAuthEndpoint = requestUrl.includes('/admin/login') ||
                requestUrl.includes('/admin/user');

            // Don't redirect for auth endpoints (show error instead)
            // Don't redirect if already on login page
            if (!isAuthEndpoint && !window.location.pathname.includes('/login')) {
                console.warn('Unauthorized request to:', requestUrl);
                // Only redirect if it's a user/auth check that fails
                if (requestUrl.includes('/admin/user')) {
                    localStorage.removeItem('auth_token');
                    window.location.href = '/login';
                }
            }
        }

        if (error.response?.status === 419) {
            // CSRF token mismatch - try to refresh CSRF token
            console.error('CSRF token mismatch - refreshing token...');
            // Don't redirect, just let the request fail and retry
        }

        return Promise.reject(error);
    }
);

export default apiClient;
