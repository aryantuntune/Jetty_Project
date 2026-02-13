/**
 * Expo Config Plugin: SSL Certificate Pinning for Checker App
 *
 * Enables SSL certificate pinning during EAS Build.
 * See customer app's withSSLPinning.js for full documentation.
 */
const { withAndroidManifest, withInfoPlist } = require('expo/config-plugins');

const CERTIFICATE_HASH = 'REPLACE_WITH_ACTUAL_HASH_AFTER_RUNNING_OPENSSL_COMMAND';
const PINNED_DOMAIN = 'carferry.online';

function withAndroidSSLPinning(config) {
    return withAndroidManifest(config, async (config) => {
        const mainApplication = config.modResults.manifest.application?.[0];
        if (mainApplication) {
            mainApplication.$['android:networkSecurityConfig'] = '@xml/network_security_config';
        }
        return config;
    });
}

function withIOSSSLPinning(config) {
    return withInfoPlist(config, (config) => {
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

module.exports = function withSSLPinning(config) {
    config = withAndroidSSLPinning(config);
    config = withIOSSSLPinning(config);
    return config;
};
