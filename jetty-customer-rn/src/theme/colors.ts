// Theme Colors following the migration guide
export const colors = {
    // Primary colors
    primary: '#006994',
    primaryDark: '#004A6B',
    primaryLight: '#00A8E8',

    // Accent colors
    accent: '#00D4FF',
    secondary: '#4CAF50',

    // Background colors
    background: '#F5F5F5',
    cardBackground: '#FFFFFF',
    inputBackground: '#F9F9F9',

    // Text colors
    textPrimary: '#212121',
    textSecondary: '#757575',
    textHint: '#BDBDBD',
    textWhite: '#FFFFFF',

    // Status colors
    success: '#4CAF50',
    error: '#F44336',
    warning: '#FF9800',
    info: '#2196F3',

    // Border colors
    border: '#E0E0E0',
    divider: '#EEEEEE',

    // Gradient
    primaryGradient: ['#006994', '#00A8E8'] as const,
};

export type Colors = typeof colors;
