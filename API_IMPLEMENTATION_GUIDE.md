# API Implementation Guide for Laravel Backend

This document explains what needs to be implemented in the Laravel controllers to make the API work with the Flutter app.

## ⚠️ Important: API vs Web Routes

The existing controllers return **views** (HTML) for the web interface. The API needs to return **JSON responses**.

You have two options:
1. **Add new methods** to existing controllers for API responses
2. **Create new API-specific controllers** (recommended for clean separation)

---

## 🔧 Required Changes

### 1. Install Laravel Sanctum (if not already installed)

Laravel Sanctum is required for API token authentication.

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### 2. Update Customer Model

Add Sanctum's `HasApiTokens` trait to `app/Models/Customer.php`:

```php
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // ... rest of the model
}
```

---

## 📝 API Methods to Implement

### A. **CustomerAuth/LoginController.php**

Add these methods for API login:

```php
// API Login - returns JSON with token
public function apiLogin(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $customer = \App\Models\Customer::where('email', $request->email)->first();

    if (!$customer || !\Hash::check($request->password, $customer->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    $token = $customer->createToken('mobile-app')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Login successful',
        'data' => [
            'token' => $token,
            'customer' => $customer
        ]
    ]);
}

// API Logout
public function apiLogout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'success' => true,
        'message' => 'Logged out successfully'
    ]);
}
```

**Update routes/api.php** to use these methods:
```php
Route::post('customer/login', [LoginController::class, 'apiLogin']);
Route::post('customer/logout', [LoginController::class, 'apiLogout'])->middleware('auth:sanctum');
```

---

### B. **CustomerAuth/RegisterController.php**

The existing `sendOtp()` and `verifyOtp()` methods already return JSON, but you need to update `verifyOtp()` to return a token:

```php
public function verifyOtp(Request $request)
{
    if ($request->otp != session('otp')) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP'
        ], 400);
    }

    $data = session('pending_user');

    $customer = Customer::create([
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'mobile' => $data['mobile'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
    ]);

    // Create token for immediate login
    $token = $customer->createToken('mobile-app')->plainTextToken;

    session()->forget(['pending_user', 'otp']);

    return response()->json([
        'success' => true,
        'message' => 'Registration successful',
        'data' => [
            'token' => $token,
            'customer' => $customer
        ]
    ]);
}
```

---

### C. **BranchController.php**

Add these methods for API responses:

```php
// Get all branches (API)
public function index()
{
    $branches = \App\Models\Branch::all(['id', 'branch_id', 'branch_name']);

    return response()->json([
        'success' => true,
        'message' => 'Branches retrieved successfully',
        'data' => $branches
    ]);
}

// Get routes between two branches
public function getRoutes($fromBranchId, $toBranchId)
{
    // You need to implement this based on your routes table structure
    // This is a placeholder example
    $routes = \DB::table('routes')
        ->where('from_branch_id', $fromBranchId)
        ->where('to_branch_id', $toBranchId)
        ->get();

    return response()->json([
        'success' => true,
        'message' => 'Routes retrieved successfully',
        'data' => $routes
    ]);
}
```

---

### D. **FerryBoatController.php**

Add this method:

```php
// Get ferries for a specific branch
public function getFerriesByBranch($branchId)
{
    $ferries = \App\Models\FerryBoat::where('branch_id', $branchId)
        ->get(['id', 'number', 'name', 'branch_id']);

    return response()->json([
        'success' => true,
        'message' => 'Ferries retrieved successfully',
        'data' => $ferries
    ]);
}
```

---

### E. **ItemRateController.php**

Add this method for API:

```php
// Get item rates by branch ID (query parameter)
public function getItemRates(Request $request)
{
    $branchId = $request->query('branch_id');

    if (!$branchId) {
        return response()->json([
            'success' => false,
            'message' => 'branch_id is required'
        ], 400);
    }

    $itemRates = \App\Models\ItemRate::where('branch_id', $branchId)
        ->whereDate('starting_date', '<=', now())
        ->where(function($q) {
            $q->whereNull('ending_date')
              ->orWhereDate('ending_date', '>=', now());
        })
        ->get(['id', 'item_name', 'item_category_id', 'item_rate', 'item_lavy', 'branch_id']);

    return response()->json([
        'success' => true,
        'message' => 'Item rates retrieved successfully',
        'data' => $itemRates
    ]);
}
```

---

### F. **BookingController.php**

Check if this controller exists and what methods it has. You'll need:

