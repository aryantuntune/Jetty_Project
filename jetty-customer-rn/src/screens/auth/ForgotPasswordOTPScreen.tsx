// Forgot Password OTP Screen following the migration guide
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
import {
    ForgotPasswordOTPScreenNavigationProp,
    ForgotPasswordOTPScreenRouteProp,
} from '@/types/navigation';
import { colors, typography, spacing } from '@/theme';
import { Button } from '@/components/common';
import { authService, getErrorMessage } from '@/services';

const OTP_LENGTH = 6;

export const ForgotPasswordOTPScreen: React.FC = () => {
    const navigation = useNavigation<ForgotPasswordOTPScreenNavigationProp>();
    const route = useRoute<ForgotPasswordOTPScreenRouteProp>();

    const { email } = route.params;

    const [otp, setOtp] = useState<string[]>(Array(OTP_LENGTH).fill(''));
    const [countdown, setCountdown] = useState(60);
    const [canResend, setCanResend] = useState(false);
    const [isLoading, setIsLoading] = useState(false);

    const inputRefs = useRef<(TextInput | null)[]>([]);

    useEffect(() => {
        if (countdown > 0) {
            const timer = setInterval(() => {
                setCountdown((prev) => prev - 1);
            }, 1000);
            return () => clearInterval(timer);
        } else {
            setCanResend(true);
        }
    }, [countdown]);

    const handleOtpChange = (value: string, index: number) => {
        if (value && !/^\d$/.test(value)) return;

        const newOtp = [...otp];
        newOtp[index] = value;
        setOtp(newOtp);

        if (value && index < OTP_LENGTH - 1) {
            inputRefs.current[index + 1]?.focus();
        }
    };

    const handleKeyPress = (key: string, index: number) => {
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

        setIsLoading(true);
        try {
            await authService.verifyPasswordOTP({ email, otp: otpString });
            navigation.navigate('ResetPassword', { email });
        } catch (error) {
            Alert.alert('Verification Failed', getErrorMessage(error));
        } finally {
            setIsLoading(false);
        }
    };

    const handleResend = async () => {
        setCanResend(false);
        setCountdown(60);
        setOtp(Array(OTP_LENGTH).fill(''));

        try {
            await authService.requestPasswordOTP(email);
            Alert.alert('OTP Sent', `A new OTP has been sent to ${email}`);
        } catch (error) {
            Alert.alert('Error', getErrorMessage(error));
        }
    };

    return (
        <View style={styles.container}>
            <View style={styles.content}>
                <Text style={styles.title}>Enter OTP</Text>
                <Text style={styles.subtitle}>
                    Enter the 6-digit code sent to{'\n'}
                    <Text style={styles.email}>{email}</Text>
                </Text>

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

export default ForgotPasswordOTPScreen;
