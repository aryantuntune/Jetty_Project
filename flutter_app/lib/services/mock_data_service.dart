import '../models/customer.dart';
import '../models/branch.dart';
import '../models/ferry.dart';
import '../models/item_rate.dart';
import '../models/booking.dart';
import '../models/vehicle.dart';

class MockDataService {
  static Customer getMockCustomer() {
    return Customer(
      id: 1,
      firstName: 'John',
      lastName: 'Doe',
      email: 'john.doe@example.com',
      mobile: '1234567890',
      createdAt: DateTime.now(),
    );
  }

  static List<Branch> getMockBranches() {
    return [
      Branch(id: 1, name: 'Mumbai Gateway', location: 'Mumbai'),
      Branch(id: 2, name: 'Elephanta Island', location: 'Elephanta'),
      Branch(id: 3, name: 'Mandwa', location: 'Mandwa'),
      Branch(id: 4, name: 'Alibaug', location: 'Alibaug'),
    ];
  }

  static List<Branch> getMockDestinations(int fromBranchId) {
    final allBranches = getMockBranches();
    return allBranches.where((b) => b.id != fromBranchId).toList();
  }

  static List<Ferry> getMockFerries() {
    return [
      Ferry(id: 1, name: 'Sea Express', capacity: 100, description: 'Fast ferry service'),
      Ferry(id: 2, name: 'Ocean Liner', capacity: 150, description: 'Comfortable journey'),
      Ferry(id: 3, name: 'Wave Rider', capacity: 80, description: 'Budget friendly'),
    ];
  }

  static List<ItemRate> getMockItemRates() {
    return [
      ItemRate(id: 1, itemName: 'Adult Ticket', price: 250.0, description: 'Standard adult fare'),
      ItemRate(id: 2, itemName: 'Child Ticket', price: 150.0, description: 'For children under 12'),
      ItemRate(id: 3, itemName: 'Senior Citizen', price: 200.0, description: 'Senior citizen discount'),
    ];
  }

  static List<Vehicle> getMockVehicles() {
    return [
      Vehicle(id: 1, name: 'Motorcycle/Scooter', type: '2-Wheeler', price: 500.0, isAvailable: true, description: 'Two-wheeler vehicles'),
      Vehicle(id: 2, name: 'Car (Sedan)', type: '4-Wheeler', price: 1500.0, isAvailable: true, description: 'Standard cars'),
      Vehicle(id: 3, name: 'Car (SUV)', type: '4-Wheeler', price: 2000.0, isAvailable: true, description: 'SUV and large vehicles'),
      Vehicle(id: 4, name: 'Mini Truck', type: 'Commercial', price: 3000.0, isAvailable: true, description: 'Small commercial vehicles'),
      Vehicle(id: 5, name: 'Large Truck', type: 'Commercial', price: 5000.0, isAvailable: false, description: 'Heavy commercial vehicles'),
    ];
  }

  static List<Booking> getMockBookings() {
    return [
      Booking(
        id: 1,
        customerId: 1,
        ferryId: 1,
        fromBranchId: 1,
        toBranchId: 2,
        bookingDate: '2025-12-15',
        departureTime: '09:00:00',
        totalAmount: 650.0,
        status: 'confirmed',
        qrCode: 'JETTY-BOOKING-001',
        createdAt: DateTime.now().subtract(const Duration(days: 2)),
      ),
      Booking(
        id: 2,
        customerId: 1,
        ferryId: 2,
        fromBranchId: 1,
        toBranchId: 3,
        bookingDate: '2025-12-20',
        departureTime: '14:30:00',
        totalAmount: 800.0,
        status: 'pending',
        qrCode: 'JETTY-BOOKING-002',
        createdAt: DateTime.now().subtract(const Duration(days: 1)),
      ),
      Booking(
        id: 3,
        customerId: 1,
        ferryId: 3,
        fromBranchId: 2,
        toBranchId: 1,
        bookingDate: '2025-12-10',
        departureTime: '11:00:00',
        totalAmount: 400.0,
        status: 'cancelled',
        qrCode: 'JETTY-BOOKING-003',
        createdAt: DateTime.now().subtract(const Duration(days: 5)),
      ),
    ];
  }

  static Booking? getMockBookingById(int id) {
    try {
      return getMockBookings().firstWhere((b) => b.id == id);
    } catch (e) {
      return null;
    }
  }
}
