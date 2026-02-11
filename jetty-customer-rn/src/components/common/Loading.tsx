// Loading Component following the migration guide
import React from 'react';
import { View, ActivityIndicator, StyleSheet, Text } from 'react-native';
import { colors, typography, spacing } from '@/theme';

interface LoadingProps {
    message?: string;
    overlay?: boolean;
}

export const Loading: React.FC<LoadingProps> = ({ message, overlay = true }) => {
    return (
        <View style={[styles.container, overlay ? styles.overlay : null]}>
            <View style={styles.content}>
                <ActivityIndicator size="large" color={colors.primary} />
                {message && <Text style={styles.message}>{message}</Text>}
            </View>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
    },
    overlay: {
        ...StyleSheet.absoluteFillObject,
        backgroundColor: 'rgba(255, 255, 255, 0.9)',
        zIndex: 999,
    },
    content: {
        alignItems: 'center',
        padding: spacing.xl,
    },
    message: {
        marginTop: spacing.md,
        fontSize: typography.fontSize.base,
        color: colors.textSecondary,
    },
});

export default Loading;
