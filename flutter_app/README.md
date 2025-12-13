# 🚢 Jetty Ferry Booking - Mobile App

Customer-side mobile application for booking ferry tickets.

## 📱 Features

- ✅ User registration with OTP verification
- ✅ Login/Logout with token authentication
- ✅ Browse ferry routes and schedules
- ✅ Book tickets with passenger details
- ✅ Select vehicles (Motorcycle, Car, Truck)
- ✅ View booking history
- ✅ QR code for ticket verification
- ✅ Modern, sleek UI with blue ocean theme

## 🛠️ Tech Stack

- **Flutter 3.x** - Cross-platform framework
- **Dart** - Programming language
- **Laravel Sanctum** - Backend authentication
- **REST API** - Backend communication
- **Material Design 3** - UI design system

## 📂 Project Structure

```
lib/
├── config/           # App configuration
│   ├── api_config.dart       # API endpoints
│   ├── app_config.dart       # App settings (mock mode)
│   └── app_colors.dart       # Color theme
├── models/           # Data models
│   ├── customer.dart
│   ├── branch.dart
│   ├── ferry.dart
│   ├── vehicle.dart
│   ├── booking.dart
│   └── api_response.dart
├── services/         # API services
│   ├── api_service.dart      # HTTP client
│   ├── auth_service.dart     # Authentication
│   ├── booking_service.dart  # Bookings
│   ├── vehicle_service.dart  # Vehicles
│   ├── storage_service.dart  # Local storage
│   └── mock_data_service.dart # Mock data
├── screens/          # UI screens
│   ├── splash_screen.dart
│   ├── login_screen.dart
│   ├── register_screen.dart
│   ├── otp_screen.dart
│   ├── home_screen.dart
│   ├── route_selection_screen.dart
│   ├── bookings_screen.dart
│   ├── booking_detail_screen.dart
│   ├── booking_success_screen.dart
│   └── profile_screen.dart
├── widgets/          # Reusable widgets
│   ├── custom_button.dart
│   ├── custom_textfield.dart
│   └── loading_overlay.dart
├── utils/            # Utilities
│   ├── constants.dart
│   └── validators.dart
└── main.dart         # App entry point
```

## 🚀 Getting Started

### Prerequisites

- Flutter SDK (3.0+)
- Android Studio / VS Code
- Android SDK
- Physical device or emulator

### Installation

1. **Clone the repository**
```bash
git clone <your-repo-url>
cd flutter_app
```

2. **Install dependencies**
```bash
flutter pub get
```

3. **Run the app**
```bash
# On Chrome (for testing UI)
flutter run -d chrome

# On Android device/emulator
flutter run
```

## 🔧 Configuration

### Mock Mode (Default)

App runs with fake data, no backend needed.

**File**: `lib/config/app_config.dart`
```dart
static const bool useMockData = true;  // Mock mode ON
```

**Test Credentials**:
- Email: any@test.com
- Password: 123456

### Connect to Real API

**Step 1**: Turn off mock mode

**File**: `lib/config/app_config.dart`
```dart
static const bool useMockData = false;
static const bool apiEnabled = true;
```

**Step 2**: Set API URL

**File**: `lib/config/api_config.dart`
```dart
static const String baseUrl = 'https://unfurling.ninja/api';
```

See **API_CONNECTION_GUIDE.md** for details.

## 📦 Build APK

### Debug APK (Testing)
```bash
flutter build apk --debug
```

### Release APK (Distribution)
```bash
flutter build apk --release
```

Output: `build/app/outputs/flutter-apk/app-release.apk`

See **BUILD_APK_GUIDE.md** for details.

## 🎨 UI Theme

**Color Scheme**: Modern blue ocean theme

- Primary: `#0A7AFF` (Bright blue)
- Background: `#F2F7FF` (Light blue)
- Success: `#34C759` (Green)
- Error: `#FF3B30` (Red)

**Design Features**:
- Gradient headers
- Rounded cards (20px radius)
- Soft shadows
- Icon-based navigation
- Smooth animations

## 📱 Screens

1. **Splash Screen** - App logo with animation
2. **Login Screen** - Email/password authentication
3. **Register Screen** - New user registration
4. **OTP Screen** - Email verification
5. **Home Screen** - Quick actions dashboard
6. **Route Selection** - Book new ticket
7. **Bookings List** - View all bookings
8. **Booking Detail** - Ticket with QR code
9. **Booking Success** - Confirmation screen
10. **Profile Screen** - User account details

## 🔌 API Endpoints

All endpoints at `https://unfurling.ninja/api/`

**Public**:
- `POST /customer/register/send-otp`
- `POST /customer/register/verify-otp`
- `POST /customer/login`

**Protected** (Requires token):
- `POST /customer/logout`
- `GET /customer/profile`
- `GET /branches`
- `GET /ferries`
- `GET /vehicles`
- `GET /item-rates`
- `POST /bookings`
- `GET /bookings`
- `GET /bookings/{id}`
- `POST /bookings/{id}/cancel`

## 🧪 Testing

### Run Tests
```bash
flutter test
```

### UI Testing
```bash
# Mock mode - no backend needed
flutter run -d chrome
```

**Test Flow**:
1. Login with any credentials
2. Click "Book New Ticket"
3. Select route, ferry, date/time
4. Add passengers (Adults/Children)
5. Select vehicle (optional)
6. View total and confirm
7. See success screen with QR code

## 📚 Documentation

- **API_CONNECTION_GUIDE.md** - Connect to Laravel backend
- **BUILD_APK_GUIDE.md** - Build and publish APK
- **TEST_FLUTTER_APP_WITHOUT_API.md** - Test with mock data

## 🐛 Troubleshooting

### Flutter command not found
```bash
flutter doctor
```

### Dependencies error
```bash
flutter clean
flutter pub get
```

### Build error
```bash
flutter clean
flutter pub get
flutter build apk --release
```

## 📋 TODO

- [ ] Connect to production API
- [ ] Add payment gateway integration
- [ ] Add push notifications
- [ ] Add booking history filters
- [ ] Add offline mode
- [ ] Add multi-language support
- [ ] Submit to Play Store

## 📄 License

Proprietary - Jetty Ferry Management System

## 👥 Team

- **Backend**: Laravel API development
- **Mobile**: Flutter app development
- **QA**: Testing and bug reporting

## 📞 Support

For issues or questions, contact the development team.

---

**Version**: 1.0.0
**Last Updated**: 2025-12-10
**Status**: Development (Mock mode)
