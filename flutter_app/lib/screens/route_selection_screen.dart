import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../config/app_colors.dart';
import '../models/branch.dart';
import '../models/ferry.dart';
import '../models/item_rate.dart';
import '../models/vehicle.dart';
import '../services/booking_service.dart';
import '../services/vehicle_service.dart';
import '../widgets/custom_button.dart';
import '../widgets/loading_overlay.dart';
import 'booking_success_screen.dart';

class RouteSelectionScreen extends StatefulWidget {
  const RouteSelectionScreen({Key? key}) : super(key: key);

  @override
  State<RouteSelectionScreen> createState() => _RouteSelectionScreenState();
}

class _RouteSelectionScreenState extends State<RouteSelectionScreen> {
  List<Branch> _branches = [];
  List<Branch> _destinations = [];
  List<Ferry> _ferries = [];
  List<ItemRate> _passengerTypes = [];
  List<Vehicle> _vehicles = [];

  Branch? _selectedFromBranch;
  Branch? _selectedToBranch;
  Ferry? _selectedFerry;
  DateTime? _selectedDate;
  TimeOfDay? _selectedTime;

  int _adultCount = 0;
  int _childCount = 0;
  int _seniorCount = 0;
  Vehicle? _selectedVehicle;

  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);

    final branchesResponse = await BookingService.getBranches();

    setState(() {
      if (branchesResponse.success && branchesResponse.data != null) {
        _branches = branchesResponse.data!;
      }
      _isLoading = false;
    });
  }

  Future<void> _loadFerriesAndRates(int branchId) async {
    setState(() => _isLoading = true);

    final ferriesResponse = await BookingService.getFerries(branchId: branchId);
    final itemsResponse = await BookingService.getItemRates(branchId: branchId);
    final vehiclesResponse = await VehicleService.getVehicles();

    setState(() {
      if (ferriesResponse.success && ferriesResponse.data != null) {
        _ferries = ferriesResponse.data!;
      }
      if (itemsResponse.success && itemsResponse.data != null) {
        _passengerTypes = itemsResponse.data!;
      }
      if (vehiclesResponse.success && vehiclesResponse.data != null) {
        _vehicles = vehiclesResponse.data!.where((v) => v.isAvailable).toList();
      }
      _isLoading = false;
    });
  }

  Future<void> _loadDestinations(int branchId) async {
    final response = await BookingService.getDestinations(branchId);
    if (response.success && response.data != null) {
      setState(() => _destinations = response.data!);
    }
  }

  Future<void> _selectDate() async {
    final date = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 90)),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: AppColors.primary,
              onPrimary: Colors.white,
              surface: Colors.white,
              onSurface: AppColors.textPrimary,
            ),
          ),
          child: child!,
        );
      },
    );
    if (date != null) {
      setState(() => _selectedDate = date);
    }
  }

  Future<void> _selectTime() async {
    final time = await showTimePicker(
      context: context,
      initialTime: TimeOfDay.now(),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: AppColors.primary,
              onPrimary: Colors.white,
              surface: Colors.white,
              onSurface: AppColors.textPrimary,
            ),
          ),
          child: child!,
        );
      },
    );
    if (time != null) {
      setState(() => _selectedTime = time);
    }
  }

  double _calculateTotal() {
    double total = 0;

    // Passengers
    if (_passengerTypes.isNotEmpty) {
      final adult = _passengerTypes.firstWhere((p) => p.itemName.contains('Adult'));
      final child = _passengerTypes.firstWhere((p) => p.itemName.contains('Child'));
      final senior = _passengerTypes.firstWhere((p) => p.itemName.contains('Senior'));

      total += adult.price * _adultCount;
      total += child.price * _childCount;
      total += senior.price * _seniorCount;
    }

    // Vehicle
    if (_selectedVehicle != null) {
      total += _selectedVehicle!.price;
    }

    return total;
  }

  Future<void> _handleBooking() async {
    if (_selectedFromBranch == null ||
        _selectedToBranch == null ||
        _selectedFerry == null ||
        _selectedDate == null ||
        _selectedTime == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please fill all required fields')),
      );
      return;
    }

    if (_adultCount == 0 && _childCount == 0 && _seniorCount == 0 && _selectedVehicle == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select at least one passenger or vehicle')),
      );
      return;
    }

    setState(() => _isLoading = true);

    final items = <Map<String, dynamic>>[];

    // Add passengers
    if (_adultCount > 0) {
      items.add({'item_rate_id': 1, 'quantity': _adultCount});
    }
    if (_childCount > 0) {
      items.add({'item_rate_id': 2, 'quantity': _childCount});
    }
    if (_seniorCount > 0) {
      items.add({'item_rate_id': 3, 'quantity': _seniorCount});
    }

    // Add vehicle
    if (_selectedVehicle != null) {
      items.add({'item_rate_id': _selectedVehicle!.id + 10, 'quantity': 1});
    }

    final response = await BookingService.createBooking(
      ferryId: _selectedFerry!.id,
      fromBranchId: _selectedFromBranch!.id,
      toBranchId: _selectedToBranch!.id,
      bookingDate: DateFormat('yyyy-MM-dd').format(_selectedDate!),
      departureTime: '${_selectedTime!.hour.toString().padLeft(2, '0')}:${_selectedTime!.minute.toString().padLeft(2, '0')}:00',
      items: items,
    );

    setState(() => _isLoading = false);

    if (!mounted) return;

    if (response.success && response.data != null) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder: (context) => BookingSuccessScreen(booking: response.data!),
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(response.message),
          backgroundColor: AppColors.error,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        elevation: 0,
        title: const Text('Book Ferry Ticket'),
      ),
      body: LoadingOverlay(
        isLoading: _isLoading,
        message: 'Processing booking...',
        child: SingleChildScrollView(
          child: Column(
            children: [
              // Header with gradient
              Container(
                width: double.infinity,
                padding: const EdgeInsets.fromLTRB(24, 0, 24, 24),
                decoration: const BoxDecoration(
                  gradient: AppColors.primaryGradient,
                  borderRadius: BorderRadius.only(
                    bottomLeft: Radius.circular(32),
                    bottomRight: Radius.circular(32),
                  ),
                ),
                child: Column(
                  children: [
                    const Icon(Icons.directions_boat, size: 48, color: Colors.white),
                    const SizedBox(height: 12),
                    Text(
                      'Select Your Route',
                      style: TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                        color: Colors.white.withOpacity(0.95),
                      ),
                    ),
                  ],
                ),
              ),

              Padding(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Route Details Card
                    _buildModernCard(
                      title: 'Route Details',
                      icon: Icons.route_rounded,
                      child: Column(
                        children: [
                          _buildModernDropdown<Branch>(
                            value: _selectedFromBranch,
                            label: 'From',
                            hint: 'Select starting point',
                            icon: Icons.my_location_rounded,
                            items: _branches,
                            itemBuilder: (branch) => branch.name,
                            onChanged: (branch) {
                              setState(() {
                                _selectedFromBranch = branch;
                                _selectedToBranch = null;
                                _destinations = [];
                                _ferries = [];
                                _passengerTypes = [];
                              });
                              if (branch != null) {
                                _loadDestinations(branch.id);
                                _loadFerriesAndRates(branch.id);
                              }
                            },
                          ),
                          const SizedBox(height: 20),
                          _buildModernDropdown<Branch>(
                            value: _selectedToBranch,
                            label: 'To',
                            hint: 'Select destination',
                            icon: Icons.location_on_rounded,
                            items: _destinations,
                            itemBuilder: (branch) => branch.name,
                            onChanged: (branch) {
                              setState(() => _selectedToBranch = branch);
                            },
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 20),

                    // Ferry & Schedule Card
                    _buildModernCard(
                      title: 'Ferry & Schedule',
                      icon: Icons.calendar_today_rounded,
                      child: Column(
                        children: [
                          _buildModernDropdown<Ferry>(
                            value: _selectedFerry,
                            label: 'Select Ferry',
                            hint: 'Choose ferry',
                            icon: Icons.directions_boat_rounded,
                            items: _ferries,
                            itemBuilder: (ferry) => '${ferry.name} (${ferry.capacity} seats)',
                            onChanged: (ferry) {
                              setState(() => _selectedFerry = ferry);
                            },
                          ),
                          const SizedBox(height: 20),
                          Row(
                            children: [
                              Expanded(
                                child: _buildDateTimeSelector(
                                  label: 'Date',
                                  value: _selectedDate != null
                                      ? DateFormat('dd MMM yyyy').format(_selectedDate!)
                                      : 'Select Date',
                                  icon: Icons.calendar_month_rounded,
                                  onTap: _selectDate,
                                ),
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: _buildDateTimeSelector(
                                  label: 'Time',
                                  value: _selectedTime != null
                                      ? _selectedTime!.format(context)
                                      : 'Select Time',
                                  icon: Icons.access_time_rounded,
                                  onTap: _selectTime,
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 20),

                    // Passengers Card
                    _buildModernCard(
                      title: 'Passengers',
                      icon: Icons.people_rounded,
                      child: Column(
                        children: [
                          _buildPassengerCounter(
                            'Adults',
                            '₹${_passengerTypes.isNotEmpty ? _passengerTypes[0].price.toStringAsFixed(0) : '0'}',
                            Icons.person_rounded,
                            _adultCount,
                            (value) => setState(() => _adultCount = value),
                          ),
                          const SizedBox(height: 16),
                          _buildPassengerCounter(
                            'Children',
                            '₹${_passengerTypes.length > 1 ? _passengerTypes[1].price.toStringAsFixed(0) : '0'}',
                            Icons.child_care_rounded,
                            _childCount,
                            (value) => setState(() => _childCount = value),
                          ),
                          const SizedBox(height: 16),
                          _buildPassengerCounter(
                            'Senior Citizens',
                            '₹${_passengerTypes.length > 2 ? _passengerTypes[2].price.toStringAsFixed(0) : '0'}',
                            Icons.elderly_rounded,
                            _seniorCount,
                            (value) => setState(() => _seniorCount = value),
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 20),

                    // Vehicle Card
                    _buildModernCard(
                      title: 'Vehicle (Optional)',
                      icon: Icons.directions_car_rounded,
                      child: _buildVehicleSelector(),
                    ),

                    const SizedBox(height: 24),

                    // Total Amount Card
                    Container(
                      padding: const EdgeInsets.all(24),
                      decoration: BoxDecoration(
                        gradient: AppColors.primaryGradient,
                        borderRadius: BorderRadius.circular(20),
                        boxShadow: [
                          BoxShadow(
                            color: AppColors.primary.withOpacity(0.3),
                            blurRadius: 15,
                            offset: const Offset(0, 8),
                          ),
                        ],
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Total Amount',
                                style: TextStyle(
                                  fontSize: 14,
                                  color: Colors.white70,
                                ),
                              ),
                              SizedBox(height: 4),
                              Text(
                                'Payable at counter',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Colors.white60,
                                ),
                              ),
                            ],
                          ),
                          Text(
                            '₹${_calculateTotal().toStringAsFixed(2)}',
                            style: const TextStyle(
                              fontSize: 32,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                            ),
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 24),

                    // Confirm Button
                    CustomButton(
                      text: 'Confirm Booking',
                      onPressed: _handleBooking,
                      isLoading: _isLoading,
                    ),

                    const SizedBox(height: 20),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildModernCard({
    required String title,
    required IconData icon,
    required Widget child,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.all(20),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: AppColors.primary.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(icon, color: AppColors.primary, size: 24),
                ),
                const SizedBox(width: 12),
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: AppColors.textPrimary,
                  ),
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 0, 20, 20),
            child: child,
          ),
        ],
      ),
    );
  }

  Widget _buildModernDropdown<T>({
    required T? value,
    required String label,
    required String hint,
    required IconData icon,
    required List<T> items,
    required String Function(T) itemBuilder,
    required void Function(T?) onChanged,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w600,
            color: AppColors.textSecondary,
          ),
        ),
        const SizedBox(height: 8),
        Container(
          decoration: BoxDecoration(
            color: AppColors.background,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: AppColors.divider),
          ),
          child: DropdownButtonFormField<T>(
            value: value,
            decoration: InputDecoration(
              prefixIcon: Icon(icon, color: AppColors.primary),
              hintText: hint,
              border: InputBorder.none,
              contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            ),
            items: items.map((item) {
              return DropdownMenuItem(
                value: item,
                child: Text(itemBuilder(item)),
              );
            }).toList(),
            onChanged: onChanged,
          ),
        ),
      ],
    );
  }

  Widget _buildDateTimeSelector({
    required String label,
    required String value,
    required IconData icon,
    required VoidCallback onTap,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w600,
            color: AppColors.textSecondary,
          ),
        ),
        const SizedBox(height: 8),
        InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(12),
          child: Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: AppColors.background,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: AppColors.divider),
              ),
            child: Row(
              children: [
                Icon(icon, color: AppColors.primary, size: 20),
                const SizedBox(width: 12),
                Expanded(
                  child: Text(
                    value,
                    style: const TextStyle(
                      fontSize: 15,
                      color: AppColors.textPrimary,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildPassengerCounter(
    String label,
    String price,
    IconData icon,
    int count,
    void Function(int) onChanged,
  ) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: AppColors.primary.withOpacity(0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, color: AppColors.primary, size: 24),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
              ),
              Text(
                price,
                style: const TextStyle(
                  fontSize: 14,
                  color: AppColors.textSecondary,
                ),
              ),
            ],
          ),
        ),
        Container(
          decoration: BoxDecoration(
            color: AppColors.background,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: AppColors.divider),
          ),
          child: Row(
            children: [
              IconButton(
                icon: const Icon(Icons.remove_circle_outline),
                color: count > 0 ? AppColors.primary : AppColors.textSecondary,
                onPressed: count > 0 ? () => onChanged(count - 1) : null,
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12),
                child: Text(
                  '$count',
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: AppColors.textPrimary,
                  ),
                ),
              ),
              IconButton(
                icon: const Icon(Icons.add_circle_outline),
                color: AppColors.primary,
                onPressed: () => onChanged(count + 1),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildVehicleSelector() {
    return Column(
      children: [
        if (_vehicles.isEmpty)
          const Center(
            child: Padding(
              padding: EdgeInsets.all(20),
              child: Text(
                'No vehicles available',
                style: TextStyle(color: AppColors.textSecondary),
              ),
            ),
          )
        else
          ..._vehicles.map((vehicle) {
            final isSelected = _selectedVehicle?.id == vehicle.id;
            return Padding(
              padding: const EdgeInsets.only(bottom: 12),
              child: InkWell(
                onTap: () {
                  setState(() {
                    _selectedVehicle = isSelected ? null : vehicle;
                  });
                },
                borderRadius: BorderRadius.circular(12),
                child: Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: isSelected ? AppColors.primary.withOpacity(0.1) : AppColors.background,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(
                      color: isSelected ? AppColors.primary : AppColors.divider,
                      width: isSelected ? 2 : 1,
                    ),
                  ),
                  child: Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: isSelected
                              ? AppColors.primary
                              : AppColors.primary.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Icon(
                          Icons.directions_car_rounded,
                          color: isSelected ? Colors.white : AppColors.primary,
                          size: 24,
                        ),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              vehicle.name,
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.w600,
                                color: isSelected ? AppColors.primary : AppColors.textPrimary,
                              ),
                            ),
                            Text(
                              vehicle.type,
                              style: const TextStyle(
                                fontSize: 13,
                                color: AppColors.textSecondary,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Text(
                        '₹${vehicle.price.toStringAsFixed(0)}',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: isSelected ? AppColors.primary : AppColors.textPrimary,
                        ),
                      ),
                      const SizedBox(width: 8),
                      Icon(
                        isSelected ? Icons.check_circle : Icons.circle_outlined,
                        color: isSelected ? AppColors.primary : AppColors.divider,
                      ),
                    ],
                  ),
                ),
              ),
            );
          }),
      ],
    );
  }
}
