import 'package:shared_preferences/shared_preferences.dart';

class StorageService {
  static SharedPreferences? _prefs;

  static Future<void> init() async {
    _prefs = await SharedPreferences.getInstance();
  }

  static Future<bool> saveToken(String token) async {
    return await _prefs?.setString('auth_token', token) ?? false;
  }

  static String? getToken() {
    return _prefs?.getString('auth_token');
  }

  static Future<bool> saveCustomerId(int id) async {
    return await _prefs?.setInt('customer_id', id) ?? false;
  }

  static int? getCustomerId() {
    return _prefs?.getInt('customer_id');
  }

  static Future<bool> saveCustomerData(Map<String, dynamic> data) async {
    return await _prefs?.setString('customer_data', data.toString()) ?? false;
  }

  static Future<bool> clearAll() async {
    return await _prefs?.clear() ?? false;
  }

  static bool isLoggedIn() {
    return getToken() != null;
  }
}
