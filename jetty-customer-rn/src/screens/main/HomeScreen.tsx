// Home Screen following the migration guide
import React, { useEffect } from 'react';
import {
    View,
    Text,
    StyleSheet,
    ScrollView,
    RefreshControl,
    TouchableOpacity,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { HomeScreenNavigationProp } from '@/types/navigation';
import { colors, typography, spacing } from '@/theme';
import { Card, Avatar, Badge } from '@/components/common';
import { useAppSelector, useAppDispatch } from '@/store';
import { fetchBookings } from '@/store/slices/bookingSlice';
import { format, isValid, parseISO } from 'date-fns';

// Safe date formatting helper
const formatDateSafe = (dateString: string | null | undefined, formatStr: string = 'dd MMM yyyy'): string => {
    if (!dateString) return 'N/A';
    try {
        // Try parsing as ISO string first
        const date = typeof dateString === 'string' ? parseISO(dateString) : new Date(dateString);
        if (!isValid(date)) return 'N/A';
        return format(date, formatStr);
    } catch {
        return 'N/A';
    }
};

export const HomeScreen: React.FC = () => {
    const navigation = useNavigation<HomeScreenNavigationProp>();
    const dispatch = useAppDispatch();
    const { customer } = useAppSelector((state) => state.auth);
    const { bookings, isLoading } = useAppSelector((state) => state.booking);

    useEffect(() => {
        dispatch(fetchBookings(1));
    }, [dispatch]);

    const onRefresh = () => {
        dispatch(fetchBookings(1));
    };

    // Get upcoming bookings (confirmed, not verified)
    const upcomingBookings = bookings
        .filter((b) => b.status === 'confirmed' && !b.verifiedAt)
        .slice(0, 3);

    const getStatusBadgeVariant = (status: string) => {
        switch (status) {
            case 'confirmed':
                return 'success';
            case 'pending':
                return 'warning';
            case 'cancelled':
                return 'error';
            default:
                return 'info';
        }
    };

    return (
        <ScrollView
            style={styles.container}
            contentContainerStyle={styles.content}
            refreshControl={
                <RefreshControl
                    refreshing={isLoading}
                    onRefresh={onRefresh}
                    tintColor={colors.primary}
                />
            }
        >
            {/* Welcome Section */}
            <View style={styles.welcomeSection}>
                <View style={styles.welcomeText}>
                    <Text style={styles.greeting}>Welcome back,</Text>
                    <Text style={styles.userName}>
                        {customer?.firstName || 'Guest'}!
                    </Text>
                </View>
                <Avatar
                    uri={customer?.profileImage}
                    name={`${customer?.firstName || ''} ${customer?.lastName || ''}`}
                    size={50}
                />
            </View>

            {/* Action Cards */}
            <View style={styles.actionCards}>
                <Card
                    style={styles.actionCard}
                    onPress={() => navigation.navigate('Booking')}
                >
                    <Text style={styles.actionIcon}>🎫</Text>
                    <Text style={styles.actionTitle}>Book New Ticket</Text>
                    <Text style={styles.actionSubtitle}>Book your ferry journey</Text>
                </Card>

                <Card
                    style={styles.actionCard}
                    onPress={() => navigation.navigate('BookingsTab')}
                >
                    <Text style={styles.actionIcon}>📋</Text>
                    <Text style={styles.actionTitle}>View Bookings</Text>
                    <Text style={styles.actionSubtitle}>Check your trips</Text>
                </Card>
            </View>

            {/* Upcoming Trips Section */}
            <View style={styles.section}>
                <View style={styles.sectionHeader}>
                    <Text style={styles.sectionTitle}>Upcoming Trips</Text>
                    {bookings.length > 0 && (
                        <TouchableOpacity onPress={() => navigation.navigate('BookingsTab')}>
                            <Text style={styles.viewAll}>View All</Text>
                        </TouchableOpacity>
                    )}
                </View>

                {upcomingBookings.length > 0 ? (
                    upcomingBookings.map((booking) => (
                        <Card
                            key={booking.id}
                            style={styles.bookingCard}
                            onPress={() =>
                                navigation.navigate('BookingsTab', {
                                    screen: 'BookingDetail',
                                    params: { bookingId: booking.id },
                                } as never)
                            }
                        >
                            <View style={styles.bookingHeader}>
                                <View style={styles.routeInfo}>
                                    <Text style={styles.routeText}>
                                        {booking.fromBranch} → {booking.toBranch || 'N/A'}
                                    </Text>
                                    <Text style={styles.ferryText}>{booking.ferryBoat}</Text>
                                </View>
                                <Badge
                                    text={booking.status}
                                    variant={getStatusBadgeVariant(booking.status)}
                                />
                            </View>
                            <View style={styles.bookingDetails}>
                                <Text style={styles.dateText}>
                                    {formatDateSafe(booking.createdAt)} • {booking.ferryTime || 'N/A'}
                                </Text>
                                <Text style={styles.amountText}>₹{booking.totalAmount}</Text>
                            </View>
                        </Card>
                    ))
                ) : (
                    <Card style={styles.emptyCard}>
                        <Text style={styles.emptyIcon}>🚢</Text>
                        <Text style={styles.emptyText}>No upcoming trips</Text>
                        <Text style={styles.emptySubtext}>Book your first ferry journey!</Text>
                    </Card>
                )}
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
    welcomeSection: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: spacing.xl,
    },
    welcomeText: {
        flex: 1,
    },
    greeting: {
        fontSize: typography.fontSize.base,
        color: colors.textSecondary,
    },
    userName: {
        fontSize: typography.fontSize['2xl'],
        fontWeight: typography.fontWeight.bold,
        color: colors.textPrimary,
    },
    actionCards: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        marginBottom: spacing.xl,
    },
    actionCard: {
        width: '48%',
        alignItems: 'center',
        paddingVertical: spacing.xl,
    },
    actionIcon: {
        fontSize: 32,
        marginBottom: spacing.sm,
    },
    actionTitle: {
        fontSize: typography.fontSize.base,
        fontWeight: typography.fontWeight.semibold,
        color: colors.textPrimary,
        marginBottom: spacing.xs,
        textAlign: 'center',
    },
    actionSubtitle: {
        fontSize: typography.fontSize.xs,
        color: colors.textSecondary,
        textAlign: 'center',
    },
    section: {
        marginBottom: spacing.xl,
    },
    sectionHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: spacing.md,
    },
    sectionTitle: {
        fontSize: typography.fontSize.lg,
        fontWeight: typography.fontWeight.semibold,
        color: colors.textPrimary,
    },
    viewAll: {
        fontSize: typography.fontSize.sm,
        color: colors.primary,
        fontWeight: typography.fontWeight.medium,
    },
    bookingCard: {
        marginBottom: spacing.md,
    },
    bookingHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'flex-start',
        marginBottom: spacing.sm,
    },
    routeInfo: {
        flex: 1,
    },
    routeText: {
        fontSize: typography.fontSize.base,
        fontWeight: typography.fontWeight.semibold,
        color: colors.textPrimary,
    },
    ferryText: {
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
        marginTop: spacing.xs,
    },
    bookingDetails: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
    },
    dateText: {
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
    },
    amountText: {
        fontSize: typography.fontSize.base,
        fontWeight: typography.fontWeight.bold,
        color: colors.primary,
    },
    emptyCard: {
        alignItems: 'center',
        paddingVertical: spacing['2xl'],
    },
    emptyIcon: {
        fontSize: 48,
        marginBottom: spacing.md,
    },
    emptyText: {
        fontSize: typography.fontSize.lg,
        fontWeight: typography.fontWeight.medium,
        color: colors.textSecondary,
        marginBottom: spacing.xs,
    },
    emptySubtext: {
        fontSize: typography.fontSize.sm,
        color: colors.textHint,
    },
});

export default HomeScreen;
