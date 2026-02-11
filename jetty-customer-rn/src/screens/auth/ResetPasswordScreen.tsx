// Reset Password Screen following the migration guide
import React, { useState } from 'react';
import {
    View,
    Text,
    StyleSheet,
    Alert,
} from 'react-native';
import { useNavigation, useRoute } from '@react-navigation/native';
import {
    ResetPasswordScreenNavigationProp,
    ResetPasswordScreenRouteProp,
} from '@/types/navigation';
import { colors, typography, spacing } from '@/theme';
import { Button, Input } from '@/components/common';
import { authService, getErrorMessage } from '@/services';
import { getPasswordError, getConfirmPasswordError } from '@/utils/validators';

export const ResetPasswordScreen: React.FC = () => {
    const navigation = useNavigation<ResetPasswordScreenNavigationProp>();
    const route = useRoute<ResetPasswordScreenRouteProp>();

    const { email } = route.params;

    const [password, setPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');
    const [passwordError, setPasswordError] = useState<string | null>(null);
    const [confirmError, setConfirmError] = useState<string | null>(null);
    const [isLoading, setIsLoading] = useState(false);

    const handleSubmit = async () => {
        const passErr = getPasswordError(password);
        const confErr = getConfirmPasswordError(password, confirmPassword);

        setPasswordError(passErr);
        setConfirmError(confErr);

        if (passErr || confErr) return;

        setIsLoading(true);
        try {
            await authService.resetPassword({
                email,
                password,
                passwordConfirmation: confirmPassword,
            });

            Alert.alert(
                'Password Reset',
                'Your password has been reset successfully. Please login with your new password.',
                [
                    {
                        text: 'OK',
                        onPress: () => navigation.navigate('Login'),
                    },
                ]
            );
        } catch (error) {
            Alert.alert('Error', getErrorMessage(error));
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <View style={styles.container}>
            <View style={styles.content}>
                <Text style={styles.title}>Create New Password</Text>
                <Text style={styles.subtitle}>
                    Your new password must be at least 8 characters long.
                </Text>

                <Input
                    label="New Password"
                    value={password}
                    onChangeText={setPassword}
                    placeholder="Enter new password"
                    secureTextEntry
                    error={passwordError}
                />

                <Input
                    label="Confirm Password"
                    value={confirmPassword}
                    onChangeText={setConfirmPassword}
                    placeholder="Re-enter new password"
                    secureTextEntry
                    error={confirmError}
                />

                <View style={styles.requirements}>
                    <Text style={styles.requirementsTitle}>Password Requirements:</Text>
                    <Text style={styles.requirementItem}>• At least 8 characters</Text>
                </View>

                <Button
                    text="Reset Password"
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
    },
    requirements: {
        backgroundColor: colors.inputBackground,
        padding: spacing.md,
        borderRadius: 8,
        marginBottom: spacing.xl,
    },
    requirementsTitle: {
        fontSize: typography.fontSize.sm,
        fontWeight: typography.fontWeight.semibold,
        color: colors.textSecondary,
        marginBottom: spacing.xs,
    },
    requirementItem: {
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
    },
    submitButton: {
        marginTop: spacing.md,
    },
});

export default ResetPasswordScreen;
