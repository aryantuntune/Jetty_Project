// Loading Component - Full screen loading overlay
import React from 'react';
import { View, ActivityIndicator, Text, StyleSheet, Modal } from 'react-native';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';

interface LoadingProps {
    visible?: boolean;
    message?: string;
    overlay?: boolean;
}

export const Loading: React.FC<LoadingProps> = ({
    visible = true,
    message,
    overlay = false,
}) => {
    if (!visible) return null;

    const content = (
        <View style={[styles.container, overlay && styles.overlay]}>
            <View style={styles.content}>
                <ActivityIndicator size="large" color={colors.primary} />
                {message && <Text style={styles.message}>{message}</Text>}
            </View>
        </View>
    );

    if (overlay) {
        return (
            <Modal transparent={true} visible={visible} animationType="fade">
                {content}
            </Modal>
        );
    }

    return content;
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
    },
    overlay: {
        backgroundColor: 'rgba(0, 0, 0, 0.5)',
    },
    content: {
        backgroundColor: colors.cardBackground,
        padding: spacing['2xl'],
        borderRadius: 16,
        alignItems: 'center',
        minWidth: 150,
    },
    message: {
        marginTop: spacing.lg,
        fontSize: typography.fontSize.base,
        color: colors.textPrimary,
        textAlign: 'center',
    },
});

export default Loading;
