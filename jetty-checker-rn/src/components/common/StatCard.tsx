// StatCard Component - For displaying verification count
import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing, borderRadius, shadows } from '../../theme/spacing';

interface StatCardProps {
    title: string;
    value: number | string;
    subtitle?: string;
    color?: string;
    backgroundColor?: string;
}

export const StatCard: React.FC<StatCardProps> = ({
    title,
    value,
    subtitle,
    color = colors.textWhite,
    backgroundColor = colors.success,
}) => {
    return (
        <View style={[styles.card, { backgroundColor }]}>
            <Text style={[styles.title, { color }]}>{title}</Text>
            <Text style={[styles.value, { color }]}>{value}</Text>
            {subtitle && <Text style={[styles.subtitle, { color }]}>{subtitle}</Text>}
        </View>
    );
};

const styles = StyleSheet.create({
    card: {
        padding: spacing.xl,
        borderRadius: borderRadius.lg,
        alignItems: 'center',
        ...shadows.md,
    },
    title: {
        fontSize: typography.fontSize.md,
        fontWeight: typography.fontWeight.medium,
        marginBottom: spacing.sm,
        opacity: 0.9,
    },
    value: {
        fontSize: typography.fontSize['5xl'],
        fontWeight: typography.fontWeight.bold,
        lineHeight: typography.fontSize['5xl'] * 1.2,
    },
    subtitle: {
        fontSize: typography.fontSize.base,
        marginTop: spacing.sm,
        opacity: 0.8,
    },
});

export default StatCard;
