/**
 * Conditional Logger Utility for Checker App
 *
 * Purpose: Prevent sensitive data exposure in production logs
 * - In development: Logs normally to console
 * - In production: Only logs if user is superadmin
 *
 * This allows you to debug production issues without exposing
 * ticket data, verification info, or API responses to all checkers.
 */

import { store } from '../store';

/**
 * Check if current user is superadmin
 * Superadmin role_id should be 1 (adjust if different in your system)
 */
const isSuperAdmin = (): boolean => {
    try {
        const state = store.getState();
        const checker = state.auth.checker;

        // Superadmin check - adjust role_id if needed
        // Role 1 = Super Admin (can see logs)
        // Role 5 = Checker (cannot see logs in production)
        return checker?.role_id === 1;
    } catch {
        return false;
    }
};

/**
 * Check if logging is allowed
 * Returns true if:
 * - Running in development mode, OR
 * - Current user is superadmin
 */
const canLog = (): boolean => {
    if (__DEV__) {
        return true; // Always log in development
    }

    return isSuperAdmin(); // Only log for superadmin in production
};

/**
 * Conditional Logger
 * Logs only when appropriate based on environment and user role
 */
class Logger {
    /**
     * Log general information
     * Use for: App flow, navigation, non-sensitive events
     */
    log(...args: any[]): void {
        if (canLog()) {
            console.log(...args);
        }
    }

    /**
     * Log informational messages
     * Use for: Status updates, informational messages
     */
    info(...args: any[]): void {
        if (canLog()) {
            console.info(...args);
        }
    }

    /**
     * Log warnings
     * Use for: Recoverable errors, deprecation notices
     */
    warn(...args: any[]): void {
        if (canLog()) {
            console.warn(...args);
        }
    }

    /**
     * Log errors
     * Errors are ALWAYS logged (even in production) but without sensitive data
     * Use for: Exceptions, critical failures
     */
    error(message: string, error?: any): void {
        if (__DEV__ || isSuperAdmin()) {
            // Full error logging with details
            console.error(message, error);
        } else {
            // Production: Log sanitized error without sensitive data
            console.error(message, {
                type: error?.name || 'Error',
                code: error?.code || error?.status || 'UNKNOWN',
                // Do NOT log: error.message, error.response, error.data
            });
        }
    }

    /**
     * Log API requests (only for superadmin in production)
     * Use for: Debugging API calls
     */
    api(method: string, url: string, data?: any): void {
        if (canLog()) {
            console.log(`[API] ${method} ${url}`, data);
        }
    }

    /**
     * Log API responses (only for superadmin in production)
     * Use for: Debugging API responses
     */
    apiResponse(url: string, response: any): void {
        if (canLog()) {
            console.log(`[API Response] ${url}`, response);
        }
    }

    /**
     * Log verification events (only for superadmin in production)
     * Use for: Debugging ticket verifications
     */
    verification(ticketId: string | number, result: string): void {
        if (canLog()) {
            console.log(`[Verification] Ticket ${ticketId}: ${result}`);
        }
    }

    /**
     * Force log (always logs regardless of role)
     * Use ONLY for: Critical system errors, security events
     * WARNING: Never log sensitive data with this method
     */
    always(message: string): void {
        console.log(`[SYSTEM] ${message}`);
    }

    /**
     * Debug logging (only in development)
     * Use for: Verbose debugging, state dumps
     */
    debug(...args: any[]): void {
        if (__DEV__) {
            console.debug(...args);
        }
    }

    /**
     * Check if current user can see logs
     */
    canLog(): boolean {
        return canLog();
    }

    /**
     * Get user role info (for debugging)
     */
    getUserInfo(): string {
        try {
            const state = store.getState();
            const checker = state.auth.checker;
            return `Checker: ${checker?.name || 'Unknown'}, Role: ${checker?.role_id || 'None'}`;
        } catch {
            return 'Checker info unavailable';
        }
    }
}

export default new Logger();

/**
 * Usage Examples:
 *
 * import Logger from '../utils/logger';
 *
 * // General logging (hidden in production unless superadmin)
 * Logger.log('[VerificationService] Verifying ticket');
 *
 * // Verification logging (hidden in production unless superadmin)
 * Logger.verification('TKT-001', 'Success');
 *
 * // API logging (hidden in production unless superadmin)
 * Logger.api('POST', '/checker/verify-ticket', { ticket_id: 123 });
 *
 * // Error logging (sanitized in production, full in dev/superadmin)
 * Logger.error('[ScannerService] QR scan failed', error);
 *
 * // Always log (use sparingly!)
 * Logger.always('Critical security event detected');
 *
 * // Debug logging (only in dev)
 * Logger.debug('[Redux] State updated', newState);
 */
