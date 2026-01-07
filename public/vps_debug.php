<?php

// vps_debug.php - Place this in your 'public' folder and visit it.

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>VPS Debugger</h1>";

// 1. Check Database Connection
echo "<h2>1. Database Connection</h2>";
try {
    \DB::connection()->getPdo();
    echo "<span style='color:green'>[PASS] Database Connected!</span><br>";
    echo "Database: " . \DB::connection()->getDatabaseName();
} catch (\Exception $e) {
    echo "<span style='color:red'>[FAIL] Could not connect to DB: " . $e->getMessage() . "</span>";
    die(); // Stop if no DB
}

// 2. Check User existence
echo "<h2>2. User Check</h2>";
$email = 'superadmin@gmail.com';
$user = \App\Models\User::where('email', $email)->first();

if ($user) {
    echo "<span style='color:green'>[PASS] User '$email' Found!</span><br>";
    echo "ID: " . $user->id . "<br>";
    echo "Role ID: " . $user->role_id . "<br>";
} else {
    echo "<span style='color:red'>[FAIL] User '$email' NOT FOUND in table 'users'.</span><br>";
    echo "Total Users in DB: " . \App\Models\User::count();
    // Try to find ANY user
    $anyUser = \App\Models\User::first();
    if ($anyUser) {
        echo "<br>Found someone else: " . $anyUser->email;
    }
}

// 3. Password Verification
if ($user) {
    echo "<h2>3. Password Verification</h2>";
    $testPass = 'admin123';

    // Manual Check
    $isMatch = \Hash::check($testPass, $user->password);

    if ($isMatch) {
        echo "<span style='color:green'>[PASS] Password 'admin123' MATCHES the hash in DB.</span>";
    } else {
        echo "<span style='color:red'>[FAIL] Password 'admin123' does NOT match.</span><br>";
        echo "Stored Hash: " . substr($user->password, 0, 10) . "...<br>";
        echo "Re-hashing 'admin123': " . substr(\Hash::make($testPass), 0, 10) . "...<br>";
        echo "<b>Fix:</b> You may need to re-seed or manually reset the password via Tinker.";
    }
}

// 4. Session & Domain Config
echo "<h2>4. Environment & Session</h2>";
echo "APP_URL: " . config('app.url') . "<br>";
echo "SESSION_DOMAIN: " . (config('session.domain') ?: 'null (defaults to host)') . "<br>";
echo "SESSION_SECURE_COOKIE: " . (config('session.secure') ? 'true' : 'false') . "<br>";
echo "SANCTUM_STATEFUL: " . config('sanctum.stateful')[0] ?? 'none';


// 5. Storage Permissions
echo "<h2>5. Storage Permissions</h2>";
$path = storage_path('framework/sessions');
if (is_writable($path)) {
    echo "<span style='color:green'>[PASS] Session folder is writable.</span>";
} else {
    echo "<span style='color:red'>[FAIL] Session folder is NOT writable! Login will fail.</span><br>";
    echo "Path: $path";
}
