// Login Screen following the migration guide
import React, { useState, useEffect } from 'react';
import {
    View,
    Text,
    StyleSheet,
    ScrollView,
    TouchableOpacity,
    KeyboardAvoidingView,
    Platform,
    Alert,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { LoginScreenNavigationProp } from '@/types/navigation';
import { colors, typography, spacing } from '@/theme';
import { Button, Input } from '@/components/common';
import { useAppDispatch, useAppSelector } from '@/store';
import { login, clearError } from '@/store/slices/authSlice';
import { getEmailError, getPasswordError } from '@/utils/validators';

export const LoginScreen: React.FC = () => {
    const navigation = useNavigation<LoginScreenNavigationProp>();
    const dispatch = useAppDispatch();
    const { isLoading, error } = useAppSelector((state) => state.auth);

    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [emailError, setEmailError] = useState<string | null>(null);
    const [passwordError, setPasswordError] = useState<string | null>(null);

    useEffect(() => {
        // Clear errors on unmount
        return () => {
            dispatch(clearError());
        };
    }, [dispatch]);

    useEffect(() => {
        // Show API error
        if (error) {
            Alert.alert('Login Failed', error);
        }
    }, [error]);

    const validateForm = (): boolean => {
        const emailErr = getEmailError(email);
        const passErr = getPasswordError(password);

        setEmailError(emailErr);
        setPasswordError(passErr);

        return !emailErr && !passErr;
    };

    const handleLogin = async () => {
        if (!validateForm()) return;

        dispatch(login({ email, password }));
    };

    const handleGoogleSignIn = async () => {
        try {
            // Import google auth service
            const { initiateGoogleSignIn, isGoogleConfigured } = await import('@/services/googleAuthService');

            // Check if configured
            if (!isGoogleConfigured()) {
                Alert.alert(
                    'Not Configured',
                    'Google Sign-In requires configuration. Please add your Google Client ID in app.json and rebuild with EAS build.',
                    [{ text: 'OK' }]
                );
                return;
            }

            // Initiate Google Sign-In
            const result = await initiateGoogleSignIn();

            if (!result.success) {
                Alert.alert('Google Sign-In', result.error || 'Sign-in was cancelled.');
                return;
            }

            // If successful, the login state will be updated by authSlice
            if (result.loginResponse) {
                Alert.alert('Welcome!', `Signed in as ${result.loginResponse.customer?.firstName || 'User'}`);
            }
        } catch (error) {
            Alert.alert('Error', 'Failed to sign in with Google. Please try again.');
            console.error('[LoginScreen] Google Sign-In error:', error);
        }
    };

    return (
        <KeyboardAvoidingView
            style={styles.container}
            behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        >
            <ScrollView
                contentContainerStyle={styles.scrollContent}
                keyboardShouldPersistTaps="handled"
            >
                {/* Logo */}
                <View style={styles.logoContainer}>
                    <Text style={styles.logoIcon}>⛴️</Text>
                    <Text style={styles.title}>Jetty Ferry</Text>
                    <Text style={styles.subtitle}>Welcome back!</Text>
                </View>

                {/* Form */}
                <View style={styles.formContainer}>
                    <Input
                        label="Email"
                        value={email}
                        onChangeText={setEmail}
                        placeholder="Enter your email"
                        keyboardType="email-address"
                        autoCapitalize="none"
                        autoCorrect={false}
                        error={emailError}
                    />

                    <Input
                        label="Password"
                        value={password}
                        onChangeText={setPassword}
                        placeholder="Enter your password"
                        secureTextEntry
                        error={passwordError}
                    />

                    <TouchableOpacity
                        style={styles.forgotPassword}
                        onPress={() => navigation.navigate('ForgotPassword')}
                    >
                        <Text style={styles.forgotPasswordText}>Forgot Password?</Text>
                    </TouchableOpacity>

                    <Button
                        text="Login"
                        onPress={handleLogin}
                        loading={isLoading}
                        fullWidth
                        style={styles.loginButton}
                    />

                    <View style={styles.divider}>
                        <View style={styles.dividerLine} />
                        <Text style={styles.dividerText}>OR</Text>
                        <View style={styles.dividerLine} />
                    </View>

                    <Button
                        text="Continue with Google"
                        onPress={handleGoogleSignIn}
                        variant="outline"
                        fullWidth
                    />
                </View>

                {/* Register Link */}
                <View style={styles.registerContainer}>
                    <Text style={styles.registerText}>Don't have an account? </Text>
                    <TouchableOpacity onPress={() => navigation.navigate('Register')}>
                        <Text style={styles.registerLink}>Register</Text>
                    </TouchableOpacity>
                </View>
            </ScrollView>
        </KeyboardAvoidingView>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.background,
    },
    scrollContent: {
        flexGrow: 1,
        padding: spacing.xl,
    },
    logoContainer: {
        alignItems: 'center',
        marginTop: spacing['3xl'],
        marginBottom: spacing['2xl'],
    },
    logoIcon: {
        fontSize: 60,
        marginBottom: spacing.md,
    },
    title: {
        fontSize: typography.fontSize['3xl'],
        fontWeight: typography.fontWeight.bold,
        color: colors.primary,
        marginBottom: spacing.xs,
    },
    subtitle: {
        fontSize: typography.fontSize.lg,
        color: colors.textSecondary,
    },
    formContainer: {
        marginBottom: spacing.xl,
    },
    forgotPassword: {
        alignSelf: 'flex-end',
        marginBottom: spacing.lg,
        marginTop: -spacing.sm,
    },
    forgotPasswordText: {
        fontSize: typography.fontSize.sm,
        color: colors.primary,
    },
    loginButton: {
        marginBottom: spacing.lg,
    },
    divider: {
        flexDirection: 'row',
        alignItems: 'center',
        marginVertical: spacing.lg,
    },
    dividerLine: {
        flex: 1,
        height: 1,
        backgroundColor: colors.border,
    },
    dividerText: {
        marginHorizontal: spacing.md,
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
    },
    registerContainer: {
        flexDirection: 'row',
        justifyContent: 'center',
        marginTop: spacing.lg,
    },
    registerText: {
        fontSize: typography.fontSize.base,
        color: colors.textSecondary,
    },
    registerLink: {
        fontSize: typography.fontSize.base,
        color: colors.primary,
        fontWeight: typography.fontWeight.semibold,
    },
});

export default LoginScreen;
