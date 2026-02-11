// QRScannerScreen - Camera-based QR code scanning for ticket verification
import React, { useState, useEffect, useRef } from 'react';
import {
    View,
    Text,
    StyleSheet,
    TouchableOpacity,
    Dimensions,
    Alert,
} from 'react-native';
import { CameraView, useCameraPermissions, BarcodeScanningResult } from 'expo-camera';
import * as Haptics from 'expo-haptics';
import { useNavigation } from '@react-navigation/native';
import { colors } from '../theme/colors';
import { typography } from '../theme/typography';
import { spacing, borderRadius } from '../theme/spacing';
import { useAppDispatch, useAppSelector } from '../store';
import { verifyTicket, clearVerificationResult } from '../store/slices/verificationSlice';
import { verificationService } from '../services/verificationService';
import { Loading } from '../components/common';
import { TicketDetailsModal } from '../components/ticket';
import { MainNavigationProp } from '../types/navigation';

const { width: SCREEN_WIDTH, height: SCREEN_HEIGHT } = Dimensions.get('window');
const SCANNER_SIZE = 250;

export const QRScannerScreen: React.FC = () => {
    const navigation = useNavigation<MainNavigationProp<'QRScanner'>>();
    const dispatch = useAppDispatch();

    const { isVerifying, lastVerificationResult } = useAppSelector(
        (state) => state.verification
    );

    const [permission, requestPermission] = useCameraPermissions();
    const [isScanning, setIsScanning] = useState(true);
    const [flashOn, setFlashOn] = useState(false);
    const [showResultModal, setShowResultModal] = useState(false);

    const lastScannedRef = useRef<string | null>(null);
    const scanTimeoutRef = useRef<NodeJS.Timeout | null>(null);

    // Show result modal when verification completes
    useEffect(() => {
        if (lastVerificationResult) {
            setShowResultModal(true);
        }
    }, [lastVerificationResult]);

    // Cleanup timeout on unmount
    useEffect(() => {
        return () => {
            if (scanTimeoutRef.current) {
                clearTimeout(scanTimeoutRef.current);
            }
        };
    }, []);

    const handleBarCodeScanned = async (result: BarcodeScanningResult) => {
        // Prevent multiple scans
        if (!isScanning || isVerifying) return;

        const { data } = result;

        // Debounce: Don't scan same code within 2 seconds
        if (lastScannedRef.current === data) return;

        lastScannedRef.current = data;
        setIsScanning(false);

        // Parse ticket ID from QR data
        const ticketId = verificationService.parseTicketId(data);

        if (!ticketId) {
            // Invalid QR code
            Haptics.notificationAsync(Haptics.NotificationFeedbackType.Error);
            Alert.alert(
                'Invalid QR Code',
                'Could not extract ticket ID from QR code. Please try again or enter manually.',
                [
                    {
                        text: 'Try Again',
                        onPress: () => {
                            lastScannedRef.current = null;
                            setIsScanning(true);
                        },
                    },
                    {
                        text: 'Go Back',
                        onPress: () => navigation.goBack(),
                        style: 'cancel',
                    },
                ]
            );
            return;
        }

        // Haptic feedback for successful scan
        Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Medium);

        // Verify the ticket
        const resultAction = await dispatch(verifyTicket(ticketId));

        // Haptic feedback based on result
        if (verifyTicket.fulfilled.match(resultAction)) {
            const result = resultAction.payload;
            if (result.success) {
                Haptics.notificationAsync(Haptics.NotificationFeedbackType.Success);
            } else if (result.alreadyVerified) {
                Haptics.notificationAsync(Haptics.NotificationFeedbackType.Warning);
            } else {
                Haptics.notificationAsync(Haptics.NotificationFeedbackType.Error);
            }
        }
    };

    const handleCloseResult = () => {
        setShowResultModal(false);
        dispatch(clearVerificationResult());

        // Allow continuous scanning - reset and stay on scanner
        lastScannedRef.current = null;
        setIsScanning(true);
    };

    const handleExitScanner = () => {
        setShowResultModal(false);
        dispatch(clearVerificationResult());
        navigation.goBack();
    };

    const handleTryAgain = () => {
        setShowResultModal(false);
        dispatch(clearVerificationResult());
        lastScannedRef.current = null;
        setIsScanning(true);
    };

    const toggleFlash = () => {
        setFlashOn(!flashOn);
    };

    // Handle permission states
    if (!permission) {
        return (
            <View style={styles.permissionContainer}>
                <Loading message="Loading camera..." />
            </View>
        );
    }

    if (!permission.granted) {
        return (
            <View style={styles.permissionContainer}>
                <Text style={styles.permissionText}>
                    Camera permission is required to scan QR codes
                </Text>
                <TouchableOpacity style={styles.permissionButton} onPress={requestPermission}>
                    <Text style={styles.permissionButtonText}>Grant Permission</Text>
                </TouchableOpacity>
                <TouchableOpacity
                    style={[styles.permissionButton, styles.secondaryButton]}
                    onPress={() => navigation.goBack()}
                >
                    <Text style={[styles.permissionButtonText, styles.secondaryButtonText]}>
                        Go Back
                    </Text>
                </TouchableOpacity>
            </View>
        );
    }

    return (
        <View style={styles.container}>
            {/* Camera View */}
            <CameraView
                style={styles.camera}
                facing="back"
                enableTorch={flashOn}
                barcodeScannerSettings={{
                    barcodeTypes: ['qr'],
                }}
                onBarcodeScanned={isScanning && !isVerifying ? handleBarCodeScanned : undefined}
            >
                {/* Overlay */}
                <View style={styles.overlay}>
                    {/* Top overlay */}
                    <View style={styles.overlayTop} />

                    {/* Middle row with scanner frame */}
                    <View style={styles.overlayMiddle}>
                        <View style={styles.overlaySide} />

                        {/* Scanner frame */}
                        <View style={styles.scannerFrame}>
                            {/* Corner decorations */}
                            <View style={[styles.corner, styles.topLeft]} />
                            <View style={[styles.corner, styles.topRight]} />
                            <View style={[styles.corner, styles.bottomLeft]} />
                            <View style={[styles.corner, styles.bottomRight]} />
                        </View>

                        <View style={styles.overlaySide} />
                    </View>

                    {/* Bottom overlay */}
                    <View style={styles.overlayBottom}>
                        <Text style={styles.instructionText}>
                            Position the QR code within the frame
                        </Text>
                    </View>
                </View>

                {/* Controls */}
                <View style={styles.controls}>
                    {/* Close button */}
                    <TouchableOpacity
                        style={[styles.controlButton, styles.closeButton]}
                        onPress={handleExitScanner}
                    >
                        <Text style={styles.closeIcon}>✕</Text>
                    </TouchableOpacity>

                    {/* Flash toggle */}
                    <TouchableOpacity
                        style={[styles.controlButton, flashOn && styles.flashActive]}
                        onPress={toggleFlash}
                    >
                        <Text style={styles.flashIcon}>{flashOn ? '⚡' : '☀'}</Text>
                        <Text style={styles.flashLabel}>{flashOn ? 'ON' : 'Flash'}</Text>
                    </TouchableOpacity>
                </View>
            </CameraView>

            {/* Loading Overlay */}
            <Loading visible={isVerifying} message="Verifying ticket..." overlay={true} />

            {/* Result Modal */}
            <TicketDetailsModal
                visible={showResultModal}
                result={lastVerificationResult}
                onClose={handleCloseResult}
            />
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#000',
    },
    camera: {
        flex: 1,
    },
    permissionContainer: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        backgroundColor: colors.background,
        padding: spacing.xl,
    },
    permissionText: {
        fontSize: typography.fontSize.lg,
        color: colors.textPrimary,
        textAlign: 'center',
        marginBottom: spacing.xl,
    },
    permissionButton: {
        backgroundColor: colors.primary,
        paddingHorizontal: spacing.xl,
        paddingVertical: spacing.md,
        borderRadius: borderRadius.md,
        marginBottom: spacing.md,
    },
    permissionButtonText: {
        color: colors.textWhite,
        fontSize: typography.fontSize.md,
        fontWeight: typography.fontWeight.semibold,
    },
    secondaryButton: {
        backgroundColor: 'transparent',
        borderWidth: 1,
        borderColor: colors.primary,
    },
    secondaryButtonText: {
        color: colors.primary,
    },
    overlay: {
        ...StyleSheet.absoluteFillObject,
    },
    overlayTop: {
        flex: 1,
        backgroundColor: colors.overlayDark,
    },
    overlayMiddle: {
        flexDirection: 'row',
        height: SCANNER_SIZE,
    },
    overlaySide: {
        flex: 1,
        backgroundColor: colors.overlayDark,
    },
    overlayBottom: {
        flex: 1,
        backgroundColor: colors.overlayDark,
        justifyContent: 'flex-start',
        alignItems: 'center',
        paddingTop: spacing['2xl'],
    },
    scannerFrame: {
        width: SCANNER_SIZE,
        height: SCANNER_SIZE,
        backgroundColor: 'transparent',
    },
    corner: {
        position: 'absolute',
        width: 30,
        height: 30,
        borderColor: colors.textWhite,
    },
    topLeft: {
        top: 0,
        left: 0,
        borderTopWidth: 3,
        borderLeftWidth: 3,
    },
    topRight: {
        top: 0,
        right: 0,
        borderTopWidth: 3,
        borderRightWidth: 3,
    },
    bottomLeft: {
        bottom: 0,
        left: 0,
        borderBottomWidth: 3,
        borderLeftWidth: 3,
    },
    bottomRight: {
        bottom: 0,
        right: 0,
        borderBottomWidth: 3,
        borderRightWidth: 3,
    },
    instructionText: {
        color: colors.textWhite,
        fontSize: typography.fontSize.md,
        textAlign: 'center',
        opacity: 0.9,
    },
    controls: {
        position: 'absolute',
        top: spacing['3xl'],
        left: spacing.lg,
        right: spacing.lg,
        flexDirection: 'row',
        justifyContent: 'space-between',
    },
    controlButton: {
        minWidth: 60,
        height: 50,
        borderRadius: 25,
        backgroundColor: 'rgba(255, 255, 255, 0.15)',
        justifyContent: 'center',
        alignItems: 'center',
        flexDirection: 'row',
        paddingHorizontal: spacing.md,
        borderWidth: 1,
        borderColor: 'rgba(255, 255, 255, 0.3)',
    },
    closeButton: {
        backgroundColor: 'rgba(0, 0, 0, 0.6)',
        width: 50,
        paddingHorizontal: 0,
    },
    closeIcon: {
        fontSize: 20,
        color: colors.textWhite,
        fontWeight: typography.fontWeight.bold,
    },
    flashActive: {
        backgroundColor: colors.primary,
        borderColor: colors.primary,
    },
    flashIcon: {
        fontSize: 18,
        color: colors.textWhite,
        marginRight: 4,
    },
    flashLabel: {
        fontSize: typography.fontSize.sm,
        color: colors.textWhite,
        fontWeight: typography.fontWeight.medium,
    },
    controlButtonText: {
        fontSize: 24,
    },
});

export default QRScannerScreen;
