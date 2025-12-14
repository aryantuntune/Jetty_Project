import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import '../models/api_response.dart';
import 'storage_service.dart';

class ApiService {
  static Future<ApiResponse<T>> get<T>(
    String endpoint, {
    bool requiresAuth = true,
    T? Function(dynamic)? fromJson,
  }) async {
    try {
      final url = Uri.parse('${ApiConfig.baseUrl}$endpoint');
      final headers = await _getHeaders(requiresAuth);

      final response = await http.get(url, headers: headers);
      return _handleResponse<T>(response, fromJson);
    } catch (e) {
      return ApiResponse<T>(
        success: false,
        message: 'Network error: ${e.toString()}',
      );
    }
  }

  static Future<ApiResponse<T>> post<T>(
    String endpoint, {
    Map<String, dynamic>? body,
    bool requiresAuth = true,
    T? Function(dynamic)? fromJson,
  }) async {
    try {
      final url = Uri.parse('${ApiConfig.baseUrl}$endpoint');
      final baseHeaders = await _getHeaders(requiresAuth);

      print('🌐 API POST Request:');
      print('   URL: $url');
      print('   Body: $body');
      print('   RequiresAuth: $requiresAuth');

      // Send as JSON to properly handle arrays and nested objects
      final response = await http.post(
        url,
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          if (requiresAuth && baseHeaders.containsKey('Authorization'))
            'Authorization': baseHeaders['Authorization']!,
        },
        body: body != null ? jsonEncode(body) : null,
      );

      print('📥 API Response:');
      print('   Status: ${response.statusCode}');
      print('   Body: ${response.body}');

      return _handleResponse<T>(response, fromJson);
    } catch (e) {
      print('❌ API Error: $e');
      return ApiResponse<T>(
        success: false,
        message: 'Network error: ${e.toString()}',
      );
    }
  }

  static Future<ApiResponse<T>> put<T>(
    String endpoint, {
    Map<String, dynamic>? body,
    bool requiresAuth = true,
    T? Function(dynamic)? fromJson,
  }) async {
    try {
      final url = Uri.parse('${ApiConfig.baseUrl}$endpoint');
      final baseHeaders = await _getHeaders(requiresAuth);

      print('🌐 API PUT Request:');
      print('   URL: $url');
      print('   Body: $body');
      print('   RequiresAuth: $requiresAuth');

      final response = await http.put(
        url,
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          if (requiresAuth && baseHeaders.containsKey('Authorization'))
            'Authorization': baseHeaders['Authorization']!,
        },
        body: body != null ? jsonEncode(body) : null,
      );

      print('📥 API Response:');
      print('   Status: ${response.statusCode}');
      print('   Body: ${response.body}');

      return _handleResponse<T>(response, fromJson);
    } catch (e) {
      print('❌ API Error: $e');
      return ApiResponse<T>(
        success: false,
        message: 'Network error: ${e.toString()}',
      );
    }
  }

  static Future<Map<String, String>> _getHeaders(bool requiresAuth) async {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    if (requiresAuth) {
      final token = StorageService.getToken();
      if (token != null) {
        headers['Authorization'] = 'Bearer $token';
      }
    }

    return headers;
  }

  static ApiResponse<T> _handleResponse<T>(
    http.Response response,
    T? Function(dynamic)? fromJson,
  ) {
    try {
      final data = jsonDecode(response.body);

      if (response.statusCode >= 200 && response.statusCode < 300) {
        return ApiResponse<T>.fromJson(data, fromJson);
      } else {
        return ApiResponse<T>(
          success: false,
          message: data['message'] ?? 'Request failed',
          errors: data['errors'] != null ? List<String>.from(data['errors']) : null,
        );
      }
    } catch (e) {
      return ApiResponse<T>(
        success: false,
        message: 'Failed to parse response: ${e.toString()}',
      );
    }
  }
}
