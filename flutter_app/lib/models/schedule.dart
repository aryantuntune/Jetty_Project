class Schedule {
  final int id;
  final int ferryId;
  final int fromBranchId;
  final int toBranchId;
  final String departureTime;
  final String? arrivalTime;
  final double price;

  Schedule({
    required this.id,
    required this.ferryId,
    required this.fromBranchId,
    required this.toBranchId,
    required this.departureTime,
    this.arrivalTime,
    required this.price,
  });

  factory Schedule.fromJson(Map<String, dynamic> json) {
    return Schedule(
      id: json['id'],
      ferryId: json['ferry_id'],
      fromBranchId: json['from_branch_id'],
      toBranchId: json['to_branch_id'],
      departureTime: json['departure_time'] ?? '',
      arrivalTime: json['arrival_time'],
      price: double.parse(json['price'].toString()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'ferry_id': ferryId,
      'from_branch_id': fromBranchId,
      'to_branch_id': toBranchId,
      'departure_time': departureTime,
      'arrival_time': arrivalTime,
      'price': price,
    };
  }
}
