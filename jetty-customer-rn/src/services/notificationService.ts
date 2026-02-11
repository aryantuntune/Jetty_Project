// Notification Service - Push Notifications using expo-notifications
// Note: Push tokens require EAS build for full functionality

import Constants from 'expo-constants';
import { Platform } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { api } from './api';

// Storage key for push token
const PUSH_TOKEN_KEY = 'push_token';

// Check if notifications are supported (requires EAS build)
let Notifications: typeof import('expo-notifications') | null = null;
let Device: typeof import('expo-device') | null = null;

try {
    // eslint-disable-next-line @typescript-eslint/no-require-imports
    Notifications = require('expo-notifications');
    // eslint-disable-next-line @typescript-eslint/no-require-imports
    Device = require('expo-device');
} catch {
    console.log('[NotificationService] expo-notifications not available');
}

/**
 * Check if push notifications are supported
 */
export const isNotificationsSupported = (): boolean => {
    return Notifications !== null && Device !== null;
};

/**
 * Request notification permissions
 */
export const requestPermissions = async (): Promise<boolean> => {
    if (!Notifications) {
        console.log('[NotificationService] Notifications not available');
        return false;
    }

    try {
        const { status: existingStatus } = await Notifications.getPermissionsAsync();
        let finalStatus = existingStatus;

        if (existingStatus !== 'granted') {
            const { status } = await Notifications.requestPermissionsAsync();
            finalStatus = status;
        }

        return finalStatus === 'granted';
    } catch (error) {
        console.error('[NotificationService] Permission request failed:', error);
        return false;
    }
};

/**
 * Register for push notifications and get Expo push token
 */
export const registerForPushNotifications = async (): Promise<string | null> => {
    console.log('[NotificationService] Registering for push notifications');

    if (!isNotificationsSupported()) {
        console.log('[NotificationService] Notifications not supported');
        return null;
    }

    // Check if running on physical device (required for push)
    if (Device && !Device.isDevice) {
        console.log('[NotificationService] Push notifications require a physical device');
        return null;
    }

    // Request permissions
    const hasPermission = await requestPermissions();
    if (!hasPermission) {
        console.log('[NotificationService] Permission not granted');
        return null;
    }

    try {
        // Get Expo push token
        const projectId = Constants.expoConfig?.extra?.eas?.projectId;
        const tokenData = await Notifications!.getExpoPushTokenAsync({
            projectId: projectId,
        });

        const token = tokenData.data;
        console.log('[NotificationService] Got push token:', token);

        // Save token locally
        await AsyncStorage.setItem(PUSH_TOKEN_KEY, token);

        // Send token to backend
        await sendTokenToBackend(token);

        return token;
    } catch (error) {
        console.error('[NotificationService] Failed to get push token:', error);
        return null;
    }
};

/**
 * Send push token to backend for server-side push
 */
const sendTokenToBackend = async (token: string): Promise<void> => {
    try {
        await api.post('/customer/push-token', { token, platform: Platform.OS });
        console.log('[NotificationService] Token sent to backend');
    } catch (error) {
        console.error('[NotificationService] Failed to send token to backend:', error);
        // Don't throw - token is saved locally and can be retried
    }
};

/**
 * Configure notification handlers
 */
export const configureNotifications = (): void => {
    if (!Notifications) return;

    // Set notification handler for when app is in foreground
    Notifications.setNotificationHandler({
        handleNotification: async () => ({
            shouldShowAlert: true,
            shouldPlaySound: true,
            shouldSetBadge: true,
            shouldShowBanner: true,
            shouldShowList: true,
        }),
    });
};

/**
 * Add notification received listener
 */
export const addNotificationReceivedListener = (
    callback: (notification: unknown) => void
): (() => void) | undefined => {
    if (!Notifications) return undefined;

    const subscription = Notifications.addNotificationReceivedListener(callback);
    return () => subscription.remove();
};

/**
 * Add notification response listener (when user taps notification)
 */
export const addNotificationResponseListener = (
    callback: (response: unknown) => void
): (() => void) | undefined => {
    if (!Notifications) return undefined;

    const subscription = Notifications.addNotificationResponseReceivedListener(callback);
    return () => subscription.remove();
};

/**
 * Get stored push token
 */
export const getStoredPushToken = async (): Promise<string | null> => {
    return AsyncStorage.getItem(PUSH_TOKEN_KEY);
};

export const notificationService = {
    isNotificationsSupported,
    requestPermissions,
    registerForPushNotifications,
    configureNotifications,
    addNotificationReceivedListener,
    addNotificationResponseListener,
    getStoredPushToken,
};

export default notificationService;
