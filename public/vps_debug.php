<?php

// vps_debug.php - Version 4.0 (Comprehensive Diagnostics)

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<style>
body{font-family:monospace;line-height:1.6;padding:20px;background:#1a1a2e;color:#eee;}
h1,h2{color:#00d9ff;}
.card{background:#16213e;padding:15px;border-radius:8px;margin:15px 0;border-left:4px solid #00d9ff;}
.pass{color:#0f0;} .fail{color:#f00;} .warn{color:#ff0;}
a.btn{display:inline-block;padding:8px 16px;margin:5px;text-decoration:none;color:#fff;border-radius:5px;font-weight:bold;}
.btn-blue{background:#0066cc;} .btn-green{background:#00aa00;} .btn-red{background:#cc0000;} .btn-orange{background:#ff9900;}
pre{background:#0f0f23;color:#ccc;padding:10px;overflow-x:auto;max-height:400px;font-size:12px;}
table{border-collapse:collapse;width:100%;} th,td{border:1px solid #333;padding:8px;text-align:left;} th{background:#0a3d62;}
</style>";

echo "<h1>🔧 VPS Debug Tool v4.0</h1>";

$mode = $_GET['mode'] ?? 'dashboard';

// NAV
echo "<div class='card'>";
echo "<a href='?mode=dashboard' class='btn btn-blue'>📊 Dashboard</a>";
echo "<a href='?mode=tables' class='btn btn-orange'>📋 Table Counts</a>";
echo "<a href='?mode=logs' class='btn btn-red'>📜 Error Logs</a>";
echo "<a href='?mode=mail' class='btn btn-green'>✉️ Test Email</a>";
echo "<a href='?mode=env' class='btn btn-blue'>⚙️ .env Values</a>";
echo "</div>";

if ($mode == 'dashboard') {
    echo "<div class='card'><h2>1️⃣ Database Connection</h2>";
    try {
        \DB::connection()->getPdo();
        echo "<span class='pass'>✅ Connected to: " . \DB::connection()->getDatabaseName() . "</span>";
    } catch (\Exception $e) {
        echo "<span class='fail'>❌ Error: " . $e->getMessage() . "</span>";
    }
    echo "</div>";

    echo "<div class='card'><h2>2️⃣ User Login Test</h2>";
    if (\Auth::attempt(['email' => 'superadmin@gmail.com', 'password' => 'admin123'])) {
        echo "<span class='pass'>✅ Auth::attempt() PASSED for superadmin@gmail.com</span>";
    } else {
        echo "<span class='fail'>❌ Auth::attempt() FAILED - run 'php artisan db:seed' on VPS</span>";
    }
    echo "</div>";

    echo "<div class='card'><h2>3️⃣ Critical Data</h2>";
    echo "<table>";
    echo "<tr><th>Table</th><th>Count</th><th>Status</th></tr>";
    $tables = ['users', 'branches', 'item_rates', 'item_categories', 'ferryboats', 'ferry_schedules', 'routes'];
    foreach ($tables as $t) {
        try {
            $count = \DB::table($t)->count();
            $status = $count > 0 ? "<span class='pass'>OK</span>" : "<span class='warn'>EMPTY!</span>";
            echo "<tr><td>$t</td><td>$count</td><td>$status</td></tr>";
        } catch (\Exception $e) {
            echo "<tr><td>$t</td><td colspan='2'><span class='fail'>ERROR: Table missing?</span></td></tr>";
        }
    }
    echo "</table></div>";

    echo "<div class='card'><h2>4️⃣ Storage Permissions</h2>";
    $paths = ['storage/logs', 'storage/framework/sessions', 'bootstrap/cache'];
    foreach ($paths as $p) {
        $full = base_path($p);
        $ok = is_writable($full);
        echo ($ok ? "<span class='pass'>✅" : "<span class='fail'>❌") . " $p</span><br>";
    }
    echo "</div>";

} elseif ($mode == 'tables') {
    echo "<div class='card'><h2>All Table Row Counts</h2><table><tr><th>Table</th><th>Rows</th></tr>";
    $tables = \DB::select('SHOW TABLES');
    $key = 'Tables_in_' . \DB::connection()->getDatabaseName();
    foreach ($tables as $t) {
        $name = $t->$key;
        $count = \DB::table($name)->count();
        echo "<tr><td>$name</td><td>$count</td></tr>";
    }
    echo "</table></div>";

} elseif ($mode == 'logs') {
    echo "<div class='card'><h2>Laravel Error Logs (Last 80 Lines)</h2>";
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $lines = file($logFile);
        echo "<pre>" . htmlspecialchars(implode('', array_slice($lines, -80))) . "</pre>";
    } else {
        echo "No log file found.";
    }
    echo "</div>";

} elseif ($mode == 'mail') {
    echo "<div class='card'><h2>Email Test</h2>";
    $to = config('mail.from.address');
    echo "Sending to: $to...<br><br>";
    try {
        \Mail::raw('Test from VPS Debug v4', fn($m) => $m->to($to)->subject('VPS Mail Test'));
        echo "<span class='pass'>✅ Sent! Check your inbox.</span>";
    } catch (\Exception $e) {
        echo "<span class='fail'>❌ " . $e->getMessage() . "</span>";
    }
    echo "</div>";

} elseif ($mode == 'env') {
    echo "<div class='card'><h2>.env Configuration</h2><table>";
    $keys = ['APP_URL', 'APP_DEBUG', 'DB_CONNECTION', 'DB_HOST', 'DB_DATABASE', 'SESSION_DRIVER', 'SESSION_DOMAIN', 'SANCTUM_STATEFUL_DOMAINS', 'MAIL_MAILER', 'MAIL_HOST'];
    foreach ($keys as $k) {
        $v = config(strtolower(str_replace('_', '.', $k))) ?? env($k) ?? '<not set>';
        if (str_contains($k, 'PASSWORD') || str_contains($k, 'SECRET'))
            $v = '****';
        echo "<tr><td>$k</td><td>$v</td></tr>";
    }
    echo "</table></div>";
}
