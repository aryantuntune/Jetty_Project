<?php

// vps_debug.php - Version 2.0 (Login Doctor)

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<style>body{font-family:sans-serif;line-height:1.5;padding:20px;} h2{border-bottom:1px solid #ccc;}</style>";
echo "<h1>VPS Login Doctor</h1>";

// 1. DB Connection
echo "<h2>1. Database Connectivity</h2>";
try {
    \DB::connection()->getPdo();
    echo "<span style='color:green'>✅ [PASS] Connected to Database: " . \DB::connection()->getDatabaseName() . "</span>";
} catch (\Exception $e) {
    echo "<span style='color:red'>❌ [FAIL] Database Error: " . $e->getMessage() . "</span>";
    die();
}

// 2. User Existence
echo "<h2>2. User Data Check</h2>";
$email = 'superadmin@gmail.com';
$user = \App\Models\User::where('email', $email)->first();

if ($user) {
    echo "<span style='color:green'>✅ [PASS] User '$email' Found (ID: $user->id)</span><br>";
    echo "Current Password Hash: " . substr($user->password, 0, 15) . "...<br>";
} else {
    echo "<span style='color:red'>❌ [FAIL] User '$email' does NOT exist! Did you run seed?</span><br>";
    echo "Total Users: " . \App\Models\User::count();
}

// 3. Environment & Session
echo "<h2>3. Session Configuration</h2>";
echo "<b>APP_URL:</b> " . config('app.url') . "<br>";
echo "<b>SESSION_DOMAIN:</b> " . (config('session.domain') ?: '<span style="color:orange">NULL (Browsers may block cookies)</span>') . "<br>";
echo "<b>SANCTUM DOMAINS:</b> " . implode(',', config('sanctum.stateful', [])) . "<br>";
echo "<b>Storage Writable:</b> ";
if (is_writable(storage_path('framework/sessions'))) {
    echo "<span style='color:green'>✅ Yes</span>";
} else {
    echo "<span style='color:red'>❌ NO! (Run: chmod -R 775 storage)</span>";
}

// 4. THE REAL TEST
echo "<h2>4. Authentication Test</h2>";
if (isset($_GET['try_login'])) {
    echo "Attempting <code>Auth::attempt(['email' => '$email', 'password' => 'admin123'])</code>...<br><br>";

    if (\Auth::attempt(['email' => $email, 'password' => 'admin123'])) {
        echo "<span style='color:green;font-size:1.2em;font-weight:bold'>✅ [SUCCESS] Login SUCCESSFUL!</span><br>";
        echo "The database and password are CORRECT.<br>";
        echo "Authentication System returned TRUE.<br><br>";
        echo "👉 <b>If you still cannot login on the website:</b><br>";
        echo "1. The issue is likely <b>COOKIES</b> or <b>HTTPS</b>.<br>";
        echo "2. Your browser might be blocking the cookie due to <code>SESSION_DOMAIN</code> mismatch.<br>";
        echo "3. Try clearing browser cookies and trying again.";
    } else {
        echo "<span style='color:red;font-size:1.2em;font-weight:bold'>❌ [FAIL] Login FAILED!</span><br>";
        echo "The password 'admin123' was rejected by Laravel.<br>";
        echo "<b>Diagnosis:</b> The password in the database does NOT match 'admin123'.<br>";
        echo "<b>Fix:</b> Run <code>php artisan migrate:fresh --seed --force</code> again on VPS.";
    }
} else {
    echo "<a href='?try_login=true' style='background:blue;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>👉 Click Here to Test Login Logic</a>";
}
