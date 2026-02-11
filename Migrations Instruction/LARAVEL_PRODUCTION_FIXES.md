# LARAVEL PRODUCTION FIXES - Step-by-Step Guide
## Jetty Ferry Management System - Resolve Random Failures

---

## 🎯 OBJECTIVE

Fix the following production issues:
- ❌ Random login failures (admin can't login even with correct credentials)
- ❌ 419 CSRF token errors appearing randomly
- ❌ 500 internal server errors after cache clear
- ❌ Need to re-seed database repeatedly
- ❌ System instability affecting 2000+ daily tickets

**Target:** Stable, production-ready Laravel backend that works 24/7 reliably.

---

## 📋 CRITICAL FIXES CHECKLIST

Execute these fixes in ORDER. Do not skip steps.

- [ ] **FIX 1:** Install and configure Redis
- [ ] **FIX 2:** Fix session configuration
- [ ] **FIX 3:** Fix file permissions
- [ ] **FIX 4:** Configure proper caching
- [ ] **FIX 5:** Optimize Laravel for production
- [ ] **FIX 6:** Fix database connection pooling
- [ ] **FIX 7:** Implement proper logging
- [ ] **FIX 8:** Add health monitoring
- [ ] **FIX 9:** Security hardening
- [ ] **FIX 10:** Performance optimization

---

## 🚀 FIX 1: INSTALL AND CONFIGURE REDIS (CRITICAL)

**Problem:** File-based sessions and cache are unstable and cause 419/500 errors.

**Solution:** Use Redis for sessions, cache, and queues.

### Step 1.1: Install Redis Server

```bash
# Update system packages
sudo apt update

# Install Redis
sudo apt install redis-server -y

# Start Redis service
sudo systemctl start redis-server

# Enable Redis to start on boot
sudo systemctl enable redis-server

# Verify Redis is running
redis-cli ping
# Expected output: PONG
```

### Step 1.2: Install PHP Redis Extension

```bash
# Install PHP Redis extension
sudo apt install php8.2-redis -y
# OR if using PHP 8.3
sudo apt install php8.3-redis -y

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
# OR
sudo systemctl restart php8.3-fpm

# Verify installation
php -m | grep redis
# Should show: redis
```

### Step 1.3: Install Laravel Redis Package

```bash
# Navigate to Laravel project
cd /var/www/jetty  # Adjust path to your project

# Install Predis (Laravel Redis client)
composer require predis/predis

# OR install PhpRedis (faster, C extension)
composer require phpredis/phpredis
```

### Step 1.4: Configure Laravel to Use Redis

**Edit `.env` file:**

```bash
# Open .env file
nano .env

# Update these lines:
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis connection settings
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_CLIENT=predis  # Or 'phpredis' if using PhpRedis

# Session settings (IMPORTANT)
SESSION_LIFETIME=720           # 12 hours
SESSION_EXPIRE_ON_CLOSE=false
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true     # If using HTTPS
SESSION_SAME_SITE=lax
```

### Step 1.5: Update Redis Configuration

**Edit `config/database.php`:**

```php
// Find the 'redis' section and ensure it looks like this:

'redis' => [
    'client' => env('REDIS_CLIENT', 'predis'),

    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
    ],

    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
    ],

    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),
    ],

    'session' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_SESSION_DB', '2'),
    ],
],
```

### Step 1.6: Clear and Restart

```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx  # or apache2

# Test Redis connection
php artisan tinker
# Inside tinker:
Redis::set('test', 'working');
Redis::get('test');
# Should output: "working"
exit
```

---

## 🔐 FIX 2: FIX SESSION CONFIGURATION (CRITICAL)

**Problem:** Sessions expire randomly causing login failures and CSRF errors.

### Step 2.1: Update Session Configuration

**Edit `config/session.php`:**

```php
return [
    // Use Redis (already set in .env)
    'driver' => env('SESSION_DRIVER', 'redis'),

    // Increase session lifetime (12 hours = 720 minutes)
    'lifetime' => env('SESSION_LIFETIME', 720),

    // Don't expire on browser close
    'expire_on_close' => false,

    // Encrypt sessions for security
    'encrypt' => false,  // Set to true if dealing with sensitive data

    // HTTP only cookies (prevent XSS)
    'http_only' => true,

    // Use secure cookies on HTTPS
    'secure' => env('SESSION_SECURE_COOKIE', true),

    // SameSite setting
    'same_site' => 'lax',

    // Cookie path
    'path' => '/',

    // Cookie domain (IMPORTANT for subdomains)
    'domain' => env('SESSION_DOMAIN', null),

    // Session store (use redis connection)
    'connection' => 'session',

    // Session table (only for database driver)
    'table' => 'sessions',

    // Session store prefix
    'store' => env('SESSION_STORE'),

    // Session lottery (garbage collection)
    'lottery' => [2, 100],

    // Session cookie name
    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),
];
```

### Step 2.2: Update .env with Session Settings

```bash
# Edit .env
nano .env

# Add/update these session settings:
SESSION_DRIVER=redis
SESSION_LIFETIME=720
SESSION_EXPIRE_ON_CLOSE=false
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_DOMAIN=null
SESSION_CONNECTION=session
```

### Step 2.3: Clear Session Data

```bash
# Clear old sessions
php artisan session:clear  # If command exists
# OR manually flush Redis sessions
redis-cli
> SELECT 2  # Session database
> FLUSHDB
> exit

# Clear config cache
php artisan config:clear
php artisan cache:clear

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

---

## 📁 FIX 3: FIX FILE PERMISSIONS (CRITICAL)

**Problem:** Laravel can't write to storage/cache directories causing 500 errors.

### Step 3.1: Set Correct Ownership

```bash
# Navigate to project root
cd /var/www/jetty  # Adjust to your path

# Set correct owner (www-data for Nginx/Apache)
sudo chown -R www-data:www-data .

# If using different user (check with: ps aux | grep php-fpm)
# sudo chown -R your-user:www-data .
```

### Step 3.2: Set Correct Permissions

```bash
# Set directory permissions
sudo find . -type d -exec chmod 755 {} \;

# Set file permissions
sudo find . -type f -exec chmod 644 {} \;

# Storage and cache directories need write access
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Ensure www-data can write
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
```

### Step 3.3: Verify Permissions

```bash
# Check storage permissions
ls -la storage/

# Should show:
# drwxrwxr-x www-data www-data ... framework
# drwxrwxr-x www-data www-data ... logs
# drwxrwxr-x www-data www-data ... app

# Test write access
sudo -u www-data touch storage/test.txt
# Should succeed without error
rm storage/test.txt
```

---

## ⚡ FIX 4: CONFIGURE PROPER CACHING (HIGH PRIORITY)

**Problem:** Cache corruption causes 500 errors.

### Step 4.1: Update Cache Configuration

**Edit `config/cache.php`:**

```php
return [
    // Default cache store
    'default' => env('CACHE_DRIVER', 'redis'),

    'stores' => [
        // Redis store configuration
        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
        ],

        // Keep file store as fallback
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ],
    ],

    // Cache key prefix
    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),
];
```

### Step 4.2: Clear All Caches

```bash
# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Clear compiled files
php artisan clear-compiled

