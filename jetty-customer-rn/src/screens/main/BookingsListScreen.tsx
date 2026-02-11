// Bookings List Screen following the migration guide
import React, { useEffect, useState } from 'react';
import {
    View,
    Text,
    StyleSheet,
    FlatList,
    RefreshControl,
    TouchableOpacity,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { BookingsListScreenNavigationProp } from '@/types/navigation';
import { colors, typography, spacing } from '@/theme';
import { Card, Badge, Loading } from '@/components/common';
import { useAppSelector, useAppDispatch } from '@/store';
import { fetchBookings } from '@/store/slices/bookingSlice';
import { Booking } from '@/types/models';
import { format, isValid, parseISO } from 'date-fns';

type FilterType = 'all' | 'upcoming' | 'completed' | 'cancelled';

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

export const BookingsListScreen: React.FC = () => {
    const navigation = useNavigation<BookingsListScreenNavigationProp>();
    const dispatch = useAppDispatch();
    const { bookings, isLoading, currentPage, lastPage } = useAppSelector(
        (state) => state.booking
    );

    const [filter, setFilter] = useState<FilterType>('all');
    const [refreshing, setRefreshing] = useState(false);

    useEffect(() => {
        dispatch(fetchBookings(1));
    }, [dispatch]);

    const onRefresh = async () => {
        setRefreshing(true);
        await dispatch(fetchBookings(1));
        setRefreshing(false);
    };

    const loadMore = () => {
        if (currentPage < lastPage && !isLoading) {
            dispatch(fetchBookings(currentPage + 1));
        }
    };

    const getFilteredBookings = (): Booking[] => {
        switch (filter) {
            case 'upcoming':
                return bookings.filter((b) => b.status === 'confirmed' && !b.verifiedAt);
            case 'completed':
                return bookings.filter((b) => b.status === 'completed' || b.verifiedAt);
            case 'cancelled':
                return bookings.filter((b) => b.status === 'cancelled');
            default:
                return bookings;
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

    const getStatusText = (status: string, verifiedAt?: string) => {
        if (verifiedAt) return 'Verified';
        return status;
    };

    const filteredBookings = getFilteredBookings();

    const renderFilterTab = (type: FilterType, label: string) => (
        <TouchableOpacity
            style={[
                styles.filterTab,
                filter === type && styles.filterTabActive,
            ]}
            onPress={() => setFilter(type)}
        >
            <Text
                style={[
                    styles.filterTabText,
                    filter === type && styles.filterTabTextActive,
                ]}
            >
                {label}
            </Text>
        </TouchableOpacity>
    );

    const renderBookingItem = ({ item }: { item: Booking }) => (
        <Card
            style={styles.bookingCard}
            onPress={() => navigation.navigate('BookingDetail', { bookingId: item.id })}
        >
            <View style={styles.bookingHeader}>
                <View style={styles.routeInfo}>
                    <Text style={styles.routeText}>
                        {item.fromBranch} → {item.toBranch || 'N/A'}
                    </Text>
                </View>
                <Badge
                    text={getStatusText(item.status, item.verifiedAt)}
                    variant={getStatusBadgeVariant(item.status, item.verifiedAt)}
                />
            </View>

            <View style={styles.bookingDetails}>
                <View>
                    <Text style={styles.ferryText}>{item.ferryBoat}</Text>
                    <Text style={styles.dateText}>
                        {formatDateSafe(item.createdAt)} • {formatTime12Hour(item.ferryTime)}
                    </Text>
                </View>
                <Text style={styles.amountText}>₹{item.totalAmount}</Text>
            </View>
        </Card>
    );

    const renderEmpty = () => (
        <View style={styles.emptyContainer}>
            <Text style={styles.emptyIcon}>🚢</Text>
            <Text style={styles.emptyText}>No bookings found</Text>
            <Text style={styles.emptySubtext}>
                {filter === 'all'
                    ? 'Book your first ferry journey!'
                    : `No ${filter} bookings`}
            </Text>
        </View>
    );

    const renderFooter = () => {
        if (!isLoading || refreshing) return null;
        return (
            <View style={styles.footer}>
                <Loading overlay={false} />
            </View>
        );
    };

    return (
        <View style={styles.container}>
            {/* Filter Tabs */}
            <View style={styles.filterContainer}>
                {renderFilterTab('all', 'All')}
                {renderFilterTab('upcoming', 'Upcoming')}
                {renderFilterTab('completed', 'Completed')}
                {renderFilterTab('cancelled', 'Cancelled')}
            </View>

            {/* Bookings List */}
            <FlatList
                data={filteredBookings}
                renderItem={renderBookingItem}
                keyExtractor={(item) => item.id.toString()}
                contentContainerStyle={styles.listContent}
                refreshControl={
                    <RefreshControl
                        refreshing={refreshing}
                        onRefresh={onRefresh}
                        tintColor={colors.primary}
                    />
                }
                onEndReached={loadMore}
                onEndReachedThreshold={0.3}
                ListEmptyComponent={renderEmpty}
                ListFooterComponent={renderFooter}
            />
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.background,
    },
    filterContainer: {
        flexDirection: 'row',
        backgroundColor: colors.cardBackground,
        paddingHorizontal: spacing.md,
        paddingVertical: spacing.sm,
        borderBottomWidth: 1,
        borderBottomColor: colors.border,
    },
    filterTab: {
        paddingHorizontal: spacing.md,
        paddingVertical: spacing.sm,
        marginRight: spacing.xs,
        borderRadius: 20,
    },
    filterTabActive: {
        backgroundColor: colors.primary,
    },
    filterTabText: {
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
        fontWeight: typography.fontWeight.medium,
    },
    filterTabTextActive: {
        color: colors.textWhite,
    },
    listContent: {
        padding: spacing.lg,
        flexGrow: 1,
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
        marginRight: spacing.sm,
    },
    routeText: {
        fontSize: typography.fontSize.base,
        fontWeight: typography.fontWeight.bold,
        color: colors.textPrimary,
    },
    bookingDetails: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'flex-end',
    },
    ferryText: {
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
        marginBottom: spacing.xs,
    },
    dateText: {
        fontSize: typography.fontSize.sm,
        color: colors.textHint,
    },
    amountText: {
        fontSize: typography.fontSize.lg,
        fontWeight: typography.fontWeight.bold,
        color: colors.primary,
    },
    emptyContainer: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        paddingVertical: spacing['5xl'],
    },
    emptyIcon: {
        fontSize: 64,
        marginBottom: spacing.lg,
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
    footer: {
        paddingVertical: spacing.lg,
    },
});

export default BookingsListScreen;
