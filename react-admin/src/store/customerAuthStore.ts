import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import { Customer } from '@/types/customer';

interface CustomerAuthState {
    customer: Customer | null;
    token: string | null;
    isAuthenticated: boolean;

    setAuth: (customer: Customer, token: string) => void;
    updateCustomer: (customer: Partial<Customer>) => void;
    clearAuth: () => void;
}

export const useCustomerAuthStore = create<CustomerAuthState>()(
    persist(
        (set, get) => ({
            customer: null,
            token: null,
            isAuthenticated: false,

            setAuth: (customer, token) => {
                localStorage.setItem('customer_token', token);
                set({ customer, token, isAuthenticated: true });
            },

            updateCustomer: (updates) => {
                const current = get().customer;
                if (current) {
                    set({ customer: { ...current, ...updates } });
                }
            },

            clearAuth: () => {
                localStorage.removeItem('customer_token');
                set({ customer: null, token: null, isAuthenticated: false });
            },
        }),
        {
            name: 'customer-auth-storage',
        }
    )
);