# Optimize autoloader
composer dump-autoload --optimize
```

### Step 4.3: Cache Configuration for Production

```bash
# Cache configuration (speeds up app)
php artisan config:cache

# Cache routes (speeds up routing)
php artisan route:cache

# Cache views (speeds up blade)
php artisan view:cache

# Cache events
php artisan event:cache
```

---

## 🔧 FIX 5: OPTIMIZE LARAVEL FOR PRODUCTION (HIGH PRIORITY)

**Problem:** Development settings are enabled in production.

### Step 5.1: Update .env for Production

```bash
# Edit .env
nano .env

# CRITICAL: Set to production
APP_ENV=production
APP_DEBUG=false

# Enable maintenance mode page
APP_MAINTENANCE_MODE=false

# Set proper URL
APP_URL=https://unfurling.ninja

# Timezone (for ferry schedules)
APP_TIMEZONE=Asia/Kolkata

# Locale
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_IN
```

### Step 5.2: Optimize Composer Autoloader

```bash
# Optimize autoloader for production
composer install --optimize-autoloader --no-dev

# Generate optimized autoload files
composer dump-autoload --optimize --classmap-authoritative
```

### Step 5.3: Enable Opcache (PHP Performance)

```bash
# Edit PHP ini file
sudo nano /etc/php/8.2/fpm/php.ini

# Find and update these settings:
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0  # Important for production!
opcache.revalidate_freq=0
opcache.save_comments=1
opcache.fast_shutdown=1

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

### Step 5.4: Optimize PHP-FPM Pool

