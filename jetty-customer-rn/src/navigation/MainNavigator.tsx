// Main Navigator following the migration guide
import React from 'react';
import { createStackNavigator } from '@react-navigation/stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { Text, View, StyleSheet } from 'react-native';
import {
    MainTabParamList,
    HomeStackParamList,
    BookingsStackParamList,
    ProfileStackParamList,
} from '@/types/navigation';
import { colors, typography } from '@/theme';

// Main Screens
import HomeScreen from '@/screens/main/HomeScreen';
import BookingScreen from '@/screens/main/BookingScreen';
import BookingsListScreen from '@/screens/main/BookingsListScreen';
import BookingDetailScreen from '@/screens/main/BookingDetailScreen';
import ProfileScreen from '@/screens/main/ProfileScreen';
import EditProfileScreen from '@/screens/main/EditProfileScreen';
import ChangePasswordScreen from '@/screens/main/ChangePasswordScreen';

// Create navigators
const Tab = createBottomTabNavigator<MainTabParamList>();
const HomeStack = createStackNavigator<HomeStackParamList>();
const BookingsStack = createStackNavigator<BookingsStackParamList>();
const ProfileStack = createStackNavigator<ProfileStackParamList>();

// Stack screen options
const stackScreenOptions = {
    headerStyle: {
        backgroundColor: colors.primary,
    },
    headerTintColor: colors.textWhite,
    headerTitleStyle: {
        fontWeight: '600' as const,
    },
    headerBackTitleVisible: false,
};

// Tab icon component
const TabIcon: React.FC<{ name: string; focused: boolean }> = ({ name, focused }) => {
    const getIcon = () => {
        switch (name) {
            case 'home':
                return '🏠';
            case 'bookings':
                return '📋';
            case 'profile':
                return '👤';
            default:
                return '•';
        }
    };

    return (
        <View style={styles.iconContainer}>
            <Text style={[styles.icon, focused && styles.iconFocused]}>{getIcon()}</Text>
        </View>
    );
};

// Home Stack Navigator
const HomeStackNavigator: React.FC = () => {
    return (
        <HomeStack.Navigator screenOptions={stackScreenOptions}>
            <HomeStack.Screen
                name="Home"
                component={HomeScreen}
                options={{ title: 'Jetty Ferry' }}
            />
            <HomeStack.Screen
                name="Booking"
                component={BookingScreen}
                options={{ title: 'Book Ticket' }}
            />
        </HomeStack.Navigator>
    );
};

// Bookings Stack Navigator
const BookingsStackNavigator: React.FC = () => {
    return (
        <BookingsStack.Navigator screenOptions={stackScreenOptions}>
            <BookingsStack.Screen
                name="BookingsList"
                component={BookingsListScreen}
                options={{ title: 'My Bookings' }}
            />
            <BookingsStack.Screen
                name="BookingDetail"
                component={BookingDetailScreen}
                options={{ title: 'Booking Details' }}
            />
        </BookingsStack.Navigator>
    );
};

// Profile Stack Navigator
const ProfileStackNavigator: React.FC = () => {
    return (
        <ProfileStack.Navigator screenOptions={stackScreenOptions}>
            <ProfileStack.Screen
                name="Profile"
                component={ProfileScreen}
                options={{ title: 'My Profile' }}
            />
            <ProfileStack.Screen
                name="EditProfile"
                component={EditProfileScreen}
                options={{ title: 'Edit Profile' }}
            />
            <ProfileStack.Screen
                name="ChangePassword"
                component={ChangePasswordScreen}
                options={{ title: 'Change Password' }}
            />
        </ProfileStack.Navigator>
    );
};

// Main Tab Navigator
export const MainNavigator: React.FC = () => {
    return (
        <Tab.Navigator
            screenOptions={{
                headerShown: false,
                tabBarActiveTintColor: colors.primary,
                tabBarInactiveTintColor: colors.textSecondary,
                tabBarStyle: {
                    backgroundColor: colors.cardBackground,
                    borderTopColor: colors.border,
                    paddingBottom: 5,
                    paddingTop: 5,
                    height: 60,
                },
                tabBarLabelStyle: {
                    fontSize: typography.fontSize.xs,
                    fontWeight: '500',
                },
            }}
        >
            <Tab.Screen
                name="HomeTab"
                component={HomeStackNavigator}
                options={{
                    tabBarLabel: 'Home',
                    tabBarIcon: ({ focused }) => <TabIcon name="home" focused={focused} />,
                }}
            />
            <Tab.Screen
                name="BookingsTab"
                component={BookingsStackNavigator}
                options={{
                    tabBarLabel: 'Bookings',
                    tabBarIcon: ({ focused }) => <TabIcon name="bookings" focused={focused} />,
                }}
            />
            <Tab.Screen
                name="ProfileTab"
                component={ProfileStackNavigator}
                options={{
                    tabBarLabel: 'Profile',
                    tabBarIcon: ({ focused }) => <TabIcon name="profile" focused={focused} />,
                }}
            />
        </Tab.Navigator>
    );
};

const styles = StyleSheet.create({
    iconContainer: {
        alignItems: 'center',
        justifyContent: 'center',
    },
    icon: {
        fontSize: 20,
        opacity: 0.6,
    },
    iconFocused: {
        opacity: 1,
    },
});

export default MainNavigator;
