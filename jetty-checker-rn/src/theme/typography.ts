// Typography definitions for consistent text styling
import { TextStyle } from 'react-native';

// Font weight type - using React Native's accepted values
type FontWeight = TextStyle['fontWeight'];

export const typography = {
    // Font sizes
    fontSize: {
        xs: 10,
        sm: 12,
        base: 14,
        md: 16,
        lg: 18,
        xl: 20,
        '2xl': 24,
        '3xl': 32,
        '4xl': 40,
        '5xl': 48,
    },

    // Font weights - typed as React Native FontWeight
    fontWeight: {
        regular: '400' as FontWeight,
        medium: '500' as FontWeight,
        semibold: '600' as FontWeight,
        bold: '700' as FontWeight,
    },

    // Line heights
    lineHeight: {
        tight: 1.2,
        normal: 1.5,
        relaxed: 1.75,
    },
};

export default typography;