```bash
# Edit PHP-FPM pool configuration
sudo nano /etc/php/8.2/fpm/pool.d/www.conf

# Update these settings:
pm = dynamic
pm.max_children = 50        # Adjust based on RAM (each ~30MB)
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 1000      # Restart workers after 1000 requests

# Process idle timeout
pm.process_idle_timeout = 10s

# Request timeout
request_terminate_timeout = 300

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

---

## 🗄️ FIX 6: FIX DATABASE CONNECTION POOLING (MEDIUM PRIORITY)

**Problem:** Running out of database connections causing errors.

### Step 6.1: Update Database Configuration

**Edit `config/database.php`:**

```php
'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'jetty_db'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        PDO::ATTR_TIMEOUT => 5,              // Connection timeout
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'",
    ]) : [],
    
    // Connection pooling
    'pool' => [
        'min' => 2,
        'max' => 10,
    ],
    
    // Reconnect settings
    'reconnect' => true,
    'retry_after' => 100,  // milliseconds
],
```

### Step 6.2: Optimize MySQL Configuration

```bash
# Edit MySQL configuration
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf

# Add/update these settings:
[mysqld]
max_connections = 200              # Increase from default 151
wait_timeout = 600                 # 10 minutes
interactive_timeout = 600
connect_timeout = 10
max_allowed_packet = 64M

# Connection pooling
thread_cache_size = 50
table_open_cache = 4000
table_definition_cache = 2000

# Query cache (if MySQL 5.7)
# query_cache_type = 1
# query_cache_size = 64M
# query_cache_limit = 2M

# InnoDB settings
innodb_buffer_pool_size = 1G       # 70% of RAM if dedicated server
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT

# Save and restart MySQL
sudo systemctl restart mysql
```

### Step 6.3: Update .env Database Settings

```bash
# Edit .env
nano .env

# Database connection
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jetty_db
DB_USERNAME=root
DB_PASSWORD=your_password

# Connection settings
DB_TIMEOUT=5
DB_RECONNECT=true
```

---

## 📊 FIX 7: IMPLEMENT PROPER LOGGING (MEDIUM PRIORITY)

**Problem:** Can't debug issues without proper logs.

### Step 7.1: Configure Logging

**Edit `config/logging.php`:**

```php
return [
    'default' => env('LOG_CHANNEL', 'daily'),

    'channels' => [
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'error'),
            'days' => 14,  // Keep logs for 14 days
            'replace_placeholders' => true,
        ],

        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'slack'],  // Add slack for critical errors
            'ignore_exceptions' => false,
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'error'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        // Custom channel for debugging sessions
        'session_debug' => [
            'driver' => 'daily',
            'path' => storage_path('logs/session.log'),
            'level' => 'debug',
            'days' => 7,
        ],

        // Custom channel for authentication issues
        'auth_debug' => [
            'driver' => 'daily',
            'path' => storage_path('logs/auth.log'),
            'level' => 'debug',
            'days' => 7,
        ],
    ],
];
```

### Step 7.2: Update .env Logging Settings

```bash
# Edit .env
nano .env

# Logging settings
LOG_CHANNEL=daily
LOG_LEVEL=error              # error, warning, info, debug
LOG_DEPRECATIONS_CHANNEL=null
LOG_STACK=daily
```

### Step 7.3: Add Custom Logging to Auth

**Edit `app/Http/Controllers/Auth/LoginController.php` (or wherever login happens):**

```php
use Illuminate\Support\Facades\Log;

public function login(Request $request)
{
    // Log login attempt
    Log::channel('auth_debug')->info('Login attempt', [
        'email' => $request->email,
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);

    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->filled('remember'))) {
        $request->session()->regenerate();
        
        Log::channel('auth_debug')->info('Login successful', [
            'user_id' => Auth::id(),
            'email' => $request->email,
        ]);

        return redirect()->intended('home');
    }

    Log::channel('auth_debug')->warning('Login failed', [
        'email' => $request->email,
        'ip' => $request->ip(),
    ]);

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
}
```

### Step 7.4: Monitor Logs

```bash
# Monitor Laravel logs in real-time
tail -f storage/logs/laravel.log

# Monitor auth logs
tail -f storage/logs/auth.log

# Monitor session logs
tail -f storage/logs/session.log

# Search for errors
grep "ERROR" storage/logs/laravel.log

# Search for 419 errors
grep "419" storage/logs/laravel.log

