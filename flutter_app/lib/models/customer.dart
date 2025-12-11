class Customer {
  final int id;
  final String firstName;
  final String lastName;
  final String email;
  final String mobile;
  final DateTime? createdAt;

  Customer({
    required this.id,
    required this.firstName,
    required this.lastName,
    required this.email,
    required this.mobile,
    this.createdAt,
  });

  factory Customer.fromJson(Map<String, dynamic> json) {
    return Customer(
      id: json['id'],
      firstName: json['first_name'] ?? '',
      lastName: json['last_name'] ?? '',
      email: json['email'] ?? '',
      mobile: json['mobile'] ?? '',
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'first_name': firstName,
      'last_name': lastName,
      'email': email,
      'mobile': mobile,
      'created_at': createdAt?.toIso8601String(),
    };
  }

  String get fullName => '$firstName $lastName';
}
