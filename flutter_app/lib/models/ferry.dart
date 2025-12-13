class Ferry {
  final int id;
  final String name;
  final int capacity;
  final String? description;

  Ferry({
    required this.id,
    required this.name,
    required this.capacity,
    this.description,
  });

  factory Ferry.fromJson(Map<String, dynamic> json) {
    return Ferry(
      id: json['id'],
      name: json['name'] ?? '',
      capacity: json['capacity'] ?? 0,
      description: json['description'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'capacity': capacity,
      'description': description,
    };
  }
}
