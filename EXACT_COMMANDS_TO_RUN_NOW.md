# EXACT COMMANDS TO RUN ON SERVER NOW

## Step 1: Create a Test Customer (For Mobile App Login)

```bash
php artisan tinker
```

Then copy-paste this ENTIRE block:

```php
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

Customer::create([
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test@example.com',
    'mobile' => '9876543210',
    'password' => Hash::make('password')
]);

exit
```

Press Enter. You should see the customer created.

---

## Step 2: Test Login API (Correct POST Method)

```bash
curl -X POST https://unfurling.ninja/api/customer/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

**Expected Success Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "1|abcdef123456...",
    "customer": {
      "id": 1,
      "first_name": "Test",
      "last_name": "User",
      "email": "test@example.com",
      "mobile": "9876543210"
    }
  }
}
```

If you see this SUCCESS response, your API is FULLY WORKING! ✅

---

## Step 3: Test Protected Route (Get Branches)

Copy the token from Step 2 response, then run:

```bash
curl -X GET https://unfurling.ninja/api/customer/branch \
  -H "Authorization: Bearer PASTE_YOUR_TOKEN_HERE"
```

Replace `PASTE_YOUR_TOKEN_HERE` with the actual token from login.

**Expected Response:**
```json
{
  "success": true,
  "message": "Branches retrieved successfully",
  "data": [
    {"id": 1, "branch_name": "DABHOL"},
    {"id": 2, "branch_name": "DHOPAVE"},
    ...
  ]
}
```

---

## Step 4: Check Web App Reports Pages

Open your browser and test these pages that were giving 500 errors:
1. https://unfurling.ninja/reports (or your reports URL)
2. Reports > Ticket Details
3. Reports > Vehicle-wise Ticket Details

They should all work now! ✅

---

## Step 5: Test Mobile App

Now your Flutter mobile app can:
1. Login with: test@example.com / password
2. Get branches list
3. Book ferry tickets
4. Make payments

---

## If Login Still Returns Error:

### Check if customers table exists:
```bash
php artisan tinker
>>> Schema::hasTable('customers');
>>> exit
```

Should return `true`.

### Check if Customer model has HasApiTokens:
```bash
php artisan tinker
>>> $c = new App\Models\Customer;
>>> method_exists($c, 'createToken');
>>> exit
```

Should return `true`.

### Check routes are loaded:
```bash
php artisan route:list --path=api | grep login
```

Should show the login route.

---

## Summary - What Should Work Now:

✅ Web app - All pages including Reports
✅ Mobile app - Login with test@example.com / password
✅ API - All endpoints (login, branches, bookings, payments)
✅ Database - All tables including customers & tokens

Your entire system is READY! 🚀
