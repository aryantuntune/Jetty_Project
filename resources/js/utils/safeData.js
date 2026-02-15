/**
 * Safe Data Utilities
 * 
 * PHP Eloquent Collections serialize as {} (objects) when empty instead of [] (arrays).
 * PHP associative arrays always serialize as objects.
 * These utilities prevent React error #310 and "x.map is not a function" crashes.
 */

/**
 * Ensures the given value is always an array.
 * Handles: undefined, null, [], {}, {0: 'a', 1: 'b'}, and proper arrays.
 */
export const toSafeArray = (val) => {
    if (Array.isArray(val)) return val;
    if (val && typeof val === 'object') return Object.values(val);
    return [];
};

/**
 * Safely converts a value to a renderable string for JSX.
 * Prevents React error #310 when objects/arrays are accidentally rendered as children.
 */
export const toSafeString = (val) => {
    if (val === null || val === undefined) return '';
    if (typeof val === 'string') return val;
    if (typeof val === 'number' || typeof val === 'boolean') return String(val);
    // If it's an object or array, don't render it — return empty string
    return '';
};

/**
 * Sanitizes all array-type props from an Inertia page component.
 * Usage: const safe = sanitizeProps({ branches, guests, paymentModes }, ['branches', 'guests', 'paymentModes']);
 * Returns object with the same keys but all values converted to safe arrays.
 */
export const sanitizeArrayProps = (props, arrayKeys) => {
    const result = {};
    for (const key of arrayKeys) {
        result[key] = toSafeArray(props[key]);
    }
    return result;
};
