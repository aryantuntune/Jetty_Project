import '../config/api_config.dart';
import '../config/app_config.dart';
import '../models/api_response.dart';
import '../models/customer.dart';
import 'api_service.dart';
import 'storage_service.dart';
import 'mock_data_service.dart';

class AuthService {
  static Future<ApiResponse> sendOTP({
    required String firstName,
    required String lastName,
    required String email,
    required String mobile,
    required String password,
  }) async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(seconds: 1));
      return ApiResponse(
        success: true,
        message: 'OTP sent successfully (Mock)',
      );
    }

    return await ApiService.post(
      ApiConfig.registerSendOtp,
      body: {
        'first_name': firstName,
        'last_name': lastName,
        'email': email,
        'mobile': mobile,
        'password': password,
      },
      requiresAuth: false,
    );
  }

  static Future<ApiResponse<Map<String, dynamic>>> verifyOTP({
    required String email,
    required String otp,
  }) async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(seconds: 1));

      final customer = MockDataService.getMockCustomer();
      await StorageService.saveToken('mock-token-12345');
      await StorageService.saveCustomerId(customer.id);

      return ApiResponse<Map<String, dynamic>>(
        success: true,
        message: 'Registration successful (Mock)',
        data: {
          'token': 'mock-token-12345',
          'customer': customer.toJson(),
        },
      );
    }

    final response = await ApiService.post<Map<String, dynamic>>(
      ApiConfig.registerVerifyOtp,
      body: {
        'email': email,
        'otp': otp,
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
  }

  static Future<ApiResponse<Map<String, dynamic>>> login({
    required String email,
    required String password,
  }) async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(seconds: 1));

      final customer = MockDataService.getMockCustomer();
      await StorageService.saveToken('mock-token-12345');
      await StorageService.saveCustomerId(customer.id);

      return ApiResponse<Map<String, dynamic>>(
        success: true,
        message: 'Login successful (Mock)',
        data: {
          'token': 'mock-token-12345',
          'customer': customer.toJson(),
        },
      );
    }

    final response = await ApiService.post<Map<String, dynamic>>(
      ApiConfig.login,
      body: {
        'email': email,
        'password': password,
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
  }

  static Future<ApiResponse> logout() async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(milliseconds: 500));
      await StorageService.clearAll();
      return ApiResponse(success: true, message: 'Logged out (Mock)');
    }

    final response = await ApiService.post(ApiConfig.logout);

    if (response.success) {
      await StorageService.clearAll();
    }

    return response;
  }

  static Future<ApiResponse<Customer>> getProfile() async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(milliseconds: 500));
      return ApiResponse<Customer>(
        success: true,
        message: 'Profile loaded (Mock)',
        data: MockDataService.getMockCustomer(),
      );
    }

    return await ApiService.get<Customer>(
      ApiConfig.profile,
      fromJson: (data) => Customer.fromJson(data),
    );
  }

  // Password Reset Methods
  static Future<ApiResponse> requestPasswordResetOTP({
    required String email,
  }) async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(seconds: 1));
      return ApiResponse(
        success: true,
        message: 'OTP sent successfully (Mock)',
      );
    }

    return await ApiService.post(
      ApiConfig.forgotPasswordRequestOtp,
      body: {
        'email': email,
      },
      requiresAuth: false,
    );
  }

  static Future<ApiResponse> verifyPasswordResetOTP({
    required String email,
    required String otp,
  }) async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(seconds: 1));
      return ApiResponse(
        success: true,
        message: 'OTP verified successfully (Mock)',
      );
    }

    return await ApiService.post(
      ApiConfig.forgotPasswordVerifyOtp,
      body: {
        'email': email,
        'otp': otp,
      },
      requiresAuth: false,
    );
  }

  static Future<ApiResponse> resetPassword({
    required String email,
    required String otp,
    required String password,
  }) async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(seconds: 1));
      return ApiResponse(
        success: true,
        message: 'Password reset successfully (Mock)',
      );
    }

    return await ApiService.post(
      ApiConfig.resetPassword,
      body: {
        'email': email,
        'otp': otp,
        'password': password,
      },
      requiresAuth: false,
    );
  }

  static Future<ApiResponse<Customer>> updateProfile({    required String firstName,    required String lastName,    required String mobile,  }) async {    if (AppConfig.useMockData) {      await Future.delayed(const Duration(milliseconds: 500));      return ApiResponse<Customer>(        success: true,        message: 'Profile updated successfully (Mock)',        data: MockDataService.getMockCustomer(),      );    }
    final response = await ApiService.put<Customer>(      ApiConfig.updateProfile,      body: {        'first_name': firstName,        'last_name': lastName,        'mobile': mobile,      },      fromJson: (data) => Customer.fromJson(data),    );
    return response;  }
  static bool isLoggedIn() {
    return StorageService.isLoggedIn();
  }
}
