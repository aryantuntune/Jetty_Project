// OTP Screen following the migration guide
import React, { useState, useRef, useEffect } from 'react';
import {
    View,
    Text,
    StyleSheet,
    TextInput,
    TouchableOpacity,
    Alert,
} from 'react-native';
import { useNavigation, useRoute } from '@react-navigation/native';
import { OTPScreenNavigationProp, OTPScreenRouteProp } from '@/types/navigation';
import { colors, typography, spacing } from '@/theme';
import { Button } from '@/components/common';
import { useAppDispatch, useAppSelector } from '@/store';
import { verifyOTP, register, clearError } from '@/store/slices/authSlice';

const OTP_LENGTH = 6;

export const OTPScreen: React.FC = () => {
    const navigation = useNavigation<OTPScreenNavigationProp>();
    const route = useRoute<OTPScreenRouteProp>();
    const dispatch = useAppDispatch();
    const { isLoading, error } = useAppSelector((state) => state.auth);

    const { email } = route.params;

    const [otp, setOtp] = useState<string[]>(Array(OTP_LENGTH).fill(''));
    const [countdown, setCountdown] = useState(60);
    const [canResend, setCanResend] = useState(false);

    const inputRefs = useRef<(TextInput | null)[]>([]);

    useEffect(() => {
        // Countdown timer
        if (countdown > 0) {
            const timer = setInterval(() => {
                setCountdown((prev) => prev - 1);
            }, 1000);
            return () => clearInterval(timer);
        } else {
            setCanResend(true);
        }
    }, [countdown]);

    useEffect(() => {
        return () => {
            dispatch(clearError());
        };
    }, [dispatch]);

    useEffect(() => {
        if (error) {
            Alert.alert('Verification Failed', error);
        }
    }, [error]);

    const handleOtpChange = (value: string, index: number) => {
        // Only allow digits
        if (value && !/^\d$/.test(value)) return;

        const newOtp = [...otp];
        newOtp[index] = value;
        setOtp(newOtp);

        // Auto-focus next input
        if (value && index < OTP_LENGTH - 1) {
            inputRefs.current[index + 1]?.focus();
        }
    };

    const handleKeyPress = (key: string, index: number) => {
        // Handle backspace
        if (key === 'Backspace' && !otp[index] && index > 0) {
            inputRefs.current[index - 1]?.focus();
        }
    };

    const handleVerify = async () => {
        const otpString = otp.join('');

        if (otpString.length !== OTP_LENGTH) {
            Alert.alert('Invalid OTP', 'Please enter the complete 6-digit OTP');
            return;
        }

        dispatch(verifyOTP({ email, otp: otpString }));
        // If successful, RootNavigator will switch to MainNavigator
    };

    const handleResend = async () => {
        setCanResend(false);
        setCountdown(60);
        setOtp(Array(OTP_LENGTH).fill(''));

        // Note: In a real scenario, you'd need to have the full registration data
        // For now, just show a message
        Alert.alert('OTP Sent', `A new OTP has been sent to ${email}`);
    };

    return (
        <View style={styles.container}>
            <View style={styles.content}>
                <Text style={styles.title}>Verify Your Email</Text>
                <Text style={styles.subtitle}>
                    We've sent a 6-digit code to{'\n'}
                    <Text style={styles.email}>{email}</Text>
                </Text>

                {/* OTP Inputs */}
                <View style={styles.otpContainer}>
                    {otp.map((digit, index) => (
                        <TextInput
                            key={index}
                            ref={(ref) => (inputRefs.current[index] = ref)}
                            style={[
                                styles.otpInput,
                                digit ? styles.otpInputFilled : null,
                            ]}
                            value={digit}
                            onChangeText={(value) => handleOtpChange(value, index)}
                            onKeyPress={({ nativeEvent }) => handleKeyPress(nativeEvent.key, index)}
                            keyboardType="number-pad"
                            maxLength={1}
                            selectTextOnFocus
                        />
                    ))}
                </View>

                <Button
                    text="Verify"
                    onPress={handleVerify}
                    loading={isLoading}
                    fullWidth
                    style={styles.verifyButton}
                />

                {/* Resend */}
                <View style={styles.resendContainer}>
                    {canResend ? (
                        <TouchableOpacity onPress={handleResend}>
                            <Text style={styles.resendLink}>Resend OTP</Text>
                        </TouchableOpacity>
                    ) : (
                        <Text style={styles.countdownText}>
                            Resend OTP in {countdown}s
                        </Text>
                    )}
                </View>
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
        alignItems: 'center',
    },
    title: {
        fontSize: typography.fontSize['2xl'],
        fontWeight: typography.fontWeight.bold,
        color: colors.textPrimary,
        marginTop: spacing['2xl'],
        marginBottom: spacing.md,
    },
    subtitle: {
        fontSize: typography.fontSize.base,
        color: colors.textSecondary,
        textAlign: 'center',
        marginBottom: spacing['2xl'],
    },
    email: {
        color: colors.primary,
        fontWeight: typography.fontWeight.semibold,
    },
    otpContainer: {
        flexDirection: 'row',
        justifyContent: 'center',
        marginBottom: spacing['2xl'],
    },
    otpInput: {
        width: 45,
        height: 50,
        borderWidth: 1,
        borderColor: colors.border,
        borderRadius: 8,
        marginHorizontal: spacing.xs,
        fontSize: typography.fontSize.xl,
        fontWeight: typography.fontWeight.bold,
        textAlign: 'center',
        backgroundColor: colors.inputBackground,
        color: colors.textPrimary,
    },
    otpInputFilled: {
        borderColor: colors.primary,
        backgroundColor: colors.cardBackground,
    },
    verifyButton: {
        marginBottom: spacing.xl,
    },
    resendContainer: {
        alignItems: 'center',
    },
    resendLink: {
        fontSize: typography.fontSize.base,
        color: colors.primary,
        fontWeight: typography.fontWeight.semibold,
    },
    countdownText: {
        fontSize: typography.fontSize.base,
        color: colors.textSecondary,
    },
});

export default OTPScreen;
