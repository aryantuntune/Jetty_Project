class ItemRate {
  final int id;
  final String itemName;
  final double price;
  final String? description;

  ItemRate({
    required this.id,
    required this.itemName,
    required this.price,
    this.description,
  });

  factory ItemRate.fromJson(Map<String, dynamic> json) {
    return ItemRate(
      id: json['id'],
      itemName: json['item_name'] ?? '',
      price: double.parse(json['price'].toString()),
      description: json['description'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'item_name': itemName,
      'price': price,
      'description': description,
    };
  }
}
