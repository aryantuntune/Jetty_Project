// VerificationBadge Component - Shows verification status
import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing, borderRadius } from '../../theme/spacing';

type BadgeStatus = 'success' | 'already_verified' | 'error' | 'info';

interface VerificationBadgeProps {
    status: BadgeStatus;
    message: string;
}

export const VerificationBadge: React.FC<VerificationBadgeProps> = ({
    status,
    message,
}) => {
    const getStatusColors = () => {
        switch (status) {
            case 'success':
                return { bg: colors.successLight, text: colors.success, icon: '✓' };
            case 'already_verified':
                return { bg: colors.alreadyVerifiedLight, text: colors.alreadyVerified, icon: '⟳' };
            case 'error':
                return { bg: colors.errorLight, text: colors.error, icon: '✕' };
            case 'info':
            default:
                return { bg: colors.infoLight, text: colors.info, icon: 'ⓘ' };
        }
    };

    const statusColors = getStatusColors();

    return (
        <View style={[styles.badge, { backgroundColor: statusColors.bg }]}>
            <Text style={[styles.icon, { color: statusColors.text }]}>
                {statusColors.icon}
            </Text>
            <Text style={[styles.message, { color: statusColors.text }]}>
                {message}
            </Text>
        </View>
    );
};

const styles = StyleSheet.create({
    badge: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingVertical: spacing.md,
        paddingHorizontal: spacing.lg,
        borderRadius: borderRadius.md,
    },
    icon: {
        fontSize: typography.fontSize.xl,
        fontWeight: typography.fontWeight.bold,
        marginRight: spacing.sm,
    },
    message: {
        fontSize: typography.fontSize.base,
        fontWeight: typography.fontWeight.medium,
        flex: 1,
    },
});

export default VerificationBadge;
