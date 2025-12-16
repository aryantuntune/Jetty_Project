# Technical Architecture - Ferry Booking System

## System Overview

**Dual-Interface System:**
1. **Web Portal** - Admin/staff counter + customer web booking
2. **Mobile App** - Flutter customer booking app

**Two Parallel Systems:**
- **Admin System:** `users` table → creates `tickets` (counter sales)
- **Customer System:** `customers` table → creates `bookings` (app/web bookings)

---

## Server-Side Architecture (Laravel)

### 1. Authentication - Two Separate Guards

```
┌─────────────────────────────────────────────┐
│           Laravel Backend                   │
├─────────────────────────────────────────────┤
│  ┌────────────────┐   ┌──────────────────┐ │
│  │  Admin Auth    │   │  Customer Auth   │ │
│  │  (Session)     │   │  (Sanctum)       │ │
│  ├────────────────┤   ├──────────────────┤ │
│  │ Table: users   │   │ Table: customers │ │
│  │ Guard: web     │   │ Guard: customer  │ │
│  │ Middleware:    │   │ Middleware:      │ │
│  │  - auth        │   │  - customer.api  │ │
│  │  - role:1,2    │   │                  │ │
│  └────────────────┘   └──────────────────┘ │
│         │                      │            │
│         ↓                      ↓            │
│   Creates tickets      Creates bookings    │
└─────────────────────────────────────────────┘
```

**Admin Auth:**
- Guard: `web` (session-based)
- Table: `users`
- Middleware: `auth`, `role:1,2,3,4,5`
- Use: Admin panel, reports, ticket counter

**Customer Auth:**
- Guard: `customer` (Sanctum token)
- Table: `customers`
- Middleware: `customer.api`
- Tokens: `personal_access_tokens`
- Use: Mobile app, customer web portal

---

### 2. Middleware System

**customer.api Middleware:**
```php
// app/Http/Middleware/CustomerApiMiddleware.php

public function handle($request, Closure $next)
{
    $token = $request->bearerToken();
    $personalAccessToken = PersonalAccessToken::findToken($token);
    Auth::guard('customer')->setUser($personalAccessToken->tokenable);
    return $next($request);
}
```

**Registered in:** `bootstrap/app.php`
```php
$middleware->alias([
    'customer.api' => \App\Http\Middleware\CustomerApiMiddleware::class,
]);
```

---

### 3. Routing Architecture

**API Routes** (`routes/api.php` - auto-prefixed with `/api`):
```php
// PUBLIC (no auth)
Route::prefix('customer')->group(function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('generate-otp', [RegisterController::class, 'sendOtp']);
});

// PROTECTED (requires Sanctum token)
Route::middleware('customer.api')->group(function () {
    Route::get('customer/profile', fn($req) => $req->user());
    Route::get('customer/branch', [BranchController::class, 'getBranches']);
    Route::get('customer/ferries/branch/{id}', [FerryBoatController::class, 'getFerriesByBranch']);
    Route::get('customer/rates/branch/{id}', [ItemRateController::class, 'getItemRatesByBranch']);
    Route::get('bookings', [BookingController::class, 'index']);
    Route::post('bookings', [BookingController::class, 'store']);
    Route::post('razorpay/order', [RazorpayController::class, 'createOrder']);
    Route::post('razorpay/verify', [RazorpayController::class, 'verifyPayment']);
});
```

**Web Routes** (`routes/web.php`):
```php
// Admin (session auth)
Route::middleware(['auth', 'blockRole5'])->group(function () {
    Route::get('/reports/tickets', [TicketReportController::class, 'index']);
});

// Customer web (customer session)
Route::middleware('auth:customer')->group(function () {
    Route::get('customer/dashboard', [BookingController::class, 'showDashboard']);
});
```

---

### 4. Controller Pattern

**Single Controller, Multiple Methods:**
```php
class BookingController extends Controller
{
    // API METHOD (mobile app)
    public function index(Request $request)
    {
        $customerId = $request->user()->id;  // From Sanctum
        $bookings = Booking::where('customer_id', $customerId)->get();
        return response()->json(['success' => true, 'data' => $bookings]);
    }

    // WEB METHOD (browser)
    public function showDashboard()
    {
        $branches = Branch::all();
        return view('customer.dashboard', compact('branches'));
    }
}
```

---

### 5. Database Schema

**Two Booking Systems:**
```
ADMIN SYSTEM
users → tickets → ticket_lines

CUSTOMER SYSTEM
customers → bookings
    ↓
personal_access_tokens (Sanctum)
```

