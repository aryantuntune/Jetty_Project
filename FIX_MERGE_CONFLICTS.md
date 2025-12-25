# Fix Merge Conflicts & Test APIs

## 🚨 Current Problem

Your `routes/api.php` file has merge conflict markers:

```php
<<<<<<< HEAD
    // Your old code (rate limiting with separate controllers)
=======
    // Your dev's new code (single ApiController)
>>>>>>> cec8bd500a2414f2e1e01d6f31775feb03f828d4
```

This is breaking your API routes!

## ✅ Solution: Resolve Conflicts Manually

### Step 1: Open routes/api.php

Find the conflict markers (lines 24-52) and **DELETE all the conflict markers and old code**.

### Step 2: Keep Your Dev's Clean Version

Replace lines 23-52 with this CLEAN version:

```php
// Public routes (no authentication required)
Route::prefix('customer')->group(function () {
    // Registration
    Route::post('generate-otp', [ApiController::class, 'sendOtp']);
    Route::post('verify-otp', [ApiController::class, 'verifyOtp']);

    // Login
    Route::post('login', [ApiController::class, 'customerlogin']);
    Route::post('google-signin', [ApiController::class, 'customergoogleSignIn']);

    // Password Reset
    Route::post('password-reset/request-otp', [ApiController::class, 'requestOTP']);
    Route::post('password-reset/verify-otp', [ApiController::class, 'verifyOTP']);
    Route::post('password-reset/reset', [ApiController::class, 'resetPassword']);
});
```

### Step 3: Fix BookingController.php Conflicts

Your BookingController also has conflicts. Find the conflict markers around line 30-70 and keep the SIMPLER version (the one that just does web routes).

## 📋 What Your Current API Architecture Is

Based on your dev's changes:

### All Customer APIs are in: `ApiController`

Located at: `app/Http/Controllers/Api/ApiController.php`

**This controller handles ALL mobile app APIs:**
- Registration (sendOtp, verifyOtp)
- Login (customerlogin, customergoogleSignIn)
- Password Reset (requestOTP, verifyOTP, resetPassword)
- Logout (customerlogout)
- Bookings (index, store, show, cancel, getSuccessfulBookings)
- **getToBranches** ← This is the one we need to verify/fix!

### BookingController is ONLY for WEB

Located at: `app/Http/Controllers/BookingController.php`

**This handles web customer dashboard:**
- show() - Display booking form
- getToBranches() - For web dropdown
- getItems() - For web form
- createOrder() - Razorpay web payment
- verifyPayment() - Razorpay web verification

## 🧪 Testing APIs After Fixing Conflicts

### Step 1: Fix the conflicts in both files

### Step 2: Run these commands ON SERVER:

```bash
cd /var/www/unfurling.ninja
git pull origin master
php artisan config:clear
php artisan route:clear
php artisan cache:clear
sudo systemctl restart php8.3-fpm
```

### Step 3: Test getToBranches API

```bash
# Get a customer token first
php artisan tinker
$customer = \App\Models\Customer::first();
$token = $customer->createToken('test')->plainTextToken;
echo "Token: $token\n";
exit

# Test the API
TOKEN="paste_token_here"

curl -X GET "https://unfurling.ninja/api/branches/1/to-branches" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

**Expected Response:**
```json
{
  "success": true,
  "message": "To branches retrieved successfully",
  "data": [
    {"id": 2, "name": "DHOPAVE"},
    {"id": 3, "name": "VESHVI"},
    {"id": 4, "name": "BAGMANDALE"},
    {"id": 5, "name": "JAIGAD"}
  ]
}
```

### Step 4: Test Other Critical APIs

```bash
# Test branches list
curl -X GET "https://unfurling.ninja/api/customer/branch" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"

# Test ferries for branch
curl -X GET "https://unfurling.ninja/api/customer/ferries/branch/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"

# Test item rates for branch
curl -X GET "https://unfurling.ninja/api/customer/rates/branch/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

## 🚢 Understanding getToBranches Logic

Based on your ferry system (direct point-to-point routes):

### Routes Table Structure:
```
route_id | branch_id | sequence
---------|-----------|----------
    1    |     1     |    1     (DABHOL)
    1    |     2     |    2     (DHOPAVE) - Ferry connects DABHOL ↔ DHOPAVE
    2    |     1     |    1     (DABHOL)
    2    |     3     |    2     (VESHVI)  - Ferry connects DABHOL ↔ VESHVI
```

### Current getToBranches Logic:

```php
public function getToBranches($branchId)
{
    // Gets ONE route_id for this branch
    $routeId = DB::table('routes')->where('branch_id', $branchId)->value('route_id');

    // Gets other branches on THAT ONE route
    $toBranchIds = DB::table('routes')
        ->where('route_id', $routeId)
        ->where('branch_id', '!=', $branchId)
        ->pluck('branch_id');
}
```

**Problem:** Only returns branches from the FIRST route!

If DABHOL is on Route 1 AND Route 2:
- ❌ Returns only Route 1 destinations (DHOPAVE)
- ❌ Missing Route 2 destinations (VESHVI, etc.)

### Fixed Logic (What We Need):

```php
public function getToBranches($branchId)
{
    // Get ALL route_ids this branch is on
    $routeIds = DB::table('routes')
        ->where('branch_id', $branchId)
        ->pluck('route_id');

    // Get all branches connected via ANY of these routes
    $toBranchIds = DB::table('routes')
        ->whereIn('route_id', $routeIds)
        ->where('branch_id', '!=', $branchId)
        ->distinct()
        ->pluck('branch_id');

    $branches = Branch::whereIn('id', $toBranchIds)
        ->select('id', 'branch_name as name')
        ->orderBy('branch_name')
        ->get();

    return response()->json([
        'success' => true,
        'message' => 'To branches retrieved successfully',
        'data' => $branches
    ]);
}
```

**This returns ALL connected branches from ALL routes!**

## 🔧 What Needs to Be Done

1. **Fix routes/api.php** - Remove conflict markers, keep ApiController version
2. **Fix BookingController.php** - Remove conflict markers
3. **Fix getToBranches in ApiController** - Use `pluck('route_id')` instead of `value('route_id')`
4. **Test all APIs** - Verify they work correctly
5. **Rebuild Flutter app** - With the vehicle_service.dart fix we already did

## 📱 Mobile App Status

Your Flutter app mobile app grey screen fix is ready:
- ✅ vehicle_service.dart fixed (no more /vehicles API call)
- ⏳ Need to rebuild app
- ⏳ Need server APIs working (fix conflicts first!)

## 🎯 Priority Order

1. **URGENT:** Fix merge conflicts in routes/api.php and BookingController.php
2. **HIGH:** Fix getToBranches in ApiController (pluck instead of value)
3. **MEDIUM:** Test all APIs on server
4. **MEDIUM:** Rebuild Flutter app
5. **LOW:** Test complete booking flow

---

**Don't commit anything yet! Just fix the conflicts locally first, test, then commit.**
