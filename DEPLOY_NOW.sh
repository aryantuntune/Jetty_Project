#!/bin/bash
# ============================================
# DEPLOY BUG FIXES TO PRODUCTION SERVER
# Date: December 16, 2025
# ============================================

echo "=========================================="
echo "Starting deployment of critical bug fixes"
echo "=========================================="
echo ""

# Navigate to project directory
cd /var/www/carferry.online || { echo "ERROR: Cannot find project directory"; exit 1; }

echo "Step 1: Pulling latest code from GitHub..."
git pull origin master
if [ $? -ne 0 ]; then
    echo "ERROR: Git pull failed"
    exit 1
fi
echo "✅ Code pulled successfully"
echo ""

echo "Step 2: Running database migrations..."
echo "This will run 4 new migrations:"
echo "  - 2025_12_16_000001_add_missing_fields_to_tickets_table"
echo "  - 2025_12_16_000002_add_user_id_to_ticket_lines_table"
echo "  - 2025_12_16_000003_add_database_indexes"
echo "  - 2025_12_16_000004_add_foreign_key_constraints"
echo ""
php artisan migrate --force
if [ $? -ne 0 ]; then
    echo "ERROR: Migration failed"
    exit 1
fi
echo "✅ Migrations completed successfully"
echo ""

echo "Step 3: Seeding ferry boats data..."
php artisan db:seed --class=FerryBoatsTableSeeder --force
echo "✅ Ferry boats seeded"
echo ""

echo "Step 4: Seeding item rates data..."
php artisan db:seed --class=ItemRatesSeeder --force
echo "✅ Item rates seeded"
echo ""

echo "Step 5: Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "✅ Caches cleared"
echo ""

echo "Step 6: Restarting PHP-FPM..."
sudo systemctl restart php8.3-fpm
if [ $? -ne 0 ]; then
    echo "ERROR: PHP-FPM restart failed"
    exit 1
fi
echo "✅ PHP-FPM restarted"
echo ""

echo "=========================================="
echo "✅ DEPLOYMENT COMPLETED SUCCESSFULLY"
echo "=========================================="
echo ""
echo "Next Steps:"
echo "1. Run verification commands (see below)"
echo "2. Test ticket creation in admin panel"
echo "3. Test mobile app booking"
echo "4. Check reports page"
echo ""
echo "=========================================="
echo "VERIFICATION COMMANDS"
echo "=========================================="
echo ""
echo "# Check tickets table structure:"
echo "mysql -u root -p jetty_db -e 'DESCRIBE tickets;'"
echo ""
echo "# Check indexes were created:"
echo "mysql -u root -p jetty_db -e \"SHOW INDEX FROM personal_access_tokens WHERE Key_name = 'idx_tokens_token';\""
echo ""
echo "# Check foreign keys:"
echo "mysql -u root -p jetty_db -e \"SELECT CONSTRAINT_NAME, TABLE_NAME, REFERENCED_TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = 'jetty_db' AND REFERENCED_TABLE_NAME IS NOT NULL;\""
echo ""
echo "=========================================="