# Search for 500 errors
grep "500" storage/logs/laravel.log
```

---

## 🏥 FIX 8: ADD HEALTH MONITORING (MEDIUM PRIORITY)

**Problem:** No way to know if system is healthy before users complain.

### Step 8.1: Create Health Check Endpoint

**Create `app/Http/Controllers/HealthController.php`:**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    /**
     * Health check endpoint
     */
    public function check(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'session' => $this->checkSession(),
        ];

        $healthy = !in_array(false, $checks, true);
        $status = $healthy ? 200 : 503;

        return response()->json([
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toISOString(),
            'checks' => $checks,
        ], $status);
    }

    /**
     * Check database connection
     */
    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            return true;
        } catch (\Exception $e) {
            \Log::error('Health check: Database failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check Redis connection
     */
    private function checkRedis(): bool
    {
        try {
            Redis::ping();
            return true;
        } catch (\Exception $e) {
            \Log::error('Health check: Redis failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check cache functionality
     */
    private function checkCache(): bool
    {
        try {
            $key = 'health_check_' . time();
            Cache::put($key, 'test', 10);
            $value = Cache::get($key);
            Cache::forget($key);
            return $value === 'test';
        } catch (\Exception $e) {
            \Log::error('Health check: Cache failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check storage write permissions
     */
    private function checkStorage(): bool
    {
        try {
            $testFile = storage_path('framework/cache/health_check.txt');
            file_put_contents($testFile, 'test');
            $content = file_get_contents($testFile);
            unlink($testFile);
            return $content === 'test';
        } catch (\Exception $e) {
            \Log::error('Health check: Storage failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check session functionality
     */
    private function checkSession(): bool
    {
        try {
            session(['health_check' => 'test']);
            $value = session('health_check');
            session()->forget('health_check');
            return $value === 'test';
        } catch (\Exception $e) {
            \Log::error('Health check: Session failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
```

### Step 8.2: Add Health Check Route

**Edit `routes/web.php`:**

```php
use App\Http\Controllers\HealthController;

// Health check endpoint (no auth required)
Route::get('/health', [HealthController::class, 'check'])->name('health.check');
```

### Step 8.3: Test Health Check

```bash
# Test the health endpoint
curl http://localhost/health

# Should return:
# {
#   "status": "healthy",
#   "timestamp": "2024-01-15T10:30:00.000000Z",
#   "checks": {
#     "database": true,
#     "redis": true,
#     "cache": true,
#     "storage": true,
#     "session": true
#   }
# }
```

### Step 8.4: Set Up Monitoring Cron Job

```bash
# Create monitoring script
sudo nano /usr/local/bin/check-jetty-health.sh

# Add this content:
#!/bin/bash
HEALTH_URL="http://localhost/health"
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" $HEALTH_URL)

if [ $RESPONSE != "200" ]; then
    echo "$(date): Jetty health check FAILED (HTTP $RESPONSE)" >> /var/log/jetty-health.log
    # Optional: Send alert email
    # echo "Jetty health check failed" | mail -s "ALERT: Jetty Down" admin@example.com
else
    echo "$(date): Jetty health check OK" >> /var/log/jetty-health.log
fi

# Make executable
sudo chmod +x /usr/local/bin/check-jetty-health.sh

# Add to crontab (check every 5 minutes)
sudo crontab -e

# Add this line:
*/5 * * * * /usr/local/bin/check-jetty-health.sh
```

---

## 🔒 FIX 9: SECURITY HARDENING (LOW PRIORITY BUT IMPORTANT)

### Step 9.1: Update APP_KEY

```bash
# Generate new APP_KEY (ONLY if you're okay with invalidating existing sessions)
php artisan key:generate

# This will update .env automatically
```

### Step 9.2: Enable HTTPS Enforcement

**Edit `app/Providers/AppServiceProvider.php`:**

```php
use Illuminate\Support\Facades\URL;

public function boot()
{
    // Force HTTPS in production
    if (config('app.env') === 'production') {
        URL::forceScheme('https');
    }
}
```

### Step 9.3: Update CORS Configuration

**Edit `config/cors.php`:**

```php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    
    'allowed_methods' => ['*'],
    
    'allowed_origins' => [
        env('APP_URL'),
        'https://unfurling.ninja',
    ],
    
    'allowed_origins_patterns' => [],
    
    'allowed_headers' => ['*'],
    
    'exposed_headers' => [],
    
    'max_age' => 0,
    
    'supports_credentials' => true,
];
```

