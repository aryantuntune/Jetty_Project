# Jetty Ferry Management System - Complete Project Guide

## Table of Contents
1. [Project Overview](#project-overview)
2. [Technology Stack](#technology-stack)
3. [System Architecture](#system-architecture)
4. [Core Business Entities](#core-business-entities)
5. [Features & Functionality](#features--functionality)
6. [User Roles & Access Control](#user-roles--access-control)
7. [Database Schema](#database-schema)
8. [API Documentation](#api-documentation)
9. [Web Routes](#web-routes)
10. [Mobile Applications](#mobile-applications)
11. [Payment Integration](#payment-integration)
12. [Reporting System](#reporting-system)
13. [Installation & Setup](#installation--setup)
14. [Deployment](#deployment)
15. [Directory Structure](#directory-structure)
16. [Future Enhancements](#future-enhancements)

---

## Project Overview

**Jetty** is a comprehensive Ferry Booking & Management System designed for managing ferry services across the Konkan coast in Maharashtra, India. The system provides end-to-end management of ferry operations including online booking, counter ticketing, schedule management, and operational analytics.

### Purpose
Enable online ferry ticket booking, offline counter ticketing, and operational management of ferry services across multiple routes and terminals.

### Target Users
- **Customers**: Book ferry tickets online via web or mobile app
- **Counter Operators**: Sell tickets at ferry terminals
- **Checkers/Guards**: Verify tickets at boarding gates
- **Managers**: Oversee branch operations and reports
- **Administrators**: Manage system-wide configuration and users

### Production URL
https://unfurling.ninja

### Key Ferry Routes
The system manages 4 established ferry routes:
1. **Dabhol – Dhopave** (operating since 2003)
2. **Jaigad – Tawsal**
3. **Dighi – Agardande**
4. **Veshvi – Bagmandale**

---

## Technology Stack

### Backend Framework
- **Framework**: Laravel 12
- **PHP Version**: 8.2+
- **Database**: PostgreSQL (primary) / MySQL (legacy support)
- **Authentication**: Laravel Sanctum (API tokens) + Session-based auth

### Key Backend Dependencies
```json
{
  "laravel/framework": "^12.0",
  "laravel/sanctum": "API authentication",
  "razorpay/razorpay": "Payment gateway",
  "barryvdh/laravel-dompdf": "PDF generation",
  "endroid/qr-code": "QR code generation",
  "silviolleite/laravelpwa": "Progressive Web App support"
}
```

### Frontend Technologies
- **Web UI**: Laravel Blade templates
- **CSS Frameworks**: TailwindCSS 4.0 + Bootstrap 5
- **Build Tool**: Vite 7.1.4
- **JavaScript**: Vanilla JS with Alpine.js patterns

### Mobile Applications
- **Customer App**: Flutter 3.x (Dart)
- **Checker App**: Flutter 3.x (Dart)
- **Design System**: Material Design 3

### Infrastructure
- **Server**: Linux VPS with PHP-FPM 8.3
- **Web Server**: Nginx/Apache
- **Session Storage**: Database
- **Cache Driver**: Database
- **Queue Driver**: Database
- **File Storage**: Local filesystem

---

## System Architecture

### Multi-Platform Architecture
```
┌─────────────────────────────────────────────────────────────┐
│                    Jetty System Ecosystem                    │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │   Web Portal │  │ Customer App │  │ Checker App  │     │
│  │   (Blade)    │  │  (Flutter)   │  │  (Flutter)   │     │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘     │
│         │                  │                  │              │
│         └─────────┬────────┴──────────────────┘             │
│                   │                                          │
│         ┌─────────▼─────────────────────┐                   │
│         │  Laravel Backend (API + Web)  │                   │
│         │  - Controllers (38 files)     │                   │
│         │  - Models (17 entities)       │                   │
│         │  - Middleware (Auth/Roles)    │                   │
│         └─────────┬─────────────────────┘                   │
│                   │                                          │
│         ┌─────────▼─────────────────────┐                   │
│         │   PostgreSQL Database         │                   │
│         │   - 24+ tables                │                   │
│         │   - Complex relationships     │                   │
│         └─────────┬─────────────────────┘                   │
│                   │                                          │
│         ┌─────────▼─────────────────────┐                   │
│         │   External Services           │                   │
│         │   - Razorpay (Payments)       │                   │
│         │   - Email (SMTP)              │                   │
│         │   - QR Code Service           │                   │
│         └───────────────────────────────┘                   │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### MVC Structure
```
app/
├── Http/
│   ├── Controllers/          # Business logic (38 controllers)
│   │   ├── Api/              # Mobile app API endpoints
│   │   ├── CustomerAuth/     # Customer authentication
│   │   └── [Features]/       # Feature controllers
│   ├── Middleware/           # Auth, roles, custom middleware
│   └── Requests/             # Form validation
├── Models/                   # Eloquent models (17 core entities)
├── Mail/                     # Email notifications
└── Services/                 # Business services

routes/
├── web.php                   # Session-based routes
└── api.php                   # Token-based API routes

resources/
├── views/                    # Blade templates (50+ files)
│   ├── admin/               # Admin dashboard
│   ├── customer/            # Customer portal
│   ├── public/              # Public pages
│   ├── tickets/             # Ticket management
│   └── reports/             # Reporting
└── js/css/                  # Frontend assets

database/
├── migrations/              # Schema migrations (36 files)
└── seeders/                 # Initial data
```

---

## Core Business Entities

### Primary Models

#### 1. Branch (Ferry Terminal)
Represents physical ferry terminals/jetties.

**Key Fields**:
- `branch_id` - Unique identifier
- `branch_name` - Terminal name
- `branch_address` - Physical address
- `latitude`, `longitude` - GPS coordinates
- `branch_phone`, `branch_email` - Contact info
- `is_active` - Operational status

**Relationships**:
- Has many: Ferry boats, schedules, item rates, tickets
- Has many: Users (staff assigned to branch)

#### 2. FerryBoat
Ferry vessels operating on routes.

**Key Fields**:
- `ferry_number` - Boat identifier
- `ferry_name` - Display name
- `capacity_passengers` - Maximum passengers
- `capacity_vehicles` - Maximum vehicles
- `is_active` - Operational status

**Relationships**:
- Belongs to: Branch
- Has many: Schedules, tickets
- Has many: Users (assigned crew/checkers)

#### 3. FerrySchedule
Scheduled departure times for ferries.

**Key Fields**:
- `schedule_id` - Unique ID
- `departure_hour`, `departure_minute` - Time
- `branch_id` - Departure terminal
- `ferry_boat_id` - Assigned ferry

**Purpose**: Defines recurring daily departures.

#### 4. User (Staff/Employees)
System users including admins, operators, checkers.

**Key Fields**:
- `user_id` - Unique identifier
- `name`, `email`, `password` - Auth credentials
- `role_id` - Role assignment (1-5)
- `branch_id` - Assigned branch
- `ferry_boat_id` - Assigned ferry (for checkers)

**Roles**:
1. Super Admin - Full system access
2. Administrator - Operational management
3. Manager - Branch management
4. Operator - Ticket sales
5. Checker - Ticket verification only

#### 5. Customer
Online booking users.

**Key Fields**:
- `customer_id` - Unique identifier
- `email`, `mobile`, `password` - Credentials
- `first_name`, `last_name` - Personal info
- `profile_image` - Avatar
- `google_id` - OAuth integration
- `email_verified_at` - Verification timestamp

**Relationships**:
- Has many: Bookings, houseboat bookings

#### 6. Ticket
Counter-sold tickets (offline sales).

**Key Fields**:
- `ticket_id` - Unique ID
- `ticket_no` - Display number (e.g., TKT-2025-0001)
- `ticket_date`, `ticket_time` - Journey date/time
- `from_branch_id`, `to_branch_id` - Route
- `ferry_boat_id` - Assigned ferry
- `total_amount` - Total price
- `payment_mode` - Cash/Card/UPI/Online
- `ticket_verified` - Verification status
- `verified_by_user_id` - Checker who verified
- `verified_at` - Verification timestamp

**Relationships**:
- Has many: Ticket lines (line items)
- Belongs to: From/to branches, ferry boat
- Belongs to: Customer (optional for counter tickets)
- Belongs to: Checker user (verification)

#### 7. TicketLine
Individual items on a ticket (passengers, vehicles).

**Key Fields**:
- `line_id` - Unique ID
- `ticket_id` - Parent ticket
- `item_name` - Item type (Adult, Child, Motorcycle, etc.)
- `qty` - Quantity
- `rate` - Base price
- `levy` - Additional levy
- `amount` - Line total (qty × (rate + levy))

#### 8. Booking
Online reservations via web/app.

**Key Fields**:
- `booking_id` - Unique ID
- `booking_reference` - Display reference (e.g., BOOK-ABC123)
- `customer_id` - Customer who booked
- `from_branch_id`, `to_branch_id` - Route
- `booking_date`, `booking_time` - Journey details
- `items` - JSON array of booked items
- `total_amount` - Total price
- `payment_status` - pending/paid/failed
- `booking_status` - pending/confirmed/completed/cancelled
- `razorpay_order_id`, `razorpay_payment_id` - Payment tracking

**Status Workflow**:
1. pending → User creates booking
2. confirmed → Payment successful
3. completed → Ticket verified at gate
4. cancelled → User/admin cancels

#### 9. ItemCategory
Categories of ticketable items.

**Key Fields**:
- `category_id` - Unique ID
- `category_name` - Name (Passengers, Vehicles, Cargo)
- `levy` - Standard levy amount

**Examples**:
- Passengers: Adults, Children, Infants
- Vehicles: Motorcycles, Cars, Trucks
- Cargo: Parcels, Livestock

#### 10. ItemRate
Pricing for specific items.

**Key Fields**:
- `rate_id` - Unique ID
- `item_name` - Item identifier
- `item_rate` - Base price
- `branch_id` - Branch-specific pricing
- `starting_date`, `ending_date` - Validity period
- `surcharge` - Additional charges

**Features**:
- Date-based pricing (seasonal rates)
- Branch-specific rates
- Overlapping rate validation

#### 11. Guest
Regular passenger database for quick lookup.

**Key Fields**:
- `guest_id` - Unique ID
- `first_name`, `last_name` - Name
- `mobile` - Contact
- `id_proof_type`, `id_proof_number` - Identification

**Purpose**: Speed up counter ticket entry for frequent travelers.

#### 12. SpecialCharge
Additional fees and surcharges.

**Key Fields**:
- `charge_id` - Unique ID
- `charge_name` - Description
- `amount` - Fee amount
- `charge_type` - Category
- `applicable_from_date`, `applicable_to_date` - Validity

**Examples**:
- Holiday surcharges
- Fuel levies
- Special event fees

#### 13. HouseboatRoom
Houseboat rental inventory.

**Key Fields**:
- `room_id` - Unique ID
- `room_name` - Display name
- `price_per_night` - Rental rate
- `max_capacity` - Guest limit
- `amenities` - JSON array of features
- `gallery` - JSON array of image paths
- `is_available` - Availability status

#### 14. HouseboatBooking
Houseboat reservations.

**Key Fields**:
- `booking_id` - Unique ID
- `booking_reference` - Display reference
- `room_id` - Booked room
- `customer_id` - Customer
- `check_in`, `check_out` - Dates
- `num_guests` - Guest count
- `total_price` - Total cost
- `booking_status` - pending/confirmed/completed/cancelled

#### 15. DailySummary
Aggregated daily reports (automated).

**Key Fields**:
- `summary_id` - Unique ID
- `summary_date` - Report date
- `branch_id` - Branch
- `ferry_boat_id` - Ferry (optional)
- `total_tickets` - Ticket count
- `total_passengers` - Passenger count
- `total_vehicles` - Vehicle count
- `total_revenue_cash`, `total_revenue_card`, `total_revenue_upi`, `total_revenue_online` - Revenue by payment mode

**Purpose**: Daily automated aggregation for analytics.

#### 16. BranchTransfer
Employee transfer records.

**Key Fields**:
- `transfer_id` - Unique ID
- `user_id` - Employee
- `from_branch_id`, `to_branch_id` - Transfer route
- `transfer_date` - Effective date
- `approved_by` - Approving manager
- `approved_at` - Approval timestamp

---

## Features & Functionality

### 1. Public Website Features

#### Route Information Pages
- **URL Pattern**: `/route/{slug}`
- **Routes**: Dabhol-Dhopave, Jaigad-Tawsal, Dighi-Agardande, Veshvi-Bagmandale
- **Content**: Route details, schedules, pricing, contact info

#### About & Contact Pages
- Company information
- Contact forms
- Location maps

#### Houseboat Showcase
- Room gallery
- Pricing information
- Booking redirect to customer portal

### 2. Customer Portal (Web)

#### Authentication
- **Registration**: Email/password with OTP verification
- **Login**: Email + password or Google OAuth
- **Password Reset**: Email-based OTP flow

#### Dashboard
- Browse available routes
- View upcoming bookings
- Quick booking access

#### Booking Flow
1. Select route (from/to branches)
2. Choose date and time
3. Add passengers (adults, children)
4. Add vehicles (motorcycles, cars, etc.)
5. Review total amount
6. Proceed to payment (Razorpay)
7. Receive QR code ticket

#### Booking Management
- View booking history
- Download tickets (PDF with QR code)
- Check verification status
- Cancel bookings (if permitted)

#### Profile Management
- Update personal details
- Change password
- Upload profile picture
- Manage saved addresses

### 3. Customer Mobile App (Flutter)

#### Features
- **OTP-Based Registration**: 6-digit email OTP
- **Google Sign-In**: OAuth integration
- **Route Browsing**: View all available ferry routes
- **Schedule Lookup**: Check departure times
- **Booking Creation**: Multi-item selection with payment
- **QR Ticket Display**: Scannable QR codes
- **Booking History**: Past and upcoming trips
- **Push Notifications**: Booking confirmations (future)
- **Offline Mode**: Cache booking details (future)

#### API Integration
All endpoints use Bearer token authentication via Laravel Sanctum.

### 4. Counter Operations (Web Dashboard)

#### Ticket Entry Form
**Route**: `/ticket-entry`

**Workflow**:
1. Select route (from/to branch)
2. Choose ferry and departure time
3. Add passenger details:
   - Name, age, ID proof
   - Guest lookup for regulars
4. Add vehicle details (if applicable):
   - Type (motorcycle, car, truck)
   - Registration number
5. Add items with quantities:
   - Adults, children
   - Vehicle types
6. Apply discounts/surcharges
7. Select payment mode (Cash/Card/UPI)
8. Generate ticket with QR code
9. Print ticket

**Features**:
- Auto-calculation of amounts
- Item rate lookup based on date and branch
- Guest quick-add from database
- Duplicate ticket prevention
- Sequential ticket numbering

#### Ticket Reports
**Route**: `/reports/tickets`

**Filters**:
- Date range
- Branch
- Ferry boat
- Payment mode

**Metrics**:
- Total tickets sold
- Passenger count
- Vehicle count
- Revenue by payment mode

**Export**: CSV download

#### Vehicle Ticket Report
**Route**: `/reports/vehicle-tickets`

**Features**:
- Vehicle-wise breakdown
- Registration number tracking
- Vehicle type statistics

### 5. Administrative Dashboard

#### User Management
**Route**: `/users`

**Features**:
- Create users with role assignment
- Edit user details and branch assignment
- Deactivate/activate users
- Reset passwords
- View login history (future)

**Permissions**:
- Super Admin: All users
- Administrator: Branch-level users
- Manager: Operators and checkers only

#### Branch Management
**Route**: `/branches`

**CRUD Operations**:
- Create new ferry terminals
- Edit branch details (name, address, contact)
- Set GPS coordinates
- Toggle active status
- View branch statistics

#### Ferry Boat Management
**Route**: `/ferry-boats`

**Features**:
- Add new ferries with capacity info
- Assign to branches
- Set operational status
- Track maintenance (future)

#### Schedule Management
**Route**: `/ferry-schedules`

**Features**:
- Create recurring departure times
- Assign ferries to schedules
- Bulk schedule creation
- Seasonal schedule variations (future)

#### Item Category Management
**Route**: `/item-categories`

**Operations**:
- Define new categories (Passengers, Vehicles, etc.)
- Set standard levy amounts
- Organize items hierarchically

#### Item Rate Management
**Route**: `/item-rates`

**Advanced Pricing**:
- Create date-based pricing
- Branch-specific rates
- Seasonal pricing
- Bulk rate updates
- Historical rate tracking

**Validation**:
- No overlapping date ranges for same item/branch
- Starting date must be before ending date

#### Guest Management
**Route**: `/guests`

**Features**:
- Add frequent passengers
- Quick lookup during ticket entry
- Update contact info
- Track travel history

#### Special Charges Management
**Route**: `/special-charges`

**Use Cases**:
- Holiday surcharges
- Fuel levy
- Environmental fees
- Event-based charges

#### Employee Transfer Management
**Route**: `/employee-transfers`

**Workflow**:
1. Manager creates transfer request
2. Administrator approves
3. Employee branch_id updates automatically
4. Historical record maintained

### 6. Checker/Guard Operations

#### Mobile App (Flutter)
**Purpose**: Ticket verification at boarding gates

**Features**:
- Login with staff credentials
- Scan QR codes from customer tickets
- Verify ticket validity:
  - Check if ticket is for current date/time
  - Verify not already used
  - Confirm correct ferry/route
- Mark ticket as verified
- View verification history

**API Endpoints**:
- `POST /api/checker/login` - Authentication
- `POST /api/checker/verify-ticket` - QR verification

### 7. Houseboat Management

#### Public Booking Interface
**Route**: `/houseboat`

**Features**:
- Browse available rooms
- View room gallery
- Check availability calendar
- Book rooms (redirect to customer portal)

#### Admin Panel
**Route**: `/houseboat-admin`

**Room Management**:
- Create rooms with details
- Set pricing and capacity
- Upload gallery images
- Manage amenities (JSON array)
- Toggle availability

**Booking Management**:
- View all bookings
- Update booking status
- Send confirmation emails
- Generate booking reports

### 8. Reporting & Analytics

#### Daily Summary Report
**Automated Process** (Laravel Scheduler):
- Runs daily at midnight
- Aggregates tickets by:
  - Date
  - Branch
  - Ferry boat
- Calculates:
  - Total tickets
  - Passenger count
  - Vehicle count
  - Revenue by payment mode (Cash/Card/UPI/Online)

#### Custom Reports
**Available Reports**:
1. **Ticket Report** - Date range, branch, ferry filters
2. **Vehicle Report** - Vehicle-specific analysis
3. **Payment Mode Report** - Cash vs digital payments
4. **Customer Booking Report** - Online booking analysis
5. **Ferry Utilization Report** - Ferry-wise trips and revenue

**Export Formats**:
- CSV (tickets, vehicles)
- PDF (individual tickets)
- Excel (future enhancement)

---

## User Roles & Access Control

### Role Hierarchy

| Role ID | Role Name | Description | Access Level |
|---------|-----------|-------------|--------------|
| 1 | Super Admin | System owner | Full access to all features |
| 2 | Administrator | Operations manager | Manage branches, users, settings (except super admin functions) |
| 3 | Manager | Branch manager | Branch-level operations, reports, staff management |
| 4 | Operator | Counter staff | Ticket entry, guest management, basic reports |
| 5 | Checker | Gate guard | Ticket verification only (blocked from dashboard) |

### Permission Matrix

| Feature | Super Admin | Administrator | Manager | Operator | Checker |
|---------|-------------|---------------|---------|----------|---------|
| Dashboard Access | ✓ | ✓ | ✓ | ✓ | ✗ |
| Ticket Entry | ✓ | ✓ | ✓ | ✓ | ✗ |
| Ticket Reports | ✓ | ✓ | ✓ | Limited | ✗ |
| User Management | ✓ | ✓ (no super admins) | Limited | ✗ | ✗ |
| Branch Management | ✓ | ✓ | ✗ | ✗ | ✗ |
| Ferry Management | ✓ | ✓ | ✗ | ✗ | ✗ |
| Schedule Management | ✓ | ✓ | ✓ | ✗ | ✗ |
| Item Rate Management | ✓ | ✓ | ✓ | ✗ | ✗ |
| Guest Management | ✓ | ✓ | ✓ | ✓ | ✗ |
| Special Charges | ✓ | ✓ | ✓ | ✗ | ✗ |
| Employee Transfers | ✓ | ✓ (approve) | ✓ (request) | ✗ | ✗ |
| Houseboat Admin | ✓ | ✓ | ✗ | ✗ | ✗ |
| Ticket Verification (mobile) | ✗ | ✗ | ✗ | ✗ | ✓ |

### Middleware Guards

**Web Routes** (Session-based):
- `auth` - Requires authenticated user
- `role:1,2` - Requires specific role IDs
- `blockRole5` - Blocks checkers from admin panel

**API Routes** (Token-based):
- `auth:sanctum` - Requires valid API token
- Customer guard - Separate authentication for customers

---

## Database Schema

### Entity Relationship Diagram (Conceptual)

```
┌─────────────┐       ┌──────────────┐       ┌─────────────┐
│   Branch    │───┬───│  FerryBoat   │───────│ FerrySchedule│
└─────────────┘   │   └──────────────┘       └─────────────┘
       │          │          │
       │          │          │
       ▼          ▼          ▼
┌─────────────────────────────────┐
│          Ticket                 │
│  - ticket_no                    │
│  - from_branch_id               │
│  - to_branch_id                 │
│  - ferry_boat_id                │
│  - ticket_verified              │
└─────────────────────────────────┘
       │                    │
       ▼                    ▼
┌─────────────┐       ┌─────────────┐
│ TicketLine  │       │   Customer  │
│  - item_name│       │  (optional) │
│  - qty      │       └─────────────┘
│  - amount   │              │
└─────────────┘              ▼
                      ┌─────────────┐
                      │   Booking   │
                      │  - online   │
                      └─────────────┘
```

### Key Tables

#### branches
```sql
- branch_id (PK)
- branch_name
- branch_address
- latitude, longitude
- branch_phone, branch_email
- is_active (boolean)
- created_at, updated_at
```

#### ferry_boats
```sql
- ferry_boat_id (PK)
- ferry_number (unique)
- ferry_name
- capacity_passengers
- capacity_vehicles
- branch_id (FK → branches)
- is_active (boolean)
- created_at, updated_at
```

#### ferry_schedules
```sql
- schedule_id (PK)
- departure_hour
- departure_minute
- branch_id (FK → branches)
- ferry_boat_id (FK → ferry_boats)
- created_at, updated_at
```

#### users
```sql
- user_id (PK)
- name
- email (unique)
- password
- role_id (1-5)
- branch_id (FK → branches, nullable)
- ferry_boat_id (FK → ferry_boats, nullable)
- is_active (boolean)
- created_at, updated_at
```

#### customers
```sql
- customer_id (PK)
- email (unique)
- mobile (unique)
- password
- first_name, last_name
- profile_image (nullable)
- google_id (nullable)
- email_verified_at
- created_at, updated_at
```

#### tickets
```sql
- ticket_id (PK)
- ticket_no (unique, e.g., TKT-2025-0001)
- ticket_date (indexed)
- ticket_time
- from_branch_id (FK → branches)
- to_branch_id (FK → branches)
- ferry_boat_id (FK → ferry_boats)
- customer_id (FK → customers, nullable)
- total_amount
- payment_mode (cash/card/upi/online)
- ticket_verified (boolean, indexed)
- verified_by_user_id (FK → users, nullable)
- verified_at (nullable)
- created_at, updated_at
```

#### ticket_lines
```sql
- line_id (PK)
- ticket_id (FK → tickets, cascade delete)
- item_name
- qty
- rate
- levy
- amount (calculated: qty × (rate + levy))
- created_at, updated_at
```

#### bookings
```sql
- booking_id (PK)
- booking_reference (unique, e.g., BOOK-ABC123)
- customer_id (FK → customers)
- from_branch_id (FK → branches)
- to_branch_id (FK → branches)
- booking_date
- booking_time
- items (JSON array)
- total_amount
- payment_status (pending/paid/failed)
- booking_status (pending/confirmed/completed/cancelled)
- razorpay_order_id
- razorpay_payment_id
- created_at, updated_at
```

#### item_categories
```sql
- category_id (PK)
- category_name
- levy (standard levy amount)
- created_at, updated_at
```

#### item_rates
```sql
- rate_id (PK)
- item_name
- item_rate
- branch_id (FK → branches)
- starting_date
- ending_date
- surcharge
- created_at, updated_at
- UNIQUE(item_name, branch_id, starting_date)
```

#### guests
```sql
- guest_id (PK)
- first_name
- last_name
- mobile
- id_proof_type
- id_proof_number
- created_at, updated_at
```

#### special_charges
```sql
- charge_id (PK)
- charge_name
- amount
- charge_type
- applicable_from_date
- applicable_to_date
- created_at, updated_at
```

#### houseboat_rooms
```sql
- room_id (PK)
- room_name
- price_per_night
- max_capacity
- amenities (JSON array)
- gallery (JSON array of image paths)
- is_available (boolean)
- created_at, updated_at
```

#### houseboat_bookings
```sql
- booking_id (PK)
- booking_reference (unique)
- room_id (FK → houseboat_rooms)
- customer_id (FK → customers)
- check_in
- check_out
- num_guests
- total_price
- booking_status (pending/confirmed/completed/cancelled)
- created_at, updated_at
```

#### daily_summaries
```sql
- summary_id (PK)
- summary_date (indexed)
- branch_id (FK → branches, nullable)
- ferry_boat_id (FK → ferry_boats, nullable)
- total_tickets
- total_passengers
- total_vehicles
- total_revenue_cash
- total_revenue_card
- total_revenue_upi
- total_revenue_online
- created_at, updated_at
- UNIQUE(summary_date, branch_id, ferry_boat_id)
```

#### branch_transfers
```sql
- transfer_id (PK)
- user_id (FK → users)
- from_branch_id (FK → branches)
- to_branch_id (FK → branches)
- transfer_date
- approved_by (FK → users, nullable)
- approved_at (nullable)
- created_at, updated_at
```

### Database Indexes

**Performance Optimizations**:
- Primary keys on all tables
- Foreign key indexes
- `tickets.ticket_date` - Fast date filtering
- `tickets.ticket_verified` - Quick verification lookups
- `bookings.booking_status` - Status filtering
- `daily_summaries (summary_date, branch_id)` - Composite index for reports
- Unique constraints on: ticket_no, booking_reference, email, mobile

---

## API Documentation

### Base URL
- **Production**: `https://unfurling.ninja/api`
- **Local**: `http://localhost/api`

### Authentication

All protected endpoints require Bearer token in headers:
```
Authorization: Bearer {token}
```

Tokens obtained from login/registration endpoints and stored by mobile app.

### Public Endpoints

#### 1. Customer Registration - Generate OTP
```
POST /api/customer/generate-otp
```

**Request**:
```json
{
  "email": "customer@example.com",
  "mobile": "9876543210",
  "first_name": "John",
  "last_name": "Doe",
  "password": "securepassword"
}
```

**Response**:
```json
{
  "success": true,
  "message": "OTP sent to email"
}
```

**Notes**: 6-digit OTP valid for 10 minutes, cached in database.

#### 2. Customer Registration - Verify OTP
```
POST /api/customer/verify-otp
```

**Request**:
```json
{
  "email": "customer@example.com",
  "otp": "123456"
}
```

**Response**:
```json
{
  "success": true,
  "message": "Registration successful",
  "token": "1|abcdef123456...",
  "customer": {
    "customer_id": 1,
    "email": "customer@example.com",
    "first_name": "John",
    "last_name": "Doe",
    "mobile": "9876543210"
  }
}
```

#### 3. Customer Login
```
POST /api/customer/login
```

**Request**:
```json
{
  "email": "customer@example.com",
  "password": "securepassword"
}
```

**Response**:
```json
{
  "success": true,
  "token": "2|ghijkl789012...",
  "customer": {
    "customer_id": 1,
    "email": "customer@example.com",
    "first_name": "John",
    "last_name": "Doe"
  }
}
```

#### 4. Checker Login
```
POST /api/checker/login
```

**Request**:
```json
{
  "email": "checker@example.com",
  "password": "checkerpassword"
}
```

**Response**:
```json
{
  "success": true,
  "token": "3|mnopqr345678...",
  "user": {
    "user_id": 5,
    "name": "Checker Name",
    "role_id": 5,
    "branch": {
      "branch_id": 1,
      "branch_name": "Dabhol Jetty"
    },
    "ferry_boat": {
      "ferry_boat_id": 1,
      "ferry_name": "Sea King"
    }
  }
}
```

### Protected Endpoints (Require Authentication)

#### 5. Get Customer Profile
```
GET /api/customer/profile
Authorization: Bearer {token}
```

**Response**:
```json
{
  "customer_id": 1,
  "email": "customer@example.com",
  "mobile": "9876543210",
  "first_name": "John",
  "last_name": "Doe",
  "profile_image": "/storage/profiles/123.jpg"
}
```

#### 6. Update Customer Profile
```
PUT /api/customer/profile
Authorization: Bearer {token}
```

**Request** (multipart/form-data):
```
first_name: John
last_name: Smith
mobile: 9876543210
profile_image: [file]
```

**Response**:
```json
{
  "success": true,
  "message": "Profile updated",
  "customer": { ... }
}
```

#### 7. Get Branches
```
GET /api/customer/branch
Authorization: Bearer {token}
```

**Response**:
```json
{
  "branches": [
    {
      "branch_id": 1,
      "branch_name": "Dabhol Jetty",
      "branch_address": "Dabhol, Maharashtra",
      "latitude": "17.5833",
      "longitude": "73.1667",
      "branch_phone": "02357-123456"
    }
  ]
}
```

#### 8. Get Ferries for Branch
```
GET /api/ferries/branch/{branch_id}
Authorization: Bearer {token}
```

**Response**:
```json
{
  "ferries": [
    {
      "ferry_boat_id": 1,
      "ferry_number": "MH-01",
      "ferry_name": "Sea King",
      "capacity_passengers": 200,
      "capacity_vehicles": 20,
      "schedules": [
        {
          "schedule_id": 1,
          "departure_hour": 8,
          "departure_minute": 0
        },
        {
          "schedule_id": 2,
          "departure_hour": 14,
          "departure_minute": 30
        }
      ]
    }
  ]
}
```

#### 9. Get Item Rates for Branch
```
GET /api/rates/branch/{branch_id}?date=2026-01-15
Authorization: Bearer {token}
```

**Response**:
```json
{
  "rates": [
    {
      "item_name": "Adult Passenger",
      "item_rate": 50,
      "levy": 5,
      "surcharge": 0,
      "total": 55
    },
    {
      "item_name": "Child Passenger",
      "item_rate": 25,
      "levy": 5,
      "surcharge": 0,
      "total": 30
    },
    {
      "item_name": "Motorcycle",
      "item_rate": 100,
      "levy": 10,
      "surcharge": 0,
      "total": 110
    }
  ]
}
```

#### 10. Get Customer Bookings
```
GET /api/bookings
Authorization: Bearer {token}
```

**Response**:
```json
{
  "bookings": [
    {
      "booking_id": 1,
      "booking_reference": "BOOK-ABC123",
      "from_branch": "Dabhol Jetty",
      "to_branch": "Dhopave Jetty",
      "booking_date": "2026-01-15",
      "booking_time": "08:00:00",
      "items": [
        {"item_name": "Adult Passenger", "qty": 2},
        {"item_name": "Motorcycle", "qty": 1}
      ],
      "total_amount": 220,
      "booking_status": "confirmed",
      "payment_status": "paid",
      "qr_code_url": "/storage/qr/BOOK-ABC123.png"
    }
  ]
}
```

#### 11. Create Booking
```
POST /api/bookings
Authorization: Bearer {token}
```

**Request**:
```json
{
  "from_branch_id": 1,
  "to_branch_id": 2,
  "booking_date": "2026-01-15",
  "booking_time": "08:00",
  "items": [
    {"item_name": "Adult Passenger", "qty": 2, "rate": 55},
    {"item_name": "Motorcycle", "qty": 1, "rate": 110}
  ],
  "total_amount": 220
}
```

**Response**:
```json
{
  "success": true,
  "booking_id": 1,
  "booking_reference": "BOOK-ABC123",
  "razorpay_order_id": "order_123456",
  "razorpay_key": "rzp_test_..."
}
```

**Next Step**: Use Razorpay SDK to process payment with `razorpay_order_id`.

#### 12. Get Booking Details
```
GET /api/bookings/{id}
Authorization: Bearer {token}
```

**Response**:
```json
{
  "booking_id": 1,
  "booking_reference": "BOOK-ABC123",
  "from_branch": {
    "branch_id": 1,
    "branch_name": "Dabhol Jetty"
  },
  "to_branch": {
    "branch_id": 2,
    "branch_name": "Dhopave Jetty"
  },
  "booking_date": "2026-01-15",
  "booking_time": "08:00:00",
  "items": [...],
  "total_amount": 220,
  "payment_status": "paid",
  "booking_status": "confirmed",
  "qr_code_url": "/storage/qr/BOOK-ABC123.png",
  "created_at": "2026-01-10T10:30:00Z"
}
```

#### 13. Create Razorpay Order
```
POST /api/razorpay/order
Authorization: Bearer {token}
```

**Request**:
```json
{
  "amount": 220,
  "booking_id": 1
}
```

**Response**:
```json
{
  "order_id": "order_123456",
  "amount": 22000,
  "currency": "INR",
  "razorpay_key": "rzp_test_..."
}
```

**Notes**: Amount in paise (₹220 → 22000 paise).

#### 14. Verify Razorpay Payment
```
POST /api/razorpay/verify
Authorization: Bearer {token}
```

**Request**:
```json
{
  "razorpay_order_id": "order_123456",
  "razorpay_payment_id": "pay_789012",
  "razorpay_signature": "abc123...",
  "booking_id": 1
}
```

**Response**:
```json
{
  "success": true,
  "message": "Payment verified successfully",
  "booking": {
    "booking_id": 1,
    "payment_status": "paid",
    "booking_status": "confirmed"
  }
}
```

**Action**: Booking status updates to "confirmed", QR code generated.

#### 15. Verify Ticket (Checker App)
```
POST /api/checker/verify-ticket
Authorization: Bearer {token}
```

**Request**:
```json
{
  "ticket_identifier": "BOOK-ABC123"
}
```

**Response** (Success):
```json
{
  "success": true,
  "message": "Ticket verified successfully",
  "ticket": {
    "ticket_no": "BOOK-ABC123",
    "passenger_name": "John Doe",
    "from_branch": "Dabhol Jetty",
    "to_branch": "Dhopave Jetty",
    "booking_date": "2026-01-15",
    "booking_time": "08:00",
    "items": [...],
    "verified_at": "2026-01-15T07:45:00Z"
  }
}
```

**Response** (Already Verified):
```json
{
  "success": false,
  "message": "Ticket already verified",
  "verified_at": "2026-01-15T07:30:00Z"
}
```

**Response** (Invalid):
```json
{
  "success": false,
  "message": "Invalid ticket"
}
```

### Error Responses

All API endpoints return errors in this format:
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

**Common HTTP Status Codes**:
- `200` - Success
- `201` - Created
- `400` - Bad Request (validation errors)
- `401` - Unauthorized (missing/invalid token)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found
- `422` - Unprocessable Entity (validation failed)
- `500` - Internal Server Error

---

## Web Routes

### Public Routes

| Route | Method | Controller | Description |
|-------|--------|------------|-------------|
| `/` | GET | PublicController@index | Homepage |
| `/about` | GET | PublicController@about | About page |
| `/contact` | GET | PublicController@contact | Contact page |
| `/route/{slug}` | GET | PublicController@route | Ferry route details |
| `/houseboat` | GET | HouseboatController@index | Houseboat showcase |

### Customer Authentication

| Route | Method | Controller | Description |
|-------|--------|------------|-------------|
| `/customer/register` | GET | CustomerAuthController@showRegistrationForm | Registration form |
| `/customer/register` | POST | CustomerAuthController@register | Process registration |
| `/customer/login` | GET | CustomerAuthController@showLoginForm | Login form |
| `/customer/login` | POST | CustomerAuthController@login | Process login |
| `/customer/logout` | POST | CustomerAuthController@logout | Logout |
| `/customer/password/reset` | GET/POST | Password reset flow | Reset password |

### Customer Portal (Protected)

**Middleware**: `auth:customer`

| Route | Method | Controller | Description |
|-------|--------|------------|-------------|
| `/customer/dashboard` | GET | CustomerController@dashboard | Customer dashboard |
| `/customer/booking` | GET | BookingController@create | Booking form |
| `/customer/booking` | POST | BookingController@store | Create booking |
| `/customer/bookings` | GET | BookingController@index | Booking history |
| `/customer/bookings/{id}` | GET | BookingController@show | Booking details |
| `/customer/profile` | GET | CustomerController@profile | View profile |
| `/customer/profile` | PUT | CustomerController@updateProfile | Update profile |

### Admin Authentication

| Route | Method | Controller | Description |
|-------|--------|------------|-------------|
| `/login` | GET | Auth\LoginController@showLoginForm | Admin login form |
| `/login` | POST | Auth\LoginController@login | Process admin login |
| `/logout` | POST | Auth\LoginController@logout | Admin logout |

### Admin Dashboard (Protected)

**Middleware**: `auth`, `blockRole5` (blocks checkers)

| Route | Method | Controller | Description |
|-------|--------|------------|-------------|
| `/home` | GET | HomeController@index | Admin dashboard |
| `/dashboard` | GET | HomeController@dashboard | Dashboard alternative |

### Ticket Management

| Route | Method | Controller | Description |
|-------|--------|------------|-------------|
| `/ticket-entry` | GET | TicketEntryController@create | Ticket entry form |
| `/ticket-entry` | POST | TicketEntryController@store | Save ticket |
| `/verify` | GET | TicketVerifyController@index | Ticket verification |
| `/verify-ticket` | POST | TicketVerifyController@verify | Process verification |

### Reports

| Route | Method | Controller | Description |
|-------|--------|------------|-------------|
| `/reports/tickets` | GET | TicketReportController@index | Daily ticket report |
| `/reports/tickets/csv` | GET | TicketReportController@exportCsv | Export CSV |
| `/reports/vehicle-tickets` | GET | TicketReportController@vehicleReport | Vehicle report |

### Branch Management

| Route | Method | Controller | Description |
|-------|--------|------------|-------------|
| `/branches` | GET | BranchController@index | List branches |
| `/branches/create` | GET | BranchController@create | Create form |
| `/branches` | POST | BranchController@store | Save branch |
| `/branches/{id}/edit` | GET | BranchController@edit | Edit form |
| `/branches/{id}` | PUT | BranchController@update | Update branch |
| `/branches/{id}` | DELETE | BranchController@destroy | Delete branch |

### Ferry Boat Management

| Route | Method | Controller | Description |
|-------|--------|------------|-------------|
| `/ferry-boats` | GET | FerryBoatController@index | List ferries |
| `/ferry-boats/create` | GET | FerryBoatController@create | Create form |
| `/ferry-boats` | POST | FerryBoatController@store | Save ferry |
| `/ferry-boats/{id}/edit` | GET | FerryBoatController@edit | Edit form |
| `/ferry-boats/{id}` | PUT | FerryBoatController@update | Update ferry |
| `/ferry-boats/{id}` | DELETE | FerryBoatController@destroy | Delete ferry |

### Schedule Management

| Route | Method | Controller | Description |
|-------|--------|------------|-------------|
| `/ferry-schedules` | GET | FerryScheduleController@index | List schedules |
| `/ferry-schedules/create` | GET | FerryScheduleController@create | Create form |
| `/ferry-schedules` | POST | FerryScheduleController@store | Save schedule |
| `/ferry-schedules/{id}/edit` | GET | FerryScheduleController@edit | Edit form |
| `/ferry-schedules/{id}` | PUT | FerryScheduleController@update | Update schedule |
| `/ferry-schedules/{id}` | DELETE | FerryScheduleController@destroy | Delete schedule |

### Item Management

| Route | Method | Controller | Description |
|-------|--------|------------|-------------|
| `/item-categories` | GET | ItemCategoryController@index | List categories |
| `/item-rates` | GET | ItemRateController@index | List rates |
| `/item-rates/create` | GET | ItemRateController@create | Create rate form |
| `/item-rates` | POST | ItemRateController@store | Save rate |
| `/item-rates/{id}/edit` | GET | ItemRateController@edit | Edit rate form |
| `/item-rates/{id}` | PUT | ItemRateController@update | Update rate |

### Guest Management

| Route | Method | Controller | Description |
|-------|--------|------------|-------------|
| `/guests` | GET | GuestController@index | List guests |
| `/guests/create` | GET | GuestController@create | Create guest form |
| `/guests` | POST | GuestController@store | Save guest |
| `/guests/{id}/edit` | GET | GuestController@edit | Edit guest form |
| `/guests/{id}` | PUT | GuestController@update | Update guest |

### Checker Management

| Route | Method | Controller | Description |
|-------|--------|------------|-------------|
| `/checkers` | GET | CheckerController@index | List checkers |
| `/checkers/create` | GET | CheckerController@create | Create checker |
| `/checkers` | POST | CheckerController@store | Save checker |

### Special Charges

| Route | Method | Controller | Description |
|-------|--------|------------|-------------|
| `/special-charges` | GET | SpecialChargeController@index | List charges |
| `/special-charges/create` | GET | SpecialChargeController@create | Create charge |
| `/special-charges` | POST | SpecialChargeController@store | Save charge |

### Employee Transfers

| Route | Method | Controller | Description |
|-------|--------|------------|-------------|
| `/employee-transfers` | GET | EmployeeTransferController@index | List transfers |
| `/employee-transfers/create` | GET | EmployeeTransferController@create | Create transfer |
| `/employee-transfers` | POST | EmployeeTransferController@store | Save transfer |
| `/employee-transfers/{id}/approve` | POST | EmployeeTransferController@approve | Approve transfer |

### Houseboat Admin

| Route | Method | Controller | Description |
|-------|--------|------------|-------------|
| `/houseboat-admin` | GET | HouseboatAdminController@index | Houseboat dashboard |
| `/houseboat-admin/rooms` | GET | HouseboatAdminController@rooms | List rooms |
| `/houseboat-admin/rooms/create` | GET | HouseboatAdminController@createRoom | Create room |
| `/houseboat-admin/bookings` | GET | HouseboatAdminController@bookings | List bookings |

---

## Mobile Applications

### Customer Mobile App (Flutter)

#### Project Structure
```
flutter_app/
├── lib/
│   ├── main.dart                 # App entry point
│   ├── screens/                  # UI screens
│   │   ├── auth/
│   │   │   ├── login_screen.dart
│   │   │   ├── register_screen.dart
│   │   │   └── otp_screen.dart
│   │   ├── home/
│   │   │   └── dashboard_screen.dart
│   │   ├── booking/
│   │   │   ├── booking_form_screen.dart
│   │   │   ├── booking_list_screen.dart
│   │   │   └── booking_detail_screen.dart
│   │   └── profile/
│   │       └── profile_screen.dart
│   ├── services/                 # API services
│   │   ├── api_service.dart
│   │   ├── auth_service.dart
│   │   └── booking_service.dart
│   ├── models/                   # Data models
│   │   ├── customer.dart
│   │   ├── booking.dart
│   │   └── branch.dart
│   └── widgets/                  # Reusable widgets
├── android/                      # Android config
├── ios/                          # iOS config
└── pubspec.yaml                  # Dependencies
```

#### Key Dependencies
```yaml
dependencies:
  flutter:
    sdk: flutter
  http: ^1.1.0                    # HTTP requests
  shared_preferences: ^2.2.0      # Local storage
  qr_flutter: ^4.1.0              # QR code generation
  razorpay_flutter: ^1.3.5        # Payment integration
  google_sign_in: ^6.1.5          # OAuth
  image_picker: ^1.0.4            # Profile image
```

#### Authentication Flow
1. **Registration**:
   - User fills form (email, mobile, password, name)
   - App calls `POST /api/customer/generate-otp`
   - User enters 6-digit OTP
   - App calls `POST /api/customer/verify-otp`
   - Receives token, stores in SharedPreferences

2. **Login**:
   - User enters email + password
   - App calls `POST /api/customer/login`
   - Receives token, stores locally

3. **Google Sign-In**:
   - User taps Google button
   - OAuth flow via google_sign_in package
   - App sends Google ID to backend
   - Backend creates/finds customer, returns token

#### Booking Flow
1. **Select Route**:
   - Fetch branches via `GET /api/customer/branch`
   - User selects from/to branches

2. **Select Date & Time**:
   - Fetch schedules via `GET /api/ferries/branch/{id}`
   - User selects date and departure time

3. **Add Items**:
   - Fetch rates via `GET /api/rates/branch/{id}?date=...`
   - User selects passengers, vehicles
   - App calculates total amount

4. **Payment**:
   - Create booking via `POST /api/bookings`
   - Receive Razorpay order_id
   - Open Razorpay checkout
   - On success, verify via `POST /api/razorpay/verify`

5. **Display Ticket**:
   - Fetch booking details via `GET /api/bookings/{id}`
   - Display QR code using qr_flutter
   - Allow download/share

#### State Management
Currently using basic setState(). Future enhancement: Provider/Riverpod.

### Checker Mobile App (Flutter)

#### Purpose
Verify tickets at boarding gates by scanning QR codes.

#### Key Features
1. **Login**: Staff credentials authentication
2. **QR Scanner**: Camera-based barcode scanning
3. **Verification**: Real-time ticket validation
4. **Offline Cache**: Store verified tickets locally (future)

#### Project Structure
```
Checker mobile app/
├── lib/
│   ├── main.dart
│   ├── screens/
│   │   ├── login_screen.dart
│   │   ├── scanner_screen.dart
│   │   └── verify_result_screen.dart
│   └── services/
│       └── checker_service.dart
```

#### Key Dependencies
```yaml
dependencies:
  qr_code_scanner: ^1.0.1         # QR scanning
  http: ^1.1.0                    # API calls
  shared_preferences: ^2.2.0      # Token storage
```

#### Verification Flow
1. Checker logs in via `POST /api/checker/login`
2. App stores token
3. Opens QR scanner screen
4. Scans customer's QR code (extracts booking reference)
5. Calls `POST /api/checker/verify-ticket` with reference
6. Displays result:
   - ✅ Valid ticket → Show green screen + passenger details
   - ❌ Already verified → Show yellow warning + timestamp
   - ❌ Invalid → Show red error

---

## Payment Integration

### Razorpay Configuration

#### Setup
1. **Create Razorpay Account**: https://razorpay.com
2. **Get API Keys**:
   - Key ID: `rzp_test_...` (test mode)
   - Key Secret: (store in `.env`)
3. **Configure Webhooks** (optional for payment notifications)

#### Environment Variables
```env
RAZORPAY_KEY=rzp_test_abcdefghijklmnop
RAZORPAY_SECRET=your_secret_key_here
```

#### Backend Flow

**Step 1**: Create Order
```php
use Razorpay\Api\Api;

$api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

$order = $api->order->create([
    'amount' => $amount * 100, // Amount in paise
    'currency' => 'INR',
    'receipt' => 'booking_' . $booking->booking_id
]);

// Save order_id to booking
$booking->razorpay_order_id = $order->id;
$booking->save();

// Return to frontend
return response()->json([
    'order_id' => $order->id,
    'amount' => $order->amount,
    'currency' => $order->currency,
    'razorpay_key' => env('RAZORPAY_KEY')
]);
```

**Step 2**: Verify Payment (after customer pays)
```php
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

try {
    $attributes = [
        'razorpay_order_id' => $request->razorpay_order_id,
        'razorpay_payment_id' => $request->razorpay_payment_id,
        'razorpay_signature' => $request->razorpay_signature
    ];

    $api->utility->verifyPaymentSignature($attributes);

    // Payment verified successfully
    $booking->payment_status = 'paid';
    $booking->booking_status = 'confirmed';
    $booking->razorpay_payment_id = $request->razorpay_payment_id;
    $booking->save();

    // Generate QR code
    $this->generateQRCode($booking);

    return response()->json(['success' => true]);

} catch (SignatureVerificationError $e) {
    // Payment verification failed
    return response()->json(['success' => false, 'message' => 'Invalid payment'], 400);
}
```

#### Frontend Integration (Web)

**Include Razorpay Checkout Script**:
```html
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
```

**Open Checkout**:
```javascript
var options = {
    "key": "{{ $razorpay_key }}",
    "amount": "{{ $amount * 100 }}",
    "currency": "INR",
    "name": "Jetty Ferry Service",
    "description": "Ferry Booking",
    "order_id": "{{ $order_id }}",
    "handler": function (response){
        // Send to backend for verification
        fetch('/api/razorpay/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify({
                razorpay_order_id: response.razorpay_order_id,
                razorpay_payment_id: response.razorpay_payment_id,
                razorpay_signature: response.razorpay_signature,
                booking_id: bookingId
            })
        }).then(res => res.json())
          .then(data => {
              if(data.success) {
                  window.location.href = '/customer/bookings/' + bookingId;
              }
          });
    },
    "prefill": {
        "name": "{{ $customer->first_name }}",
        "email": "{{ $customer->email }}",
        "contact": "{{ $customer->mobile }}"
    },
    "theme": {
        "color": "#3399cc"
    }
};

var rzp = new Razorpay(options);
rzp.open();
```

#### Mobile Integration (Flutter)

```dart
import 'package:razorpay_flutter/razorpay_flutter.dart';

class BookingScreen extends StatefulWidget {
  @override
  _BookingScreenState createState() => _BookingScreenState();
}

class _BookingScreenState extends State<BookingScreen> {
  late Razorpay _razorpay;

  @override
  void initState() {
    super.initState();
    _razorpay = Razorpay();
    _razorpay.on(Razorpay.EVENT_PAYMENT_SUCCESS, _handlePaymentSuccess);
    _razorpay.on(Razorpay.EVENT_PAYMENT_ERROR, _handlePaymentError);
  }

  void openCheckout(String orderId, int amount) {
    var options = {
      'key': 'rzp_test_...',
      'amount': amount,
      'currency': 'INR',
      'name': 'Jetty Ferry Service',
      'description': 'Ferry Booking',
      'order_id': orderId,
      'prefill': {
        'contact': customer.mobile,
        'email': customer.email
      }
    };

    try {
      _razorpay.open(options);
    } catch (e) {
      print('Error: $e');
    }
  }

  void _handlePaymentSuccess(PaymentSuccessResponse response) {
    // Verify payment with backend
    ApiService.verifyPayment(
      orderId: response.orderId!,
      paymentId: response.paymentId!,
      signature: response.signature!,
      bookingId: bookingId
    ).then((result) {
      if (result.success) {
        // Navigate to ticket screen
        Navigator.push(context, ...);
      }
    });
  }

  void _handlePaymentError(PaymentFailureResponse response) {
    // Handle payment failure
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Payment Failed'),
        content: Text(response.message ?? 'Unknown error'),
      ),
    );
  }

  @override
  void dispose() {
    _razorpay.clear();
    super.dispose();
  }
}
```

### Payment Modes

The system supports 4 payment modes:

| Mode | Usage | Processing |
|------|-------|------------|
| Cash | Counter tickets | Immediate confirmation |
| Card | Counter tickets (POS) | Immediate confirmation |
| UPI | Counter tickets | Immediate confirmation |
| Online | Web/app bookings | Razorpay gateway, pending → confirmed |

---

## Reporting System

### Daily Summary Generation

#### Scheduled Task
```php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    // Run daily at midnight
    $schedule->call(function () {
        $this->generateDailySummary(now()->subDay());
    })->dailyAt('00:00');
}

protected function generateDailySummary($date)
{
    $branches = Branch::all();

    foreach ($branches as $branch) {
        $ferries = FerryBoat::where('branch_id', $branch->branch_id)->get();

        foreach ($ferries as $ferry) {
            $tickets = Ticket::whereDate('ticket_date', $date)
                ->where('from_branch_id', $branch->branch_id)
                ->where('ferry_boat_id', $ferry->ferry_boat_id)
                ->get();

            $summary = new DailySummary();
            $summary->summary_date = $date;
            $summary->branch_id = $branch->branch_id;
            $summary->ferry_boat_id = $ferry->ferry_boat_id;
            $summary->total_tickets = $tickets->count();
            $summary->total_passengers = $this->countPassengers($tickets);
            $summary->total_vehicles = $this->countVehicles($tickets);
            $summary->total_revenue_cash = $tickets->where('payment_mode', 'cash')->sum('total_amount');
            $summary->total_revenue_card = $tickets->where('payment_mode', 'card')->sum('total_amount');
            $summary->total_revenue_upi = $tickets->where('payment_mode', 'upi')->sum('total_amount');
            $summary->total_revenue_online = $tickets->where('payment_mode', 'online')->sum('total_amount');
            $summary->save();
        }
    }
}
```

### Report Types

#### 1. Daily Ticket Report
**Route**: `/reports/tickets`

**Filters**:
- Date range (from/to)
- Branch
- Ferry boat
- Payment mode

**Columns**:
- Date
- Ticket number
- From/to branches
- Ferry
- Passengers
- Vehicles
- Amount
- Payment mode
- Verification status

**Actions**:
- Export to CSV
- Print

#### 2. Vehicle Ticket Report
**Route**: `/reports/vehicle-tickets`

**Filters**:
- Date range
- Branch
- Vehicle type

**Columns**:
- Date
- Ticket number
- Vehicle type
- Registration number
- From/to branches
- Amount

#### 3. Revenue Report
**Generated from DailySummary table**

**Metrics**:
- Total revenue
- Cash revenue
- Card revenue
- UPI revenue
- Online revenue
- Revenue by branch
- Revenue by ferry

#### 4. Customer Booking Report
**Route**: `/reports/customer-bookings`

**Columns**:
- Booking reference
- Customer name
- Email/mobile
- Route
- Date/time
- Amount
- Payment status
- Booking status
- Verification status

### CSV Export

**Example**: Ticket Report Export
```php
public function exportCsv(Request $request)
{
    $tickets = Ticket::with(['fromBranch', 'toBranch', 'ferryBoat'])
        ->whereBetween('ticket_date', [$request->from_date, $request->to_date])
        ->get();

    $filename = 'tickets_' . now()->format('Y-m-d_H-i-s') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];

    $callback = function() use ($tickets) {
        $file = fopen('php://output', 'w');

        // Header row
        fputcsv($file, ['Date', 'Ticket No', 'From', 'To', 'Ferry', 'Passengers', 'Vehicles', 'Amount', 'Payment Mode', 'Verified']);

        // Data rows
        foreach ($tickets as $ticket) {
            fputcsv($file, [
                $ticket->ticket_date,
                $ticket->ticket_no,
                $ticket->fromBranch->branch_name,
                $ticket->toBranch->branch_name,
                $ticket->ferryBoat->ferry_name,
                $this->countPassengers($ticket),
                $this->countVehicles($ticket),
                $ticket->total_amount,
                $ticket->payment_mode,
                $ticket->ticket_verified ? 'Yes' : 'No'
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
```

---

## Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer 2.x
- Node.js 18.x or higher
- npm 9.x or higher
- PostgreSQL 14+ or MySQL 8+
- Git

### Local Development Setup

#### 1. Clone Repository
```bash
git clone <repository-url>
cd "Jetty - Working - L - Copy"
```

#### 2. Install PHP Dependencies
```bash
composer install
```

#### 3. Install Node Dependencies
```bash
npm install
```

#### 4. Environment Configuration
```bash
cp .env.example .env
```

Edit `.env`:
```env
APP_NAME="Jetty Ferry Management"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=jetty_db
DB_USERNAME=postgres
DB_PASSWORD=your_password

CACHE_DRIVER=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

RAZORPAY_KEY=rzp_test_...
RAZORPAY_SECRET=...

MAIL_MAILER=log
```

#### 5. Generate Application Key
```bash
php artisan key:generate
```

#### 6. Create Database
```sql
CREATE DATABASE jetty_db;
```

#### 7. Run Migrations
```bash
php artisan migrate
```

#### 8. Seed Database (Optional)
```bash
php artisan db:seed
```

Creates initial data:
- Super admin user (email: admin@jetty.com, password: password)
- Sample branches
- Sample ferries
- Sample item categories and rates

#### 9. Build Frontend Assets
```bash
npm run build
```

For development with hot reload:
```bash
npm run dev
```

#### 10. Create Storage Link
```bash
php artisan storage:link
```

#### 11. Start Development Server
```bash
php artisan serve
```

Access at: http://localhost:8000

### Flutter App Setup

#### Customer App
```bash
cd flutter_app
flutter pub get
flutter run
```

**Configure API Base URL**:
Edit `lib/services/api_service.dart`:
```dart
static const String baseUrl = 'http://10.0.2.2:8000/api'; // Android emulator
// static const String baseUrl = 'http://localhost:8000/api'; // iOS simulator
// static const String baseUrl = 'https://unfurling.ninja/api'; // Production
```

#### Checker App
```bash
cd "Checker mobile app"
flutter pub get
flutter run
```

---

## Deployment

### Production VPS Setup

#### Server Requirements
- Ubuntu 22.04 LTS
- PHP 8.3 with extensions: pdo, pgsql, mbstring, xml, bcmath, gd
- PostgreSQL 15+
- Nginx or Apache
- Composer
- Node.js 18+

#### 1. Server Preparation
```bash
sudo apt update
sudo apt upgrade -y

# Install PHP 8.3
sudo add-apt-repository ppa:ondrej/php
sudo apt install php8.3 php8.3-fpm php8.3-pgsql php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-gd php8.3-curl

# Install PostgreSQL
sudo apt install postgresql postgresql-contrib

# Install Nginx
sudo apt install nginx

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### 2. Create Database
```bash
sudo -u postgres psql
```

```sql
CREATE DATABASE jetty_production;
CREATE USER jetty_user WITH PASSWORD 'secure_password';
GRANT ALL PRIVILEGES ON DATABASE jetty_production TO jetty_user;
\q
```

#### 3. Deploy Application
```bash
cd /var/www
sudo git clone <repository-url> jetty
cd jetty

# Install dependencies
sudo composer install --optimize-autoloader --no-dev
sudo npm install
sudo npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/jetty
sudo chmod -R 755 /var/www/jetty
sudo chmod -R 775 /var/www/jetty/storage
sudo chmod -R 775 /var/www/jetty/bootstrap/cache
```

#### 4. Configure Environment
```bash
sudo cp .env.example .env
sudo nano .env
```

```env
APP_NAME="Jetty Ferry Management"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://unfurling.ninja

DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=jetty_production
DB_USERNAME=jetty_user
DB_PASSWORD=secure_password

CACHE_DRIVER=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

RAZORPAY_KEY=rzp_live_...
RAZORPAY_SECRET=...

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=app_password
MAIL_ENCRYPTION=tls
```

```bash
sudo php artisan key:generate
sudo php artisan migrate --force
sudo php artisan storage:link
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
```

#### 5. Configure Nginx
```bash
sudo nano /etc/nginx/sites-available/jetty
```

```nginx
server {
    listen 80;
    server_name unfurling.ninja www.unfurling.ninja;
    root /var/www/jetty/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/jetty /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### 6. SSL Certificate (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d unfurling.ninja -d www.unfurling.ninja
```

#### 7. Setup Cron for Scheduled Tasks
```bash
sudo crontab -e -u www-data
```

Add:
```
* * * * * cd /var/www/jetty && php artisan schedule:run >> /dev/null 2>&1
```

#### 8. Configure PHP-FPM
```bash
sudo nano /etc/php/8.3/fpm/pool.d/www.conf
```

Adjust:
```ini
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

```bash
sudo systemctl restart php8.3-fpm
```

### Mobile App Deployment

#### Build Android APK
```bash
cd flutter_app
flutter build apk --release
```

APK location: `build/app/outputs/flutter-apk/app-release.apk`

#### Build iOS App
```bash
flutter build ios --release
```

Submit to App Store via Xcode.

---

## Directory Structure

### Complete Project Layout

```
Jetty - Working - L - Copy/
│
├── app/                                  # Laravel application code
│   ├── Console/
│   │   └── Kernel.php                   # Scheduled tasks
│   ├── Exceptions/
│   │   └── Handler.php                  # Exception handling
│   ├── Http/
│   │   ├── Controllers/                 # 38 controllers
│   │   │   ├── Api/                     # API for mobile
│   │   │   │   ├── ApiController.php
│   │   │   │   └── CheckerAuthController.php
│   │   │   ├── CustomerAuth/            # Customer auth
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── RegisterController.php
│   │   │   │   └── PasswordResetController.php
│   │   │   ├── BookingController.php
│   │   │   ├── TicketEntryController.php
│   │   │   ├── TicketReportController.php
│   │   │   ├── BranchController.php
│   │   │   ├── FerryBoatController.php
│   │   │   ├── FerryScheduleController.php
│   │   │   ├── ItemRateController.php
│   │   │   ├── GuestController.php
│   │   │   ├── CheckerController.php
│   │   │   ├── SpecialChargeController.php
│   │   │   ├── EmployeeTransferController.php
│   │   │   ├── HouseboatController.php
│   │   │   ├── HouseboatAdminController.php
│   │   │   └── PublicController.php
│   │   ├── Middleware/
│   │   │   ├── Authenticate.php
│   │   │   ├── BlockRole5.php          # Block checkers
│   │   │   └── RedirectIfAuthenticated.php
│   │   └── Kernel.php                   # HTTP kernel
│   ├── Models/                          # Eloquent models (17 files)
│   │   ├── Branch.php
│   │   ├── FerryBoat.php
│   │   ├── FerrySchedule.php
│   │   ├── User.php
│   │   ├── Customer.php
│   │   ├── Ticket.php
│   │   ├── TicketLine.php
│   │   ├── Booking.php
│   │   ├── ItemCategory.php
│   │   ├── ItemRate.php
│   │   ├── Guest.php
│   │   ├── SpecialCharge.php
│   │   ├── HouseboatRoom.php
│   │   ├── HouseboatBooking.php
│   │   ├── DailySummary.php
│   │   └── BranchTransfer.php
│   ├── Mail/                            # Email notifications
│   └── Providers/
│       ├── AppServiceProvider.php
│       ├── AuthServiceProvider.php
│       └── RouteServiceProvider.php
│
├── bootstrap/                           # Laravel bootstrap
│   ├── app.php
│   └── cache/
│
├── config/                              # Configuration files
│   ├── app.php
│   ├── auth.php
│   ├── database.php                    # DB configuration
│   ├── mail.php
│   ├── queue.php
│   ├── sanctum.php                     # API auth
│   └── services.php
│
├── database/
│   ├── migrations/                     # 36 migration files
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   ├── 2025_12_30_000001_legacy_schema_upgrades.php
│   │   ├── 2026_01_07_000000_create_houseboat_tables.php
│   │   ├── 2026_01_10_000000_postgresql_optimizations.php
│   │   └── ...
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── BranchSeeder.php
│   │   ├── FerryBoatSeeder.php
│   │   └── ItemRateSeeder.php
│   └── database.sqlite
│
├── public/                             # Web root
│   ├── index.php                       # Entry point
│   ├── .htaccess
│   ├── favicon.ico
│   ├── css/                            # Compiled CSS
│   ├── js/                             # Compiled JS
│   ├── images/
│   └── storage/                        # Symlink to storage/app/public
│
├── resources/
│   ├── views/                          # Blade templates (50+ files)
│   │   ├── layouts/
│   │   │   ├── app.blade.php          # Main layout
│   │   │   ├── admin.blade.php        # Admin layout
│   │   │   └── customer.blade.php     # Customer layout
│   │   ├── auth/                      # Auth views
│   │   │   ├── login.blade.php
│   │   │   └── register.blade.php
│   │   ├── admin/
│   │   │   ├── dashboard.blade.php
│   │   │   └── users/
│   │   ├── customer/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── booking/
│   │   │   └── profile.blade.php
│   │   ├── public/
│   │   │   ├── index.blade.php        # Homepage
│   │   │   ├── about.blade.php
│   │   │   ├── contact.blade.php
│   │   │   └── route.blade.php
│   │   ├── tickets/
│   │   │   ├── entry.blade.php        # Ticket form
│   │   │   └── verify.blade.php
│   │   ├── reports/
│   │   │   ├── tickets.blade.php
│   │   │   └── vehicles.blade.php
│   │   ├── branches/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   └── edit.blade.php
│   │   ├── ferry-boats/
│   │   ├── ferry-schedules/
│   │   ├── item-rates/
│   │   ├── guests/
│   │   ├── checkers/
│   │   ├── special-charges/
│   │   ├── employee-transfers/
│   │   └── houseboat/
│   ├── css/
│   │   └── app.css                    # Source CSS
│   ├── js/
│   │   └── app.js                     # Source JS
│   └── lang/                          # Localization (future)
│
├── routes/
│   ├── web.php                        # Web routes (150+ routes)
│   ├── api.php                        # API routes (20+ endpoints)
│   ├── console.php                    # Artisan commands
│   └── channels.php                   # Broadcasting (future)
│
├── storage/
│   ├── app/
│   │   ├── public/                    # Publicly accessible files
│   │   │   ├── profiles/             # Customer profile images
│   │   │   ├── qr/                   # QR code images
│   │   │   └── houseboats/           # Houseboat gallery
│   │   └── private/                   # Private files
│   ├── framework/
│   │   ��── cache/
│   │   ├── sessions/
│   │   └── views/                     # Compiled Blade views
│   └── logs/
│       └── laravel.log
│
├── tests/                             # PHPUnit tests
│   ├── Feature/
│   └── Unit/
│
├── vendor/                            # Composer dependencies
│
├── flutter_app/                       # Customer mobile app
│   ├── lib/
│   │   ├── main.dart
│   │   ├── screens/
│   │   ├── services/
│   │   ├── models/
│   │   └── widgets/
│   ├── android/
│   ├── ios/
│   ├── pubspec.yaml
│   └── README.md
│
├── Checker mobile app/                # Checker mobile app
│   ├── lib/
│   │   ├── main.dart
│   │   └── screens/
│   ├── android/
│   ├── ios/
│   └── pubspec.yaml
│
├── jetty_node/                        # Node.js utilities
│
├── Source Notes/                      # Documentation
│   ├── DOCUMENTATION_DATABASE.md
│   ├── DOCUMENTATION_BACKEND.md
│   ├── DOCUMENTATION_FRONTEND.md
│   ├── DOCUMENTATION_CUSTOMER_MOBILE_APP.md
│   ├── DOCUMENTATION_CHECKER_MOBILE_APP.md
│   ├── API_ENDPOINTS_MAPPING.md
│   ├── API_CONNECTION_GUIDE.md
│   └── BUILD_APK_GUIDE.md
│
├── Migrations Instruction/            # Migration guides
│
├── .env                               # Environment variables (not in git)
├── .env.example                       # Example env file
├── .gitignore
├── artisan                            # Laravel CLI
├── composer.json                      # PHP dependencies
├── composer.lock
├── package.json                       # Node dependencies
├── package-lock.json
├── vite.config.js                     # Vite build config
├── tailwind.config.js                 # TailwindCSS config
├── phpunit.xml                        # PHPUnit config
├── README.md
└── Jetty.postman_collection.json      # API test collection
```

---

## Future Enhancements

### Planned Features

#### 1. Real-Time Tracking
- GPS tracking for ferries
- Live departure/arrival updates
- Push notifications to customers
- Real-time capacity updates

#### 2. Advanced Booking
- Multi-trip booking
- Round-trip discounts
- Group booking discounts
- Loyalty program

#### 3. Analytics Dashboard
- Revenue trends
- Popular routes
- Peak hours
- Customer demographics
- Ferry utilization heatmaps

#### 4. Mobile App Enhancements
- Offline ticket storage
- Push notifications
- Booking reminders
- Favorite routes
- Saved payment methods
- Wallet integration

#### 5. Integration Improvements
- WhatsApp booking
- SMS ticket delivery
- Email confirmations
- Calendar integration (Google/Apple)

#### 6. Operational Features
- Maintenance scheduling
- Fuel consumption tracking
- Crew shift management
- Incident reporting
- Weather alerts

#### 7. Customer Features
- Referral program
- Seasonal passes
- Corporate accounts
- Travel history export
- Feedback and ratings

#### 8. Security Enhancements
- Two-factor authentication
- Biometric login (mobile)
- Rate limiting
- Audit logs
- GDPR compliance tools

#### 9. Performance Optimizations
- Redis caching
- CDN for assets
- Database query optimization
- Image optimization
- API response caching

#### 10. Multi-Language Support
- Hindi, Marathi translations
- RTL support
- Currency localization

---

## Support & Maintenance

### Regular Maintenance Tasks

**Daily**:
- Monitor error logs (`storage/logs/laravel.log`)
- Check daily summary generation
- Verify payment gateway status

**Weekly**:
- Database backup
- Review user activity
- Check disk space
- Update rate cards (if seasonal)

**Monthly**:
- Update dependencies (`composer update`, `npm update`)
- Review security advisories
- Optimize database (`php artisan db:optimize`)
- Clear old logs

**Quarterly**:
- Security audit
- Performance review
- User feedback review
- Feature planning

### Backup Strategy

**Database Backup**:
```bash
# Daily automated backup
0 2 * * * pg_dump jetty_production > /backups/jetty_$(date +\%Y\%m\%d).sql

# Keep 30 days of backups
35 2 * * * find /backups -name "jetty_*.sql" -mtime +30 -delete
```

**File Backup**:
```bash
# Weekly backup of storage
0 3 * * 0 tar -czf /backups/storage_$(date +\%Y\%m\%d).tar.gz /var/www/jetty/storage
```

### Troubleshooting

**Common Issues**:

1. **500 Internal Server Error**
   - Check `storage/logs/laravel.log`
   - Verify file permissions (`sudo chmod -R 775 storage`)
   - Clear cache (`php artisan cache:clear`)

2. **Database Connection Failed**
   - Verify `.env` credentials
   - Check PostgreSQL service (`sudo systemctl status postgresql`)
   - Test connection: `psql -U jetty_user -d jetty_production`

3. **Payment Gateway Not Working**
   - Verify Razorpay API keys in `.env`
   - Check Razorpay dashboard for failed transactions
   - Test with Razorpay test mode first

4. **QR Codes Not Generating**
   - Check `endroid/qr-code` package installation
   - Verify storage permissions
   - Check GD extension: `php -m | grep gd`

5. **Mobile App Can't Connect**
   - Verify API base URL in app
   - Check CORS settings (if cross-origin)
   - Test endpoints with Postman

### Contact & Support

**Documentation**:
- Database: `Source Notes/DOCUMENTATION_DATABASE.md`
- Backend: `Source Notes/DOCUMENTATION_BACKEND.md`
- Frontend: `Source Notes/DOCUMENTATION_FRONTEND.md`
- Mobile Apps: Check respective folders

**API Testing**:
- Postman Collection: `Jetty.postman_collection.json`

**Issue Reporting**:
- Check logs first
- Provide error message, stack trace, steps to reproduce
- Include environment details (PHP version, database, OS)

---

## Conclusion

The **Jetty Ferry Management System** is a comprehensive, production-ready platform designed to modernize ferry operations across the Konkan coast. With support for online booking, counter operations, mobile verification, and advanced reporting, it provides a complete solution for ferry service management.

**Key Strengths**:
- ✅ Multi-platform (Web + Mobile)
- ✅ Secure authentication & authorization
- ✅ Integrated payment gateway
- ✅ Comprehensive reporting
- ✅ Scalable architecture
- ✅ Active development & maintenance

**Technology Highlights**:
- Laravel 12 + PostgreSQL backend
- Flutter mobile apps for customers & checkers
- Razorpay payment integration
- QR code ticket verification
- Progressive Web App support

For detailed implementation guides, API documentation, and migration instructions, refer to the extensive documentation in the `Source Notes/` directory.

---

**Project Version**: 1.0.0
**Last Updated**: 2026-01-11
**Production URL**: https://unfurling.ninja
**License**: Proprietary
