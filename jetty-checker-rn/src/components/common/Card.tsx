// Card Component
import React from 'react';
import { View, StyleSheet, ViewStyle } from 'react-native';
import { colors } from '../../theme/colors';
import { spacing, borderRadius, shadows } from '../../theme/spacing';

interface CardProps {
    children: React.ReactNode;
    style?: ViewStyle;
    noPadding?: boolean;
}

export const Card: React.FC<CardProps> = ({ children, style, noPadding = false }) => {
    return (
        <View style={[styles.card, !noPadding && styles.padding, style]}>
            {children}
        </View>
    );
};

const styles = StyleSheet.create({
    card: {
        backgroundColor: colors.cardBackground,
        borderRadius: borderRadius.lg,
        ...shadows.md,
    },
    padding: {
        padding: spacing.lg,
    },
});

export default Card;
