class Vehicle {
  final int id;
  final String name;
  final String type;
  final double price;
  final bool isAvailable;
  final String? description;

  Vehicle({
    required this.id,
    required this.name,
    required this.type,
    required this.price,
    this.isAvailable = true,
    this.description,
  });

  factory Vehicle.fromJson(Map<String, dynamic> json) {
    return Vehicle(
      id: json['id'],
      name: json['name'] ?? '',
      type: json['type'] ?? '',
      price: double.parse(json['price'].toString()),
      isAvailable: json['is_available'] ?? true,
      description: json['description'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'type': type,
      'price': price,
      'is_available': isAvailable,
      'description': description,
    };
  }
}
