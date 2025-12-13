# API Integration Documentation

## Backend API Overview
The app communicates with a Laravel-based backend API for all data operations.

## Base Configuration
- **Base URL**: Configurable environment variable
- **API Version**: v1
- **Protocol**: HTTPS (production), HTTP (development)
- **Authentication**: Bearer token (JWT)

## API Endpoints Used

### Authentication Endpoints
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `POST /api/forgot-password` - Password recovery
- `GET /api/user` - Get authenticated user details

### Ferry Routes
- `GET /api/routes` - Fetch all available routes
- `GET /api/routes/{id}` - Get specific route details
- `GET /api/routes/search` - Search routes by location

### Schedules
- `GET /api/schedules` - Get all schedules
- `GET /api/schedules/route/{routeId}` - Get schedules for specific route
- `GET /api/schedules/available` - Get available schedules for date

### Bookings
- `POST /api/bookings` - Create new booking
- `GET /api/bookings` - Get user's bookings
- `GET /api/bookings/{id}` - Get booking details
- `PUT /api/bookings/{id}` - Update booking
- `DELETE /api/bookings/{id}` - Cancel booking

### Payments
- `POST /api/payments/initiate` - Initiate payment
- `POST /api/payments/verify` - Verify payment status
- `GET /api/payments/methods` - Get available payment methods

### User Profile
- `PUT /api/profile` - Update user profile
- `POST /api/profile/picture` - Upload profile picture
- `GET /api/profile/bookings` - Get booking history

## Request/Response Format
- **Content-Type**: `application/json`
- **Accept**: `application/json`
- **Authorization**: `Bearer {token}`

## Error Handling
- Standardized error responses
- HTTP status codes
- User-friendly error messages
- Retry mechanism for network failures
- Offline queue for critical operations

## API Service Layer
- Centralized API client using Dio package
- Automatic token injection
- Request/response interceptors
- Logging for debugging
- Timeout configuration (30 seconds)
