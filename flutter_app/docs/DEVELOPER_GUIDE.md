# Developer Guide

## Getting Started

### Prerequisites
- **Flutter SDK**: 3.x or higher
- **Dart SDK**: 3.x or higher
- **Android Studio** or **VS Code**
- **Git**: Version control
- **Android SDK**: API level 21 or higher
- **Java JDK**: Version 11 or higher

### Installation Steps

1. **Clone Repository**
```bash
git clone https://github.com/aryantuntune/Jetty_Project_Mobileapp.git
cd Jetty_Project_Mobileapp
```

2. **Install Dependencies**
```bash
flutter pub get
```

3. **Configure Environment**
Create `.env` file in root:
```env
API_BASE_URL=http://your-api-url.com/api
RAZORPAY_KEY=your_razorpay_key
GOOGLE_CLIENT_ID=your_google_client_id
```

4. **Run the App**
```bash
flutter run
```

## Project Structure

```
lib/
├── main.dart                 # App entry point
├── config/                   # Configuration files
│   ├── routes.dart          # Route definitions
│   ├── theme.dart           # App theme
│   └── constants.dart       # App constants
├── models/                   # Data models
│   ├── user.dart
│   ├── booking.dart
│   ├── ferry_route.dart
│   └── schedule.dart
├── providers/                # State management
│   ├── auth_provider.dart
│   ├── booking_provider.dart
│   ├── route_provider.dart
│   └── payment_provider.dart
├── screens/                  # UI screens
│   ├── auth/
│   ├── home/
│   ├── booking/
│   ├── profile/
│   └── payment/
├── widgets/                  # Reusable widgets
│   ├── common/
│   ├── booking/
│   └── route/
├── services/                 # Business logic
│   ├── api_service.dart
│   ├── auth_service.dart
│   ├── booking_service.dart
│   └── storage_service.dart
└── utils/                    # Utility functions
    ├── validators.dart
    ├── formatters.dart
    └── helpers.dart
```

## Development Workflow

### 1. Feature Development
```bash
# Create feature branch
git checkout -b feature/feature-name

# Make changes
# ...

# Run tests
flutter test

# Commit changes
git add .
git commit -m "Add feature: description"

# Push to remote
git push origin feature/feature-name

# Create pull request
```

### 2. Code Style
- Follow Dart style guide
- Use `flutter format` before commits
- Run `flutter analyze` to check issues
- Use meaningful variable names
- Write comments for complex logic

### 3. Testing
```bash
# Run all tests
flutter test

# Run specific test file
flutter test test/models/user_test.dart

# Run with coverage
flutter test --coverage

# View coverage report
genhtml coverage/lcov.info -o coverage/html
```

## Common Development Tasks

### Adding a New Screen

1. Create screen file in `lib/screens/`
2. Define route in `config/routes.dart`
3. Add navigation from existing screen
4. Implement UI and logic
5. Add tests

### Adding a New API Endpoint

1. Define method in appropriate service
2. Create/update model if needed
3. Update provider to use new method
4. Handle errors appropriately
5. Add loading states

### Adding a New Provider

1. Create provider file in `lib/providers/`
2. Extend `ChangeNotifier`
3. Define state variables
4. Implement methods
5. Register in `main.dart`
6. Use in widgets with `Provider.of` or `Consumer`

## State Management Patterns

### Using Provider
```dart
// Access provider
final authProvider = Provider.of<AuthProvider>(context);

// Listen to changes
Consumer<AuthProvider>(
  builder: (context, auth, child) {
    return Text(auth.user?.name ?? 'Guest');
  },
)

// Update state
await authProvider.login(email, password);
```

### Provider Best Practices
- Call `notifyListeners()` after state changes
- Use `Selector` for targeted rebuilds
- Avoid provider in `initState()`, use `didChangeDependencies()`
- Dispose resources in provider's `dispose()`

## API Integration

