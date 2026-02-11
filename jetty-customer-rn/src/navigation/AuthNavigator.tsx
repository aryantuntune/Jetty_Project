// Auth Navigator following the migration guide
import React from 'react';
import { createStackNavigator } from '@react-navigation/stack';
import { AuthStackParamList } from '@/types/navigation';
import { colors } from '@/theme';

// Screens
import SplashScreen from '@/screens/auth/SplashScreen';
import LoginScreen from '@/screens/auth/LoginScreen';
import RegisterScreen from '@/screens/auth/RegisterScreen';
import OTPScreen from '@/screens/auth/OTPScreen';
import ForgotPasswordScreen from '@/screens/auth/ForgotPasswordScreen';
import ForgotPasswordOTPScreen from '@/screens/auth/ForgotPasswordOTPScreen';
import ResetPasswordScreen from '@/screens/auth/ResetPasswordScreen';

const Stack = createStackNavigator<AuthStackParamList>();

export const AuthNavigator: React.FC = () => {
    return (
        <Stack.Navigator
            initialRouteName="Splash"
            screenOptions={{
                headerStyle: {
                    backgroundColor: colors.primary,
                },
                headerTintColor: colors.textWhite,
                headerTitleStyle: {
                    fontWeight: '600',
                },
                headerBackTitleVisible: false,
            }}
        >
            <Stack.Screen
                name="Splash"
                component={SplashScreen}
                options={{ headerShown: false }}
            />
            <Stack.Screen
                name="Login"
                component={LoginScreen}
                options={{ headerShown: false }}
            />
            <Stack.Screen
                name="Register"
                component={RegisterScreen}
                options={{ title: 'Create Account' }}
            />
            <Stack.Screen
                name="OTP"
                component={OTPScreen}
                options={{ title: 'Verify OTP' }}
            />
            <Stack.Screen
                name="ForgotPassword"
                component={ForgotPasswordScreen}
                options={{ title: 'Forgot Password' }}
            />
            <Stack.Screen
                name="ForgotPasswordOTP"
                component={ForgotPasswordOTPScreen}
                options={{ title: 'Verify OTP' }}
            />
            <Stack.Screen
                name="ResetPassword"
                component={ResetPasswordScreen}
                options={{ title: 'Reset Password' }}
            />
        </Stack.Navigator>
    );
};

export default AuthNavigator;
