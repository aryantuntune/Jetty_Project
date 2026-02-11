// Booking Detail Screen following the migration guide
import React, { useEffect } from 'react';
import {
    View,
    Text,
    StyleSheet,
    ScrollView,
    Alert,
    Share,
} from 'react-native';
import { useRoute } from '@react-navigation/native';
import { BookingDetailScreenRouteProp } from '@/types/navigation';
import { colors, typography, spacing } from '@/theme';
import { Card, Badge, Button, Loading } from '@/components/common';
import { useAppSelector, useAppDispatch } from '@/store';
import { fetchBookingDetail } from '@/store/slices/bookingSlice';
import { format, isValid, parseISO } from 'date-fns';
import QRCode from 'react-native-qrcode-svg';

// Safe date formatting helper
const formatDateSafe = (dateString: string | null | undefined, formatStr: string = 'dd MMM yyyy'): string => {
    if (!dateString) return 'N/A';
    try {
        const date = typeof dateString === 'string' ? parseISO(dateString) : new Date(dateString);
        if (!isValid(date)) return 'N/A';
        return format(date, formatStr);
    } catch {
        return 'N/A';
    }
};

// Convert 24-hour time to 12-hour format (e.g., "14:00" -> "2:00 PM")
const formatTime12Hour = (time24: string): string => {
    if (!time24) return '';
    const [hourStr, minute] = time24.split(':');
    let hour = parseInt(hourStr, 10);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    hour = hour % 12 || 12;
    return `${hour}:${minute} ${ampm}`;
};

