// HomeScreen - Main dashboard for checker (Super Admin Dashboard Aesthetic)
import React, { useState, useCallback } from 'react';
import {
    View,
    Text,
    StyleSheet,
    ScrollView,
    RefreshControl,
    Alert,
    TextInput,
    Modal,
    TouchableOpacity,
    SafeAreaView,
    Image,
} from 'react-native';
import { useFocusEffect, useNavigation } from '@react-navigation/native';
import { colors } from '../theme/colors';
import { typography } from '../theme/typography';
import { spacing, borderRadius, shadows } from '../theme/spacing';
import { useAppDispatch, useAppSelector } from '../store';
import { logout } from '../store/slices/authSlice';
import { loadVerificationCount, verifyTicket, clearVerificationResult } from '../store/slices/verificationSlice';
import { Button, Loading } from '../components/common';
import { TicketDetailsModal } from '../components/ticket';
import { MainNavigationProp } from '../types/navigation';

// Helper to format today's date
const formatDate = () => {
    const today = new Date();
    const options: Intl.DateTimeFormatOptions = {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    };
    return today.toLocaleDateString('en-IN', options);
};

// Helper to format time
const formatTime = (dateString: string) => {
    try {
        return new Date(dateString).toLocaleTimeString('en-IN', {
            hour: '2-digit',
            minute: '2-digit',
        });
    } catch {
        return '-';
    }
};

