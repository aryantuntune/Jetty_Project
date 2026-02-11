// Booking Screen - Enhanced UX with centered modals and categorized items
import React, { useEffect, useState, useMemo } from 'react';
import {
    View,
    Text,
    StyleSheet,
    ScrollView,
    Alert,
    TouchableOpacity,
    Modal,
    FlatList,
    Dimensions,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { BookingScreenNavigationProp } from '@/types/navigation';
import { colors, typography, spacing } from '@/theme';
import { Card, Button, Loading } from '@/components/common';
import { useAppSelector, useAppDispatch } from '@/store';
import {
    fetchBranches,
    fetchToBranches,
    fetchFerriesByBranch,
    fetchRatesByBranch,
    setFromBranch,
    setToBranch,
    setSelectedFerry,
    setSelectedTime,
    setSelectedDate,
    updateItemQty,
    clearBookingForm,
    addLocalBooking,
    createBooking,
} from '@/store/slices/bookingSlice';
import { Branch, Ferry, ItemRate, Booking } from '@/types/models';
import { getErrorMessage } from '@/services';

const { height: SCREEN_HEIGHT } = Dimensions.get('window');

// Convert 24-hour time to 12-hour format (e.g., "14:00" -> "2:00 PM")
const formatTime12Hour = (time24: string): string => {
    const [hourStr, minute] = time24.split(':');
    let hour = parseInt(hourStr, 10);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    hour = hour % 12 || 12; // Convert 0 to 12 for midnight, 13-23 to 1-11
    return `${hour}:${minute} ${ampm}`;
};

// Available departure times (extended schedule) - stored in 24-hour format internally
const DEPARTURE_TIMES = [
    '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
    '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00'
];

// Get available times based on selected date
// For today: filter out past times (15 min buffer)
// For future dates: return all times
const getAvailableTimesForDate = (selectedDate: string | null): string[] => {
    // If no date selected, return empty array
    if (!selectedDate) return [];

    const now = new Date();
    const todayStr = now.toISOString().split('T')[0];

    // If selected date is today, filter out past times
    if (selectedDate === todayStr) {
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();

        return DEPARTURE_TIMES.filter(time => {
            const [hour, minute] = time.split(':').map(Number);
            // Only show times that are at least 15 minutes in the future
            if (hour > currentHour) return true;
            if (hour === currentHour && minute > currentMinute + 15) return true;
            return false;
        });
    }

    // For future dates, all times are available
    return DEPARTURE_TIMES;
};

// Legacy function for backward compatibility (deprecated - use getAvailableTimesForDate)
const getAvailableTimes = (): { times: string[]; isNextDay: boolean } => {
    const times = getAvailableTimesForDate(new Date().toISOString().split('T')[0]);
    return { times: times.length > 0 ? times : DEPARTURE_TIMES, isNextDay: times.length === 0 };
};

// Generate available dates (today + next 30 days)
const generateAvailableDates = (): { value: string; label: string }[] => {
    const dates: { value: string; label: string }[] = [];
    const today = new Date();

    for (let i = 0; i < 30; i++) {
        const date = new Date(today);
        date.setDate(today.getDate() + i);

        const value = date.toISOString().split('T')[0]; // YYYY-MM-DD
        const dayName = date.toLocaleDateString('en-IN', { weekday: 'short' });
        const dayNum = date.getDate();
        const monthName = date.toLocaleDateString('en-IN', { month: 'short' });

        const label = i === 0 ? `Today (${dayNum} ${monthName})`
            : i === 1 ? `Tomorrow (${dayNum} ${monthName})`
                : `${dayName}, ${dayNum} ${monthName}`;

        dates.push({ value, label });
    }

    return dates;
};

// Format date for display (YYYY-MM-DD -> readable format)
const formatDateDisplay = (dateStr: string | null): string => {
    if (!dateStr) return '';
    const date = new Date(dateStr + 'T00:00:00');
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);

    if (date.getTime() === today.getTime()) return 'Today';
    if (date.getTime() === tomorrow.getTime()) return 'Tomorrow';

    return date.toLocaleDateString('en-IN', { weekday: 'short', day: 'numeric', month: 'short' });
};

// Item category keywords for intelligent sorting
const PASSENGER_KEYWORDS = ['passenger', 'adult', 'child', 'senior', 'student', 'person'];
const VEHICLE_KEYWORDS = ['car', 'vehicle', 'bike', 'motorcycle', 'tempo', 'bus', 'truck', 'auto', 'rickshaw', 'cycle', 'ambulance', 'loaded', 'empty', 'whlr', 'wheeler'];

