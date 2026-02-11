// Navigation type definitions

import { StackNavigationProp } from '@react-navigation/stack';
import { RouteProp } from '@react-navigation/native';

// Auth stack screens (before login)
export type AuthStackParamList = {
    Splash: undefined;
    Login: undefined;
};

// Main stack screens (after login)
export type MainStackParamList = {
    Home: undefined;
    QRScanner: undefined;
};

// Combined root param list
export type RootStackParamList = AuthStackParamList & MainStackParamList;

// Navigation prop types
export type AuthNavigationProp<T extends keyof AuthStackParamList> = StackNavigationProp<AuthStackParamList, T>;
export type MainNavigationProp<T extends keyof MainStackParamList> = StackNavigationProp<MainStackParamList, T>;

// Route prop types
export type AuthRouteProp<T extends keyof AuthStackParamList> = RouteProp<AuthStackParamList, T>;
export type MainRouteProp<T extends keyof MainStackParamList> = RouteProp<MainStackParamList, T>;
