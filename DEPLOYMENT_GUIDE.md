# Deployment Guide for Jetty Project

## What Was Fixed

This version includes critical database fixes from the original fork:

1. **Migration Order**: Ferryboats migration now runs AFTER branches (fixes FK errors)
2. **Missing Seeders**: Added UserSeeder and CustomerSeeder for demo data
3. **Seeder Fields**: Fixed missing user_id and branch_id in all seeders
4. **Foreign Keys**: Fixed ItemRatesSeeder to use correct branch_id reference
5. **API Ready**: Customer model has HasApiTokens, bootstrap configured for API routes

## Deployment Steps

### Step 1: Upload Files to Server
Upload all files from this directory to your server at `unfurling.ninja`

### Step 2: Configure Environment
```bash
# Copy environment file
cp .env.example .env

# Edit .env with your database credentials
nano .env
```

Set these values in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 3: Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### Step 4: Generate Application Key
```bash
php artisan key:generate
```

### Step 5: Run Migrations and Seeders
```bash
# Fresh migration (WARNING: This will drop all existing tables!)
php artisan migrate:fresh --seed
```

### Step 6: Clear Caches
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Step 7: Set Permissions
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Demo Credentials

After seeding, you can use these credentials:

### Admin (Web App):
- Email: `admin@jetty.com`
- Password: `password`

### Customers (Mobile App):
- Email: `john.doe@example.com` | Password: `password`
- Email: `jane.smith@example.com` | Password: `password`
- Email: `test@example.com` | Password: `password`

## API Endpoints

### Public (No Authentication):
- POST `/api/customer/login` - Customer login
- POST `/api/customer/generate-otp` - Register step 1
- POST `/api/customer/verify-otp` - Register step 2
- POST `/api/customer/password-reset/request-otp` - Password reset step 1
- POST `/api/customer/password-reset/verify-otp` - Password reset step 2
- POST `/api/customer/password-reset/reset` - Password reset step 3

### Protected (Requires Sanctum Token):
- POST `/api/customer/logout`
- GET `/api/customer/profile`
- GET `/api/branches`
- GET `/api/branches/{id}/ferries`
- GET `/api/item-rates`
- GET/POST `/api/bookings`

## Testing the API

### 1. Login Test:
```bash
curl -X POST https://unfurling.ninja/api/customer/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

Expected response:
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "...",
    "customer": {...}
  }
}
```

### 2. Get Branches (requires token):
```bash
curl -X GET https://unfurling.ninja/api/branches \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Troubleshooting

### If migrations fail:
```bash
# Check database connection
php artisan migrate:status

# Reset and try again
php artisan migrate:fresh --seed
```

### If API returns 404:
- Check that `bootstrap/app.php` includes the `api:` line
- Run `php artisan route:clear` and `php artisan route:cache`

### If login returns 500:
- Check storage logs: `tail -f storage/logs/laravel.log`
- Verify Customer model has `use Laravel\Sanctum\HasApiTokens`

### If "Field doesn't have default value":
- The seeders have been fixed to include all required fields
- Run `php artisan migrate:fresh --seed` to start clean

## Important Notes

- ✅ Database structure is now correct and complete
- ✅ All seeders include required fields (user_id, branch_id, etc.)
- ✅ Migration order is fixed (branches before ferryboats)
- ✅ API authentication is configured and working
- ✅ Customer model has token support
- ⚠️ Remember to backup your database before running `migrate:fresh`!

## Next Steps

After successful deployment:
1. Test login with demo customer credentials
2. Test fetching branches and ferries from mobile app
3. Create real customer accounts via registration flow
4. Configure profile image uploads if needed