export const HomeScreen: React.FC = () => {
    const navigation = useNavigation<MainNavigationProp<'Home'>>();
    const dispatch = useAppDispatch();

    const { checker, isLoading: authLoading } = useAppSelector((state) => state.auth);
    const { verifiedToday, recentVerifications, isVerifying, lastVerificationResult } = useAppSelector(
        (state) => state.verification
    );

    const [refreshing, setRefreshing] = useState(false);
    const [manualEntryVisible, setManualEntryVisible] = useState(false);
    const [manualTicketId, setManualTicketId] = useState('');
    const [showResultModal, setShowResultModal] = useState(false);

    // Reload count when screen is focused
    useFocusEffect(
        useCallback(() => {
            dispatch(loadVerificationCount());
        }, [dispatch])
    );

    // Show result modal when verification completes
    React.useEffect(() => {
        if (lastVerificationResult) {
            setShowResultModal(true);
        }
    }, [lastVerificationResult]);

    const handleRefresh = async () => {
        setRefreshing(true);
        await dispatch(loadVerificationCount());
        setRefreshing(false);
    };

    const handleScanQR = () => {
        navigation.navigate('QRScanner');
    };

    const handleManualEntry = () => {
        setManualTicketId('');
        setManualEntryVisible(true);
    };

    const handleManualVerify = () => {
        const ticketId = parseInt(manualTicketId.trim(), 10);
        if (isNaN(ticketId) || ticketId <= 0) {
            Alert.alert('Invalid Input', 'Please enter a valid ticket ID (positive number)');
            return;
        }

        setManualEntryVisible(false);
        dispatch(verifyTicket(ticketId));
    };

    const handleCloseResultModal = () => {
        setShowResultModal(false);
        dispatch(clearVerificationResult());
    };

    const handleLogout = () => {
        Alert.alert(
            'Logout',
            'Are you sure you want to logout?',
            [
                { text: 'Cancel', style: 'cancel' },
                { text: 'Logout', style: 'destructive', onPress: () => dispatch(logout()) },
            ]
        );
    };

    const branchName = checker?.branch?.branch_name || 'Dabhol';

    return (
        <SafeAreaView style={styles.safeArea}>
            <View style={styles.container}>
                {/* Header */}
                <View style={styles.header}>
                    <View style={styles.headerTop}>
                        {/* Logo and Title */}
                        <View style={styles.logoContainer}>
                            <View style={styles.logoCircle}>
                                <Text style={styles.logoEmoji}>🚢</Text>
                            </View>
                            <View style={styles.headerTitleContainer}>
                                <Text style={styles.headerTitle}>Jetty Checker</Text>
                                <Text style={styles.headerSubtitle}>Ticket Verification</Text>
                            </View>
                        </View>

                        {/* Profile / Logout */}
                        <TouchableOpacity style={styles.profileButton} onPress={handleLogout}>
                            <View style={styles.profileAvatar}>
                                <Text style={styles.profileInitial}>
                                    {(checker?.name || 'C')[0].toUpperCase()}
                                </Text>
                            </View>
                        </TouchableOpacity>
                    </View>

                    {/* User Info Bar */}
                    <View style={styles.userBar}>
                        <View style={styles.userInfo}>
                            <Text style={styles.userName}>{checker?.name || 'Checker'}</Text>
                        </View>

                        {/* Location Chip */}
                        <View style={styles.locationChip}>
                            <Text style={styles.locationIcon}>📍</Text>
                            <Text style={styles.locationText}>{branchName}</Text>
                        </View>
                    </View>
                </View>

                <ScrollView
                    style={styles.content}
                    contentContainerStyle={styles.contentContainer}
                    refreshControl={
                        <RefreshControl
                            refreshing={refreshing}
                            onRefresh={handleRefresh}
                            colors={[colors.primary]}
                        />
                    }
                >
                    {/* Date Badge */}
                    <View style={styles.dateBadge}>
                        <Text style={styles.dateText}>{formatDate()}</Text>
                    </View>

                    {/* Verified Today Metric Card */}
                    <View style={styles.metricCard}>
                        <View style={styles.metricHeader}>
                            <Text style={styles.metricLabel}>Verified Today</Text>
                            <View style={styles.metricBadge}>
                                <Text style={styles.metricBadgeText}>Live</Text>
                            </View>
                        </View>
                        <Text style={styles.metricValue}>{verifiedToday}</Text>
                        <Text style={styles.metricSubtext}>tickets verified</Text>
                    </View>

                    {/* Action Buttons */}
                    <View style={styles.actionsContainer}>
                        {/* Primary: Scan QR - Solid Indigo */}
                        <TouchableOpacity
                            style={styles.scanButton}
                            onPress={handleScanQR}
                            activeOpacity={0.8}
                        >
                            <Text style={styles.scanButtonIcon}>📷</Text>
                            <Text style={styles.scanButtonText}>Scan QR Code</Text>
                        </TouchableOpacity>

                        {/* Secondary: Manual Entry - Outlined */}
                        <TouchableOpacity
                            style={styles.manualButton}
                            onPress={handleManualEntry}
                            activeOpacity={0.8}
                        >
                            <Text style={styles.manualButtonIcon}>⌨️</Text>
                            <Text style={styles.manualButtonText}>Manual Entry</Text>
                        </TouchableOpacity>
                    </View>

                    {/* Recent Verifications Section */}
                    <View style={styles.recentSection}>
                        <View style={styles.recentHeader}>
                            <Text style={styles.recentTitle}>Recent Verifications</Text>
                            <Text style={styles.recentSubtitle}>Last 5 tickets</Text>
                        </View>

                        <View style={styles.recentList}>
                            {(!recentVerifications || recentVerifications.length === 0) ? (
                                <View style={styles.emptyState}>
                                    <Text style={styles.emptyIcon}>📋</Text>
                                    <Text style={styles.emptyText}>No verifications yet today</Text>
                                    <Text style={styles.emptySubtext}>Scan a ticket to get started</Text>
                                </View>
                            ) : (
                                recentVerifications.slice(0, 5).map((item, index) => (
                                    <View key={index} style={styles.recentItem}>
                                        <View style={styles.recentItemLeft}>
                                            <View style={[
                                                styles.statusDot,
                                                { backgroundColor: item.success ? colors.success : colors.error }
                                            ]} />
                                            <View>
                                                <Text style={styles.ticketNo}>
                                                    #{item.ticket?.ticket_no || item.ticket?.id || '-'}
                                                </Text>
                                                <Text style={styles.ticketRoute}>
                                                    {item.ticket?.from_branch || 'From'} → {item.ticket?.to_branch || 'To'}
                                                </Text>
                                            </View>
                                        </View>
                                        <View style={styles.recentItemRight}>
                                            <Text style={styles.ticketAmount}>
                                                ₹{item.ticket?.net_amount || 0}
                                            </Text>
                                            <Text style={styles.ticketTime}>
                                                {formatTime(item.verifiedAt || new Date().toISOString())}
                                            </Text>
                                        </View>
                                    </View>
                                ))
                            )}
                        </View>
                    </View>
                </ScrollView>

                {/* Manual Entry Modal */}
                <Modal
                    visible={manualEntryVisible}
                    transparent={true}
                    animationType="fade"
                    onRequestClose={() => setManualEntryVisible(false)}
                >
                    <View style={styles.modalOverlay}>
                        <View style={styles.modalContent}>
                            <Text style={styles.modalTitle}>Enter Ticket ID</Text>
                            <TextInput
                                style={styles.modalInput}
                                placeholder="e.g. 12345"
                                placeholderTextColor={colors.textMuted}
                                value={manualTicketId}
                                onChangeText={setManualTicketId}
                                keyboardType="number-pad"
                                autoFocus={true}
                            />
                            <View style={styles.modalButtons}>
                                <TouchableOpacity
                                    style={styles.modalCancelBtn}
                                    onPress={() => setManualEntryVisible(false)}
                                >
                                    <Text style={styles.modalCancelText}>Cancel</Text>
                                </TouchableOpacity>
                                <TouchableOpacity
                                    style={styles.modalVerifyBtn}
                                    onPress={handleManualVerify}
                                >
                                    <Text style={styles.modalVerifyText}>Verify</Text>
                                </TouchableOpacity>
                            </View>
                        </View>
                    </View>
                </Modal>

                {/* Verification Result Modal */}
                <TicketDetailsModal
                    visible={showResultModal}
                    result={lastVerificationResult}
                    onClose={handleCloseResultModal}
                />

                {/* Loading Overlay */}
                <Loading visible={isVerifying} message="Verifying ticket..." overlay={true} />
            </View>
        </SafeAreaView>
    );
};

