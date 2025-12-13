# API CONNECTION GUIDE

## When Backend is Ready

### Step 1: Turn Off Mock Mode

**File**: `lib/config/app_config.dart`

```dart
class AppConfig {
  // Change these to connect to real API
  static const bool useMockData = false;  // ← Change to false
  static const bool apiEnabled = true;     // ← Change to true
}
```

### Step 2: Update API Base URL

**File**: `lib/config/api_config.dart`

```dart
class ApiConfig {
  // Change this to your Laravel server URL
  static const String baseUrl = 'https://unfurling.ninja/api';  // ← Your domain

  // All endpoints are already configured:
  static const String registerSendOtp = '/customer/register/send-otp';
  static const String registerVerifyOtp = '/customer/register/verify-otp';
  static const String login = '/customer/login';
  static const String logout = '/customer/logout';
  static const String profile = '/customer/profile';
  static const String branches = '/branches';
  static const String ferries = '/ferries';
  static const String itemRates = '/item-rates';
  static const String bookings = '/bookings';
}
```

### Step 3: That's It!

No other changes needed. The app will automatically:
- Use real API calls instead of mock data
- Send authentication tokens
- Handle responses from Laravel

---

## Backend Developer Checklist

### Required Laravel API Endpoints

All these endpoints must be created at `https://unfurling.ninja/api/...`:

#### Public Endpoints (No Auth):
```
POST /api/customer/register/send-otp
POST /api/customer/register/verify-otp
POST /api/customer/login
```

#### Protected Endpoints (Require Auth Token):
```
POST   /api/customer/logout
GET    /api/customer/profile
GET    /api/branches
GET    /api/branches/{id}/destinations
GET    /api/ferries
GET    /api/item-rates
GET    /api/vehicles                        ← NEW for vehicle dropdown
POST   /api/bookings
GET    /api/bookings
GET    /api/bookings/{id}
POST   /api/bookings/{id}/cancel
```

### Response Format

All responses must follow this format:

```json
{
  "success": true,
  "message": "Success message",
  "data": { /* your data here */ }
}
```

Or for errors:

```json
{
  "success": false,
  "message": "Error message",
  "errors": ["Error 1", "Error 2"]
}
```

### Authentication

- Use **Laravel Sanctum** for token-based authentication
- App sends token in header: `Authorization: Bearer {token}`
- Return token on login/register success

---

## Testing API Connection

### 1. Test with Postman First

Before connecting the app, test your API with Postman:

**Login Example**:
```
POST https://unfurling.ninja/api/customer/login
Content-Type: application/json

{
  "email": "test@example.com",
  "password": "password123"
}
```

**Expected Response**:
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "1|abc123...",
    "customer": {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "email": "test@example.com",
      "mobile": "1234567890"
    }
  }
}
```

### 2. Connect App to API

Once Postman tests pass:

1. Change `app_config.dart` (turn off mock mode)
2. Update `api_config.dart` (set your URL)
3. Run app: `flutter run`
4. Test login/booking

---

## Debugging API Issues

### Enable Logging

**File**: `lib/services/api_service.dart`

Add this at line 15:

```dart
static Future<ApiResponse<T>> get<T>(
  String endpoint, {
  bool requiresAuth = true,
  T? Function(dynamic)? fromJson,
}) async {
  try {
    final url = Uri.parse('${ApiConfig.baseUrl}$endpoint');
    print('🌐 GET: $url');  // ← Add this

    final headers = await _getHeaders(requiresAuth);
    print('📤 Headers: $headers');  // ← Add this

    final response = await http.get(url, headers: headers);
    print('📥 Response: ${response.statusCode} - ${response.body}');  // ← Add this

    return _handleResponse<T>(response, fromJson);
  } catch (e) {
    print('❌ Error: $e');  // ← Add this
    // ... rest of code
  }
}
```

This will show all API calls in the console.

---

## Common Issues & Solutions

### Issue 1: CORS Error
**Error**: "Access-Control-Allow-Origin"

**Solution**: Add to Laravel `config/cors.php`:
```php
'paths' => ['api/*'],
'allowed_origins' => ['*'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

### Issue 2: 401 Unauthorized
**Cause**: Token not being sent or expired

**Check**:
1. Token is saved: Check `StorageService.getToken()`
2. Header format: `Authorization: Bearer {token}`
3. Token is valid in Laravel

### Issue 3: 404 Not Found
**Cause**: Route doesn't exist

**Check**:
1. Laravel routes: `php artisan route:list`
2. API prefix is correct
3. URL matches exactly

### Issue 4: 500 Internal Server Error
**Cause**: Laravel backend error

**Check**:
1. Laravel logs: `storage/logs/laravel.log`
2. Database connection
3. Missing migrations

---

## Quick Reference

### Files to Edit When Connecting API:

1. **lib/config/app_config.dart** - Turn off mock mode
2. **lib/config/api_config.dart** - Set your URL

### Files That Handle API Calls (Don't Edit):

- `lib/services/api_service.dart` - HTTP client
- `lib/services/auth_service.dart` - Auth API calls
- `lib/services/booking_service.dart` - Booking API calls
- `lib/services/vehicle_service.dart` - Vehicle API calls

---

## Testing Checklist

After connecting to real API:

- [ ] Login works with real credentials
- [ ] Registration + OTP works
- [ ] Profile loads user data
- [ ] Branches load from database
- [ ] Ferries load from database
- [ ] Vehicles load with availability
- [ ] Can create real bookings
- [ ] Bookings list shows real data
- [ ] QR codes are generated
- [ ] Logout clears token

---

## Production Deployment

### Before Publishing to Play Store:

1. **Update API URL** to production: `https://unfurling.ninja/api`
2. **Build release APK**: `flutter build apk --release`
3. **Test on real device** with production API
4. **Verify SSL certificate** is valid

### Security Checklist:

- [ ] HTTPS is enforced (not HTTP)
- [ ] API tokens expire appropriately
- [ ] Sensitive data is not logged
- [ ] SSL certificate is valid
- [ ] Rate limiting is enabled
- [ ] Input validation on backend

---

**Last Updated**: 2025-12-10
**App Version**: 1.0.0
**Status**: Ready to connect when API is ready