export const BookingDetailScreen: React.FC = () => {
    const route = useRoute<BookingDetailScreenRouteProp>();
    const dispatch = useAppDispatch();
    const { currentBooking, isLoading } = useAppSelector((state) => state.booking);

    const { bookingId } = route.params;

    useEffect(() => {
        dispatch(fetchBookingDetail(bookingId));
    }, [dispatch, bookingId]);

    const handleShare = async () => {
        if (!currentBooking) return;

        try {
            await Share.share({
                message: `Jetty Ferry Booking\n\nRoute: ${currentBooking.fromBranch} → ${currentBooking.toBranch}\nFerry: ${currentBooking.ferryBoat}\nTime: ${formatTime12Hour(currentBooking.ferryTime)}\nAmount: ₹${currentBooking.totalAmount}\nTicket: ${currentBooking.ticket?.ticketNo || 'N/A'}`,
            });
        } catch {
            // Share cancelled
        }
    };

    const handleDownloadPDF = async () => {
        if (!currentBooking) return;

        try {
            // Import Linking to open URL
            const { Linking, Alert } = await import('react-native');
            const { bookingService } = await import('@/services');

            // Get PDF URL
            const pdfUrl = await bookingService.downloadTicketPDF(currentBooking.id);

            // Open in browser to download
            const supported = await Linking.canOpenURL(pdfUrl);
            if (supported) {
                await Linking.openURL(pdfUrl);
            } else {
                Alert.alert('Error', 'Cannot open PDF. Please try again.');
            }
        } catch (error) {
            const { Alert } = await import('react-native');
            Alert.alert('Error', 'Failed to download ticket. Please try again.');
            console.error('[BookingDetail] PDF download error:', error);
        }
    };

    const getStatusBadgeVariant = (status: string, verifiedAt?: string) => {
        if (verifiedAt) return 'success';
        switch (status) {
            case 'confirmed':
                return 'info';
            case 'pending':
                return 'warning';
            case 'cancelled':
                return 'error';
            default:
                return 'info';
        }
    };

    if (isLoading || !currentBooking) {
        return <Loading message="Loading booking..." />;
    }

    return (
        <ScrollView style={styles.container} contentContainerStyle={styles.content}>
            {/* Ticket Card */}
            <Card style={styles.ticketCard}>
                {/* Header */}
                <View style={styles.ticketHeader}>
                    <Text style={styles.ticketNumber}>
                        #{currentBooking.ticket?.ticketNo || `B-${currentBooking.id}`}
                    </Text>
                    <Badge
                        text={currentBooking.verifiedAt ? 'Verified' : currentBooking.status}
                        variant={getStatusBadgeVariant(currentBooking.status, currentBooking.verifiedAt)}
                    />
                </View>

                {/* Route */}
                <View style={styles.routeSection}>
                    <View style={styles.routePoint}>
                        <Text style={styles.routeLabel}>FROM</Text>
                        <Text style={styles.routeValue}>{currentBooking.fromBranch}</Text>
                    </View>
                    <Text style={styles.routeArrow}>→</Text>
                    <View style={styles.routePoint}>
                        <Text style={styles.routeLabel}>TO</Text>
                        <Text style={styles.routeValue}>{currentBooking.toBranch || 'N/A'}</Text>
                    </View>
                </View>

                {/* Ferry & Time */}
                <View style={styles.infoRow}>
                    <View style={styles.infoItem}>
                        <Text style={styles.infoLabel}>Ferry</Text>
                        <Text style={styles.infoValue}>{currentBooking.ferryBoat}</Text>
                    </View>
                    <View style={styles.infoItem}>
                        <Text style={styles.infoLabel}>Time</Text>
                        <Text style={styles.infoValue}>{formatTime12Hour(currentBooking.ferryTime)}</Text>
                    </View>
                </View>

                <View style={styles.infoRow}>
                    <View style={styles.infoItem}>
                        <Text style={styles.infoLabel}>Date</Text>
                        <Text style={styles.infoValue}>
                            {formatDateSafe(currentBooking.createdAt)}
                        </Text>
                    </View>
                    <View style={styles.infoItem}>
                        <Text style={styles.infoLabel}>Total</Text>
                        <Text style={[styles.infoValue, styles.totalAmount]}>
                            ₹{currentBooking.totalAmount}
                        </Text>
                    </View>
                </View>

                {/* Divider */}
                <View style={styles.divider} />

                {/* Items */}
                <Text style={styles.sectionTitle}>Booking Items</Text>
                <View style={styles.itemsTable}>
                    <View style={styles.tableHeader}>
                        <Text style={[styles.tableHeaderText, styles.itemCol]}>Item</Text>
                        <Text style={[styles.tableHeaderText, styles.qtyCol]}>Qty</Text>
                        <Text style={[styles.tableHeaderText, styles.amountCol]}>Amount</Text>
                    </View>
                    {(currentBooking.items || []).map((item, index) => (
                        <View key={index} style={styles.tableRow}>
                            <View style={styles.itemCol}>
                                <Text style={styles.itemName}>{item.itemName || item.item_name || 'Item'}</Text>
                                {item.vehicleNo && (
                                    <Text style={styles.vehicleNo}>{item.vehicleNo}</Text>
                                )}
                            </View>
                            <Text style={[styles.tableCell, styles.qtyCol]}>{item.qty || item.quantity || 1}</Text>
                            <Text style={[styles.tableCell, styles.amountCol]}>
                                ₹{item.amount || item.total || (item.rate * (item.qty || item.quantity || 1))}
                            </Text>
                        </View>
                    ))}
                </View>

                {/* Divider */}
                <View style={styles.divider} />

                {/* QR Code */}
                <View style={styles.qrSection}>
                    <QRCode
                        value={currentBooking.ticket?.id.toString() || currentBooking.id.toString()}
                        size={150}
                    />
                    <Text style={styles.qrText}>Show this code at the boarding gate</Text>
                </View>

                {/* Verified Status */}
                {currentBooking.verifiedAt && (
                    <View style={styles.verifiedBanner}>
                        <Text style={styles.verifiedIcon}>✓</Text>
                        <View>
                            <Text style={styles.verifiedText}>Ticket Verified</Text>
                            <Text style={styles.verifiedDate}>
                                {formatDateSafe(currentBooking.verifiedAt, 'dd MMM yyyy, HH:mm')}
                            </Text>
                        </View>
                    </View>
                )}
            </Card>

            {/* Actions */}
            <View style={styles.actions}>
                <Button
                    text="Share"
                    onPress={handleShare}
                    variant="outline"
                    style={styles.actionButton}
                />
                <Button
                    text="Download PDF"
                    onPress={handleDownloadPDF}
                    variant="outline"
                    style={styles.actionButton}
                />
            </View>
        </ScrollView>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.background,
    },
    content: {
        padding: spacing.lg,
    },
    ticketCard: {
        marginBottom: spacing.lg,
    },
    ticketHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: spacing.lg,
    },
    ticketNumber: {
        fontSize: typography.fontSize.xl,
        fontWeight: typography.fontWeight.bold,
        color: colors.primary,
    },
    routeSection: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        backgroundColor: colors.inputBackground,
        padding: spacing.lg,
        borderRadius: 8,
        marginBottom: spacing.lg,
    },
    routePoint: {
        flex: 1,
    },
    routeLabel: {
        fontSize: typography.fontSize.xs,
        color: colors.textSecondary,
        marginBottom: spacing.xs,
    },
    routeValue: {
        fontSize: typography.fontSize.lg,
        fontWeight: typography.fontWeight.bold,
        color: colors.textPrimary,
    },
    routeArrow: {
        fontSize: typography.fontSize['2xl'],
        color: colors.primary,
        marginHorizontal: spacing.md,
    },
    infoRow: {
        flexDirection: 'row',
        marginBottom: spacing.md,
    },
    infoItem: {
        flex: 1,
    },
    infoLabel: {
        fontSize: typography.fontSize.xs,
        color: colors.textSecondary,
        marginBottom: spacing.xs,
    },
    infoValue: {
        fontSize: typography.fontSize.base,
        fontWeight: typography.fontWeight.medium,
        color: colors.textPrimary,
    },
    totalAmount: {
        color: colors.primary,
        fontWeight: typography.fontWeight.bold,
        fontSize: typography.fontSize.lg,
    },
    divider: {
        height: 1,
        backgroundColor: colors.divider,
        marginVertical: spacing.lg,
    },
    sectionTitle: {
        fontSize: typography.fontSize.base,
        fontWeight: typography.fontWeight.semibold,
        color: colors.textPrimary,
        marginBottom: spacing.md,
    },
    itemsTable: {
        marginBottom: spacing.md,
    },
    tableHeader: {
        flexDirection: 'row',
        paddingVertical: spacing.sm,
        borderBottomWidth: 1,
        borderBottomColor: colors.divider,
    },
    tableHeaderText: {
        fontSize: typography.fontSize.xs,
        fontWeight: typography.fontWeight.semibold,
        color: colors.textSecondary,
        textTransform: 'uppercase',
    },
    tableRow: {
        flexDirection: 'row',
        paddingVertical: spacing.sm,
        alignItems: 'center',
    },
    tableCell: {
        fontSize: typography.fontSize.sm,
        color: colors.textPrimary,
    },
    itemCol: {
        flex: 2,
    },
    qtyCol: {
        flex: 0.5,
        textAlign: 'center',
    },
    amountCol: {
        flex: 1,
        textAlign: 'right',
    },
    itemName: {
        fontSize: typography.fontSize.sm,
        color: colors.textPrimary,
    },
    vehicleNo: {
        fontSize: typography.fontSize.xs,
        color: colors.textSecondary,
        marginTop: 2,
    },
    qrSection: {
        alignItems: 'center',
        paddingVertical: spacing.lg,
    },
    qrText: {
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
        marginTop: spacing.md,
        textAlign: 'center',
    },
    verifiedBanner: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: '#E8F5E9',
        padding: spacing.md,
        borderRadius: 8,
        marginTop: spacing.md,
    },
    verifiedIcon: {
        fontSize: 24,
        color: colors.success,
        marginRight: spacing.md,
    },
    verifiedText: {
        fontSize: typography.fontSize.base,
        fontWeight: typography.fontWeight.semibold,
        color: colors.success,
    },
    verifiedDate: {
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
    },
    actions: {
        flexDirection: 'row',
        justifyContent: 'space-between',
    },
    actionButton: {
        flex: 1,
        marginHorizontal: spacing.xs,
    },
    cancelButton: {
        borderColor: colors.error,
    },
    cancelButtonText: {
        color: colors.error,
    },
});

export default BookingDetailScreen;
