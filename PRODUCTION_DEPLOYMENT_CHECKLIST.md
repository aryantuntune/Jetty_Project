# Production Server Deployment Checklist

## 🚀 REQUIRED ON PRODUCTION SERVER

These steps must be performed on your **Linux production server** after deploying the code.

---

## 1️⃣ Install Redis (CRITICAL)

```bash
# Install Redis
sudo apt update
sudo apt install redis-server -y

# Start and enable Redis
sudo systemctl start redis-server
sudo systemctl enable redis-server

# Verify Redis is running
redis-cli ping
# Expected output: PONG
```

---

## 2️⃣ Install PHP Redis Extension

```bash
# For PHP 8.2
sudo apt install php8.2-redis -y

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Verify
php -m | grep redis
```

---

## 3️⃣ Update Production .env

```env
# =====================================================
# PRODUCTION SETTINGS
# =====================================================
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# =====================================================
# DATABASE - PostgreSQL (Already configured)
# =====================================================
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=jetty
DB_USERNAME=postgres
DB_PASSWORD=your_password

# =====================================================
# REDIS - Sessions, Cache, Queues
# =====================================================
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_CLIENT=predis

# =====================================================
# SESSION - Use Redis
# =====================================================
SESSION_DRIVER=redis
SESSION_LIFETIME=720
SESSION_EXPIRE_ON_CLOSE=false
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

# =====================================================
# CACHE & QUEUE - Use Redis
# =====================================================
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

# =====================================================
# LOGGING
# =====================================================
LOG_CHANNEL=daily
LOG_LEVEL=error
```

---

## 4️⃣ Install Predis Package

```bash
cd /var/www/your-project
composer require predis/predis
```

---

## 5️⃣ Fix File Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/your-project

# Set permissions
sudo chmod -R 755 /var/www/your-project
sudo chmod -R 775 /var/www/your-project/storage
sudo chmod -R 775 /var/www/your-project/bootstrap/cache
```

---

## 6️⃣ Enable PHP Opcache

Edit `/etc/php/8.2/fpm/php.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

Restart PHP:
```bash
sudo systemctl restart php8.2-fpm
```

---

## 7️⃣ Clear and Optimize for Production

```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

---

## 8️⃣ Test Health Endpoint

```bash
curl https://your-domain.com/health

# Should return:
# {
#   "status": "healthy",
#   "checks": {
#     "database": true,
#     "cache": true,
#     "storage": true,
#     "session": true,
#     "redis": true
#   }
# }
```

---

## 9️⃣ Set Up Cron Job for Monitoring

```bash
# Add to crontab
crontab -e

# Add these lines:
*/5 * * * * curl -s https://your-domain.com/health > /dev/null || echo "Jetty DOWN" | mail admin@example.com
```

---

## ✅ Verification Checklist

After completing all steps:

- [ ] Redis: `redis-cli ping` returns `PONG`
- [ ] Health: `/health` endpoint returns `200 OK`
- [ ] Login: Admin can login and stay logged in
- [ ] No 419 errors after 10+ minutes
- [ ] No 500 errors after cache clear
- [ ] Logs: Check `storage/logs/` for errors

---

## 🚨 Emergency Recovery

If system goes down:

```bash
# 1. Check services
sudo systemctl status nginx php8.2-fpm redis-server

# 2. Restart if needed
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
sudo systemctl restart redis-server

# 3. Clear caches
php artisan cache:clear
php artisan config:clear

# 4. Check logs
tail -100 storage/logs/laravel.log
```