### Making API Calls
```dart
// In service file
class BookingService {
  final ApiService _apiService = ApiService();

  Future<List<Booking>> fetchBookings() async {
    try {
      final response = await _apiService.get('/bookings');
      return (response.data as List)
          .map((json) => Booking.fromJson(json))
          .toList();
    } catch (e) {
      throw BookingException('Failed to fetch bookings');
    }
  }
}
```

### Error Handling
```dart
try {
  await bookingService.createBooking(data);
} on NetworkException catch (e) {
  showError('Network error: ${e.message}');
} catch (e) {
  showError('An unexpected error occurred');
}
```

## Debugging

### Common Issues

**Issue**: Hot reload not working
**Solution**:
- Try hot restart (Shift + R)
- Stop and restart app
- Run `flutter clean`

**Issue**: Dependencies not resolving
**Solution**:
```bash
flutter clean
flutter pub get
```

**Issue**: Build fails
**Solution**:
```bash
cd android
./gradlew clean
cd ..
flutter clean
flutter pub get
flutter run
```

### Debug Tools
- Flutter DevTools for performance
- Debug console for logs
- Breakpoints in IDE
- Network inspector for API calls

## Building for Production

### Android APK
```bash
# Build release APK
flutter build apk --release

# Build split APKs
flutter build apk --split-per-abi
```

### Android App Bundle
```bash
flutter build appbundle --release
```

### iOS (macOS required)
```bash
flutter build ios --release
```

## Code Review Checklist

- [ ] Code follows style guide
- [ ] Tests added/updated
- [ ] No hardcoded values
- [ ] Error handling implemented
- [ ] Loading states added
- [ ] Documentation updated
- [ ] No console logs in production
- [ ] Performance considerations
- [ ] Security best practices followed
- [ ] Accessibility considered

## Useful Commands

```bash
# Check Flutter doctor
flutter doctor

# List devices
flutter devices

# Run on specific device
flutter run -d device_id

# Build and analyze size
flutter build apk --analyze-size

# Generate code (build_runner)
flutter pub run build_runner build

# Update dependencies
flutter pub upgrade

# Check outdated packages
flutter pub outdated
```

## Environment Setup

### Android Studio Setup
1. Install Flutter plugin
2. Install Dart plugin
3. Configure Flutter SDK path
4. Setup Android SDK
5. Create virtual device

### VS Code Setup
1. Install Flutter extension
2. Install Dart extension
3. Configure Flutter SDK path
4. Setup launch configurations
5. Enable format on save

## Contributing Guidelines

### Pull Request Process
1. Fork the repository
2. Create feature branch
3. Make changes with tests
4. Update documentation
5. Submit PR with description
6. Address review comments
7. Squash commits if requested

### Commit Message Format
```
type(scope): subject

body

footer
```

**Types**: feat, fix, docs, style, refactor, test, chore

**Example**:
```
feat(booking): add seat selection feature

- Add interactive seat map
- Implement seat availability check
- Update booking flow

Closes #123
```

## Resources

### Official Documentation
- [Flutter Documentation](https://flutter.dev/docs)
- [Dart Documentation](https://dart.dev/guides)
- [Provider Package](https://pub.dev/packages/provider)

### Learning Resources
- Flutter YouTube Channel
- Flutter Community Medium
- Stack Overflow Flutter tag
- Flutter Discord community

### Tools
- [DartPad](https://dartpad.dev) - Online Dart editor
- [Flutter DevTools](https://flutter.dev/docs/development/tools/devtools)
- [Zapp!](https://zapp.run) - Online Flutter editor

## Getting Help

### Internal Resources
- Team documentation
- Code comments
- Pull request discussions

### External Resources
- GitHub Issues
- Stack Overflow
- Flutter Discord
- Flutter Gitter

## License & Legal

This project is proprietary software. All rights reserved.
Unauthorized copying, modification, or distribution is prohibited.

---

**Maintained by**: Jetty Development Team
**Last Updated**: December 2025
