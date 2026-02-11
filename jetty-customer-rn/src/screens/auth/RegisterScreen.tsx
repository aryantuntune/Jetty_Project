// Register Screen following the migration guide
import React, { useState, useEffect } from 'react';
import {
    View,
    StyleSheet,
    ScrollView,
    KeyboardAvoidingView,
    Platform,
    Alert,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { RegisterScreenNavigationProp } from '@/types/navigation';
import { colors, spacing } from '@/theme';
import { Button, Input } from '@/components/common';
import { useAppDispatch, useAppSelector } from '@/store';
import { register, clearError } from '@/store/slices/authSlice';
import {
    getRequiredError,
    getEmailError,
    getMobileError,
    getPasswordError,
    getConfirmPasswordError,
} from '@/utils/validators';

export const RegisterScreen: React.FC = () => {
    const navigation = useNavigation<RegisterScreenNavigationProp>();
    const dispatch = useAppDispatch();
    const { isLoading, error } = useAppSelector((state) => state.auth);

    const [firstName, setFirstName] = useState('');
    const [lastName, setLastName] = useState('');
    const [email, setEmail] = useState('');
    const [mobile, setMobile] = useState('');
    const [password, setPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');

    const [errors, setErrors] = useState<Record<string, string | null>>({});

    useEffect(() => {
        return () => {
            dispatch(clearError());
        };
    }, [dispatch]);

    useEffect(() => {
        if (error) {
            Alert.alert('Registration Failed', error);
        }
    }, [error]);

    const validateForm = (): boolean => {
        const newErrors: Record<string, string | null> = {
            firstName: getRequiredError(firstName, 'First name'),
            lastName: getRequiredError(lastName, 'Last name'),
            email: getEmailError(email),
            mobile: getMobileError(mobile),
            password: getPasswordError(password),
            confirmPassword: getConfirmPasswordError(password, confirmPassword),
        };

        setErrors(newErrors);

        return Object.values(newErrors).every((err) => !err);
    };

    const handleRegister = async () => {
        if (!validateForm()) return;

        // Clear any previous errors before starting
        dispatch(clearError());

        try {
            console.log('[RegisterScreen] Calling register API...');
            await dispatch(
                register({
                    firstName,
                    lastName,
                    email,
                    mobile,
                    password,
                    passwordConfirmation: confirmPassword,
                })
            ).unwrap();

            console.log('[RegisterScreen] Register success, pushing OTP screen...');
            // Use push instead of navigate to ensure we add to stack
            navigation.push('OTP', { email });
            console.log('[RegisterScreen] Push called');
        } catch (err: unknown) {
            // Show the actual error
            const errorMessage = err instanceof Error ? err.message : String(err);
            console.log('[RegisterScreen] Error:', errorMessage);
            Alert.alert('Registration Failed', errorMessage);
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
                <View style={styles.row}>
                    <View style={styles.halfInput}>
                        <Input
                            label="First Name"
                            value={firstName}
                            onChangeText={setFirstName}
                            placeholder="First name"
                            autoCapitalize="words"
                            error={errors.firstName}
                        />
                    </View>
                    <View style={styles.halfInput}>
                        <Input
                            label="Last Name"
                            value={lastName}
                            onChangeText={setLastName}
                            placeholder="Last name"
                            autoCapitalize="words"
                            error={errors.lastName}
                        />
                    </View>
                </View>

                <Input
                    label="Email"
                    value={email}
                    onChangeText={setEmail}
                    placeholder="Enter your email"
                    keyboardType="email-address"
                    autoCapitalize="none"
                    autoCorrect={false}
                    error={errors.email}
                />

                <Input
                    label="Mobile Number"
                    value={mobile}
                    onChangeText={setMobile}
                    placeholder="Enter 10-digit mobile"
                    keyboardType="phone-pad"
                    maxLength={10}
                    error={errors.mobile}
                />

                <Input
                    label="Password"
                    value={password}
                    onChangeText={setPassword}
                    placeholder="Minimum 8 characters"
                    secureTextEntry
                    error={errors.password}
                />

                <Input
                    label="Confirm Password"
                    value={confirmPassword}
                    onChangeText={setConfirmPassword}
                    placeholder="Re-enter password"
                    secureTextEntry
                    error={errors.confirmPassword}
                />

                <Button
                    text="Create Account"
                    onPress={handleRegister}
                    loading={isLoading}
                    fullWidth
                    style={styles.registerButton}
                />
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
    row: {
        flexDirection: 'row',
        justifyContent: 'space-between',
    },
    halfInput: {
        width: '48%',
    },
    registerButton: {
        marginTop: spacing.md,
    },
});

export default RegisterScreen;
