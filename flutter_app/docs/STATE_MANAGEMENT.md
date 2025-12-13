# State Management Documentation

## State Management Approach
The app uses Provider pattern for state management, offering a balance between simplicity and scalability.

## Why Provider?
- Official Flutter recommended pattern
- Easy to learn and implement
- Minimal boilerplate
- Good performance
- Strong community support

## Provider Structure

### 1. AuthProvider
**Purpose**: Manages authentication state across the app

**State Variables**:
- `User? currentUser` - Currently logged-in user
- `String? authToken` - JWT authentication token
- `bool isAuthenticated` - Authentication status
- `bool isLoading` - Loading state for auth operations

**Methods**:
- `login(email, password)` - User login
- `register(userData)` - User registration
- `logout()` - User logout
- `updateProfile(data)` - Update user profile
- `checkAuthStatus()` - Verify token validity

### 2. BookingProvider
**Purpose**: Manages booking-related state

**State Variables**:
- `List<Booking> bookings` - User's bookings
- `Booking? currentBooking` - Active booking in progress
- `bool isLoading` - Loading state

**Methods**:
- `fetchBookings()` - Load user bookings
- `createBooking(bookingData)` - Create new booking
- `cancelBooking(bookingId)` - Cancel existing booking
- `getBookingDetails(bookingId)` - Fetch booking details

### 3. RouteProvider
**Purpose**: Manages ferry routes and schedules

**State Variables**:
- `List<FerryRoute> routes` - Available ferry routes
- `List<Schedule> schedules` - Ferry schedules
- `FerryRoute? selectedRoute` - Currently selected route

**Methods**:
- `fetchRoutes()` - Load all routes
- `fetchSchedules(routeId)` - Get schedules for route
- `searchRoutes(query)` - Search routes by location

### 4. PaymentProvider
**Purpose**: Manages payment processing

**State Variables**:
- `List<PaymentMethod> paymentMethods` - Available payment options
- `Payment? currentPayment` - Active payment transaction
- `bool isProcessing` - Payment processing state

**Methods**:
- `initiatePayment(amount, method)` - Start payment
- `verifyPayment(paymentId)` - Verify payment status
- `fetchPaymentMethods()` - Load payment options

## State Persistence
- Authentication token stored in Flutter Secure Storage
- User preferences in SharedPreferences
- Booking cache for offline access
- Auto-sync on app restart

## Best Practices Implemented
- Single source of truth for each state
- Immutable state updates
- Error state handling
- Loading state management
- Optimistic updates where applicable
