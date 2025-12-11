# API Endpoints Mapping - Flutter App ↔ Laravel Backend

This document shows the exact mapping between Flutter app API calls and Laravel backend endpoints (from Postman collection).

**Base URL**: `https://unfurling.ninja/api`

---

## ✅ Authentication Endpoints (Public - No Token Required)

### 1. Register - Send OTP
- **Flutter Config**: `ApiConfig.registerSendOtp`
- **Endpoint**: `POST /api/customer/generate-otp`
- **Full URL**: `https://unfurling.ninja/api/customer/generate-otp`
- **Request Body** (form-urlencoded):
  ```json
  {
    "first_name": "string",
    "last_name": "string",
    "email": "string",
    "mobile": "string",
    "password": "string"
  }
  ```
- **Used in**: `AuthService.sendOTP()`

### 2. Register - Verify OTP
- **Flutter Config**: `ApiConfig.registerVerifyOtp`
- **Endpoint**: `POST /api/customer/verify-otp`
- **Full URL**: `https://unfurling.ninja/api/customer/verify-otp`
- **Request Body** (form-urlencoded):
  ```json
  {
    "email": "string",
    "otp": "string"
  }
  ```
- **Expected Response**:
  ```json
  {
    "success": true,
    "message": "Registration successful",
    "data": {
      "token": "Bearer token here",
      "customer": { customer object }
    }
  }
  ```
- **Used in**: `AuthService.verifyOTP()`

### 3. Login
- **Flutter Config**: `ApiConfig.login`
- **Endpoint**: `POST /api/customer/login`
- **Full URL**: `https://unfurling.ninja/api/customer/login`
- **Request Body** (form-urlencoded):
  ```json
  {
    "email": "string",
    "password": "string"
  }
  ```
- **Expected Response**:
  ```json
  {
    "success": true,
    "message": "Login successful",
    "data": {
      "token": "Bearer token here",
      "customer": { customer object }
    }
  }
  ```
- **Used in**: `AuthService.login()`

---

## 🔒 Protected Endpoints (Require Bearer Token)

All endpoints below require the `Authorization: Bearer {token}` header.

### 4. Get Customer Profile
- **Flutter Config**: `ApiConfig.profile`
- **Endpoint**: `GET /api/customer/profile`
- **Full URL**: `https://unfurling.ninja/api/customer/profile`
- **Headers**:
  ```
  Authorization: Bearer {token}
  ```
- **Expected Response**:
  ```json
  {
    "success": true,
    "message": "Profile retrieved successfully",
    "data": { customer object }
  }
  ```
- **Used in**: `AuthService.getProfile()`

### 5. Get All Branches
- **Flutter Config**: `ApiConfig.branches`
- **Endpoint**: `GET /api/customer/branch`
- **Full URL**: `https://unfurling.ninja/api/customer/branch`
- **Headers**:
  ```
  Authorization: Bearer {token}
  ```
- **Expected Response**:
  ```json
  {
    "success": true,
    "message": "Branches retrieved successfully",
    "data": [
      {
        "id": 1,
        "branch_id": "string",
        "branch_name": "string"
      }
    ]
  }
  ```
- **Used in**: `BookingService.getBranches()`

### 6. Get Ferries by Branch
- **Flutter Config**: `ApiConfig.getFerries(branchId)`
- **Endpoint**: `GET /api/ferryboats/branch/{branchId}`
- **Full URL**: `https://unfurling.ninja/api/ferryboats/branch/1`
- **Headers**:
  ```
  Authorization: Bearer {token}
  ```
- **URL Parameter**: `branchId` (integer)
- **Expected Response**:
  ```json
  {
    "success": true,
    "message": "Ferries retrieved successfully",
    "data": [
      {
        "id": 1,
        "number": "string",
        "name": "string",
        "branch_id": 1,
        "capacity": 100
      }
    ]
  }
  ```
- **Used in**: `BookingService.getFerries(branchId: 1)`

### 7. Get Item Rates by Branch
- **Flutter Config**: `ApiConfig.getItemRates(branchId)`
- **Endpoint**: `GET /api/item-rates/branch/{branchId}`
- **Full URL**: `https://unfurling.ninja/api/item-rates/branch/1`
- **Headers**:
  ```
  Authorization: Bearer {token}
  ```
- **URL Parameter**: `branchId` (integer)
- **Expected Response**:
  ```json
  {
    "success": true,
    "message": "Item rates retrieved successfully",
    "data": [
      {
        "id": 1,
        "item_name": "Adult",
        "item_category_id": 1,
        "item_rate": 50.00,
        "item_lavy": 5.00,
        "branch_id": 1
      }
    ]
  }
  ```
