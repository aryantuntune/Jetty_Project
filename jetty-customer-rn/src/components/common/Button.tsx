// Button Component following the migration guide
import React from 'react';
import {
    TouchableOpacity,
    Text,
    StyleSheet,
    ActivityIndicator,
    ViewStyle,
    TextStyle,
} from 'react-native';
import { colors, spacing, typography } from '@/theme';

type ButtonVariant = 'primary' | 'secondary' | 'outline' | 'text';
type ButtonSize = 'sm' | 'md' | 'lg';

interface ButtonProps {
    text: string;
    onPress: () => void;
    variant?: ButtonVariant;
    size?: ButtonSize;
    disabled?: boolean;
    loading?: boolean;
    fullWidth?: boolean;
    style?: ViewStyle;
    textStyle?: TextStyle;
}

export const Button: React.FC<ButtonProps> = ({
    text,
    onPress,
    variant = 'primary',
    size = 'md',
    disabled = false,
    loading = false,
    fullWidth = false,
    style,
    textStyle,
}) => {
    const getButtonStyles = (): ViewStyle => {
        const baseStyle: ViewStyle = {
            borderRadius: 8,
            alignItems: 'center',
            justifyContent: 'center',
            flexDirection: 'row',
        };

        // Size styles
        const sizeStyles: Record<ButtonSize, ViewStyle> = {
            sm: { paddingVertical: spacing.sm, paddingHorizontal: spacing.md },
            md: { paddingVertical: spacing.md, paddingHorizontal: spacing.lg },
            lg: { paddingVertical: spacing.lg, paddingHorizontal: spacing.xl },
        };

        // Variant styles
        const variantStyles: Record<ButtonVariant, ViewStyle> = {
            primary: { backgroundColor: colors.primary },
            secondary: { backgroundColor: colors.secondary },
            outline: { backgroundColor: 'transparent', borderWidth: 1, borderColor: colors.primary },
            text: { backgroundColor: 'transparent' },
        };

        return {
            ...baseStyle,
            ...sizeStyles[size],
            ...variantStyles[variant],
            ...(fullWidth ? { width: '100%' } : {}),
            ...(disabled ? { opacity: 0.5 } : {}),
        };
    };

    const getTextStyles = (): TextStyle => {
        const sizeStyles: Record<ButtonSize, TextStyle> = {
            sm: { fontSize: typography.fontSize.sm },
            md: { fontSize: typography.fontSize.base },
            lg: { fontSize: typography.fontSize.lg },
        };

        const variantStyles: Record<ButtonVariant, TextStyle> = {
            primary: { color: colors.textWhite },
            secondary: { color: colors.textWhite },
            outline: { color: colors.primary },
            text: { color: colors.primary },
        };

        return {
            fontWeight: typography.fontWeight.semibold,
            ...sizeStyles[size],
            ...variantStyles[variant],
        };
    };

    return (
        <TouchableOpacity
            style={[getButtonStyles(), style]}
            onPress={onPress}
            disabled={disabled || loading}
            activeOpacity={0.7}
        >
            {loading ? (
                <ActivityIndicator
                    color={variant === 'primary' || variant === 'secondary' ? colors.textWhite : colors.primary}
                    size="small"
                />
            ) : (
                <Text style={[getTextStyles(), textStyle]}>{text}</Text>
            )}
        </TouchableOpacity>
    );
};

export default Button;
