// Validators following the migration guide

// Email validator
export const isValidEmail = (email: string): boolean => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
};

// Password validator (minimum 8 characters)
export const isValidPassword = (password: string): boolean => {
    return password.length >= 8;
};

// Mobile validator (10 digits, starts with 6-9 for Indian numbers)
export const isValidMobile = (mobile: string): boolean => {
    const mobileRegex = /^[6-9]\d{9}$/;
    return mobileRegex.test(mobile);
};

// Required field validator
export const isRequired = (value: string): boolean => {
    return value !== null && value !== undefined && value.trim().length > 0;
};

// Password match validator
export const passwordsMatch = (password: string, confirmPassword: string): boolean => {
    return password === confirmPassword;
};

// OTP validator (exactly 6 digits)
export const isValidOTP = (otp: string): boolean => {
    const otpRegex = /^\d{6}$/;
    return otpRegex.test(otp);
};

// Vehicle number validator (Indian format)
export const isValidVehicleNumber = (vehicleNo: string): boolean => {
    // Format: XX-XX-XX-XXXX or XXXXXXXXXXXX (with or without dashes)
    const vehicleRegex = /^[A-Z]{2}[-\s]?[0-9]{1,2}[-\s]?[A-Z]{1,2}[-\s]?[0-9]{4}$/i;
    return vehicleRegex.test(vehicleNo);
};

// Get validation error messages
export const getEmailError = (email: string): string | null => {
    if (!isRequired(email)) return 'Email is required';
    if (!isValidEmail(email)) return 'Please enter a valid email';
    return null;
};

export const getPasswordError = (password: string): string | null => {
    if (!isRequired(password)) return 'Password is required';
    if (!isValidPassword(password)) return 'Password must be at least 8 characters';
    return null;
};

export const getMobileError = (mobile: string): string | null => {
    if (!isRequired(mobile)) return 'Mobile number is required';
    if (!isValidMobile(mobile)) return 'Please enter a valid 10-digit mobile number';
    return null;
};

export const getConfirmPasswordError = (password: string, confirmPassword: string): string | null => {
    if (!isRequired(confirmPassword)) return 'Please confirm your password';
    if (!passwordsMatch(password, confirmPassword)) return 'Passwords do not match';
    return null;
};

export const getOTPError = (otp: string): string | null => {
    if (!isRequired(otp)) return 'OTP is required';
    if (!isValidOTP(otp)) return 'Please enter a valid 6-digit OTP';
    return null;
};

export const getVehicleNumberError = (vehicleNo: string): string | null => {
    if (!isRequired(vehicleNo)) return 'Vehicle number is required';
    if (!isValidVehicleNumber(vehicleNo)) return 'Please enter a valid vehicle number';
    return null;
};

export const getRequiredError = (value: string, fieldName: string): string | null => {
    if (!isRequired(value)) return `${fieldName} is required`;
    return null;
};
