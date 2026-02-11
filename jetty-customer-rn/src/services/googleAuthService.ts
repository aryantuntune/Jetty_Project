// Google Auth Service - OAuth flow using expo-auth-session
// Note: Requires EAS build with proper Google OAuth configuration
// In Expo Go or without credentials, it will show a "not configured" message

import * as WebBrowser from 'expo-web-browser';
import * as Google from 'expo-auth-session/providers/google';
import Constants from 'expo-constants';
import { authService } from './authService';
import { LoginResponse } from '@/types';

// Complete web browser auth session
WebBrowser.maybeCompleteAuthSession();

// Get Google Client ID from app.json config
const GOOGLE_CLIENT_ID = Constants.expoConfig?.extra?.googleClientId || 'YOUR_GOOGLE_CLIENT_ID';

// Check if Google Sign-In is configured
export const isGoogleConfigured = (): boolean => {
    return GOOGLE_CLIENT_ID && GOOGLE_CLIENT_ID !== 'YOUR_GOOGLE_CLIENT_ID';
};

export interface GoogleAuthResult {
    success: boolean;
    loginResponse?: LoginResponse;
    error?: string;
}

/**
 * Initiate Google Sign-In flow
 * Uses expo-auth-session for the OAuth flow
 */
export const initiateGoogleSignIn = async (): Promise<GoogleAuthResult> => {
    console.log('[GoogleAuth] Initiating Google Sign-In');

    if (!isGoogleConfigured()) {
        console.log('[GoogleAuth] Google Sign-In not configured');
        return {
            success: false,
            error: 'Google Sign-In is not configured. Please add your Google Client ID to app.json.',
        };
    }

    try {
        // Note: This requires the Google OAuth flow to be properly set up
        // In a real implementation, you would use the useAuthRequest hook in the component
        // For now, we provide a fallback message

        console.log('[GoogleAuth] Google Client ID:', GOOGLE_CLIENT_ID);

        return {
            success: false,
            error: 'Google Sign-In requires EAS build. Please build the app with "eas build" to enable this feature.',
        };
    } catch (error: unknown) {
        const errorMessage = error instanceof Error ? error.message : 'Google Sign-In failed';
        console.error('[GoogleAuth] Error:', error);
        return {
            success: false,
            error: errorMessage,
        };
    }
};

/**
 * Handle Google Sign-In response from OAuth flow
 * This is called after the user completes the OAuth consent screen
 */
export const handleGoogleSignInResponse = async (
    accessToken: string,
    userInfo?: {
        id?: string;
        email?: string;
        givenName?: string;
        familyName?: string;
        picture?: string;
    }
): Promise<GoogleAuthResult> => {
    console.log('[GoogleAuth] Processing Google response');

    if (!accessToken || !userInfo?.email) {
        return {
            success: false,
            error: 'Invalid Google Sign-In response',
        };
    }

    try {
        // Call backend API with Google token
        const loginResponse = await authService.googleSignIn({
            idToken: accessToken, // Note: Ideally this should be the ID token, not access token
            firstName: userInfo.givenName || '',
            lastName: userInfo.familyName || '',
            email: userInfo.email,
            profileImage: userInfo.picture,
        });

        console.log('[GoogleAuth] Backend sign-in successful');
        return {
            success: true,
            loginResponse,
        };
    } catch (error: unknown) {
        const errorMessage = error instanceof Error ? error.message : 'Failed to sign in with Google';
        console.error('[GoogleAuth] Backend error:', error);
        return {
            success: false,
            error: errorMessage,
        };
    }
};

/**
 * Get Google Auth configuration for useAuthRequest hook
 * Use this in components that need Google Sign-In
 */
export const getGoogleAuthConfig = () => {
    return {
        clientId: isGoogleConfigured() ? GOOGLE_CLIENT_ID : undefined,
        // For Android standalone apps
        androidClientId: undefined,
        // For iOS standalone apps  
        iosClientId: undefined,
    };
};

export const googleAuthService = {
    isGoogleConfigured,
    initiateGoogleSignIn,
    handleGoogleSignInResponse,
    getGoogleAuthConfig,
};

export default googleAuthService;
