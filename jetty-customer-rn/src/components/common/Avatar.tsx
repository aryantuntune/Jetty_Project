// Avatar Component following the migration guide
import React from 'react';
import { View, Image, Text, StyleSheet, ViewStyle } from 'react-native';
import { colors, typography } from '@/theme';

interface AvatarProps {
    uri?: string | null;
    name?: string;
    size?: number;
    style?: ViewStyle;
}

export const Avatar: React.FC<AvatarProps> = ({
    uri,
    name = '',
    size = 50,
    style,
}) => {
    // Get initials from name
    const getInitials = (): string => {
        const names = name.trim().split(' ');
        if (names.length >= 2) {
            return `${names[0][0]}${names[names.length - 1][0]}`.toUpperCase();
        }
        return names[0] ? names[0].substring(0, 2).toUpperCase() : '?';
    };

    // Generate a consistent color based on name
    const getBackgroundColor = (): string => {
        const backgroundColors = [
            colors.primary,
            colors.secondary,
            colors.info,
            colors.warning,
            '#9C27B0',
            '#E91E63',
            '#795548',
            '#607D8B',
        ];
        const index = name.length % backgroundColors.length;
        return backgroundColors[index];
    };

    const containerStyle: ViewStyle = {
        width: size,
        height: size,
        borderRadius: size / 2,
        backgroundColor: getBackgroundColor(),
        justifyContent: 'center',
        alignItems: 'center',
        overflow: 'hidden',
    };

    const fontSize = size * 0.4;

    if (uri) {
        return (
            <View style={[containerStyle, style]}>
                <Image
                    source={{ uri }}
                    style={styles.image}
                    resizeMode="cover"
                />
            </View>
        );
    }

    return (
        <View style={[containerStyle, style]}>
            <Text style={[styles.initials, { fontSize }]}>{getInitials()}</Text>
        </View>
    );
};

const styles = StyleSheet.create({
    image: {
        width: '100%',
        height: '100%',
    },
    initials: {
        color: colors.textWhite,
        fontWeight: typography.fontWeight.semibold,
    },
});

export default Avatar;
