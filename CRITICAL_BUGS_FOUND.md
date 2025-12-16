# Critical Bugs Found & Fixed

## Bug #1: Tickets Table Missing 6 Fields ✅ FIXED

**Severity:** CRITICAL - Ticket creation will fail

**Model Expects:**
```
app/Models/Ticket.php $fillable:
- user_id
- ferry_type
- customer_name
- customer_mobile
- guest_id
- verified_at (in $dates)
```

**Migration Has:**
```
database/migrations/2025_09_27_065610_create_tickets_table.php:
- id
- branch_id
- ferry_boat_id
- payment_mode
- ferry_time
- discount_pct
- discount_rs
- total_amount
- created_at
- updated_at

MISSING: user_id, ferry_type, customer_name, customer_mobile, guest_id, verified_at
```

**Impact:**
- Admin ticket creation fails
- SQL error when inserting tickets
- Counter sales system broken

**Fix Applied:**
Created migration: `2025_12_16_000001_add_missing_fields_to_tickets_table.php`

---

## Bug #2: TicketLines Table Missing user_id Field ⚠️ NEEDS FIX

**Severity:** MEDIUM - May cause issues if user_id is used

**Model Expects:**
```
app/Models/TicketLine.php $fillable:
- ticket_id
- item_id
- item_name
- qty
- rate
- levy
- amount
- vehicle_name
- vehicle_no
- user_id  ← EXPECTS THIS
```

**Migration Has:**
```
database/migrations/2025_09_27_063409_create_ticket_lines_table.php:
- id
- ticket_id
- item_id
- item_name
- qty
- rate
- levy
- amount
- vehicle_name
- vehicle_no
- created_at
- updated_at

MISSING: user_id
```

