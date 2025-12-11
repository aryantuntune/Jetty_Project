import '../config/api_config.dart';
import '../config/app_config.dart';
import '../models/api_response.dart';
import '../models/branch.dart';
import '../models/ferry.dart';
import '../models/item_rate.dart';
import '../models/booking.dart';
import 'api_service.dart';
import 'mock_data_service.dart';

class BookingService {
  static Future<ApiResponse<List<Branch>>> getBranches() async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(milliseconds: 500));
      return ApiResponse<List<Branch>>(
        success: true,
        message: 'Branches loaded (Mock)',
        data: MockDataService.getMockBranches(),
      );
    }

    return await ApiService.get<List<Branch>>(
      ApiConfig.branches,
      fromJson: (data) => (data as List).map((item) => Branch.fromJson(item)).toList(),
    );
  }

  static Future<ApiResponse<List<Branch>>> getDestinations(int branchId) async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(milliseconds: 500));
      return ApiResponse<List<Branch>>(
        success: true,
        message: 'Destinations loaded (Mock)',
        data: MockDataService.getMockDestinations(branchId),
      );
    }

    // Note: This method returns destinations but the API might return routes
    // If API returns route objects, you may need to adjust the parsing
    return await ApiService.get<List<Branch>>(
      '${ApiConfig.branches}/$branchId/destinations',
      fromJson: (data) => (data as List).map((item) => Branch.fromJson(item)).toList(),
    );
  }

  static Future<ApiResponse> getRoutes(int fromBranchId, int toBranchId) async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(milliseconds: 500));
      return ApiResponse(
        success: true,
        message: 'Routes loaded (Mock)',
        data: MockDataService.getMockDestinations(fromBranchId),
      );
    }

    return await ApiService.get(
      ApiConfig.getRoutes(fromBranchId, toBranchId),
      fromJson: (data) => data,
    );
  }

  static Future<ApiResponse<List<Ferry>>> getFerries({int? branchId}) async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(milliseconds: 500));
      return ApiResponse<List<Ferry>>(
        success: true,
        message: 'Ferries loaded (Mock)',
        data: MockDataService.getMockFerries(),
      );
    }

    // If branchId is provided, use the branch-specific endpoint
    final endpoint = branchId != null
        ? ApiConfig.getFerries(branchId)
        : ApiConfig.branches; // fallback if no branchId

    return await ApiService.get<List<Ferry>>(
      endpoint,
      fromJson: (data) => (data as List).map((item) => Ferry.fromJson(item)).toList(),
    );
  }

  static Future<ApiResponse<List<ItemRate>>> getItemRates({required int branchId}) async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(milliseconds: 500));
      return ApiResponse<List<ItemRate>>(
        success: true,
        message: 'Item rates loaded (Mock)',
        data: MockDataService.getMockItemRates(),
      );
    }

    return await ApiService.get<List<ItemRate>>(
      ApiConfig.getItemRates(branchId),
      fromJson: (data) => (data as List).map((item) => ItemRate.fromJson(item)).toList(),
    );
  }

  static Future<ApiResponse<Booking>> createBooking({
    required int ferryId,
    required int fromBranchId,
    required int toBranchId,
    required String bookingDate,
    required String departureTime,
    required List<Map<String, dynamic>> items,
  }) async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(milliseconds: 500));

      double totalAmount = 0;
      final itemRates = MockDataService.getMockItemRates();
      for (var item in items) {
        final itemRate = itemRates.firstWhere((ir) => ir.id == item['item_rate_id']);
        totalAmount += itemRate.price * (item['quantity'] as int);
      }

      final newBooking = Booking(
        id: DateTime.now().millisecondsSinceEpoch,
        customerId: 1,
        ferryId: ferryId,
        fromBranchId: fromBranchId,
        toBranchId: toBranchId,
        bookingDate: bookingDate,
        departureTime: departureTime,
        totalAmount: totalAmount,
        status: 'confirmed',
        qrCode: 'JETTY-MOCK-${DateTime.now().millisecondsSinceEpoch}',
        createdAt: DateTime.now(),
      );

      return ApiResponse<Booking>(
        success: true,
        message: 'Booking created successfully (Mock)',
        data: newBooking,
      );
    }

    return await ApiService.post<Booking>(
      ApiConfig.bookings,
      body: {
        'ferry_id': ferryId,
        'from_branch_id': fromBranchId,
        'to_branch_id': toBranchId,
        'booking_date': bookingDate,
        'departure_time': departureTime,
        'items': items,
      },
      fromJson: (data) => Booking.fromJson(data),
    );
  }

  static Future<ApiResponse<List<Booking>>> getBookings() async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(milliseconds: 500));
      return ApiResponse<List<Booking>>(
        success: true,
        message: 'Bookings loaded (Mock)',
        data: MockDataService.getMockBookings(),
      );
    }

    return await ApiService.get<List<Booking>>(
      ApiConfig.bookings,
      fromJson: (data) => (data as List).map((item) => Booking.fromJson(item)).toList(),
    );
  }

  static Future<ApiResponse<Booking>> getBookingById(int id) async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(milliseconds: 500));
      final booking = MockDataService.getMockBookingById(id);

      if (booking != null) {
        return ApiResponse<Booking>(
          success: true,
          message: 'Booking loaded (Mock)',
          data: booking,
        );
      } else {
        return ApiResponse<Booking>(
          success: false,
          message: 'Booking not found',
        );
      }
    }

    return await ApiService.get<Booking>(
      '${ApiConfig.bookings}/$id',
      fromJson: (data) => Booking.fromJson(data),
    );
  }

  static Future<ApiResponse> cancelBooking(int id) async {
    if (AppConfig.useMockData) {
      await Future.delayed(const Duration(seconds: 1));
      return ApiResponse(
        success: true,
        message: 'Booking cancelled successfully (Mock)',
      );
    }

    return await ApiService.post('${ApiConfig.bookings}/$id/cancel');
  }
}
