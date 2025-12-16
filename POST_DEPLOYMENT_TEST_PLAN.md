# Post-Deployment Testing Plan

## CRITICAL BUGS FIXED (Dec 16, 2025)

### Bug #1: Tickets Table Missing Fields ✅ FIXED
**Migration:** `2025_12_16_000001_add_missing_fields_to_tickets_table.php`
- Added: user_id, ferry_type, customer_name, customer_mobile, guest_id, verified_at

### Bug #2: TicketLines Missing user_id ✅ FIXED
**Migration:** `2025_12_16_000002_add_user_id_to_ticket_lines_table.php`
- Added: user_id column for audit trail

### Bug #3: Missing Database Indexes ✅ FIXED (CRITICAL)
**Migration:** `2025_12_16_000003_add_database_indexes.php`
- **CRITICAL:** Added index to personal_access_tokens.token (affects EVERY API request)
- Added indexes to bookings (customer_id, status, composite)
- Added indexes to tickets (branch_id, branch_id+created_at)
- Added indexes to ticket_lines (ticket_id)
- Added indexes to item_rates (branch_id)

### Bug #4: No Foreign Key Constraints ✅ FIXED
**Migration:** `2025_12_16_000004_add_foreign_key_constraints.php`
- Added FK constraints to bookings (customer_id, ferry_id, branches)
- Added FK constraints to tickets (branch_id, ferry_boat_id)
- Added FK constraints to ticket_lines (ticket_id, item_rate_id)
- Added FK constraints to item_rates (branch_id)
- Added FK constraints to ferryboats (branch_id)

### Bug #5: No API Rate Limiting ✅ FIXED
**File:** `routes/api.php`
- OTP endpoints: 5 requests/minute
- Auth endpoints: 10 requests/minute

**See:** [CRITICAL_BUGS_FOUND.md](CRITICAL_BUGS_FOUND.md) for complete audit report

---

## Updated Deployment Commands

```bash
cd /var/www/unfurling.ninja
git pull origin master
php artisan migrate  # Will run 4 new migrations
php artisan db:seed --class=FerryBoatsTableSeeder
php artisan db:seed --class=ItemRatesSeeder
php artisan config:clear && php artisan cache:clear
sudo systemctl restart php8.3-fpm
```

**Note:** The migrate command will run these 4 new migrations in order:
1. 2025_12_16_000001_add_missing_fields_to_tickets_table
2. 2025_12_16_000002_add_user_id_to_ticket_lines_table
3. 2025_12_16_000003_add_database_indexes
4. 2025_12_16_000004_add_foreign_key_constraints

---

## Testing Checklist

### Admin Ticket Creation
- [ ] Login as admin/operator
- [ ] Navigate to /ticket-entry
- [ ] Create ticket with passengers/vehicles
- [ ] Ticket saves successfully
- [ ] user_id, customer_name, ferry_type saved

### Mobile App
- [ ] Login works
- [ ] No grey screens
- [ ] Passenger types show (Adult ₹20, Child ₹9, Senior ₹17)
- [ ] Booking creation succeeds
- [ ] QR code generated

### Web Customer Booking
- [ ] Dashboard loads (not black screen)
- [ ] Booking form works
- [ ] Razorpay payment works

### Reports
- [ ] /reports/tickets loads (no 500 error)
- [ ] Totals calculate correctly

---

## Database Verification

```sql
-- 1. Check tickets table has new fields
DESCRIBE tickets;
-- Should show: user_id, ferry_type, customer_name, customer_mobile, guest_id, verified_at

-- 2. Check ticket_lines has user_id
DESCRIBE ticket_lines;
-- Should show: user_id column

-- 3. Verify indexes were created (CRITICAL)
SHOW INDEX FROM personal_access_tokens WHERE Key_name = 'idx_tokens_token';
SHOW INDEX FROM bookings WHERE Key_name LIKE 'idx_bookings_%';
SHOW INDEX FROM tickets WHERE Key_name LIKE 'idx_tickets_%';
-- Should return rows for each index

-- 4. Verify foreign keys were created
SELECT
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'jetty_db'
  AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, CONSTRAINT_NAME;
-- Should show all FK constraints

-- 5. Check item rates distribution
SELECT branch_id, COUNT(*) FROM item_rates GROUP BY branch_id;
-- Each branch should have ~20 items

-- 6. Check ferries distribution
SELECT branch_id, COUNT(*) FROM ferry_boats GROUP BY branch_id;
-- Branches 1-10 should each have 1 ferry
```

---

## Performance Verification

After deploying, verify performance improvements:

```sql
-- Test token lookup speed (should be instant with index)
EXPLAIN SELECT * FROM personal_access_tokens WHERE token = 'test_token';
-- Should show: type=ref, key=idx_tokens_token

-- Test booking lookup speed
EXPLAIN SELECT * FROM bookings WHERE customer_id = 1 AND status = 'confirmed';
-- Should show: type=ref, key=idx_bookings_customer_status
```

---

## Security Verification

Test API rate limiting:

```bash
# Test OTP rate limit (should allow 5, block 6th)
for i in {1..6}; do
  curl -X POST https://unfurling.ninja/api/customer/generate-otp \
    -H "Content-Type: application/json" \
    -d '{"mobile":"1234567890"}'
  echo "Request $i"
done
# Expected: First 5 succeed, 6th returns 429 Too Many Requests

# Test login rate limit (should allow 10, block 11th)
for i in {1..11}; do
  curl -X POST https://unfurling.ninja/api/customer/login \
    -H "Content-Type: application/json" \
    -d '{"email":"test@test.com","password":"wrong"}'
  echo "Request $i"
done
# Expected: First 10 succeed (with error), 11th returns 429
```

---

**Last Updated:** December 16, 2025
**Migrations Added:** 4 new migrations (2025_12_16_000001 through 000004)
