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
