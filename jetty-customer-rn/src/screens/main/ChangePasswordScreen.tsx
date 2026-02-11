// Change Password Screen with OTP verification
import React, { useState } from 'react';
import {
    View,
    Text,
    StyleSheet,
    ScrollView,
    Alert,
    KeyboardAvoidingView,
    Platform,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { colors, typography, spacing } from '@/theme';
import { Button, Input, Card } from '@/components/common';
import { useAppSelector } from '@/store';
import api from '@/services/api';

export const ChangePasswordScreen: React.FC = () => {
    const navigation = useNavigation();
    const { customer } = useAppSelector((state) => state.auth);

    const [step, setStep] = useState<'request' | 'verify'>('request');
    const [currentPassword, setCurrentPassword] = useState('');
    const [newPassword, setNewPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');
    const [otp, setOtp] = useState('');
    const [loading, setLoading] = useState(false);
    const [otpSending, setOtpSending] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    const validatePasswords = (): boolean => {
        const newErrors: Record<string, string> = {};

        if (!currentPassword) {
            newErrors.currentPassword = 'Current password is required';
        }

        if (!newPassword) {
            newErrors.newPassword = 'New password is required';
        } else if (newPassword.length < 6) {
            newErrors.newPassword = 'Password must be at least 6 characters';
        }

        if (!confirmPassword) {
            newErrors.confirmPassword = 'Please confirm your new password';
        } else if (newPassword !== confirmPassword) {
            newErrors.confirmPassword = 'Passwords do not match';
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleRequestOTP = async () => {
        if (!validatePasswords()) return;

        setOtpSending(true);
        try {
            const response = await api.post('/customer/change-password/request-otp');
            if (response.data.success) {
                Alert.alert('OTP Sent', `An OTP has been sent to ${customer?.email}`);
                setStep('verify');
            } else {
                Alert.alert('Error', response.data.message || 'Failed to send OTP');
            }
        } catch (error: any) {
            Alert.alert('Error', error.response?.data?.message || 'Failed to send OTP');
        } finally {
            setOtpSending(false);
        }
    };

    const handleChangePassword = async () => {
        if (!otp) {
            setErrors({ otp: 'Please enter the OTP' });
            return;
        }

        setLoading(true);
        try {
            const response = await api.post('/customer/change-password', {
                otp,
                current_password: currentPassword,
                new_password: newPassword,
            });

            if (response.data.success) {
                Alert.alert(
                    'Success',
                    'Your password has been changed successfully!',
                    [{ text: 'OK', onPress: () => navigation.goBack() }]
                );
            } else {
                Alert.alert('Error', response.data.message || 'Failed to change password');
            }
        } catch (error: any) {
            Alert.alert('Error', error.response?.data?.message || 'Failed to change password');
        } finally {
            setLoading(false);
        }
    };

    const handleResendOTP = async () => {
        setOtpSending(true);
        try {
            const response = await api.post('/customer/change-password/request-otp');
            if (response.data.success) {
                Alert.alert('OTP Resent', 'A new OTP has been sent to your email');
            }
        } catch (error: any) {
            Alert.alert('Error', 'Failed to resend OTP');
        } finally {
            setOtpSending(false);
        }
    };

    return (
        <KeyboardAvoidingView
            style={styles.container}
            behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        >
            <ScrollView contentContainerStyle={styles.scrollContent}>
                <Card style={styles.card}>
                    <Text style={styles.title}>
                        {step === 'request' ? 'Change Password' : 'Verify OTP'}
                    </Text>

                    {step === 'request' ? (
                        <>
                            <Text style={styles.subtitle}>
                                Enter your current and new password. We'll send an OTP to verify.
                            </Text>

                            <Input
                                label="Current Password"
                                value={currentPassword}
                                onChangeText={setCurrentPassword}
                                secureTextEntry
                                placeholder="Enter current password"
                                error={errors.currentPassword}
                            />

                            <Input
                                label="New Password"
                                value={newPassword}
                                onChangeText={setNewPassword}
                                secureTextEntry
                                placeholder="Enter new password (min 6 characters)"
                                error={errors.newPassword}
                            />

                            <Input
                                label="Confirm New Password"
                                value={confirmPassword}
                                onChangeText={setConfirmPassword}
                                secureTextEntry
                                placeholder="Confirm new password"
                                error={errors.confirmPassword}
                            />

                            <Button
                                title={otpSending ? 'Sending OTP...' : 'Send OTP'}
                                onPress={handleRequestOTP}
                                disabled={otpSending}
                                style={styles.button}
                            />
                        </>
                    ) : (
                        <>
                            <Text style={styles.subtitle}>
                                Enter the 6-digit OTP sent to {customer?.email}
                            </Text>

                            <Input
                                label="OTP"
                                value={otp}
                                onChangeText={setOtp}
                                placeholder="Enter 6-digit OTP"
                                keyboardType="number-pad"
                                maxLength={6}
                                error={errors.otp}
                            />

                            <Button
                                title={loading ? 'Changing Password...' : 'Change Password'}
                                onPress={handleChangePassword}
                                disabled={loading}
                                style={styles.button}
                            />

                            <Button
                                title={otpSending ? 'Resending...' : 'Resend OTP'}
                                onPress={handleResendOTP}
                                disabled={otpSending}
                                variant="outline"
                                style={styles.resendButton}
                            />

                            <Button
                                title="Back"
                                onPress={() => setStep('request')}
                                variant="text"
                                style={styles.backButton}
                            />
                        </>
                    )}
                </Card>
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
        padding: spacing.lg,
    },
    card: {
        padding: spacing.xl,
    },
    title: {
        fontSize: typography.fontSize['2xl'],
        fontWeight: typography.fontWeight.bold,
        color: colors.textPrimary,
        marginBottom: spacing.sm,
        textAlign: 'center',
    },
    subtitle: {
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
        marginBottom: spacing.xl,
        textAlign: 'center',
    },
    button: {
        marginTop: spacing.xl,
    },
    resendButton: {
        marginTop: spacing.md,
    },
    backButton: {
        marginTop: spacing.sm,
    },
});

export default ChangePasswordScreen;