### Step 9.4: Rate Limiting

**Edit `app/Http/Kernel.php`:**

```php
protected $middlewareGroups = [
    'web' => [
        // ... existing middleware
        \Illuminate\Routing\Middleware\ThrottleRequests::class.':web',
    ],

    'api' => [
        \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
        // ... existing middleware
    ],
];
```

**Edit `app/Providers/RouteServiceProvider.php`:**

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

public function boot()
{
    // API rate limiting
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });

    // Login rate limiting
    RateLimiter::for('login', function (Request $request) {
        return Limit::perMinute(5)->by($request->email . '|' . $request->ip());
    });
}
```

---

## ⚡ FIX 10: PERFORMANCE OPTIMIZATION (LOW PRIORITY)

### Step 10.1: Database Query Optimization

**Add indexes for frequently queried columns:**

```bash
# Create migration for indexes
php artisan make:migration add_performance_indexes_to_tables

# Edit the migration file
```

**Migration content:**

```php
public function up()
{
    Schema::table('tickets', function (Blueprint $table) {
        $table->index('ticket_date');
        $table->index('branch_id');
        $table->index(['ticket_date', 'branch_id']);
        $table->index('verified_at');
    });

    Schema::table('bookings', function (Blueprint $table) {
        $table->index('customer_id');
        $table->index('status');
        $table->index(['customer_id', 'status']);
    });

    Schema::table('users', function (Blueprint $table) {
        $table->index('role_id');
        $table->index('branch_id');
    });
}

public function down()
{
    Schema::table('tickets', function (Blueprint $table) {
        $table->dropIndex(['ticket_date']);
        $table->dropIndex(['branch_id']);
        $table->dropIndex(['ticket_date', 'branch_id']);
        $table->dropIndex(['verified_at']);
    });

    Schema::table('bookings', function (Blueprint $table) {
        $table->dropIndex(['customer_id']);
        $table->dropIndex(['status']);
        $table->dropIndex(['customer_id', 'status']);
    });

    Schema::table('users', function (Blueprint $table) {
        $table->dropIndex(['role_id']);
        $table->dropIndex(['branch_id']);
    });
}
```

```bash
# Run migration
php artisan migrate
```

### Step 10.2: Enable Laravel Horizon (Optional - for queue monitoring)

```bash
# Install Horizon
composer require laravel/horizon

# Publish configuration
php artisan horizon:install

# Start Horizon
php artisan horizon

# OR set up as systemd service
sudo nano /etc/systemd/system/horizon.service
```

**Horizon service file:**

```ini
[Unit]
Description=Laravel Horizon
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/jetty
ExecStart=/usr/bin/php /var/www/jetty/artisan horizon
Restart=always
RestartSec=3

[Install]
WantedBy=multi-user.target
```

```bash
# Enable and start Horizon
sudo systemctl enable horizon
sudo systemctl start horizon
```

---

## ✅ VERIFICATION CHECKLIST

After applying all fixes, verify everything works:

### Step V1: Test Redis Connection

```bash
# Test Redis
php artisan tinker

# In tinker:
Redis::set('test', 'working');
Redis::get('test');  # Should return "working"
Cache::put('test', 'working', 60);
Cache::get('test');  # Should return "working"
exit
```

### Step V2: Test Session Persistence

```bash
# Login to admin panel
# Open browser DevTools > Application > Cookies
# Check for session cookie
# Wait 10 minutes
# Refresh page - should still be logged in
```

### Step V3: Test Database Connection

```bash
php artisan tinker

# In tinker:
use App\Models\User;
User::count();  # Should return user count without error
exit
```

### Step V4: Check File Permissions

```bash
# Test write to storage
sudo -u www-data touch storage/test.txt
# Should succeed
rm storage/test.txt
```

### Step V5: Test Health Endpoint

```bash
curl http://localhost/health
# Should return 200 with all checks passing
```

### Step V6: Monitor Logs

```bash
# Monitor for 24 hours
tail -f storage/logs/laravel.log

# Should NOT see:
# - 419 errors
# - Session errors
# - Cache errors
# - Authentication failures (except actual wrong passwords)
```

### Step V7: Load Testing (Optional)

```bash
# Install Apache Bench
sudo apt install apache2-utils

