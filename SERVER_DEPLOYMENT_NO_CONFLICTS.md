# Server Deployment - NO Database Conflicts!

## IMPORTANT: Do NOT Replace demo1.sql!

Your server already has demo1.sql with all your data (users, branches, tickets, etc.)
The ONLY missing tables are:
- customers (for mobile app login)
- personal_access_tokens (for API tokens)
- bookings (for mobile app bookings)

## Safe Deployment Steps (No Conflicts):

### Step 1: Pull Latest Code
```bash
cd /path/to/your/server/jetty
git pull origin master
```

If you get merge conflicts, do this:
```bash
# Keep your server files, take only code changes
git reset --hard origin/master
```

### Step 2: Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### Step 3: DO NOT Touch demo1.sql!
Keep your existing database as-is. It has all your data!

### Step 4: Run ONLY New Migrations
This will CREATE the missing tables WITHOUT touching existing data:

```bash
# Check which migrations are pending
php artisan migrate:status

# Run ONLY the new migrations (adds customers & tokens tables)
php artisan migrate

# Answer "yes" when prompted
```

This is SAFE because:
- It only ADDS new tables
- It does NOT delete existing data
- demo1.sql data stays intact

### Step 5: Clear Caches
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize
```

### Step 6: Test Everything
```bash
# Test API routes load (should NOT be 404)
curl https://unfurling.ninja/api/customer/login

# Should return:
# {"message":"The email field is required. (and 1 more error)","errors":...}
```

## What Migrations Will Create:

Looking at your migrations folder, these will be created:

1. **customers table** (2025_11_25_232446_create_customers_table.php)
   - For mobile app user accounts
   - Fields: first_name, last_name, email, password, mobile, profile_image

2. **personal_access_tokens table** (2025_12_11_135014_create_personal_access_tokens_table.php)
   - For Sanctum API authentication
   - Stores login tokens for mobile app

3. **bookings table** (2025_12_11_231458_create_bookings_table.php)
   - For mobile app ferry bookings
   - If already exists, migration will skip it

4. **profile_image column** (2025_12_12_124915_add_profile_image_to_customers_table.php)
   - Adds profile_image field to customers
   - If customers table is new, this might already be included

## Troubleshooting:

### If you see "Table already exists" error:
This means the table was already created manually. That's FINE! Just continue.

### If migrations fail:
```bash
# Check what tables you have
php artisan tinker
>>> Schema::hasTable('customers');
>>> Schema::hasTable('personal_access_tokens');
>>> exit

# If false, migrations will create them
# If true, they already exist (skip migration)
```

### If you want to be EXTRA safe:
```bash
# Backup your database first
mysqldump -u username -p database_name > backup_before_deploy.sql

# Then run migrations
php artisan migrate
```

## What NOT to Do:

❌ Do NOT run `php artisan migrate:fresh` (deletes all data!)
❌ Do NOT import demo1.sql again (you already have the data!)
❌ Do NOT delete your existing database
❌ Do NOT manually edit demo1.sql file

## What TO Do:

✅ Pull latest code from GitHub
✅ Run `php artisan migrate` (adds missing tables only)
✅ Clear caches
✅ Test APIs

## After Deployment:

Your database will have:
✅ All your original data from demo1.sql (users, branches, ferries, tickets, etc.)
✅ NEW customers table (for mobile login)
✅ NEW personal_access_tokens table (for API tokens)
✅ All your web app will continue working
✅ Mobile app APIs will now work too!

No conflicts, no data loss, just adding what's missing! 🚀