- **Used in**: `BookingService.getItemRates(branchId: 1)`

---

## ⚠️ Endpoints Not Yet Created by Backend Dev

These endpoints are expected by the Flutter app but were **NOT found in the Postman collection**. Your dev needs to create these:

### 8. Get Destinations/Routes
- **Flutter Config**: `ApiConfig.getRoutes(fromBranchId, toBranchId)`
- **Expected Endpoint**: `GET /api/branches/{from}/to/{to}/routes`
- **Example**: `GET /api/branches/1/to/2/routes`
- **Status**: ❌ NOT IMPLEMENTED
- **Used in**: `BookingService.getRoutes(1, 2)`
- **Note**: Currently using `BookingService.getDestinations()` as fallback

### 9. Create Booking
- **Flutter Config**: `ApiConfig.bookings`
- **Expected Endpoint**: `POST /api/bookings`
- **Status**: ❌ NOT IMPLEMENTED
- **Request Body**:
  ```json
  {
    "ferry_id": 1,
    "from_branch_id": 1,
    "to_branch_id": 2,
    "booking_date": "2025-12-15",
    "departure_time": "10:30:00",
    "items": [
      {
        "item_rate_id": 1,
        "quantity": 2
      }
    ]
  }
  ```
- **Used in**: `BookingService.createBooking()`

### 10. Get All Bookings
- **Flutter Config**: `ApiConfig.bookings`
- **Expected Endpoint**: `GET /api/bookings`
- **Status**: ❌ NOT IMPLEMENTED
- **Used in**: `BookingService.getBookings()`

### 11. Get Single Booking
- **Flutter Config**: `ApiConfig.getBooking(bookingId)`
- **Expected Endpoint**: `GET /api/bookings/{id}`
- **Status**: ❌ NOT IMPLEMENTED
- **Used in**: `BookingService.getBookingById()`

### 12. Cancel Booking
- **Flutter Config**: `ApiConfig.cancelBooking(bookingId)`
- **Expected Endpoint**: `POST /api/bookings/{id}/cancel`
- **Status**: ❌ NOT IMPLEMENTED
- **Used in**: `BookingService.cancelBooking()`

### 13. Logout
- **Flutter Config**: `ApiConfig.logout`
- **Expected Endpoint**: `POST /api/customer/logout`
- **Status**: ❌ NOT IMPLEMENTED
- **Used in**: `AuthService.logout()`

### 14. Get Vehicles
- **Expected Endpoint**: `GET /api/vehicles` (or similar)
- **Status**: ❌ NOT IMPLEMENTED
- **Used in**: `VehicleService.getVehicles()`

---

## 🧪 Testing Checklist

### ✅ Working Endpoints (According to Postman)
- [x] POST `/api/customer/generate-otp`
- [x] POST `/api/customer/verify-otp`
- [x] POST `/api/customer/login`
- [x] GET `/api/customer/profile`
- [x] GET `/api/customer/branch`
- [x] GET `/api/ferryboats/branch/{id}`
- [x] GET `/api/item-rates/branch/{id}`

### ❌ Missing Endpoints (Need Implementation)
- [ ] GET `/api/branches/{from}/to/{to}/routes` (or alternative for destinations)
- [ ] POST `/api/bookings`
- [ ] GET `/api/bookings`
- [ ] GET `/api/bookings/{id}`
- [ ] POST `/api/bookings/{id}/cancel`
- [ ] POST `/api/customer/logout`
- [ ] GET `/api/vehicles`

---

## 📝 Notes for Backend Developer

1. **Token Format**: The Flutter app expects the token in the response data:
   ```json
   {
     "data": {
       "token": "actual-token-string-here"
     }
   }
   ```

2. **Response Format**: All API responses should follow this structure:
   ```json
   {
     "success": true/false,
     "message": "Descriptive message",
     "data": { /* response data */ }
   }
   ```

3. **Authentication**: Protected endpoints must validate the Bearer token and return 401 if invalid/missing.

4. **CORS**: Ensure CORS headers are enabled for the Flutter app to connect.

---

## 🚀 Current Status

### What Works Now:
- ✅ User Registration (OTP flow)
- ✅ User Login
- ✅ Get Profile
- ✅ List Branches
- ✅ Get Ferries by Branch
- ✅ Get Item Rates by Branch

### What's Still Needed:
- ❌ Booking management (create, list, view, cancel)
- ❌ Vehicle management
- ❌ Logout functionality
- ❌ Routes/destinations between branches

---

**Last Updated**: 2025-12-11
**Flutter App Version**: Ready and configured
**Backend Status**: Partially implemented