```php
// Get all bookings for authenticated customer
public function index(Request $request)
{
    $bookings = \App\Models\Ticket::where('customer_id', $request->user()->id)
        ->with(['fromBranch', 'toBranch', 'ferry'])
        ->orderBy('booking_date', 'desc')
        ->get();

    return response()->json([
        'success' => true,
        'message' => 'Bookings retrieved successfully',
        'data' => $bookings
    ]);
}

// Create new booking
public function store(Request $request)
{
    $validated = $request->validate([
        'ferry_id' => 'required|integer',
        'from_branch_id' => 'required|integer',
        'to_branch_id' => 'required|integer',
        'booking_date' => 'required|date',
        'departure_time' => 'required',
        'items' => 'required|array',
        'items.*.item_rate_id' => 'required|integer',
        'items.*.quantity' => 'required|integer|min:1',
    ]);

    // Calculate total amount
    $totalAmount = 0;
    foreach ($validated['items'] as $item) {
        $itemRate = \App\Models\ItemRate::find($item['item_rate_id']);
        $totalAmount += ($itemRate->item_rate + $itemRate->item_lavy) * $item['quantity'];
    }

    // Create booking
    $booking = \App\Models\Ticket::create([
        'customer_id' => $request->user()->id,
        'ferry_id' => $validated['ferry_id'],
        'from_branch_id' => $validated['from_branch_id'],
        'to_branch_id' => $validated['to_branch_id'],
        'booking_date' => $validated['booking_date'],
        'departure_time' => $validated['departure_time'],
        'total_amount' => $totalAmount,
        'status' => 'confirmed',
        'qr_code' => 'JETTY-' . time() . '-' . $request->user()->id,
    ]);

    // Create booking items (ticket_lines)
    foreach ($validated['items'] as $item) {
        \App\Models\TicketLine::create([
            'ticket_id' => $booking->id,
            'item_rate_id' => $item['item_rate_id'],
            'quantity' => $item['quantity'],
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Booking created successfully',
        'data' => $booking->load('items')
    ], 201);
}

// Get single booking
public function show($id, Request $request)
{
    $booking = \App\Models\Ticket::where('id', $id)
        ->where('customer_id', $request->user()->id)
        ->with(['fromBranch', 'toBranch', 'ferry', 'items'])
        ->first();

    if (!$booking) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'message' => 'Booking retrieved successfully',
        'data' => $booking
    ]);
}

// Cancel booking
public function cancel($id, Request $request)
{
    $booking = \App\Models\Ticket::where('id', $id)
        ->where('customer_id', $request->user()->id)
        ->first();

    if (!$booking) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found'
        ], 404);
    }

    $booking->update(['status' => 'cancelled']);

    return response()->json([
        'success' => true,
        'message' => 'Booking cancelled successfully',
        'data' => $booking
    ]);
}
```

---

## 🌐 Enable CORS

Update `config/cors.php`:

```php
'paths' => ['api/*'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'], // For development only! Restrict in production
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => false,
```

Make sure the CORS middleware is enabled in `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'api' => [
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \App\Http\Middleware\HandleCors::class, // Add this if missing
    ],
];
```

---

## 🧪 Testing the API

After implementing the above changes, you can test with Postman:

### 1. Register (Send OTP)
```
POST https://unfurling.ninja/api/customer/register/send-otp
Body (JSON):
{
  "first_name": "John",
  "last_name": "Doe",
  "mobile": "1234567890",
  "email": "john@example.com",
  "password": "password123"
}
```

### 2. Register (Verify OTP)
```
POST https://unfurling.ninja/api/customer/register/verify-otp
Body (JSON):
{
  "otp": "123456"
}
```

### 3. Login
```
POST https://unfurling.ninja/api/customer/login
Body (JSON):
{
  "email": "john@example.com",
  "password": "password123"
}
Response will include: { "data": { "token": "..." } }
```

### 4. Get Branches (with token)
```
GET https://unfurling.ninja/api/branches
Headers:
Authorization: Bearer {token_from_login}
```

### 5. Get Ferries by Branch
```
GET https://unfurling.ninja/api/branches/1/ferries
Headers:
Authorization: Bearer {token_from_login}
```

### 6. Get Item Rates
```
GET https://unfurling.ninja/api/item-rates?branch_id=1
Headers:
Authorization: Bearer {token_from_login}
```

---

## ✅ Checklist for Your Dev

- [ ] Install Laravel Sanctum
- [ ] Add `HasApiTokens` to Customer model
- [ ] Update all controller methods to return JSON (not views)
- [ ] Enable CORS configuration
- [ ] Update `routes/api.php` with all endpoints
- [ ] Test all endpoints in Postman
- [ ] Deploy to production server
- [ ] Share Postman collection with working examples

---

## 📞 Need Help?

If you encounter any issues, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Make sure Sanctum is properly installed
3. Verify the database has all required tables
4. Check that CORS headers are working
