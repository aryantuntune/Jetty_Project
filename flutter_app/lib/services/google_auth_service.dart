import 'package:google_sign_in/google_sign_in.dart';
import '../config/app_config.dart';
import '../config/api_config.dart';
import '../models/api_response.dart';
import '../models/customer.dart';
import 'storage_service.dart';
import 'api_service.dart';

class GoogleAuthService {
  static final GoogleSignIn _googleSignIn = GoogleSignIn(
    scopes: ['email', 'profile'],
  );

  /// Sign in with Google
  static Future<ApiResponse<Map<String, dynamic>>> signInWithGoogle() async {
    try {
      // In mock mode, simulate Google Sign-In
      if (AppConfig.useMockData) {
        await Future.delayed(const Duration(seconds: 1));

        final customer = Customer(
          id: 999,
          firstName: 'Google',
          lastName: 'User',
          email: 'google.user@example.com',
          mobile: '9876543210',
        );

        await StorageService.saveToken('mock-google-token-${DateTime.now().millisecondsSinceEpoch}');
        await StorageService.saveCustomerId(customer.id);

        return ApiResponse<Map<String, dynamic>>(
          success: true,
          message: 'Google Sign-In successful (Mock)',
          data: {
            'token': 'mock-google-token',
            'customer': customer.toJson(),
          },
        );
      }

      // Real Google Sign-In
      final GoogleSignInAccount? googleUser = await _googleSignIn.signIn();

      if (googleUser == null) {
        // User cancelled the sign-in
        return ApiResponse<Map<String, dynamic>>(
          success: false,
          message: 'Google Sign-In cancelled',
        );
      }

      final GoogleSignInAuthentication googleAuth = await googleUser.authentication;

      // Send Google user data to Laravel backend
      final nameParts = googleUser.displayName?.split(' ') ?? ['User'];
      final firstName = nameParts.first;
      final lastName = nameParts.length > 1 ? nameParts.sublist(1).join(' ') : '';

      final response = await ApiService.post<Map<String, dynamic>>(
        ApiConfig.googleSignIn,
        body: {
          'id_token': googleAuth.idToken,
          'email': googleUser.email,
          'first_name': firstName,
          'last_name': lastName,
        },
        requiresAuth: false,
        fromJson: (data) => data as Map<String, dynamic>,
      );

      if (response.success && response.data != null) {
        final token = response.data!['token'];
        final customer = Customer.fromJson(response.data!['customer']);

        await StorageService.saveToken(token);
        await StorageService.saveCustomerId(customer.id);
      }

      return response;

    } catch (e) {
      return ApiResponse<Map<String, dynamic>>(
        success: false,
        message: 'Google Sign-In failed: ${e.toString()}',
      );
    }
  }

  /// Sign out from Google
  static Future<void> signOut() async {
    try {
      await _googleSignIn.signOut();
    } catch (e) {
      // Silent fail
    }
  }

  /// Check if user is signed in with Google
  static Future<bool> isSignedIn() async {
    return await _googleSignIn.isSignedIn();
  }
}