// Categorize items intelligently
const categorizeItems = (rates: ItemRate[]): { passengers: ItemRate[]; vehicles: ItemRate[]; others: ItemRate[] } => {
    const passengers: ItemRate[] = [];
    const vehicles: ItemRate[] = [];
    const others: ItemRate[] = [];

    rates.forEach(rate => {
        const nameLower = rate.itemName.toLowerCase();

        if (PASSENGER_KEYWORDS.some(kw => nameLower.includes(kw))) {
            passengers.push(rate);
        } else if (VEHICLE_KEYWORDS.some(kw => nameLower.includes(kw))) {
            vehicles.push(rate);
        } else {
            others.push(rate);
        }
    });

    // Sort each category alphabetically
    passengers.sort((a, b) => a.itemName.localeCompare(b.itemName));
    vehicles.sort((a, b) => a.itemName.localeCompare(b.itemName));
    others.sort((a, b) => a.itemName.localeCompare(b.itemName));

    return { passengers, vehicles, others };
};

// Centered Picker Modal Component
const PickerModal: React.FC<{
    visible: boolean;
    title: string;
    items: { label: string; value: number | string }[];
    onSelect: (value: number | string) => void;
    onClose: () => void;
}> = ({ visible, title, items, onSelect, onClose }) => (
    <Modal visible={visible} transparent animationType="fade">
        <View style={styles.modalOverlay}>
            <View style={styles.modalContent}>
                <Text style={styles.modalTitle}>{title}</Text>
                <FlatList
                    data={items}
                    keyExtractor={(item) => String(item.value)}
                    renderItem={({ item }) => (
                        <TouchableOpacity
                            style={styles.modalItem}
                            onPress={() => {
                                onSelect(item.value);
                                onClose();
                            }}
                        >
                            <Text style={styles.modalItemText}>{item.label}</Text>
                        </TouchableOpacity>
                    )}
                    style={styles.modalList}
                    showsVerticalScrollIndicator={true}
                />
                <Button text="Cancel" variant="outline" onPress={onClose} fullWidth />
            </View>
        </View>
    </Modal>
);

// Stepper component
const Stepper: React.FC<{
    value: number;
    onDecrement: () => void;
    onIncrement: () => void;
}> = ({ value, onDecrement, onIncrement }) => (
    <View style={styles.stepper}>
        <TouchableOpacity
            style={[styles.stepperButton, value === 0 && styles.stepperButtonDisabled]}
            onPress={onDecrement}
            disabled={value === 0}
        >
            <Text style={styles.stepperButtonText}>−</Text>
        </TouchableOpacity>
        <Text style={styles.stepperValue}>{value}</Text>
        <TouchableOpacity style={styles.stepperButton} onPress={onIncrement}>
            <Text style={styles.stepperButtonText}>+</Text>
        </TouchableOpacity>
    </View>
);

// Category Section Component
const CategorySection: React.FC<{
    title: string;
    items: ItemRate[];
    getItemQty: (itemRateId: number) => number;
    onQtyChange: (rate: ItemRate, increment: boolean) => void;
}> = ({ title, items, getItemQty, onQtyChange }) => {
    if (items.length === 0) return null;

    return (
        <View style={styles.categorySection}>
            <Text style={styles.categoryTitle}>{title}</Text>
            {items.map((rate) => (
                <View key={rate.id} style={styles.itemRow}>
                    <View style={styles.itemInfo}>
                        <Text style={styles.itemName} numberOfLines={2}>{rate.itemName}</Text>
                        <Text style={styles.itemPrice}>₹{rate.price.toFixed(2)}</Text>
                    </View>
                    <Stepper
                        value={getItemQty(rate.id)}
                        onDecrement={() => onQtyChange(rate, false)}
                        onIncrement={() => onQtyChange(rate, true)}
                    />
                </View>
            ))}
        </View>
    );
};

