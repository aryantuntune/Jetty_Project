// Storage Service - Secure storage for sensitive data
import AsyncStorage from '@react-native-async-storage/async-storage';
import * as SecureStore from 'expo-secure-store';
import { Customer } from '@/types/models';

const STORAGE_KEYS = {
    AUTH_TOKEN: 'auth_token',
    CUSTOMER_DATA: 'customer_data',
};

export const storageService = {
    // Token methods - Using SecureStore for encryption
    // SecureStore uses iOS Keychain and Android Keystore for secure storage
    saveToken: async (token: string): Promise<void> => {
        try {
            await SecureStore.setItemAsync(STORAGE_KEYS.AUTH_TOKEN, token);
        } catch (error) {
            console.error('[StorageService] Failed to save token securely:', error);
            // Fallback to AsyncStorage only in development if SecureStore fails
            if (__DEV__) {
                console.warn('[StorageService] Falling back to AsyncStorage (DEV ONLY)');
                await AsyncStorage.setItem(STORAGE_KEYS.AUTH_TOKEN, token);
            } else {
                throw error;
            }
        }
    },

    getToken: async (): Promise<string | null> => {
        try {
            return await SecureStore.getItemAsync(STORAGE_KEYS.AUTH_TOKEN);
        } catch (error) {
            console.error('[StorageService] Failed to get token from SecureStore:', error);
            // Fallback to AsyncStorage only in development
            if (__DEV__) {
                return await AsyncStorage.getItem(STORAGE_KEYS.AUTH_TOKEN);
            }
            return null;
        }
    },

    clearToken: async (): Promise<void> => {
        try {
            await SecureStore.deleteItemAsync(STORAGE_KEYS.AUTH_TOKEN);
        } catch (error) {
            console.error('[StorageService] Failed to clear token from SecureStore:', error);
        }
        // Also clear from AsyncStorage (for migration from old storage)
        try {
            await AsyncStorage.removeItem(STORAGE_KEYS.AUTH_TOKEN);
        } catch (error) {
            // Ignore errors
        }
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
