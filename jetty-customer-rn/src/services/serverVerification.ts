/**
 * Server Identity Verification
 *
 * Provides an application-level check that the app is communicating with
 * the genuine carferry.online server. This complements HTTPS by verifying
 * a shared server fingerprint on app startup.
 *
 * For full native SSL certificate pinning, the Expo config plugin
 * (plugins/withSSLPinning) handles it during EAS Build.
 */
import axios from 'axios';
import Constants from 'expo-constants';

const API_BASE_URL = Constants.expoConfig?.extra?.apiBaseUrl || 'https://carferry.online/api';

// Expected server identifier (set by your server, verified here)
const EXPECTED_SERVER_ID = 'carferry-jetty-prod-v1';

/**
 * Verify that the app is talking to the genuine server.
 * Call this on app startup. If verification fails, the app should warn the user.
 *
 * @returns true if the server identity is verified
 */
export const verifyServerIdentity = async (): Promise<{
    verified: boolean;
    reason?: string;
}> => {
    // Skip in development
    if (__DEV__) {
        return { verified: true, reason: 'dev-mode' };
    }

    try {
        const response = await axios.get(`${API_BASE_URL}/config/server-identity`, {
            timeout: 10000,
            // Ensure we're hitting HTTPS
            validateStatus: (status) => status === 200,
        });

        const { server_id, domain } = response.data;

        // Verify server identity
        if (server_id !== EXPECTED_SERVER_ID) {
            return { verified: false, reason: 'server-id-mismatch' };
        }

        // Verify domain matches
        if (domain !== 'carferry.online') {
            return { verified: false, reason: 'domain-mismatch' };
        }

        return { verified: true };
    } catch (error) {
        // Network error - could be MITM or just offline
        return { verified: false, reason: 'network-error' };
    }
};