export const BookingScreen: React.FC = () => {
    const navigation = useNavigation<BookingScreenNavigationProp>();
    const dispatch = useAppDispatch();

    const {
        branches = [],
        toBranches = [],
        ferries = [],
        rates = [],
        fromBranch,
        toBranch,
        selectedFerry,
        selectedTime,
        selectedDate,
        items = [],
        totalAmount = 0,
        isLoading,
    } = useAppSelector((state) => state.booking);

    // Modal states
    const [showFromBranchPicker, setShowFromBranchPicker] = useState(false);
    const [showToBranchPicker, setShowToBranchPicker] = useState(false);
    const [showFerryPicker, setShowFerryPicker] = useState(false);
    const [showTimePicker, setShowTimePicker] = useState(false);
    const [showDatePicker, setShowDatePicker] = useState(false);
    const [isProcessingPayment, setIsProcessingPayment] = useState(false);

    // Generate available dates
    const availableDates = useMemo(() => generateAvailableDates(), []);

    // Categorize items for better UX
    const categorizedRates = useMemo(() => categorizeItems(rates), [rates]);

    useEffect(() => {
        dispatch(fetchBranches());
        return () => {
            dispatch(clearBookingForm());
        };
    }, [dispatch]);

    // Handle from branch selection
    const handleFromBranchSelect = (branchId: number) => {
        const branch = branches.find((b: Branch) => b.id === branchId);
        if (branch) {
            dispatch(setFromBranch(branch));
            dispatch(fetchToBranches(branchId));
            dispatch(fetchFerriesByBranch(branchId));
            dispatch(fetchRatesByBranch(branchId));
        }
    };

    // Handle to branch selection
    const handleToBranchSelect = (branchId: number) => {
        const branch = toBranches.find((b: Branch) => b.id === branchId);
        if (branch) {
            dispatch(setToBranch(branch));
        }
    };

    // Handle ferry selection
    const handleFerrySelect = (ferryId: number) => {
        const ferry = ferries.find((f: Ferry) => f.id === ferryId);
        if (ferry) {
            dispatch(setSelectedFerry(ferry));
        }
    };

    // Get current quantity for a rate item by ID
    const getItemQty = (itemRateId: number): number => {
        const item = items.find((i: { itemRateId: number }) => i.itemRateId === itemRateId);
        return item?.qty || 0;
    };

    // Handle quantity change
    const handleQtyChange = (rate: ItemRate, increment: boolean) => {
        const currentQty = getItemQty(rate.id);
        const newQty = increment ? currentQty + 1 : Math.max(0, currentQty - 1);

        dispatch(updateItemQty({
            itemRateId: rate.id,
            itemName: rate.itemName,
            qty: newQty,
            rate: rate.price,
        }));
    };

    // Validate form
    const isFormValid = () => {
        return fromBranch && selectedFerry && selectedDate && selectedTime && items.length > 0 && totalAmount > 0;
    };

    // Handle payment and booking
    const handleProceedToPayment = async () => {
        if (!isFormValid()) {
            Alert.alert('Incomplete', 'Please select route, date, ferry, time and add at least one item.');
            return;
        }

        setIsProcessingPayment(true);

        try {
            // Import payment service
            const { initiatePayment } = await import('@/services/paymentService');

            // Get customer info from auth state (for Razorpay prefill)
            // Using placeholder values if not available
            const customerName = 'Customer';
            const customerEmail = 'customer@example.com';
            const customerPhone = '9999999999';

            // Initiate payment (uses Razorpay if available, otherwise simulates)
            const paymentResult = await initiatePayment({
                amount: Math.round(totalAmount * 100), // Convert to paise
                description: `Ferry Booking: ${fromBranch?.branchName} → ${toBranch?.branchName || 'N/A'}`,
                customerName,
                customerEmail,
                customerPhone,
            });

            if (!paymentResult.success) {
                setIsProcessingPayment(false);
                Alert.alert('Payment Failed', paymentResult.error || 'Payment was cancelled or failed.');
                return;
            }

            // Payment successful - create booking
            const result = await dispatch(createBooking({
                fromBranch: fromBranch!.id,
                toBranch: toBranch?.id,
                ferryBoatId: selectedFerry!.id,
                ferryTime: selectedTime!,
                bookingDate: selectedDate!,
                items: items.map((item: { itemRateId: number; itemName: string; qty: number; rate: number; amount: number }) => ({
                    itemRateId: item.itemRateId,
                    itemName: item.itemName,
                    qty: item.qty,
                    rate: item.rate,
                    amount: item.amount,
                })),
                totalAmount,
                paymentId: paymentResult.paymentId!,
            })).unwrap();

            setIsProcessingPayment(false);
            dispatch(clearBookingForm());

            Alert.alert(
                '✅ Booking Successful!',
                `Your booking has been confirmed and saved.\n\nRoute: ${fromBranch?.branchName} → ${toBranch?.branchName || 'N/A'}\nFerry: ${selectedFerry?.name}\nDate: ${formatDateDisplay(selectedDate)}\nTime: ${formatTime12Hour(selectedTime!)}\nAmount: ₹${totalAmount.toFixed(2)}`,
                [
                    { text: 'View Bookings', onPress: () => navigation.navigate('BookingsTab' as never) },
                    { text: 'OK' }
                ]
            );
        } catch (error) {
            setIsProcessingPayment(false);
            Alert.alert('Error', getErrorMessage(error));
        }
    };

    if (isProcessingPayment) {
        return <Loading message="Processing payment..." />;
    }

    return (
        <ScrollView style={styles.container} contentContainerStyle={styles.content}>
            {/* Step 1: Route Selection */}
            <Card style={styles.section}>
                <Text style={styles.sectionTitle}>1. Select Route</Text>

                <Text style={styles.fieldLabel}>From Branch *</Text>
                <TouchableOpacity
                    style={styles.pickerButton}
                    onPress={() => setShowFromBranchPicker(true)}
                >
                    <Text style={fromBranch ? styles.pickerButtonText : styles.pickerPlaceholder}>
                        {fromBranch?.branchName || 'Select departure branch'}
                    </Text>
                    <Text style={styles.pickerArrow}>▼</Text>
                </TouchableOpacity>

                <Text style={styles.fieldLabel}>To Branch</Text>
                <TouchableOpacity
                    style={[styles.pickerButton, !fromBranch && styles.pickerDisabled]}
                    onPress={() => fromBranch && setShowToBranchPicker(true)}
                    disabled={!fromBranch}
                >
                    <Text style={toBranch ? styles.pickerButtonText : styles.pickerPlaceholder}>
                        {toBranch?.branchName || (fromBranch ? 'Select destination (optional)' : 'Select from branch first')}
                    </Text>
                    <Text style={styles.pickerArrow}>▼</Text>
                </TouchableOpacity>
            </Card>

            {/* Step 2: Date, Ferry & Time Selection */}
            {fromBranch && (
                <Card style={styles.section}>
                    <Text style={styles.sectionTitle}>2. Select Date, Ferry & Time</Text>

                    <Text style={styles.fieldLabel}>Travel Date *</Text>
                    <TouchableOpacity
                        style={styles.pickerButton}
                        onPress={() => setShowDatePicker(true)}
                    >
                        <Text style={selectedDate ? styles.pickerButtonText : styles.pickerPlaceholder}>
                            {selectedDate ? formatDateDisplay(selectedDate) : 'Select date'}
                        </Text>
                        <Text style={styles.pickerArrow}>▼</Text>
                    </TouchableOpacity>

                    <Text style={styles.fieldLabel}>Ferry *</Text>
                    <TouchableOpacity
                        style={styles.pickerButton}
                        onPress={() => setShowFerryPicker(true)}
                    >
                        <Text style={selectedFerry ? styles.pickerButtonText : styles.pickerPlaceholder}>
                            {selectedFerry ? `${selectedFerry.name} (${selectedFerry.number})` : 'Select ferry'}
                        </Text>
                        <Text style={styles.pickerArrow}>▼</Text>
                    </TouchableOpacity>

                    <Text style={styles.fieldLabel}>Departure Time *</Text>
                    <TouchableOpacity
                        style={[styles.pickerButton, !selectedDate && styles.pickerDisabled]}
                        onPress={() => selectedDate && setShowTimePicker(true)}
                        disabled={!selectedDate}
                    >
                        <Text style={selectedTime ? styles.pickerButtonText : styles.pickerPlaceholder}>
                            {selectedTime ? formatTime12Hour(selectedTime) : (selectedDate ? 'Select time' : 'Select date first')}
                        </Text>
                        <Text style={styles.pickerArrow}>▼</Text>
                    </TouchableOpacity>
                </Card>
            )}

            {/* Step 3: Item Selection - Categorized */}
            {fromBranch && rates.length > 0 && (
                <Card style={styles.section}>
                    <Text style={styles.sectionTitle}>3. Select Items</Text>

                    <CategorySection
                        title="👤 Passengers"
                        items={categorizedRates.passengers}
                        getItemQty={getItemQty}
                        onQtyChange={handleQtyChange}
                    />

                    <CategorySection
                        title="🚗 Vehicles"
                        items={categorizedRates.vehicles}
                        getItemQty={getItemQty}
                        onQtyChange={handleQtyChange}
                    />

                    <CategorySection
                        title="📦 Other Items"
                        items={categorizedRates.others}
                        getItemQty={getItemQty}
                        onQtyChange={handleQtyChange}
                    />
                </Card>
            )}

            {/* Summary */}
            {items.length > 0 && (
                <Card style={styles.section}>
                    <Text style={styles.sectionTitle}>📋 Booking Summary</Text>

                    {fromBranch && (
                        <View style={styles.summaryInfoRow}>
                            <Text style={styles.summaryLabel}>Route:</Text>
                            <Text style={styles.summaryValue}>
                                {fromBranch.branchName} → {toBranch?.branchName || 'N/A'}
                            </Text>
                        </View>
                    )}

                    {selectedFerry && (
                        <View style={styles.summaryInfoRow}>
                            <Text style={styles.summaryLabel}>Ferry:</Text>
                            <Text style={styles.summaryValue}>{selectedFerry.name}</Text>
                        </View>
                    )}

                    {selectedTime && (
                        <View style={styles.summaryInfoRow}>
                            <Text style={styles.summaryLabel}>Time:</Text>
                            <Text style={styles.summaryValue}>{selectedTime ? formatTime12Hour(selectedTime) : ''}</Text>
                        </View>
                    )}

                    <View style={styles.divider} />

                    {items.map((item: { itemName: string; qty: number; amount: number }, index: number) => (
                        <View key={index} style={styles.summaryRow}>
                            <Text style={styles.summaryText}>{item.itemName} × {item.qty}</Text>
                            <Text style={styles.summaryAmount}>₹{item.amount.toFixed(2)}</Text>
                        </View>
                    ))}

                    <View style={styles.divider} />

                    <View style={styles.totalRow}>
                        <Text style={styles.totalLabel}>Total Amount</Text>
                        <Text style={styles.totalAmount}>₹{totalAmount.toFixed(2)}</Text>
                    </View>
                </Card>
            )}

            {/* Proceed Button */}
            <Button
                text={`Proceed to Payment - ₹${totalAmount.toFixed(2)}`}
                onPress={handleProceedToPayment}
                disabled={!isFormValid()}
                loading={isLoading}
                fullWidth
                style={styles.proceedButton}
            />

            {/* Picker Modals - Centered */}
            <PickerModal
                visible={showFromBranchPicker}
                title="Select From Branch"
                items={branches.map((b: Branch) => ({ label: b.branchName, value: b.id }))}
                onSelect={(v) => handleFromBranchSelect(Number(v))}
                onClose={() => setShowFromBranchPicker(false)}
            />
            <PickerModal
                visible={showToBranchPicker}
                title="Select To Branch"
                items={toBranches.map((b: Branch) => ({ label: b.branchName, value: b.id }))}
                onSelect={(v) => handleToBranchSelect(Number(v))}
                onClose={() => setShowToBranchPicker(false)}
            />
            <PickerModal
                visible={showDatePicker}
                title="Select Travel Date"
                items={availableDates}
                onSelect={(v) => dispatch(setSelectedDate(String(v)))}
                onClose={() => setShowDatePicker(false)}
            />
            <PickerModal
                visible={showFerryPicker}
                title="Select Ferry"
                items={ferries.map((f: Ferry) => ({ label: `${f.name} (${f.number})`, value: f.id }))}
                onSelect={(v) => handleFerrySelect(Number(v))}
                onClose={() => setShowFerryPicker(false)}
            />
            <PickerModal
                visible={showTimePicker}
                title="Select Departure Time"
                items={getAvailableTimesForDate(selectedDate).map((t: string) => ({ label: formatTime12Hour(t), value: t }))}
                onSelect={(v) => dispatch(setSelectedTime(String(v)))}
                onClose={() => setShowTimePicker(false)}
            />
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
        paddingBottom: spacing['3xl'],
    },
    section: {
        marginBottom: spacing.lg,
    },
    sectionTitle: {
        fontSize: typography.fontSize.lg,
        fontWeight: typography.fontWeight.semibold as any,
        color: colors.textPrimary,
        marginBottom: spacing.md,
    },
    fieldLabel: {
        fontSize: typography.fontSize.sm,
        fontWeight: typography.fontWeight.medium as any,
        color: colors.textSecondary,
        marginBottom: spacing.xs,
        marginTop: spacing.sm,
    },
    pickerButton: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        backgroundColor: colors.inputBackground,
        borderWidth: 1,
        borderColor: colors.border,
        borderRadius: 8,
        paddingHorizontal: spacing.md,
        paddingVertical: spacing.md,
    },
    pickerDisabled: {
        opacity: 0.5,
    },
    pickerButtonText: {
        fontSize: typography.fontSize.base,
        color: colors.textPrimary,
        flex: 1,
    },
    pickerPlaceholder: {
        fontSize: typography.fontSize.base,
        color: colors.textHint,
        flex: 1,
    },
    pickerArrow: {
        fontSize: 12,
        color: colors.textSecondary,
    },
    categorySection: {
        marginBottom: spacing.lg,
    },
    categoryTitle: {
        fontSize: typography.fontSize.base,
        fontWeight: typography.fontWeight.semibold as any,
        color: colors.primary,
        marginBottom: spacing.sm,
        paddingBottom: spacing.xs,
        borderBottomWidth: 1,
        borderBottomColor: colors.primary + '30',
    },
    itemRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingVertical: spacing.sm,
        borderBottomWidth: 1,
        borderBottomColor: colors.divider,
    },
    itemInfo: {
        flex: 1,
        marginRight: spacing.md,
    },
    itemName: {
        fontSize: typography.fontSize.sm,
        fontWeight: typography.fontWeight.medium as any,
        color: colors.textPrimary,
    },
    itemPrice: {
        fontSize: typography.fontSize.sm,
        color: colors.primary,
        marginTop: 2,
    },
    stepper: {
        flexDirection: 'row',
        alignItems: 'center',
    },
    stepperButton: {
        width: 32,
        height: 32,
        borderRadius: 16,
        backgroundColor: colors.primary,
        justifyContent: 'center',
        alignItems: 'center',
    },
    stepperButtonDisabled: {
        backgroundColor: colors.border,
    },
    stepperButtonText: {
        fontSize: 18,
        color: colors.textWhite,
        fontWeight: 'bold',
    },
    stepperValue: {
        fontSize: typography.fontSize.base,
        fontWeight: typography.fontWeight.semibold as any,
        color: colors.textPrimary,
        minWidth: 36,
        textAlign: 'center',
    },
    summaryInfoRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        paddingVertical: spacing.xs,
    },
    summaryLabel: {
        fontSize: typography.fontSize.sm,
        color: colors.textSecondary,
    },
    summaryValue: {
        fontSize: typography.fontSize.sm,
        fontWeight: typography.fontWeight.medium as any,
        color: colors.textPrimary,
    },
    summaryRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        paddingVertical: spacing.xs,
    },
    summaryText: {
        fontSize: typography.fontSize.sm,
        color: colors.textPrimary,
    },
    summaryAmount: {
        fontSize: typography.fontSize.sm,
        fontWeight: typography.fontWeight.medium as any,
        color: colors.textPrimary,
    },
    divider: {
        height: 1,
        backgroundColor: colors.divider,
        marginVertical: spacing.md,
    },
    totalRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
    },
    totalLabel: {
        fontSize: typography.fontSize.lg,
        fontWeight: typography.fontWeight.semibold as any,
        color: colors.textPrimary,
    },
    totalAmount: {
        fontSize: typography.fontSize.xl,
        fontWeight: typography.fontWeight.bold as any,
        color: colors.primary,
    },
    proceedButton: {
        marginTop: spacing.lg,
    },
    // Centered Modal styles
    modalOverlay: {
        flex: 1,
        backgroundColor: 'rgba(0,0,0,0.5)',
        justifyContent: 'center',
        alignItems: 'center',
        padding: spacing.lg,
    },
    modalContent: {
        backgroundColor: colors.cardBackground,
        borderRadius: 16,
        padding: spacing.lg,
        width: '100%',
        maxWidth: 400,
        maxHeight: SCREEN_HEIGHT * 0.6,
    },
    modalTitle: {
        fontSize: typography.fontSize.lg,
        fontWeight: typography.fontWeight.semibold as any,
        color: colors.textPrimary,
        marginBottom: spacing.md,
        textAlign: 'center',
    },
    modalList: {
        maxHeight: SCREEN_HEIGHT * 0.4,
        marginBottom: spacing.md,
    },
    modalItem: {
        paddingVertical: spacing.md,
        paddingHorizontal: spacing.sm,
        borderBottomWidth: 1,
        borderBottomColor: colors.divider,
    },
    modalItemText: {
        fontSize: typography.fontSize.base,
        color: colors.textPrimary,
    },
});

export default BookingScreen;
