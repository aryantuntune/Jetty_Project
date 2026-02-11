// TicketDetailRow Component - Label-value pair display
import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';

interface TicketDetailRowProps {
    label: string;
    value: string | number | undefined | null;
}

export const TicketDetailRow: React.FC<TicketDetailRowProps> = ({
    label,
    value,
}) => {
    return (
        <View style={styles.row}>
            <Text style={styles.label}>{label}</Text>
            <Text style={styles.value}>{value ?? '-'}</Text>
        </View>
    );
};

const styles = StyleSheet.create({
    row: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingVertical: spacing.sm,
        borderBottomWidth: 1,
        borderBottomColor: colors.borderLight,
    },
    label: {
        fontSize: typography.fontSize.base,
        color: colors.textSecondary,
        flex: 1,
    },
    value: {
        fontSize: typography.fontSize.base,
        fontWeight: typography.fontWeight.medium,
        color: colors.textPrimary,
        flex: 1,
        textAlign: 'right',
    },
});

export default TicketDetailRow;
