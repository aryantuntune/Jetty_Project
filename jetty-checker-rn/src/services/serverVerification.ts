/**
 * Server Identity Verification for Checker App
 *
 * Verifies the app is communicating with the genuine carferry.online server.
 * For full native SSL certificate pinning, use EAS Build with the config plugin.
 */
import axios from 'axios';
import Constants from 'expo-constants';

const API_BASE_URL = Constants.expoConfig?.extra?.apiBaseUrl || 'https://carferry.online/api';

const EXPECTED_SERVER_ID = 'carferry-jetty-prod-v1';

export const verifyServerIdentity = async (): Promise<{
    verified: boolean;
    reason?: string;
}> => {
    if (__DEV__) {
        return { verified: true, reason: 'dev-mode' };
    }

    try {
        const response = await axios.get(`${API_BASE_URL}/config/server-identity`, {
            timeout: 10000,
            validateStatus: (status) => status === 200,
        });

        const { server_id, domain } = response.data;

        if (server_id !== EXPECTED_SERVER_ID) {
            return { verified: false, reason: 'server-id-mismatch' };
        }

        if (domain !== 'carferry.online') {
            return { verified: false, reason: 'domain-mismatch' };
        }

        return { verified: true };
    } catch (error) {
        return { verified: false, reason: 'network-error' };
    }
};
