// RootNavigator - Main navigation with auth-based conditional rendering
import React from 'react';
import { createStackNavigator } from '@react-navigation/stack';
import { useAppSelector } from '../store';
import { SplashScreen, LoginScreen, HomeScreen, QRScannerScreen } from '../screens';
import { colors } from '../theme/colors';
import { RootStackParamList } from '../types/navigation';

const Stack = createStackNavigator<RootStackParamList>();

export const RootNavigator: React.FC = () => {
    const { isAuthenticated, isCheckingAuth } = useAppSelector((state) => state.auth);

    // Show splash while checking auth status
    if (isCheckingAuth) {
        return (
            <Stack.Navigator screenOptions={{ headerShown: false }}>
                <Stack.Screen name="Splash" component={SplashScreen} />
            </Stack.Navigator>
        );
    }

    // After auth check, show either login or main screens
    if (isAuthenticated) {
        return (
            <Stack.Navigator
                screenOptions={{
                    headerStyle: {
                        backgroundColor: colors.primary,
                    },
                    headerTintColor: colors.textWhite,
                    headerTitleStyle: {
                        fontWeight: '600',
                    },
                }}
            >
                <Stack.Screen
                    name="Home"
                    component={HomeScreen}
                    options={{ headerShown: false }}
                />
                <Stack.Screen
                    name="QRScanner"
                    component={QRScannerScreen}
                    options={{
                        headerShown: false,
                        presentation: 'modal',
                    }}
                />
            </Stack.Navigator>
        );
    }

    // Not authenticated - show login
    return (
        <Stack.Navigator screenOptions={{ headerShown: false }}>
            <Stack.Screen name="Login" component={LoginScreen} />
        </Stack.Navigator>
    );
};

export default RootNavigator;
