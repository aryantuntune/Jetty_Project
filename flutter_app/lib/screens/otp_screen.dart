import 'package:flutter/material.dart';
import '../config/app_colors.dart';
import '../services/auth_service.dart';
import '../utils/validators.dart';
import '../widgets/custom_button.dart';
import '../widgets/custom_textfield.dart';
import '../widgets/loading_overlay.dart';
import 'home_screen.dart';

class OTPScreen extends StatefulWidget {
  final String email;

  const OTPScreen({Key? key, required this.email}) : super(key: key);

  @override
  State<OTPScreen> createState() => _OTPScreenState();
}

class _OTPScreenState extends State<OTPScreen> {
  final _formKey = GlobalKey<FormState>();
  final _otpController = TextEditingController();
  bool _isLoading = false;

  @override
  void dispose() {
    _otpController.dispose();
    super.dispose();
  }

  Future<void> _handleVerifyOTP() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);

    final response = await AuthService.verifyOTP(
      email: widget.email,
      otp: _otpController.text.trim(),
    );

    setState(() => _isLoading = false);

    if (!mounted) return;

    if (response.success) {
      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (context) => const HomeScreen()),
        (route) => false,
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
        title: const Text('Verify OTP'),
      ),
      body: LoadingOverlay(
        isLoading: _isLoading,
        message: 'Verifying...',
        child: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const SizedBox(height: 40),
                  Center(
                    child: Icon(
                      Icons.mark_email_read_outlined,
                      size: 80,
                      color: AppColors.primary,
                    ),
                  ),
                  const SizedBox(height: 20),
                  const Center(
                    child: Text(
                      'Verify Your Email',
                      style: TextStyle(
                        fontSize: 28,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textPrimary,
                      ),
                    ),
                  ),
                  const SizedBox(height: 8),
                  Center(
                    child: Text(
                      'We sent a verification code to\n${widget.email}',
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                        fontSize: 16,
                        color: AppColors.textSecondary,
                      ),
                    ),
                  ),
                  const SizedBox(height: 40),
                  CustomTextField(
                    controller: _otpController,
                    label: 'OTP Code',
                    hint: 'Enter 6-digit code',
                    keyboardType: TextInputType.number,
                    validator: Validators.validateOTP,
                    prefixIcon: const Icon(Icons.pin_outlined),
                  ),
                  const SizedBox(height: 30),
                  CustomButton(
                    text: 'Verify & Continue',
                    onPressed: _handleVerifyOTP,
                    isLoading: _isLoading,
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
