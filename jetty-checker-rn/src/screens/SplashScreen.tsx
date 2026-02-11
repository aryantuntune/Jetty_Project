// SplashScreen - Initial loading screen with auth check
import React, { useEffect } from 'react';
import { View, Text, StyleSheet, ActivityIndicator } from 'react-native';
import { colors } from '../theme/colors';
import { typography } from '../theme/typography';
import { spacing } from '../theme/spacing';
import { useAppDispatch, useAppSelector } from '../store';
import { checkAuthStatus } from '../store/slices/authSlice';
import { loadVerificationCount } from '../store/slices/verificationSlice';

export const SplashScreen: React.FC = () => {
    const dispatch = useAppDispatch();
    const { isCheckingAuth } = useAppSelector((state) => state.auth);

    useEffect(() => {
        // Check auth status immediately
        dispatch(checkAuthStatus());
        dispatch(loadVerificationCount());
    }, [dispatch]);

    return (
        <View style={styles.container}>
            <View style={[styles.gradient, { backgroundColor: colors.primary }]}>
                {/* App Icon/Logo placeholder */}
                <View style={styles.logoContainer}>
                    <Text style={styles.logoIcon}>🎫</Text>
                </View>

                {/* App Title */}
                <Text style={styles.title}>Jetty Checker</Text>
                <Text style={styles.subtitle}>Verify ferry tickets</Text>

                {/* Loading indicator */}
                <View style={styles.loadingContainer}>
                    <ActivityIndicator size="large" color={colors.textWhite} />
                    <Text style={styles.loadingText}>
                        {isCheckingAuth ? 'Checking authentication...' : 'Loading...'}
                    </Text>
                </View>
            </View>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
    },
    gradient: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        padding: spacing.xl,
    },
    logoContainer: {
        width: 120,
        height: 120,
        borderRadius: 60,
        backgroundColor: 'rgba(255, 255, 255, 0.2)',
        justifyContent: 'center',
        alignItems: 'center',
        marginBottom: spacing['2xl'],
    },
    logoIcon: {
        fontSize: 60,
    },
    title: {
        fontSize: typography.fontSize['3xl'],
        fontWeight: typography.fontWeight.bold,
        color: colors.textWhite,
        marginBottom: spacing.sm,
    },
    subtitle: {
        fontSize: typography.fontSize.lg,
        color: colors.textWhite,
        opacity: 0.9,
        marginBottom: spacing['3xl'],
    },
    loadingContainer: {
        alignItems: 'center',
        marginTop: spacing['3xl'],
    },
    loadingText: {
        color: colors.textWhite,
        fontSize: typography.fontSize.base,
        marginTop: spacing.md,
        opacity: 0.8,
    },
});

export default SplashScreen;
