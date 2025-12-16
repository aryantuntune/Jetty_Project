class ApiConfig {
  // Base URL - Change this to your actual API URL
  static const String baseUrl = 'https://unfurling.ninja/api';

  // API Endpoints
  static const String registerSendOtp = '/customer/generate-otp';
  static const String registerVerifyOtp = '/customer/verify-otp';
  static const String login = '/customer/login';
  static const String googleSignIn = '/customer/google-signin';
  static const String logout = '/customer/logout';
  static const String profile = '/customer/profile';

  // Password reset endpoints
  static const String forgotPasswordRequestOtp = '/customer/password-reset/request-otp';
  static const String forgotPasswordVerifyOtp = '/customer/password-reset/verify-otp';
  static const String resetPassword = '/customer/password-reset/reset';

  // Branch and ferry endpoints - UPDATED
  static const String branches = '/customer/branch';
  static String getFerries(int branchId) => '/customer/ferries/branch/$branchId';  // FIXED
  static String getToBranches(int branchId) => '/branches/$branchId/to-branches';  // FIXED
  
  // Item rates endpoint - UPDATED
  static String getItemRates(int branchId) => '/customer/rates/branch/$branchId';  // FIXED

  // Profile update endpoints
  static const String updateProfile = '/customer/profile';
  static const String uploadProfilePicture = '/customer/profile/upload-picture';

  // Booking endpoints
  static const String bookings = '/bookings';
  static String getBooking(int bookingId) => '/bookings/$bookingId';
  static String cancelBooking(int bookingId) => '/bookings/$bookingId/cancel';
  static const String bookingsSuccess = '/bookings/success';

  // Payment endpoints
  static const String razorpayCreateOrder = '/razorpay/order';
  static const String razorpayVerifyPayment = '/razorpay/verify';

  // Timeouts
  static const Duration connectTimeout = Duration(seconds: 30);
  static const Duration receiveTimeout = Duration(seconds: 30);

  // Headers
  static Map<String, String> get headers => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };

  static Map<String, String> headersWithToken(String token) => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Authorization': 'Bearer $token',
  };
}