# Test 1000 requests with 10 concurrent users
ab -n 1000 -c 10 https://unfurling.ninja/

# Check results - should have no failed requests
```

---

## 🚨 TROUBLESHOOTING GUIDE

### Issue: Redis Not Starting

```bash
# Check Redis status
sudo systemctl status redis-server

# Check Redis logs
sudo tail -f /var/log/redis/redis-server.log

# Restart Redis
sudo systemctl restart redis-server

# If still not working, check config
sudo nano /etc/redis/redis.conf
# Ensure: bind 127.0.0.1
# Ensure: protected-mode yes
```

### Issue: 419 Errors Still Appearing

```bash
# Clear everything
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Check APP_KEY is set
cat .env | grep APP_KEY
# Should show: APP_KEY=base64:...

# If empty, generate:
php artisan key:generate

# Clear browser cookies
# Test login again
```

### Issue: 500 Errors After Cache Clear

```bash
# Check file permissions
ls -la storage/
ls -la bootstrap/cache/

# Should show www-data:www-data

# If not, fix:
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Check PHP errors
sudo tail -f /var/log/php8.2-fpm.log
```

### Issue: Database Connection Errors

```bash
# Test MySQL connection
mysql -u root -p
# Enter password
# Should connect

# Check max_connections
mysql -u root -p -e "SHOW VARIABLES LIKE 'max_connections';"
# Should be 200

# Check current connections
mysql -u root -p -e "SHOW PROCESSLIST;"
# Should not be at max

# If at max, increase in /etc/mysql/mysql.conf.d/mysqld.cnf
# max_connections = 300
# Then: sudo systemctl restart mysql
```

### Issue: Opcache Not Working

```bash
# Check if Opcache is enabled
php -i | grep opcache.enable
# Should show: opcache.enable => On => On

# If not, edit php.ini:
sudo nano /etc/php/8.2/fpm/php.ini
# Set: opcache.enable=1

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Clear Opcache
php artisan cache:clear
sudo systemctl restart php8.2-fpm
```

---

## 📊 MONITORING COMMANDS

Use these commands to monitor system health:

### Monitor System Resources

```bash
# CPU and Memory
htop

# Disk space
df -h

# Disk I/O
iotop

# Network connections
netstat -an | grep :80 | wc -l
```

### Monitor Redis

```bash
# Redis info
redis-cli info

# Monitor Redis commands in real-time
redis-cli monitor

# Check memory usage
redis-cli info memory

# Check connected clients
redis-cli client list
```

### Monitor MySQL

```bash
# MySQL connections
mysql -u root -p -e "SHOW PROCESSLIST;"

# MySQL status
mysql -u root -p -e "SHOW STATUS LIKE 'Threads_connected';"

# Slow queries
mysql -u root -p -e "SHOW STATUS LIKE 'Slow_queries';"
```

### Monitor PHP-FPM

```bash
# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Check PHP-FPM pool status (if configured)
curl http://localhost/fpm-status

# Monitor PHP-FPM log
sudo tail -f /var/log/php8.2-fpm.log
```

### Monitor Laravel

```bash
# Real-time log monitoring
tail -f storage/logs/laravel.log

# Monitor auth logs
tail -f storage/logs/auth.log

# Count errors in last 100 lines
tail -100 storage/logs/laravel.log | grep ERROR | wc -l

# Find recent 419 errors
grep "419" storage/logs/laravel.log | tail -20
```

---

## 🎯 SUCCESS METRICS

After applying all fixes, you should see:

- ✅ **Zero 419 errors** - CSRF tokens working properly
- ✅ **Zero 500 errors** - Cache/storage working properly
- ✅ **Stable logins** - Admin can login and stay logged in for hours
- ✅ **No need to re-seed database** - Database stable
- ✅ **No need to clear cache** - Cache working properly
- ✅ **Fast response times** - < 200ms for most requests
- ✅ **No memory leaks** - Memory usage stable
- ✅ **Health check passes** - All systems green

---

## 📋 MAINTENANCE SCHEDULE

Set up these regular maintenance tasks:

### Daily Tasks (Automated)

```bash
# Add to crontab: crontab -e

# Clear expired sessions (daily at 2 AM)
0 2 * * * cd /var/www/jetty && php artisan session:gc

# Clear old logs (keep last 14 days)
0 3 * * * find /var/www/jetty/storage/logs -name "*.log" -mtime +14 -delete

