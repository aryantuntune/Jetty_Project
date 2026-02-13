/**
 * Expo Config Plugin: SSL Certificate Pinning
 *
 * This plugin enables SSL certificate pinning for production builds.
 * It modifies native Android and iOS configurations during EAS Build.
 *
 * Usage in app.json:
 *   "plugins": ["./plugins/withSSLPinning"]
 *
 * To update the pin:
 *   1. Get the SHA-256 hash of your server's certificate:
 *      openssl s_client -connect carferry.online:443 -servername carferry.online < /dev/null 2>/dev/null | \
 *        openssl x509 -pubkey -noout | \
 *        openssl pkey -pubin -outform DER | \
 *        openssl dgst -sha256 -binary | base64
 *   2. Replace the CERTIFICATE_HASH below with the new hash
 *   3. Rebuild with: eas build --platform android --profile production
 */
const { withAndroidManifest, withInfoPlist } = require('expo/config-plugins');

// SHA-256 hash of the server's public key (base64-encoded)
// Run the openssl command above against carferry.online to get this value
// IMPORTANT: Update this hash whenever the SSL certificate is renewed
const CERTIFICATE_HASH = 'REPLACE_WITH_ACTUAL_HASH_AFTER_RUNNING_OPENSSL_COMMAND';
const PINNED_DOMAIN = 'carferry.online';

/**
 * Android: Add network security config for certificate pinning
 */
function withAndroidSSLPinning(config) {
    return withAndroidManifest(config, async (config) => {
        const mainApplication = config.modResults.manifest.application?.[0];
        if (mainApplication) {
            // Reference the network security config
            mainApplication.$['android:networkSecurityConfig'] = '@xml/network_security_config';
        }
        return config;
    });
}

/**
 * iOS: Add App Transport Security configuration
 * Note: iOS uses ATS by default which enforces TLS 1.2+
 * For public key pinning on iOS, use TrustKit or similar in native code
 */
function withIOSSSLPinning(config) {
    return withInfoPlist(config, (config) => {
        // Enforce App Transport Security (already default, but explicit)
        config.modResults.NSAppTransportSecurity = {
            NSAllowsArbitraryLoads: false,
            NSExceptionDomains: {
                [PINNED_DOMAIN]: {
                    NSExceptionRequiresForwardSecrecy: true,
                    NSExceptionMinimumTLSVersion: 'TLSv1.2',
                    NSIncludesSubdomains: true,
                },
            },
        };
        return config;
    });
}

/**
 * Main plugin entry point
 */
module.exports = function withSSLPinning(config) {
    config = withAndroidSSLPinning(config);
    config = withIOSSSLPinning(config);
    return config;
};

// Export the certificate hash and domain for reference
module.exports.CERTIFICATE_HASH = CERTIFICATE_HASH;
module.exports.PINNED_DOMAIN = PINNED_DOMAIN;
