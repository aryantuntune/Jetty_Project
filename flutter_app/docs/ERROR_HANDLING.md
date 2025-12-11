# Error Handling Documentation

## Error Handling Philosophy
Comprehensive error handling ensures app stability and provides users with clear, actionable feedback.

## Error Categories

### 1. Network Errors
**Common Scenarios**:
- No internet connection
- Server unavailable
- Timeout errors
- SSL/TLS errors
- DNS resolution failures

**Handling Strategy**:
- Detect connectivity before API calls
- Display offline banner
- Retry mechanism with exponential backoff
- Cache-first fallback for non-critical data
- User-friendly error messages

**Example Messages**:
- "No internet connection. Please check your network."
- "Server is temporarily unavailable. Please try again."
- "Request timed out. Please retry."

### 2. API Errors
**HTTP Status Codes**:
- **400 Bad Request**: Validation errors
- **401 Unauthorized**: Token expired/invalid
- **403 Forbidden**: Access denied
- **404 Not Found**: Resource not found
- **422 Unprocessable Entity**: Validation errors
- **429 Too Many Requests**: Rate limit exceeded
- **500 Internal Server Error**: Server error
- **503 Service Unavailable**: Maintenance mode

**Handling Strategy**:
- Parse error response from API
- Display specific error messages
- Auto-logout on 401 (token expired)
- Retry on 5xx errors
- Rate limit handling with backoff

### 3. Validation Errors
**Form Validation**:
- Email format validation
- Phone number format
- Required field validation
- Date range validation
- Payment amount validation

**Handling Strategy**:
- Inline validation feedback
- Real-time validation (debounced)
- Clear error messages below fields
- Highlight invalid fields
- Prevent submission with errors

### 4. Payment Errors
**Common Issues**:
- Insufficient funds
- Card declined
- Payment gateway timeout
- Authentication failed
- Invalid payment details

**Handling Strategy**:
- Clear payment failure messages
- Retry option
- Alternative payment methods
- Contact support option
- Transaction status tracking

### 5. Authentication Errors
**Scenarios**:
- Invalid credentials
- Account locked
- Email not verified
- Token expired
- Session timeout

**Handling Strategy**:
- Clear error messages
- Auto-redirect to login
- Password reset option
- Account unlock process
- Session refresh attempt

### 6. Booking Errors
**Common Issues**:
- No available seats
- Ferry schedule changed
- Booking window closed
- Duplicate booking
- Invalid route selection

**Handling Strategy**:
- Check availability before payment
- Real-time seat availability
- Alternative schedule suggestions
- Clear booking rules
- Support contact option

## Error Display Patterns

### 1. Toast Messages
**Use For**: Non-critical, brief notifications
```dart
ScaffoldMessenger.of(context).showSnackBar(
  SnackBar(content: Text('Booking saved successfully'))
);
```

### 2. Dialog Alerts
**Use For**: Critical errors requiring acknowledgment
```dart
showDialog(
  context: context,
  builder: (context) => AlertDialog(
    title: Text('Error'),
    content: Text('Failed to process payment'),
    actions: [
      TextButton(child: Text('Retry'), onPressed: () {}),
      TextButton(child: Text('Cancel'), onPressed: () {}),
    ],
  ),
);
```

### 3. Inline Messages
**Use For**: Form validation errors
- Red text below input fields
- Icon indicators
- Field border highlighting

### 4. Full-Screen Error Pages
**Use For**: Fatal errors, no data states
- Network error page with retry
- Empty state illustrations
- 404 error page
- Maintenance mode page

## Error Logging

### Local Logging
- Debug logs in development
- Error logs in production
- Log rotation (max 7 days)
- Sensitive data filtering

### Remote Logging
- Firebase Crashlytics integration
- Stack trace reporting
- User context (anonymous)
- Device information
- App version tracking

### Log Levels
- **DEBUG**: Development information
- **INFO**: General information
- **WARNING**: Potential issues
- **ERROR**: Handled errors
- **FATAL**: Critical unhandled errors

## Exception Handling

### Try-Catch Blocks
```dart
try {
  await bookingService.createBooking(data);
} on NetworkException catch (e) {
  showError('Network error: ${e.message}');
} on ValidationException catch (e) {
  showValidationErrors(e.errors);
} catch (e, stackTrace) {
  logError(e, stackTrace);
  showGenericError();
}
```

### Custom Exceptions
- `NetworkException` - Network-related errors
- `ValidationException` - Validation failures
- `AuthenticationException` - Auth errors
- `BookingException` - Booking-specific errors
- `PaymentException` - Payment failures

### Global Error Handling
```dart
FlutterError.onError = (FlutterErrorDetails details) {
  logError(details.exception, details.stack);
  reportToCrashlytics(details);
};
```

## User-Friendly Messages

### Guidelines
- Avoid technical jargon
- Be specific about the problem
- Provide actionable solutions
- Use empathetic tone
- Include support contact when needed

### Examples
❌ Bad: "API call failed with status 500"
✅ Good: "Something went wrong. Please try again in a moment."

❌ Bad: "Null pointer exception in booking module"
✅ Good: "We couldn't complete your booking. Please try again."

❌ Bad: "Authentication token expired"
✅ Good: "Your session has expired. Please log in again."

## Retry Mechanisms

### Automatic Retry
- Network failures: 3 retries with exponential backoff
- Rate limit errors: Retry after specified time
- Server errors (5xx): 2 retries with 2-second delay

### Manual Retry
- Retry button on error screens
- Pull-to-refresh on list screens
- "Try Again" option in dialogs
- Background sync retry

## Error Prevention

### Proactive Measures
- Validate data before API calls
- Check connectivity before operations
- Confirm destructive actions
- Prevent duplicate submissions
- Set reasonable timeouts
- Handle edge cases

### User Guidance
- Clear input requirements
- Format examples (phone, date)
- Character limits displayed
- Real-time validation feedback
- Helpful placeholder text

## Monitoring & Analytics

### Error Tracking
- Error frequency by type
- User impact metrics
- Device/OS error patterns
- Error trend analysis
- Critical error alerts

### Metrics
- Error rate per screen
- API failure rates
- Payment failure rate
- User-reported issues
- Session error count

## Testing Error Scenarios

### Manual Testing
- Airplane mode testing
- Server error simulation
- Invalid input testing
- Boundary value testing
- Payment failure scenarios

### Automated Testing
- Unit tests for error handlers
- Integration tests with mock errors
- Network error simulation
- Edge case coverage
- Error recovery testing

## Error Recovery

### Graceful Degradation
- Show cached data when offline
- Partial feature availability
- Queue operations for later
- Alternative workflows

### Data Integrity
- Transaction rollback on failure
- Orphaned data cleanup
- Consistency checks
- Data validation before save

## Support Integration

### Error Reporting
- "Report Issue" button on errors
- Auto-populate error context
- Screenshot attachment option
- User description field
- Priority routing for payment errors

### Support Contact
- In-app chat support
- Email support link
- Phone support number
- FAQ/Help section
- Known issues page
