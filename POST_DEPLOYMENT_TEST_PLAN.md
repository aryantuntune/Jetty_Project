# Post-Deployment Testing Plan

## CRITICAL ISSUE: Tickets Table Missing Fields

### Problem
The tickets table migration is missing 6 fields that the Ticket model expects.

**Missing:**
- user_id
- ferry_type
- customer_name
- customer_mobile  
- guest_id
- verified_at

**Fix Created:** New migration adds all missing fields
`2025_12_16_000001_add_missing_fields_to_tickets_table.php`

---

## Updated Deployment Commands

```bash
cd /var/www/unfurling.ninja
git pull origin master
php artisan migrate
php artisan db:seed --class=FerryBoatsTableSeeder
php artisan db:seed --class=ItemRatesSeeder
php artisan config:clear && php artisan cache:clear
sudo systemctl restart php8.3-fpm
```

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
-- Check tickets table
DESCRIBE tickets;
-- Should have: user_id, ferry_type, customer_name, etc.

-- Check item rates
SELECT branch_id, COUNT(*) FROM item_rates GROUP BY branch_id;
-- Each branch should have ~20 items

-- Check ferries
SELECT branch_id, COUNT(*) FROM ferry_boats GROUP BY branch_id;
-- Branches 1-10 should each have 1 ferry
```

---

**Last Updated:** December 16, 2025
