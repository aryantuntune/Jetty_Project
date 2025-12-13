class AppConstants {
  // App Information
  static const String appName = 'Jetty Ferry Booking';
  static const String appVersion = '1.0.0';

  // Storage Keys
  static const String keyAuthToken = 'auth_token';
  static const String keyCustomerId = 'customer_id';
  static const String keyCustomerEmail = 'customer_email';
  static const String keyCustomerName = 'customer_name';
  static const String keyIsLoggedIn = 'is_logged_in';

  // Date Formats
  static const String dateFormat = 'yyyy-MM-dd';
  static const String timeFormat = 'HH:mm';
  static const String displayDateFormat = 'dd MMM yyyy';
  static const String displayTimeFormat = 'hh:mm a';

  // Booking Status
  static const String statusConfirmed = 'confirmed';
  static const String statusCancelled = 'cancelled';
  static const String statusCompleted = 'completed';

  // Payment Status
  static const String paymentPending = 'pending';
  static const String paymentPaid = 'paid';
  static const String paymentFailed = 'failed';

  // Payment Methods
  static const String paymentOnline = 'online';
  static const String paymentCash = 'cash';

  // Messages
  static const String msgNetworkError = 'Network error. Please check your internet connection.';
  static const String msgServerError = 'Server error. Please try again later.';
  static const String msgUnknownError = 'An unknown error occurred.';
  static const String msgSessionExpired = 'Session expired. Please login again.';

  // Validation
  static const int minPasswordLength = 6;
  static const int mobileNumberLength = 10;
  static const int otpLength = 6;
}
