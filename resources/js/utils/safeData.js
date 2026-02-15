/**
 * Safe Data Utilities
 *
 * Prevents React Error #310 caused by PHP Eloquent Collections
 * being serialized as `{}` objects instead of `[]` arrays.
 */

/**
 * Ensures a value is always a proper JavaScript array.
 * Handles: null, undefined, {}, {0: x, 1: y} (PHP Collection), and real arrays.
 */
export function toSafeArray(val) {
    if (Array.isArray(val)) return val;
    if (val && typeof val === 'object') return Object.values(val);
    return [];
}

/**
 * Safely converts a value to a string for rendering.
 * Prevents React Error #310 when objects are accidentally rendered as children.
 */
export function toSafeString(val) {
    if (val === null || val === undefined) return '';
    if (typeof val === 'object') return ''; // Never render raw objects
    return String(val);
}

/**
 * Batch-sanitize multiple array props from a destructured props object.
 * Use: const { branches, guests } = sanitizeArrayProps(props, ['branches', 'guests']);
 */
export function sanitizeArrayProps(props, keys) {
    const result = { ...props };
    for (const key of keys) {
        result[key] = toSafeArray(result[key]);
    }
    return result;
}

/**
 * Checks if an object looks like a PHP-serialized Collection/array
 * (i.e. has ONLY numeric string keys like {"0": ..., "1": ..., "2": ...}).
 * Empty objects {} are NOT treated as collections — they might be keyed maps.
 */
function looksLikePhpArray(obj) {
    const keys = Object.keys(obj);
    if (keys.length === 0) return false; // empty {} = keep as object (could be a map)
    return keys.every(k => /^\d+$/.test(k));
}

/**
 * NUCLEAR DEEP SANITIZER
 *
 * Recursively walks ALL props and converts PHP-Collection-like objects
 * (objects with only numeric keys) into proper arrays.
 *
 * This catches EVERY level of nesting, not just top-level props.
 * It handles: ferryBoatsPerBranch[branchId] = {0: boat, 1: boat}
 *             destBranches = {0: branch, 1: branch}
 *
 * Max depth prevents infinite loops on circular references.
 */
export function deepSanitize(value, depth = 0) {
    // Prevent infinite recursion
    if (depth > 10) return value;

    // Primitives, null, undefined — pass through
    if (value === null || value === undefined) return value;
    if (typeof value !== 'object') return value;

    // Already a real array — sanitize children
    if (Array.isArray(value)) {
        return value.map(item => deepSanitize(item, depth + 1));
    }

    // Date objects — pass through
    if (value instanceof Date) return value;

    // Object — check if it looks like a PHP Collection (numeric-only keys, non-empty)
    if (looksLikePhpArray(value)) {
        // Convert to array + sanitize children
        return Object.values(value).map(item => deepSanitize(item, depth + 1));
    }

    // Regular object (like a model/entity or keyed map) — sanitize child values
    const sanitized = {};
    for (const [key, val] of Object.entries(value)) {
        sanitized[key] = deepSanitize(val, depth + 1);
    }
    return sanitized;
}

/**
 * Sanitizes a keyed map (like ferryBoatsPerBranch = { branchId: [...boats] }).
 * Ensures each VALUE under a named key is a proper array.
 * The outer object stays as a keyed map (not converted to array).
 */
export function deepSanitizeMap(obj) {
    if (!obj || typeof obj !== 'object') return {};
    const result = {};
    for (const [key, val] of Object.entries(obj)) {
        // Each value should be an array — force it
        result[key] = toSafeArray(deepSanitize(val));
    }
    return result;
}

