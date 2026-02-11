// Storage Service following the migration guide
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Customer } from '@/types/models';

const STORAGE_KEYS = {
    AUTH_TOKEN: 'auth_token',
    CUSTOMER_DATA: 'customer_data',
};

export const storageService = {
    // Token methods
    saveToken: async (token: string): Promise<void> => {
        await AsyncStorage.setItem(STORAGE_KEYS.AUTH_TOKEN, token);
    },

    getToken: async (): Promise<string | null> => {
        return AsyncStorage.getItem(STORAGE_KEYS.AUTH_TOKEN);
    },

    clearToken: async (): Promise<void> => {
        await AsyncStorage.removeItem(STORAGE_KEYS.AUTH_TOKEN);
    },

    // Customer methods
    saveCustomer: async (customer: Customer): Promise<void> => {
        await AsyncStorage.setItem(STORAGE_KEYS.CUSTOMER_DATA, JSON.stringify(customer));
    },

    getCustomer: async (): Promise<Customer | null> => {
        const data = await AsyncStorage.getItem(STORAGE_KEYS.CUSTOMER_DATA);
        if (data) {
            try {
                return JSON.parse(data) as Customer;
            } catch {
                return null;
            }
        }
        return null;
    },

    clearCustomer: async (): Promise<void> => {
        await AsyncStorage.removeItem(STORAGE_KEYS.CUSTOMER_DATA);
    },

    // Auth status
    isLoggedIn: async (): Promise<boolean> => {
        const token = await storageService.getToken();
        return !!token;
    },

    // Clear all
    clearAll: async (): Promise<void> => {
        await AsyncStorage.multiRemove([
            STORAGE_KEYS.AUTH_TOKEN,
            STORAGE_KEYS.CUSTOMER_DATA,
        ]);
    },
};

export default storageService;