**Impact:**
- If code tries to set user_id on ticket line, it will fail
- Potential SQL error if user_id is in INSERT statement
- Audit trail incomplete (can't track who added line items)

**Fix Required:**
Create migration to add `user_id` to ticket_lines table

---

## Bug #3: Bookings Table - No Session Management ⚠️ ANALYSIS NEEDED

**Current State:**
```
database/migrations/2025_12_11_231458_create_bookings_table.php:
- customer_id (always set)
- No session_id field
- No guest_id field
```

**Potential Issues:**
1. **Guest Bookings:** What if someone books without account?
   - Currently: customer_id required (no null)
   - Should allow: Guest bookings without customer account

2. **Session Tracking:** No way to track:
   - Browser session ID
   - Device fingerprinting
   - Guest checkout sessions

**Questions to Answer:**
- Do we allow guest checkout? (booking without account)
- Do we need session_id for web bookings?
- guest_id vs customer_id distinction?

**Recommendation:**
If allowing guest checkout:
```sql
ALTER TABLE bookings
ADD COLUMN session_id VARCHAR(255) NULLABLE AFTER customer_id,
ADD COLUMN guest_id BIGINT UNSIGNED NULLABLE AFTER session_id,
MODIFY COLUMN customer_id BIGINT UNSIGNED NULLABLE;
```

---

## Bug #4: Missing Database Indexes 🐢 PERFORMANCE

**Critical Missing Indexes:**

### bookings table:
```sql
-- Slow: WHERE customer_id = X
CREATE INDEX idx_bookings_customer ON bookings(customer_id);

-- Slow: WHERE status = 'confirmed'
CREATE INDEX idx_bookings_status ON bookings(status);

-- Slow: WHERE booking_date BETWEEN X AND Y
CREATE INDEX idx_bookings_date ON bookings(booking_date);

-- Composite index for common query
CREATE INDEX idx_bookings_customer_status ON bookings(customer_id, status);
```

### tickets table:
```sql
-- Slow: WHERE branch_id = X
CREATE INDEX idx_tickets_branch ON tickets(branch_id);

-- Slow: WHERE user_id = X
CREATE INDEX idx_tickets_user ON tickets(user_id);

-- Slow: WHERE ferry_boat_id = X
CREATE INDEX idx_tickets_ferry ON tickets(ferry_boat_id);

-- Slow: WHERE created_at > X
CREATE INDEX idx_tickets_created ON tickets(created_at);

-- Composite for reports
CREATE INDEX idx_tickets_branch_date ON tickets(branch_id, created_at);
```

### ticket_lines table:
```sql
-- Slow: WHERE ticket_id = X (N+1 query)
CREATE INDEX idx_ticket_lines_ticket ON ticket_lines(ticket_id);

-- For vehicle searches
CREATE INDEX idx_ticket_lines_vehicle_no ON ticket_lines(vehicle_no);
```

### item_rates table:
```sql
-- Slow: WHERE branch_id = X
CREATE INDEX idx_item_rates_branch ON item_rates(branch_id);

-- Composite for effective date queries
CREATE INDEX idx_item_rates_branch_dates ON item_rates(branch_id, starting_date, ending_date);
```

### personal_access_tokens table:
```sql
-- Slow: WHERE token = hashed_value (authentication on every request!)
CREATE INDEX idx_tokens_token ON personal_access_tokens(token);

-- For token cleanup
CREATE INDEX idx_tokens_expires ON personal_access_tokens(expires_at);
```

**Impact:**
- Slow queries (O(n) instead of O(log n))
- High CPU usage
- Slow API responses
- Database bottleneck under load

---

## Bug #5: No Foreign Key Constraints ⚠️ DATA INTEGRITY

**Current State:** No foreign keys defined

**Problems:**
1. Can insert booking with non-existent customer_id
2. Can insert ticket_line with non-existent ticket_id
3. Orphaned records possible
4. No cascade delete (delete ticket doesn't delete ticket_lines)

**Recommended Foreign Keys:**

```sql
-- bookings table
ALTER TABLE bookings
ADD CONSTRAINT fk_bookings_customer
    FOREIGN KEY (customer_id) REFERENCES customers(id)
    ON DELETE CASCADE;

ALTER TABLE bookings
ADD CONSTRAINT fk_bookings_ferry
    FOREIGN KEY (ferry_id) REFERENCES ferryboats(id)
    ON DELETE RESTRICT;

-- tickets table
ALTER TABLE tickets
ADD CONSTRAINT fk_tickets_branch
    FOREIGN KEY (branch_id) REFERENCES branches(id)
    ON DELETE RESTRICT;

ALTER TABLE tickets
ADD CONSTRAINT fk_tickets_ferry
    FOREIGN KEY (ferry_boat_id) REFERENCES ferryboats(id)
    ON DELETE RESTRICT;

ALTER TABLE tickets
ADD CONSTRAINT fk_tickets_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL;

-- ticket_lines table
ALTER TABLE ticket_lines
ADD CONSTRAINT fk_ticket_lines_ticket
    FOREIGN KEY (ticket_id) REFERENCES tickets(id)
    ON DELETE CASCADE;

-- item_rates table
ALTER TABLE item_rates
ADD CONSTRAINT fk_item_rates_branch
    FOREIGN KEY (branch_id) REFERENCES branches(id)
    ON DELETE CASCADE;
```

**Trade-offs:**
- ✅ Data integrity guaranteed
- ✅ Automatic cascade delete
- ❌ Slower INSERTs (constraint checking)
- ❌ Can't insert out of order

---

## Bug #6: Booking vs Ticket Confusion 🤔 ARCHITECTURAL

**Two Systems with Similar Purpose:**

**Tickets (Admin Counter Sales):**
```
users → tickets → ticket_lines
Purpose: Counter ticket sales by staff
Created by: Admin/staff
Payment: Cash/credit at counter
```

**Bookings (Customer App/Web):**
```
customers → bookings
Purpose: Online bookings via app/web
Created by: Customers
Payment: Razorpay online
Items: Stored as JSON
```

**Confusion Points:**
1. Both have `customer_name` and `customer_mobile` fields
2. No clear separation in naming (both are "bookings")
3. Ticket model has `guest_id` but Booking doesn't
4. Different item storage (normalized vs JSON)

**Recommendation:**
Rename for clarity:
- `tickets` → `counter_sales`
- `bookings` → `online_bookings`

Or add prefixes to distinguish:
- `admin_tickets`
- `customer_bookings`

---

## Bug #7: Password Reset Tokens Not Cleaned Up 🗑️ SECURITY

**Issue:** password_reset_tokens table may grow indefinitely

**Missing:**
- No expiry mechanism
- No cleanup job
- Old tokens not deleted

**Fix Required:**
```php
// Schedule in app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Delete expired tokens daily
    $schedule->call(function () {
        DB::table('password_reset_tokens')
            ->where('created_at', '<', now()->subHours(24))
            ->delete();
    })->daily();
}
```

---

## Bug #8: No API Rate Limiting 🚨 SECURITY

**Current State:** No rate limiting on API endpoints

**Vulnerability:**
- Brute force attacks on login
- DoS attacks (spam API with requests)
- No protection against abuse

**Fix Required:**
```php
// routes/api.php
Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('customer/login', ...);
});

// Strict for sensitive endpoints
Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('customer/generate-otp', ...);
    Route::post('razorpay/verify', ...);
});
```

---

## Bug #9: Sanctum Token Expiry Not Set ⏰ SECURITY

**Current State:** Tokens never expire

**Problem:**
- Stolen tokens valid forever
- No forced re-login
- Security risk

**Fix Required:**
```php
// config/sanctum.php
'expiration' => 60 * 24 * 30, // 30 days

// Or in LoginController:
$token = $customer->createToken('auth-token', ['*'], now()->addDays(30));
```

---

## Bug #10: No Soft Deletes ♻️ DATA RECOVERY

**Current State:** Deletes are permanent

**Problem:**
- Can't recover accidentally deleted records
- No audit trail for deletions
- Lost data forever

**Recommendation:**
Add soft deletes to critical tables:

```php
// In models
use SoftDeletes;

protected $dates = ['deleted_at'];

// In migrations
$table->softDeletes();
```

**Tables that need soft deletes:**
- bookings (customer might want to "cancel" not delete)
- customers (account deactivation)
- tickets (void instead of delete)

---

## Priority Fix Order

### CRITICAL (Fix Immediately):
1. ✅ Tickets table missing fields (FIXED)
2. ⚠️ TicketLines missing user_id
3. 🔒 Add API rate limiting
4. 📊 Add database indexes (performance)

### HIGH (Fix Soon):
5. 🔑 Add foreign key constraints
6. ⏰ Set token expiration
7. 🗑️ Password reset cleanup

### MEDIUM (Plan for next version):
8. ♻️ Add soft deletes
9. 🔄 Rename tables for clarity
10. 📝 Add session tracking to bookings

---

**Last Updated:** December 16, 2025
**Status:** Audit Complete - 10 Issues Found
