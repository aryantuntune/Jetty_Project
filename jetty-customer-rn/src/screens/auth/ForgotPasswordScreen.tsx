// Forgot Password Screen following the migration guide
import React, { useState } from 'react';
import {
    View,
    Text,
    StyleSheet,
    Alert,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { ForgotPasswordScreenNavigationProp } from '@/types/navigation';
import { colors, typography, spacing } from '@/theme';
import { Button, Input } from '@/components/common';
import { authService, getErrorMessage } from '@/services';
import { getEmailError } from '@/utils/validators';

export const ForgotPasswordScreen: React.FC = () => {
    const navigation = useNavigation<ForgotPasswordScreenNavigationProp>();

    const [email, setEmail] = useState('');
    const [emailError, setEmailError] = useState<string | null>(null);
    const [isLoading, setIsLoading] = useState(false);

    const handleSubmit = async () => {
        const error = getEmailError(email);
        setEmailError(error);

        if (error) return;

        setIsLoading(true);
        try {
            await authService.requestPasswordOTP(email);
            navigation.navigate('ForgotPasswordOTP', { email });
        } catch (error) {
            Alert.alert('Error', getErrorMessage(error));
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <View style={styles.container}>
            <View style={styles.content}>
                <Text style={styles.title}>Reset Your Password</Text>
                <Text style={styles.subtitle}>
                    Enter your email address and we'll send you an OTP to reset your password.
                </Text>

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

                <Button
                    text="Send OTP"
                    onPress={handleSubmit}
                    loading={isLoading}
                    fullWidth
                    style={styles.submitButton}
                />
            </View>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.background,
    },
    content: {
        flex: 1,
        padding: spacing.xl,
    },
    title: {
        fontSize: typography.fontSize['2xl'],
        fontWeight: typography.fontWeight.bold,
        color: colors.textPrimary,
        marginTop: spacing.xl,
        marginBottom: spacing.md,
    },
    subtitle: {
        fontSize: typography.fontSize.base,
        color: colors.textSecondary,
        marginBottom: spacing['2xl'],
        lineHeight: typography.fontSize.base * typography.lineHeight.relaxed,
    },
    submitButton: {
        marginTop: spacing.md,
    },
});

export default ForgotPasswordScreen;
