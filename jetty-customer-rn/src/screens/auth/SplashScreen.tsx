// Splash Screen following the migration guide
import React, { useEffect } from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { SplashScreenNavigationProp } from '@/types/navigation';
import { colors, typography, spacing } from '@/theme';
import { useAppSelector } from '@/store';

export const SplashScreen: React.FC = () => {
    const navigation = useNavigation<SplashScreenNavigationProp>();
    const { isAuthenticated, isLoading } = useAppSelector((state) => state.auth);

    useEffect(() => {
        // Wait for auth check and minimum display time
        const timer = setTimeout(() => {
            if (!isLoading) {
                if (!isAuthenticated) {
                    navigation.replace('Login');
                }
                // If authenticated, RootNavigator will switch to MainNavigator
            }
        }, 2000);

        return () => clearTimeout(timer);
    }, [isAuthenticated, isLoading, navigation]);

    return (
        <View style={styles.container}>
            <View style={styles.logoContainer}>
                <Text style={styles.logoIcon}>⛴️</Text>
                <Text style={styles.title}>Jetty Ferry</Text>
                <Text style={styles.subtitle}>Book Your Journey</Text>
            </View>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.primary,
        justifyContent: 'center',
        alignItems: 'center',
    },
    logoContainer: {
        alignItems: 'center',
    },
    logoIcon: {
        fontSize: 80,
        marginBottom: spacing.lg,
    },
    title: {
        fontSize: typography.fontSize['4xl'],
        fontWeight: typography.fontWeight.bold,
        color: colors.textWhite,
        marginBottom: spacing.sm,
    },
    subtitle: {
        fontSize: typography.fontSize.lg,
        color: colors.textWhite,
        opacity: 0.8,
    },
});

export default SplashScreen;
