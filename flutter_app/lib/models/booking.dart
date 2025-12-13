class BookingItem {
  final int id;
  final int bookingId;
  final int itemRateId;
  final int quantity;
  final double price;
  final double subtotal;
  final String? itemName;

  BookingItem({
    required this.id,
    required this.bookingId,
    required this.itemRateId,
    required this.quantity,
    required this.price,
    required this.subtotal,
    this.itemName,
  });

  factory BookingItem.fromJson(Map<String, dynamic> json) {
    return BookingItem(
      id: json['id'],
      bookingId: json['booking_id'],
      itemRateId: json['item_rate_id'],
      quantity: json['quantity'],
      price: double.parse(json['price'].toString()),
      subtotal: double.parse(json['subtotal'].toString()),
      itemName: json['item_name'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'booking_id': bookingId,
      'item_rate_id': itemRateId,
      'quantity': quantity,
      'price': price,
      'subtotal': subtotal,
      'item_name': itemName,
    };
  }
}

class Booking {
  final int id;
  final int customerId;
  final int ferryId;
  final int fromBranchId;
  final int toBranchId;
  final String bookingDate;
  final String departureTime;
  final double totalAmount;
  final String status;
  final String? qrCode;
  final List<BookingItem>? items;
  final DateTime? createdAt;

  Booking({
    required this.id,
    required this.customerId,
    required this.ferryId,
    required this.fromBranchId,
    required this.toBranchId,
    required this.bookingDate,
    required this.departureTime,
    required this.totalAmount,
    required this.status,
    this.qrCode,
    this.items,
    this.createdAt,
  });

  factory Booking.fromJson(Map<String, dynamic> json) {
    return Booking(
      id: json['id'],
      customerId: json['customer_id'],
      ferryId: json['ferry_id'],
      fromBranchId: json['from_branch_id'],
      toBranchId: json['to_branch_id'],
      bookingDate: json['booking_date'] ?? '',
      departureTime: json['departure_time'] ?? '',
      totalAmount: double.parse(json['total_amount'].toString()),
      status: json['status'] ?? 'pending',
      qrCode: json['qr_code'],
      items: json['items'] != null
          ? (json['items'] as List).map((item) => BookingItem.fromJson(item)).toList()
          : null,
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'customer_id': customerId,
      'ferry_id': ferryId,
      'from_branch_id': fromBranchId,
      'to_branch_id': toBranchId,
      'booking_date': bookingDate,
      'departure_time': departureTime,
      'total_amount': totalAmount,
      'status': status,
      'qr_code': qrCode,
      'items': items != null ? items!.map((item) => item.toJson()).toList() : null,
      'created_at': createdAt?.toIso8601String(),
    };
  }

  bool get isActive => status == 'confirmed' || status == 'pending';
  bool get isCancelled => status == 'cancelled';
}