**Bookings Table:**
```sql
CREATE TABLE bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED,
    ferry_id BIGINT UNSIGNED,
    from_branch BIGINT UNSIGNED,
    to_branch BIGINT UNSIGNED,
    booking_date DATE,
    departure_time TIME,
    items JSON,  -- [{"item_rate_id":101,"quantity":2}]
    total_amount DECIMAL(10,2),
    payment_id VARCHAR(255),
    qr_code VARCHAR(255),
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

### 6. Payment Flow (Razorpay)

```
App              Laravel                 Razorpay
 │                  │                        │
 │ 1. Create Order │                        │
 ├────────────────→ │                        │
 │                  │ 2. Create Order        │
 │                  ├───────────────────────→│
 │                  │←───────────────────────┤
 │ ←────────────────┤ {order_id, amount}     │
 │ {order_id}       │                        │
 │                  │                        │
 │ 3. User Pays     │                        │
 │ (Razorpay UI)    │                        │
 │                  │                        │
 │ 4. Verify        │                        │
 ├────────────────→ │                        │
 │ {payment_id,     │ 5. Verify Signature    │
 │  signature,      ├───────────────────────→│
 │  ferry_id,       │←───────────────────────┤
 │  items...}       │ Valid ✓                │
 │                  │                        │
 │                  │ 6. Create Booking      │
 │                  │    + Generate QR       │
 │ ←────────────────┤                        │
 │ {booking_id,     │                        │
 │  qr_code}        │                        │
```

**Code:**
```php
public function verifyPayment(Request $request)
{
    // 1. Verify signature
    $api = new Api(config('services.razorpay.key'), ...);
    $api->utility->verifyPaymentSignature([
        'razorpay_order_id' => $request->razorpay_order_id,
        'razorpay_payment_id' => $request->razorpay_payment_id,
        'razorpay_signature' => $request->razorpay_signature
    ]);

    // 2. Calculate total (server-side validation)
    $totalAmount = 0;
    foreach ($request->items as $item) {
        $itemRate = ItemRate::find($item['item_rate_id']);
        $totalAmount += ($itemRate->item_rate + $itemRate->item_lavy) * $item['quantity'];
    }

    // 3. Create booking
    $booking = Booking::create([
        'customer_id' => $request->user()->id,
        'ferry_id' => $request->ferry_id,
        'from_branch' => $request->from_branch_id,
        'to_branch' => $request->to_branch_id,
        'items' => json_encode($request->items),
        'total_amount' => $totalAmount,
        'payment_id' => $request->razorpay_payment_id,
        'qr_code' => 'JETTY-' . strtoupper(uniqid()),
        'status' => 'confirmed'
    ]);

    return response()->json(['success' => true, 'data' => $booking]);
}
```

---

## Mobile App Architecture (Flutter)

### 1. Structure

```
flutter_app/lib/
├── config/
│   ├── api_config.dart      ← API URLs
│   └── app_config.dart      ← Settings
├── models/
│   ├── customer.dart
│   ├── branch.dart
│   ├── ferry.dart
│   ├── item_rate.dart
│   ├── booking.dart
│   └── api_response.dart    ← Wrapper
├── services/
│   ├── api_service.dart     ← HTTP client
│   ├── auth_service.dart    ← Login/logout
│   ├── booking_service.dart ← Bookings
│   └── storage_service.dart ← Local storage
└── screens/
    ├── login_screen.dart
    ├── route_selection_screen.dart
    └── booking_confirmation_screen.dart
```

---

### 2. API Service

```dart
class ApiService {
  static const String baseUrl = 'https://unfurling.ninja';

