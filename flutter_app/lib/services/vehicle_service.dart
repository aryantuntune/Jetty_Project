import '../config/app_config.dart';
import '../models/api_response.dart';
import '../models/vehicle.dart';
import 'mock_data_service.dart';

class VehicleService {
  static Future<ApiResponse<List<Vehicle>>> getVehicles() async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(milliseconds: 500));
      return ApiResponse<List<Vehicle>>(
        success: true,
        message: 'Vehicles loaded (Mock)',
        data: MockDataService.getMockVehicles(),
      );
    }

    // Return empty list since vehicles come from item_rates API
    // The app should use item_rates with category_id=2 for vehicles
    return ApiResponse<List<Vehicle>>(
      success: true,
      message: 'Vehicles not available - use item rates instead',
      data: [],
    );
  }
}
