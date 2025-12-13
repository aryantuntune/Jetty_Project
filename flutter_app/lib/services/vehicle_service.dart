import '../config/api_config.dart';
import '../config/app_config.dart';
import '../models/api_response.dart';
import '../models/vehicle.dart';
import 'api_service.dart';
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

    return await ApiService.get<List<Vehicle>>(
      '${ApiConfig.baseUrl}/vehicles',
      fromJson: (data) => (data as List).map((item) => Vehicle.fromJson(item)).toList(),
    );
  }
}
