# Project Dependencies Documentation

## Core Dependencies

### 1. Flutter SDK
- **Version**: 3.x (stable channel)
- **Purpose**: Framework foundation

### 2. Dart SDK
- **Version**: 3.x
- **Purpose**: Programming language

## Key Packages

### State Management
- **provider** (^6.0.5)
  - State management solution
  - Dependency injection

### Networking
- **dio** (^5.0.0)
  - HTTP client for API calls
  - Interceptor support
  - Request/response transformation

- **http** (^1.1.0)
  - Backup HTTP client
  - Simple GET/POST requests

### Authentication
- **google_sign_in** (^6.1.0)
  - Google OAuth integration
  - Social authentication

- **flutter_secure_storage** (^9.0.0)
  - Secure token storage
  - Encrypted key-value storage

### Local Storage
- **shared_preferences** (^2.2.0)
  - Persistent key-value storage
  - User preferences

- **sqflite** (^2.3.0)
  - Local SQLite database
  - Offline data caching

### UI Components
- **cached_network_image** (^3.3.0)
  - Image caching
  - Smooth image loading

- **image_picker** (^1.0.4)
  - Camera and gallery access
  - Profile picture upload

- **flutter_svg** (^2.0.9)
  - SVG image rendering
  - Vector graphics support

### Date & Time
- **intl** (^0.18.1)
  - Internationalization
  - Date/time formatting

- **table_calendar** (^3.0.9)
  - Calendar widget
  - Date selection UI

### Navigation
- **go_router** (^12.0.0)
  - Declarative routing
  - Deep linking support

### Payment Integration
- **razorpay_flutter** (^1.3.6)
  - Payment gateway integration
  - UPI, cards, wallets support

### Utilities
- **url_launcher** (^6.2.0)
  - Open URLs and make calls
  - External app integration

- **permission_handler** (^11.0.0)
  - Runtime permissions
  - Camera, storage access

- **connectivity_plus** (^5.0.0)
  - Network connectivity check
  - Online/offline detection

- **flutter_launcher_icons** (^0.13.1)
  - App icon generation
  - Platform-specific icons

- **flutter_native_splash** (^2.3.5)
  - Splash screen generation
  - Native splash implementation

## Dev Dependencies

### Testing
- **flutter_test**
  - Widget testing
  - Unit testing framework

- **mockito** (^5.4.2)
  - Mock object creation
  - Dependency mocking

- **integration_test**
  - End-to-end testing
  - User flow testing

### Code Quality
- **flutter_lints** (^3.0.0)
  - Dart linting rules
  - Code style enforcement

## Version Management
- All packages pinned to specific versions
- Regular security updates
- Compatibility testing before updates
- `flutter pub outdated` for update checks
