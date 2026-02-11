// Storage Service for Checker App
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Checker, VerificationCount } from '../types/models';

const STORAGE_KEYS = {
    AUTH_TOKEN: 'checker_auth_token',
    CHECKER_DATA: 'checker_data',
    VERIFICATION_COUNT: 'verification_count',
    LAST_VERIFIED_AT: 'last_verified_at',
};

// Helper to get today's date string
const getTodayDateString = (): string => {
    return new Date().toISOString().split('T')[0]; // YYYY-MM-DD
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

    // Checker methods
    saveChecker: async (checker: Checker): Promise<void> => {
        await AsyncStorage.setItem(STORAGE_KEYS.CHECKER_DATA, JSON.stringify(checker));
    },

    getChecker: async (): Promise<Checker | null> => {
        const data = await AsyncStorage.getItem(STORAGE_KEYS.CHECKER_DATA);
        if (data) {
            try {
                return JSON.parse(data) as Checker;
            } catch {
                return null;
            }
        }
        return null;
    },

    clearChecker: async (): Promise<void> => {
        await AsyncStorage.removeItem(STORAGE_KEYS.CHECKER_DATA);
    },

    // Verification count methods (resets daily)
    getVerificationCount: async (): Promise<number> => {
        const data = await AsyncStorage.getItem(STORAGE_KEYS.VERIFICATION_COUNT);
        if (data) {
            try {
                const parsed: VerificationCount = JSON.parse(data);
                // Check if it's still today
                if (parsed.date === getTodayDateString()) {
                    return parsed.count;
                }
                // Different day, reset count
                await storageService.resetVerificationCount();
                return 0;
            } catch {
                return 0;
            }
        }
        return 0;
    },

    incrementVerificationCount: async (): Promise<number> => {
        const currentCount = await storageService.getVerificationCount();
        const newCount = currentCount + 1;
        const countData: VerificationCount = {
            count: newCount,
            date: getTodayDateString(),
        };
        await AsyncStorage.setItem(STORAGE_KEYS.VERIFICATION_COUNT, JSON.stringify(countData));
        return newCount;
    },

    resetVerificationCount: async (): Promise<void> => {
        const countData: VerificationCount = {
            count: 0,
            date: getTodayDateString(),
        };
        await AsyncStorage.setItem(STORAGE_KEYS.VERIFICATION_COUNT, JSON.stringify(countData));
    },

    // Last verified timestamp
    setLastVerifiedAt: async (timestamp: string): Promise<void> => {
        await AsyncStorage.setItem(STORAGE_KEYS.LAST_VERIFIED_AT, timestamp);
    },

    getLastVerifiedAt: async (): Promise<string | null> => {
        return AsyncStorage.getItem(STORAGE_KEYS.LAST_VERIFIED_AT);
    },

    // Auth status
    isLoggedIn: async (): Promise<boolean> => {
        const token = await storageService.getToken();
        return !!token;
    },

    // Clear all (on logout)
    clearAll: async (): Promise<void> => {
        await AsyncStorage.multiRemove([
            STORAGE_KEYS.AUTH_TOKEN,
            STORAGE_KEYS.CHECKER_DATA,
            // Keep verification count (local tracking)
        ]);
    },
};

export default storageService;