const styles = StyleSheet.create({
    safeArea: {
        flex: 1,
        backgroundColor: colors.primary,
    },
    container: {
        flex: 1,
        backgroundColor: colors.background,
    },
    // Header Styles
    header: {
        backgroundColor: colors.primary,
        paddingHorizontal: spacing.lg,
        paddingTop: spacing.md,
        paddingBottom: spacing.lg,
        borderBottomLeftRadius: 24,
        borderBottomRightRadius: 24,
    },
    headerTop: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
    },
    logoContainer: {
        flexDirection: 'row',
        alignItems: 'center',
    },
    logoCircle: {
        width: 44,
        height: 44,
        borderRadius: 22,
        backgroundColor: 'rgba(255, 255, 255, 0.2)',
        justifyContent: 'center',
        alignItems: 'center',
    },
    logoEmoji: {
        fontSize: 24,
    },
    headerTitleContainer: {
        marginLeft: spacing.md,
    },
    headerTitle: {
        fontSize: typography.fontSize.lg,
        fontWeight: typography.fontWeight.bold,
        color: colors.textWhite,
    },
    headerSubtitle: {
        fontSize: typography.fontSize.xs,
        color: 'rgba(255, 255, 255, 0.8)',
    },
    profileButton: {
        padding: spacing.xs,
    },
    profileAvatar: {
        width: 40,
        height: 40,
        borderRadius: 20,
        backgroundColor: 'rgba(255, 255, 255, 0.25)',
        justifyContent: 'center',
        alignItems: 'center',
        borderWidth: 2,
        borderColor: 'rgba(255, 255, 255, 0.4)',
    },
    profileInitial: {
        fontSize: typography.fontSize.md,
        fontWeight: typography.fontWeight.bold,
        color: colors.textWhite,
    },
    userBar: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginTop: spacing.lg,
    },
    userInfo: {},
    userName: {
        fontSize: typography.fontSize.md,
        fontWeight: typography.fontWeight.semibold,
        color: colors.textWhite,
    },
    locationChip: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: 'rgba(255, 255, 255, 0.2)',
        paddingHorizontal: spacing.md,
        paddingVertical: spacing.sm,
        borderRadius: 20,
    },
    locationIcon: {
        fontSize: 14,
        marginRight: 6,
    },
    locationText: {
        fontSize: typography.fontSize.sm,
        fontWeight: typography.fontWeight.medium,
        color: colors.textWhite,
    },
    // Content
    content: {
        flex: 1,
    },
    contentContainer: {
        padding: spacing.lg,
        gap: spacing.lg,
    },
    dateBadge: {
        alignSelf: 'flex-start',
    },
    dateText: {
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
        fontWeight: typography.fontWeight.medium,
    },
    // Metric Card
    metricCard: {
        backgroundColor: colors.cardBackground,
        borderRadius: 12,
        padding: spacing.xl,
        ...shadows.md,
    },
    metricHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: spacing.sm,
    },
    metricLabel: {
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
        fontWeight: typography.fontWeight.medium,
    },
    metricBadge: {
        backgroundColor: colors.successLight,
        paddingHorizontal: spacing.sm,
        paddingVertical: 2,
        borderRadius: 12,
    },
    metricBadgeText: {
        fontSize: typography.fontSize.xs,
        color: colors.success,
        fontWeight: typography.fontWeight.semibold,
    },
    metricValue: {
        fontSize: 48,
        fontWeight: typography.fontWeight.bold,
        color: colors.primary,
        lineHeight: 56,
    },
    metricSubtext: {
        fontSize: typography.fontSize.sm,
        color: colors.textMuted,
        marginTop: 4,
    },
    // Action Buttons
    actionsContainer: {
        gap: spacing.md,
    },
    scanButton: {
        backgroundColor: colors.primary,
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        paddingVertical: spacing.lg,
        borderRadius: 12,
        gap: spacing.sm,
        ...shadows.sm,
    },
    scanButtonIcon: {
        fontSize: 20,
    },
    scanButtonText: {
        fontSize: typography.fontSize.md,
        fontWeight: typography.fontWeight.semibold,
        color: colors.textWhite,
    },
    manualButton: {
        backgroundColor: colors.cardBackground,
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        paddingVertical: spacing.lg,
        borderRadius: 12,
        borderWidth: 2,
        borderColor: colors.primary,
        gap: spacing.sm,
    },
    manualButtonIcon: {
        fontSize: 18,
    },
    manualButtonText: {
        fontSize: typography.fontSize.md,
        fontWeight: typography.fontWeight.semibold,
        color: colors.primary,
    },
    // Recent Verifications
    recentSection: {
        marginTop: spacing.sm,
    },
    recentHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: spacing.md,
    },
    recentTitle: {
        fontSize: typography.fontSize.md,
        fontWeight: typography.fontWeight.semibold,
        color: colors.textPrimary,
    },
    recentSubtitle: {
        fontSize: typography.fontSize.sm,
        color: colors.textMuted,
    },
    recentList: {
        backgroundColor: colors.cardBackground,
        borderRadius: 12,
        ...shadows.sm,
        overflow: 'hidden',
    },
    emptyState: {
        padding: spacing['2xl'],
        alignItems: 'center',
    },
    emptyIcon: {
        fontSize: 32,
        marginBottom: spacing.sm,
    },
    emptyText: {
        fontSize: typography.fontSize.base,
        color: colors.textSecondary,
        fontWeight: typography.fontWeight.medium,
    },
    emptySubtext: {
        fontSize: typography.fontSize.sm,
        color: colors.textMuted,
        marginTop: 4,
    },
    recentItem: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        padding: spacing.md,
        borderBottomWidth: 1,
        borderBottomColor: colors.borderLight,
    },
    recentItemLeft: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: spacing.md,
    },
    statusDot: {
        width: 10,
        height: 10,
        borderRadius: 5,
    },
    ticketNo: {
        fontSize: typography.fontSize.base,
        fontWeight: typography.fontWeight.semibold,
        color: colors.textPrimary,
    },
    ticketRoute: {
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
        marginTop: 2,
    },
    recentItemRight: {
        alignItems: 'flex-end',
    },
    ticketAmount: {
        fontSize: typography.fontSize.base,
        fontWeight: typography.fontWeight.semibold,
        color: colors.textPrimary,
    },
    ticketTime: {
        fontSize: typography.fontSize.xs,
        color: colors.textMuted,
        marginTop: 2,
    },
    // Modal
    modalOverlay: {
        flex: 1,
        backgroundColor: 'rgba(0, 0, 0, 0.5)',
        justifyContent: 'center',
        alignItems: 'center',
        padding: spacing.xl,
    },
    modalContent: {
        backgroundColor: colors.cardBackground,
        borderRadius: 16,
        padding: spacing.xl,
        width: '100%',
        maxWidth: 340,
    },
    modalTitle: {
        fontSize: typography.fontSize.xl,
        fontWeight: typography.fontWeight.bold,
        color: colors.textPrimary,
        marginBottom: spacing.lg,
        textAlign: 'center',
    },
    modalInput: {
        borderWidth: 1,
        borderColor: colors.border,
        borderRadius: 12,
        padding: spacing.md,
        fontSize: typography.fontSize.lg,
        marginBottom: spacing.lg,
        textAlign: 'center',
        color: colors.textPrimary,
    },
    modalButtons: {
        flexDirection: 'row',
        gap: spacing.md,
    },
    modalCancelBtn: {
        flex: 1,
        paddingVertical: spacing.md,
        borderRadius: 10,
        borderWidth: 1,
        borderColor: colors.border,
        alignItems: 'center',
    },
    modalCancelText: {
        fontSize: typography.fontSize.base,
        fontWeight: typography.fontWeight.medium,
        color: colors.textSecondary,
    },
    modalVerifyBtn: {
        flex: 1,
        paddingVertical: spacing.md,
        borderRadius: 10,
        backgroundColor: colors.primary,
        alignItems: 'center',
    },
    modalVerifyText: {
        fontSize: typography.fontSize.base,
        fontWeight: typography.fontWeight.semibold,
        color: colors.textWhite,
    },
});

export default HomeScreen;
