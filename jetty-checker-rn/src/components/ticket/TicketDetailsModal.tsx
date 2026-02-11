// TicketDetailsModal Component - Shows verification result
import React from 'react';
import {
    Modal,
    View,
    Text,
    StyleSheet,
    ScrollView,
    TouchableOpacity,
} from 'react-native';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing, borderRadius } from '../../theme/spacing';
import { Ticket } from '../../types/models';
import { VerificationResult } from '../../services/verificationService';
import { VerificationBadge } from './VerificationBadge';
import { TicketDetailRow } from './TicketDetailRow';
import { Button } from '../common/Button';

interface TicketDetailsModalProps {
    visible: boolean;
    result: VerificationResult | null;
    onClose: () => void;
}

export const TicketDetailsModal: React.FC<TicketDetailsModalProps> = ({
    visible,
    result,
    onClose,
}) => {
    if (!result) return null;

    const { success, ticket, alreadyVerified, message, verifiedBy, verifiedAt } = result;

    const getBadgeStatus = () => {
        if (success) return 'success';
        if (alreadyVerified) return 'already_verified';
        return 'error';
    };

    const formatDate = (dateString?: string) => {
        if (!dateString) return '-';
        try {
            const date = new Date(dateString);
            return date.toLocaleString('en-IN', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        } catch {
            return dateString;
        }
    };

    return (
        <Modal
            visible={visible}
            transparent={true}
            animationType="slide"
            onRequestClose={onClose}
        >
            <View style={styles.overlay}>
                <View style={styles.modal}>
                    {/* Header */}
                    <View style={styles.header}>
                        <Text style={styles.headerTitle}>
                            {success ? 'Verification Successful' :
                                alreadyVerified ? 'Already Verified' : 'Verification Failed'}
                        </Text>
                    </View>

                    {/* Badge */}
                    <View style={styles.badgeContainer}>
                        <VerificationBadge status={getBadgeStatus()} message={message} />
                    </View>

                    {/* Ticket Details */}
                    {ticket && (
                        <ScrollView style={styles.detailsContainer} showsVerticalScrollIndicator={false}>
                            <TicketDetailRow
                                label="Ticket No"
                                value={ticket.ticket_number || ticket.ticket_no || `#${ticket.id}`}
                            />
                            <TicketDetailRow
                                label="Route"
                                value={`${(ticket as any).from_branch || ticket.branch?.branch_name || '-'} → ${(ticket as any).to_branch || '-'}`}
                            />
                            <TicketDetailRow
                                label="Ferry"
                                value={(ticket as any).ferry_boat || ticket.ferryBoat?.name}
                            />
                            <TicketDetailRow
                                label="Customer"
                                value={ticket.customer_name || 'Walk-in'}
                            />
                            <TicketDetailRow
                                label="Payment"
                                value={ticket.payment_mode || 'Cash'}
                            />
                            <TicketDetailRow
                                label="Amount"
                                value={(ticket as any).net_amount ? `₹${(ticket as any).net_amount}` : (ticket.total_amount ? `₹${ticket.total_amount}` : '-')}
                            />
                            <TicketDetailRow
                                label="Ferry Time"
                                value={(ticket as any).ferry_time || '-'}
                            />

                            {/* Show previous verification info if already verified */}
                            {alreadyVerified && (
                                <>
                                    <View style={styles.divider} />
                                    <Text style={styles.sectionTitle}>Previous Verification</Text>
                                    <TicketDetailRow
                                        label="Verified By"
                                        value={verifiedBy || ticket.verified_by}
                                    />
                                    <TicketDetailRow
                                        label="Verified At"
                                        value={formatDate(verifiedAt || ticket.verified_at)}
                                    />
                                </>
                            )}

                            {/* Show items if available */}
                            {ticket.lines && ticket.lines.length > 0 && (
                                <>
                                    <View style={styles.divider} />
                                    <Text style={styles.sectionTitle}>Items</Text>
                                    {ticket.lines.map((item, index) => (
                                        <View key={index} style={styles.itemRow}>
                                            <Text style={styles.itemName}>
                                                {item.item_name} x{item.qty}
                                            </Text>
                                            <Text style={styles.itemAmount}>₹{item.amount}</Text>
                                        </View>
                                    ))}
                                </>
                            )}
                        </ScrollView>
                    )}

                    {/* Footer Buttons */}
                    <View style={styles.footer}>
                        <Button
                            text="📷 Scan Next Ticket"
                            onPress={onClose}
                            variant="primary"
                            fullWidth={true}
                        />
                    </View>
                </View>
            </View>
        </Modal>
    );
};

const styles = StyleSheet.create({
    overlay: {
        flex: 1,
        backgroundColor: 'rgba(0, 0, 0, 0.5)',
        justifyContent: 'flex-end',
    },
    modal: {
        backgroundColor: colors.cardBackground,
        borderTopLeftRadius: borderRadius.xl,
        borderTopRightRadius: borderRadius.xl,
        maxHeight: '85%',
    },
    header: {
        padding: spacing.lg,
        borderBottomWidth: 1,
        borderBottomColor: colors.border,
        alignItems: 'center',
    },
    headerTitle: {
        fontSize: typography.fontSize.xl,
        fontWeight: typography.fontWeight.bold,
        color: colors.textPrimary,
    },
    badgeContainer: {
        padding: spacing.lg,
    },
    detailsContainer: {
        paddingHorizontal: spacing.lg,
        maxHeight: 400,
    },
    divider: {
        height: 1,
        backgroundColor: colors.border,
        marginVertical: spacing.md,
    },
    sectionTitle: {
        fontSize: typography.fontSize.md,
        fontWeight: typography.fontWeight.semibold,
        color: colors.textPrimary,
        marginBottom: spacing.sm,
    },
    itemRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        paddingVertical: spacing.xs,
    },
    itemName: {
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
    },
    itemAmount: {
        fontSize: typography.fontSize.sm,
        fontWeight: typography.fontWeight.medium,
        color: colors.textPrimary,
    },
    footer: {
        padding: spacing.lg,
        borderTopWidth: 1,
        borderTopColor: colors.border,
    },
});

export default TicketDetailsModal;