# Backup database (daily at 4 AM)
0 4 * * * mysqldump -u root -pYOUR_PASSWORD jetty_db > /backup/jetty_$(date +\%Y\%m\%d).sql

# Health check (every 5 minutes)
*/5 * * * * /usr/local/bin/check-jetty-health.sh
```

### Weekly Tasks (Manual)

```bash
# Check disk space
df -h

# Check MySQL slow queries
mysql -u root -p -e "SHOW STATUS LIKE 'Slow_queries';"

# Check Redis memory usage
redis-cli info memory

# Review error logs
grep ERROR storage/logs/laravel-$(date +\%Y-\%m-\%d).log
```

### Monthly Tasks (Manual)

```bash
# Update system packages
sudo apt update && sudo apt upgrade

# Check and update Composer dependencies
composer outdated
# composer update (if safe updates available)

# Optimize database tables
php artisan db:optimize

# Review and archive old logs
# Move logs older than 30 days to archive
```

---

## 🎓 LESSONS LEARNED

**What caused the original issues:**

1. **File-based sessions** → Use Redis instead
2. **File-based cache** → Use Redis instead
3. **Wrong file permissions** → Fixed with proper chown/chmod
4. **No Opcache** → Enabled for PHP performance
5. **No connection pooling** → Configured properly
6. **No monitoring** → Added health checks
7. **Debug mode on in production** → Turned off
8. **No proper logging** → Configured properly

**Prevention for future:**

- ✅ Always use Redis for sessions/cache in production
- ✅ Always set proper file permissions
- ✅ Always enable Opcache in production
- ✅ Always turn off debug mode in production
- ✅ Always set up monitoring and health checks
- ✅ Always configure proper logging
- ✅ Always optimize database connections
- ✅ Always test changes in staging first

---

## 🚀 DEPLOYMENT CHECKLIST

Before deploying any changes to production:

- [ ] Test all fixes in development/staging environment
- [ ] Backup database before making changes
- [ ] Backup `.env` file
- [ ] Enable maintenance mode: `php artisan down`
- [ ] Pull latest code: `git pull`
- [ ] Install dependencies: `composer install --no-dev`
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Clear all caches: `php artisan optimize:clear`
- [ ] Recache for production: `php artisan optimize`
- [ ] Restart services: `sudo systemctl restart php8.2-fpm nginx`
- [ ] Disable maintenance mode: `php artisan up`
- [ ] Test health endpoint: `curl http://localhost/health`
- [ ] Test admin login
- [ ] Monitor logs for 30 minutes: `tail -f storage/logs/laravel.log`

---

## 📞 EMERGENCY PROCEDURES

### If System Goes Down

1. **Enable maintenance mode:**
   ```bash
   php artisan down --message="System maintenance in progress"
   ```

2. **Check health status:**
   ```bash
   curl http://localhost/health
   ```

3. **Check services:**
   ```bash
   sudo systemctl status nginx
   sudo systemctl status php8.2-fpm
   sudo systemctl status mysql
   sudo systemctl status redis-server
   ```

4. **Check logs:**
   ```bash
   tail -100 storage/logs/laravel.log
   tail -100 /var/log/nginx/error.log
   tail -100 /var/log/php8.2-fpm.log
   ```

5. **Restart services if needed:**
   ```bash
   sudo systemctl restart php8.2-fpm
   sudo systemctl restart nginx
   sudo systemctl restart redis-server
   ```

6. **Clear caches:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

7. **Re-enable:**
   ```bash
   php artisan up
   ```

---

## 📝 FINAL NOTES

**These fixes address:**
- ✅ Random login failures (Redis sessions)
- ✅ 419 CSRF errors (stable sessions)
- ✅ 500 errors (proper permissions and cache)
- ✅ Database connection issues (connection pooling)
- ✅ Cache corruption (Redis instead of files)
- ✅ Performance issues (Opcache, optimization)
- ✅ Lack of monitoring (health checks)

**After applying all fixes, your system should:**
- Run stable 24/7
- Handle 2000+ tickets/day easily
- No need to manually clear cache
- No need to re-seed database
- Admins stay logged in properly
- No random 419/500 errors

**If issues persist after all fixes:**
- Check server resources (CPU, RAM, disk)
- Check for DDoS or unusual traffic
- Review application code for database leaks
- Consider upgrading server hardware

---

**GOOD LUCK! Your Laravel backend should now be production-ready and stable! 🚀**

---

