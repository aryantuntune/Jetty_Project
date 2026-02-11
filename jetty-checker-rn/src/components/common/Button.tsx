// Button Component
import React from 'react';
import {
    TouchableOpacity,
    Text,
    StyleSheet,
    ActivityIndicator,
    ViewStyle,
    TextStyle,
} from 'react-native';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing, borderRadius } from '../../theme/spacing';

interface ButtonProps {
    text: string;
    onPress: () => void;
    variant?: 'primary' | 'secondary' | 'outline' | 'danger';
    size?: 'sm' | 'md' | 'lg';
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
    const isDisabled = disabled || loading;

    const getButtonStyle = (): ViewStyle => {
        const baseStyle: ViewStyle = {
            ...styles.base,
            ...styles[size],
        };

        if (fullWidth) {
            baseStyle.width = '100%';
        }

        if (isDisabled) {
            return { ...baseStyle, ...styles.disabled };
        }

        switch (variant) {
            case 'secondary':
                return { ...baseStyle, ...styles.secondary };
            case 'outline':
                return { ...baseStyle, ...styles.outline };
            case 'danger':
                return { ...baseStyle, ...styles.danger };
            default:
                return { ...baseStyle, ...styles.primary };
        }
    };

    const getTextStyle = (): TextStyle => {
        const baseTextStyle: TextStyle = {
            ...styles.text,
            ...styles[`${size}Text` as keyof typeof styles],
        };

        if (isDisabled) {
            return { ...baseTextStyle, color: colors.disabled };
        }

        switch (variant) {
            case 'outline':
                return { ...baseTextStyle, color: colors.primary };
            default:
                return { ...baseTextStyle, color: colors.textWhite };
        }
    };

    return (
        <TouchableOpacity
            style={[getButtonStyle(), style]}
            onPress={onPress}
            disabled={isDisabled}
            activeOpacity={0.7}
        >
            {loading ? (
                <ActivityIndicator
                    color={variant === 'outline' ? colors.primary : colors.textWhite}
                    size="small"
                />
            ) : (
                <Text style={[getTextStyle(), textStyle]}>{text}</Text>
            )}
        </TouchableOpacity>
    );
};

const styles = StyleSheet.create({
    base: {
        alignItems: 'center',
        justifyContent: 'center',
        borderRadius: borderRadius.md,
        flexDirection: 'row',
    },
    sm: {
        paddingVertical: spacing.sm,
        paddingHorizontal: spacing.md,
        minHeight: 36,
    },
    md: {
        paddingVertical: spacing.md,
        paddingHorizontal: spacing.lg,
        minHeight: 48,
    },
    lg: {
        paddingVertical: spacing.lg,
        paddingHorizontal: spacing.xl,
        minHeight: 56,
    },
    primary: {
        backgroundColor: colors.primary,
    },
    secondary: {
        backgroundColor: colors.primaryLight,
    },
    outline: {
        backgroundColor: 'transparent',
        borderWidth: 2,
        borderColor: colors.primary,
    },
    danger: {
        backgroundColor: colors.error,
    },
    disabled: {
        backgroundColor: colors.disabledBackground,
    },
    text: {
        fontWeight: typography.fontWeight.semibold,
        textAlign: 'center',
    },
    smText: {
        fontSize: typography.fontSize.sm,
    },
    mdText: {
        fontSize: typography.fontSize.base,
    },
    lgText: {
        fontSize: typography.fontSize.md,
    },
});

export default Button;