  static Future<ApiResponse<T>> get<T>(
    String endpoint,
    {required T Function(dynamic) fromJson}
  ) async {
    final token = await StorageService.getToken();

    final response = await http.get(
      Uri.parse('$baseUrl$endpoint'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );

    final json = jsonDecode(response.body);

    return ApiResponse<T>(
      success: json['success'],
      message: json['message'],
      data: json['success'] ? fromJson(json['data']) : null,
    );
  }

  static Future<ApiResponse<T>> post<T>(
    String endpoint,
    {Map<String, dynamic>? body, required T Function(dynamic) fromJson}
  ) async {
    final token = await StorageService.getToken();

    final response = await http.post(
      Uri.parse('$baseUrl$endpoint'),
      headers: {
        'Content-Type': 'application/json',  // IMPORTANT: JSON
        'Authorization': 'Bearer $token',
      },
      body: jsonEncode(body),  // Encode as JSON
    );

    final json = jsonDecode(response.body);
    return ApiResponse<T>(
      success: json['success'],
      data: json['success'] ? fromJson(json['data']) : null,
    );
  }
}
```

---

### 3. Authentication Flow

```
App                  Laravel              Database
 │                      │                    │
 │ Login                │                    │
 ├────────────────────→ │                    │
 │ {email, password}    │ Validate           │
 │                      ├───────────────────→│
 │                      │ Customer found     │
 │                      │                    │
 │                      │ Create Token       │
 │                      ├───────────────────→│
 │                      │ INSERT INTO        │
 │                      │ personal_access_   │
 │ ←────────────────────┤ tokens             │
 │ {token: "16|xxx"}    │                    │
 │                      │                    │
 │ Store locally        │                    │
 │ SharedPreferences    │                    │
 │                      │                    │
 │ Get Bookings         │                    │
 ├────────────────────→ │                    │
 │ Bearer 16|xxx        │ Validate token     │
 │                      ├───────────────────→│
 │                      │ Token valid,       │
 │                      │ customer_id=5      │
 │                      │                    │
 │                      │ Get bookings       │
 │                      ├───────────────────→│
 │ ←────────────────────┤ WHERE customer=5   │
 │ [bookings]           │                    │
```

---

### 4. Complete Booking Flow

```
1. Select Route
   GET /api/customer/branch
   → Returns all branches

2. Select Destination
   GET /api/branches/{id}/to-branches
   → Returns valid destinations

3. Select Ferry
   GET /api/customer/ferries/branch/{id}
   → Returns ferries for branch

4. Add Passengers
   GET /api/customer/rates/branch/{id}
   → Returns item rates (Adult ₹20, Child ₹9, Senior ₹17)

5. Calculate Total (locally)
   total = Σ(price × quantity)

6. Create Payment Order
   POST /api/razorpay/order {amount: 66}
   → {order_id: "order_xxx"}

7. Pay (Razorpay UI)
   → {payment_id, signature}

8. Verify & Create Booking
   POST /api/razorpay/verify {
     razorpay_order_id,
     razorpay_payment_id,
     razorpay_signature,
     ferry_id, from_branch_id, to_branch_id,
     booking_date, departure_time,
     items: [{item_rate_id: 101, quantity: 2}]
   }
   → {booking_id, qr_code}
```

---

### 5. Data Models

```dart
class Booking {
  final int id;
  final int customerId;
  final int ferryId;
  final int fromBranchId;
  final int toBranchId;
  final String bookingDate;
  final String departureTime;
  final double totalAmount;
  final String status;
  final String qrCode;

  factory Booking.fromJson(Map<String, dynamic> json) {
    return Booking(
      id: json['id'],
      customerId: json['customer_id'],
      ferryId: json['ferry_id'],
      fromBranchId: json['from_branch_id'],
      toBranchId: json['to_branch_id'],
      bookingDate: json['booking_date'],
      departureTime: json['departure_time'],
      totalAmount: double.parse(json['total_amount'].toString()),
      status: json['status'],
      qrCode: json['qr_code'],
    );
  }
}
```

---

## Key Technical Decisions

### Why Two Auth Systems?

**Admin (users):** Role-based, session auth, creates tickets
**Customer (customers):** Simple auth, token-based, creates bookings

Separation provides cleaner code and easier permissions.

### Why JSON for Items?

Bookings are immutable (never edited). JSON is:
- Simpler (no joins)
- Faster reads
- Stores prices at booking time

Admin tickets use `ticket_lines` table because tickets can be edited.

### Why Sanctum?

- Built into Laravel
- Simple token auth
- Tokens in database (can revoke)
- Perfect for mobile apps

---

## Environment Config

**Laravel `.env`:**
```env
APP_URL=https://unfurling.ninja
DB_DATABASE=jetty_db
RAZORPAY_KEY=rzp_live_xxxxx
RAZORPAY_SECRET=xxxxxxxxxxxxx
```

**Flutter `api_config.dart`:**
```dart
class ApiConfig {
  static const String baseUrl = 'https://unfurling.ninja';
  static const String branches = '/api/customer/branch';
  static String getFerries(int id) => '/api/customer/ferries/branch/$id';
  static const String bookings = '/api/bookings';
}
```

---

## Testing

**API Test:**
```bash
# Login
curl -X POST https://unfurling.ninja/api/customer/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'

# Get branches (with token)
curl -X GET https://unfurling.ninja/api/customer/branch \
  -H "Authorization: Bearer 16|xxx..."
```

**Database Check:**
```sql
SELECT * FROM personal_access_tokens ORDER BY created_at DESC LIMIT 5;
SELECT * FROM bookings ORDER BY created_at DESC LIMIT 5;
SELECT branch_id, COUNT(*) FROM item_rates GROUP BY branch_id;
```

---

## Common Issues

**"Unauthenticated" Error:**
- Check Authorization header format: `Bearer {token}`
- Verify token exists in `personal_access_tokens`
- Check `customer.api` middleware registered

**Grey Screens:**
- Verify all branches have item rates
- Check Senior Citizen passenger type exists
- Verify ferries distributed across branches

**Field Mismatch:**
- API returns `branch_name as name`
- Backend uses `from_branch`/`to_branch`, not `from_branch_id`/`to_branch_id`
- Flutter sends `from_branch_id`, backend maps to `from_branch`

---

**Last Updated:** December 16, 2025
**Status:** ✅ Production Ready
