// Badge Component following the migration guide
import React from 'react';
import { View, Text, StyleSheet, ViewStyle, TextStyle } from 'react-native';
import { colors, typography, spacing } from '@/theme';

type BadgeVariant = 'success' | 'error' | 'warning' | 'info';

interface BadgeProps {
    text: string;
    variant?: BadgeVariant;
    style?: ViewStyle;
}

export const Badge: React.FC<BadgeProps> = ({
    text,
    variant = 'info',
    style,
}) => {
    const getBackgroundColor = (): string => {
        const variantColors: Record<BadgeVariant, string> = {
            success: colors.success,
            error: colors.error,
            warning: colors.warning,
            info: colors.info,
        };
        return variantColors[variant];
    };

    return (
        <View style={[styles.badge, { backgroundColor: getBackgroundColor() }, style]}>
            <Text style={styles.text}>{text}</Text>
        </View>
    );
};

const styles = StyleSheet.create({
    badge: {
        paddingHorizontal: spacing.sm,
        paddingVertical: spacing.xs,
        borderRadius: 12,
        alignSelf: 'flex-start',
    },
    text: {
        fontSize: typography.fontSize.xs,
        fontWeight: typography.fontWeight.semibold,
        color: colors.textWhite,
        textTransform: 'capitalize',
    },
});

export default Badge;
