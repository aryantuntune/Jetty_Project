Claude must only add things in this file and never remove them, claude can update about thier status if discontinued or what but must never remove the previous updates.

---

## TODO Items (Added: 2026-01-21)

### Website Features (Future)
- [x] **Change Password (Website)** - ✅ Added with `/password/change` route
- [x] **Routes Management UI** - ✅ Admin panel CRUD at `/routes`
  - Created `RouteController.php` with CRUD methods
  - Created blade views: `routes/index.blade.php`, `routes/create.blade.php`, `routes/edit.blade.php`
  - Added "Routes" menu item to admin sidebar

### Database Notes
- `daily_ticket_summaries` table kept but not in active use (for future reporting)
- `routes` table has Route model now, used for branch connections

---

## Completed Items (2026-01-19)
- [x] Ticket Table Unification (mobile + web use same `tickets` table)
- [x] Dropped `bookings` table (was duplicate of `tickets`)
- [x] Created `Route.php` model
- [x] Change Password feature (Mobile App) with OTP
- [x] Backend cleanup - replaced all `Booking` references with `Ticket`
- [x] Enabled confirmation emails with PDF attachment
- [x] Fixed price display (levy included in amount)
- [x] Fixed branch names showing as N/A

---

## Update 1: Secure QR Code System (21-01-2026)
**Status**: ✅ COMPLETED

### Goal
Implement secure, high-performance QR code verification for 2K+ daily tickets across 12 branches.

### Changes Required

#### 1. Database Migration
- Add `qr_hash` VARCHAR(64) column to `tickets` table
- Add unique index on `qr_hash` for fast lookups
- **No backfill** - only new tickets get hashes

#### 2. Ticket Model (`app/Models/Ticket.php`)
- Auto-generate cryptographic hash on ticket creation
- Hash format: `SHA256(UUID + timestamp)`

#### 3. Counter Ticket Print (`resources/views/tickets/print.blade.php`)
- Generate QR dynamically in view (no file storage)
- QR content: `qr_hash` (not ticket ID or URL)

#### 4. Mobile App Booking Tickets
- Same hash-based QR system for bookings
- Update PDF generation to use `qr_hash`

#### 5. Checker App Verification
- Parse `qr_hash` directly from QR scan
- POST `/checker/verify` with `qr_hash` parameter
- Double-entry prevention via `verified_at` check

#### 6. API Endpoint (`CheckerAuthController.php`)
- Accept both `qr_hash` (new) and `ticket_id` (legacy) for backward compatibility
- Return 409 Conflict if already verified

---

## Update 2: Secure Print URL & Checker App Fixes (21-01-2026)
**Status**: ✅ COMPLETED

### Fixes Applied

#### 1. Secure Print URL
- Old: `/tickets/12/print?w=58` (exposes ticket ID)
- New: `/t/{qr_hash}/print?w=58` (secure, no ID exposure)
- Added `printByHash` method to `TicketEntryController`
- Frontend now uses `print_url` from API response

#### 2. Checker App - TicketDetailsModal
- Fixed field mappings to read API response correctly:
  - `from_branch` → Route display
  - `ferry_boat` → Ferry name
  - `net_amount` → Amount display
  - `payment_mode` → Payment type
  - `ferry_time` → Ferry time

#### 3. Already Verified Response
- Added missing fields to 409 response:
  - `ticket_date`, `ferry_boat`, `ferry_time`
  - `customer_name`, `net_amount`, `payment_mode`
  - `verified_by` now shows checker name (not ID)

---
