// Navigation types following the migration guide
import { StackNavigationProp } from '@react-navigation/stack';
import { BottomTabNavigationProp } from '@react-navigation/bottom-tabs';
import { CompositeNavigationProp, RouteProp } from '@react-navigation/native';

// Auth Stack
export type AuthStackParamList = {
    Splash: undefined;
    Login: undefined;
    Register: undefined;
    OTP: { email: string };
    ForgotPassword: undefined;
    ForgotPasswordOTP: { email: string };
    ResetPassword: { email: string };
};

// Main Tab
export type MainTabParamList = {
    HomeTab: undefined;
    BookingsTab: undefined;
    ProfileTab: undefined;
};

// Home Stack (nested in HomeTab)
export type HomeStackParamList = {
    Home: undefined;
    Booking: undefined;
};

// Bookings Stack (nested in BookingsTab)
export type BookingsStackParamList = {
    BookingsList: undefined;
    BookingDetail: { bookingId: number };
};

// Profile Stack (nested in ProfileTab)
export type ProfileStackParamList = {
    Profile: undefined;
    EditProfile: undefined;
    ChangePassword: undefined;
};

// Auth screen navigation props
export type SplashScreenNavigationProp = StackNavigationProp<AuthStackParamList, 'Splash'>;
export type LoginScreenNavigationProp = StackNavigationProp<AuthStackParamList, 'Login'>;
export type RegisterScreenNavigationProp = StackNavigationProp<AuthStackParamList, 'Register'>;
export type OTPScreenNavigationProp = StackNavigationProp<AuthStackParamList, 'OTP'>;
export type OTPScreenRouteProp = RouteProp<AuthStackParamList, 'OTP'>;
export type ForgotPasswordScreenNavigationProp = StackNavigationProp<AuthStackParamList, 'ForgotPassword'>;
export type ForgotPasswordOTPScreenNavigationProp = StackNavigationProp<AuthStackParamList, 'ForgotPasswordOTP'>;
export type ForgotPasswordOTPScreenRouteProp = RouteProp<AuthStackParamList, 'ForgotPasswordOTP'>;
export type ResetPasswordScreenNavigationProp = StackNavigationProp<AuthStackParamList, 'ResetPassword'>;
export type ResetPasswordScreenRouteProp = RouteProp<AuthStackParamList, 'ResetPassword'>;

// Home stack navigation props
export type HomeScreenNavigationProp = CompositeNavigationProp<
    StackNavigationProp<HomeStackParamList, 'Home'>,
    BottomTabNavigationProp<MainTabParamList>
>;
export type BookingScreenNavigationProp = StackNavigationProp<HomeStackParamList, 'Booking'>;

// Bookings stack navigation props
export type BookingsListScreenNavigationProp = CompositeNavigationProp<
    StackNavigationProp<BookingsStackParamList, 'BookingsList'>,
    BottomTabNavigationProp<MainTabParamList>
>;
export type BookingDetailScreenNavigationProp = StackNavigationProp<BookingsStackParamList, 'BookingDetail'>;
export type BookingDetailScreenRouteProp = RouteProp<BookingsStackParamList, 'BookingDetail'>;

// Profile stack navigation props
export type ProfileScreenNavigationProp = CompositeNavigationProp<
    StackNavigationProp<ProfileStackParamList, 'Profile'>,
    BottomTabNavigationProp<MainTabParamList>
>;
export type EditProfileScreenNavigationProp = StackNavigationProp<ProfileStackParamList, 'EditProfile'>;
export type ChangePasswordScreenNavigationProp = StackNavigationProp<ProfileStackParamList, 'ChangePassword'>;
