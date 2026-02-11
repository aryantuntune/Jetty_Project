// Profile Screen following the migration guide
import React from 'react';
import {
    View,
    Text,
    StyleSheet,
    ScrollView,
    TouchableOpacity,
    Alert,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { ProfileScreenNavigationProp } from '@/types/navigation';
import { colors, typography, spacing } from '@/theme';
import { Card, Avatar } from '@/components/common';
import { useAppSelector, useAppDispatch } from '@/store';
import { logout } from '@/store/slices/authSlice';
import { setTheme, setLanguage } from '@/store/slices/appSlice';

interface MenuItem {
    icon: string;
    title: string;
    subtitle?: string;
    onPress: () => void;
    destructive?: boolean;
}

export const ProfileScreen: React.FC = () => {
    const navigation = useNavigation<ProfileScreenNavigationProp>();
    const dispatch = useAppDispatch();
    const { customer } = useAppSelector((state) => state.auth);
    const { theme, language } = useAppSelector((state) => state.app);

    const handleLogout = () => {
        Alert.alert(
            'Logout',
            'Are you sure you want to logout?',
            [
                { text: 'Cancel', style: 'cancel' },
                {
                    text: 'Logout',
                    style: 'destructive',
                    onPress: () => dispatch(logout()),
                },
            ]
        );
    };

    const toggleTheme = () => {
        dispatch(setTheme(theme === 'light' ? 'dark' : 'light'));
    };

    const toggleLanguage = () => {
        dispatch(setLanguage(language === 'en' ? 'mr' : 'en'));
    };

    const menuItems: MenuItem[] = [
        {
            icon: '✏️',
            title: 'Edit Profile',
            subtitle: 'Update your personal information',
            onPress: () => navigation.navigate('EditProfile'),
        },
        {
            icon: '🔒',
            title: 'Change Password',
            subtitle: 'Update your password',
            onPress: () => navigation.navigate('ChangePassword'),
        },
        {
            icon: '🌐',
            title: 'Language',
            subtitle: language === 'en' ? 'English' : 'मराठी',
            onPress: toggleLanguage,
        },
        {
            icon: '🎨',
            title: 'Theme',
            subtitle: theme === 'light' ? 'Light' : 'Dark',
            onPress: toggleTheme,
        },
        {
            icon: 'ℹ️',
            title: 'About Us',
            onPress: () => Alert.alert('Jetty Ferry', 'Version 1.0.0\n\nYour trusted ferry booking service.'),
        },
        {
            icon: '📄',
            title: 'Terms & Conditions',
            onPress: () => Alert.alert('Terms & Conditions', 'Please visit our website for full terms and conditions.'),
        },
        {
            icon: '🔐',
            title: 'Privacy Policy',
            onPress: () => Alert.alert('Privacy Policy', 'Please visit our website for full privacy policy.'),
        },
        {
            icon: '🚪',
            title: 'Logout',
            onPress: handleLogout,
            destructive: true,
        },
    ];

    const renderMenuItem = (item: MenuItem, index: number) => (
        <TouchableOpacity
            key={index}
            style={styles.menuItem}
            onPress={item.onPress}
        >
            <Text style={styles.menuIcon}>{item.icon}</Text>
            <View style={styles.menuContent}>
                <Text style={[
                    styles.menuTitle,
                    item.destructive && styles.menuTitleDestructive,
                ]}>
                    {item.title}
                </Text>
                {item.subtitle && (
                    <Text style={styles.menuSubtitle}>{item.subtitle}</Text>
                )}
            </View>
            <Text style={styles.menuArrow}>›</Text>
        </TouchableOpacity>
    );

    return (
        <ScrollView style={styles.container}>
            {/* Profile Header */}
            <Card style={styles.profileCard}>
                <Avatar
                    uri={customer?.profileImage}
                    name={`${customer?.firstName || ''} ${customer?.lastName || ''}`}
                    size={80}
                />
                <View style={styles.profileInfo}>
                    <Text style={styles.profileName}>
                        {customer?.firstName} {customer?.lastName}
                    </Text>
                    <Text style={styles.profileEmail}>{customer?.email}</Text>
                    <Text style={styles.profileMobile}>{customer?.mobile}</Text>
                </View>
            </Card>

            {/* Menu Items */}
            <Card style={styles.menuCard}>
                {menuItems.map(renderMenuItem)}
            </Card>
        </ScrollView>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.background,
    },
    profileCard: {
        margin: spacing.lg,
        alignItems: 'center',
        paddingVertical: spacing['2xl'],
    },
    profileInfo: {
        alignItems: 'center',
        marginTop: spacing.lg,
    },
    profileName: {
        fontSize: typography.fontSize.xl,
        fontWeight: typography.fontWeight.bold,
        color: colors.textPrimary,
        marginBottom: spacing.xs,
    },
    profileEmail: {
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
        marginBottom: spacing.xs,
    },
    profileMobile: {
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
    },
    menuCard: {
        marginHorizontal: spacing.lg,
        marginBottom: spacing.lg,
        padding: 0,
        overflow: 'hidden',
    },
    menuItem: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingVertical: spacing.lg,
        paddingHorizontal: spacing.lg,
        borderBottomWidth: 1,
        borderBottomColor: colors.divider,
    },
    menuIcon: {
        fontSize: 20,
        marginRight: spacing.md,
    },
    menuContent: {
        flex: 1,
    },
    menuTitle: {
        fontSize: typography.fontSize.base,
        fontWeight: typography.fontWeight.medium,
        color: colors.textPrimary,
    },
    menuTitleDestructive: {
        color: colors.error,
    },
    menuSubtitle: {
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
        marginTop: 2,
    },
    menuArrow: {
        fontSize: 20,
        color: colors.textHint,
    },
});

export default ProfileScreen;
