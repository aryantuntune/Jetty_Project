<?php

// vps_debug.php - Version 3.0 (Log Viewer & Mail Tester)

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<style>
body{font-family:sans-serif;line-height:1.5;padding:20px; background:#f4f4f4;} 
h2{border-bottom:1px solid #ccc; padding-bottom:5px; margin-top:30px;}
.card {background:white; padding:20px; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.1); margin-bottom:20px;}
.btn {display:inline-block; padding:10px 15px; margin:5px; text-decoration:none; color:white; border-radius:5px;}
.btn-blue {background:#007bff;}
.btn-green {background:#28a745;}
.btn-red {background:#dc3545;}
pre {background:#222; color:#0f0; padding:15px; overflow-x:auto; border-radius:5px; max-height:500px;}
</style>";

echo "<h1>VPS Debug Tool v3.0</h1>";

$mode = $_GET['mode'] ?? 'dashboard';

// NAVIGATION
echo "<div class='card'>";
echo "<a href='?mode=dashboard' class='btn btn-blue'>Dashboard</a>";
echo "<a href='?mode=logs' class='btn btn-red'>View Error Logs (500 Error Fix)</a>";
echo "<a href='?mode=mail' class='btn btn-green'>Test Email</a>";
echo "<a href='?mode=phpinfo' class='btn btn-blue'>PHP Info</a>";
echo "</div>";

if ($mode == 'dashboard') {
    echo "<div class='card'>";
    echo "<h2>State Check</h2>";

    // DB
    try {
        \DB::connection()->getPdo();
        echo "<span style='color:green'>✅ Database Connected</span><br>";
    } catch (\Exception $e) {
        echo "<span style='color:red'>❌ Database Error: " . $e->getMessage() . "</span><br>";
    }

    // AUTH
    $email = 'superadmin@gmail.com';
    if (\Auth::attempt(['email' => $email, 'password' => 'admin123'])) {
        echo "<span style='color:green'>✅ Login Logic Works (User: $email / Pass: admin123)</span><br>";
    } else {
        echo "<span style='color:red'>❌ Login Logic Failed for default creds</span><br>";
    }

    echo "</div>";

} elseif ($mode == 'logs') {
    echo "<div class='card'>";
    echo "<h2>Laravel Error Logs (Last 100 Lines)</h2>";
    $logFile = storage_path('logs/laravel.log');

    if (file_exists($logFile)) {
        $lines = file($logFile);
        $lastLines = array_slice($lines, -100);
        echo "<pre>";
        foreach ($lastLines as $line) {
            echo htmlspecialchars($line);
        }
        echo "</pre>";
    } else {
        echo "No log file found at: $logFile";
    }
    echo "</div>";

} elseif ($mode == 'mail') {
    echo "<div class='card'>";
    echo "<h2>Email Tester</h2>";
    echo "Trying to send a test email to: " . config('mail.from.address') . "...<br>";

    try {
        \Mail::raw('This is a test email from your VPS Debugger.', function ($msg) {
            $msg->to(config('mail.from.address'))
                ->subject('VPS Email Test Success');
        });
        echo "<h3 style='color:green'>✅ Email Sent Successfully!</h3>";
        echo "Check your inbox (" . config('mail.from.address') . ")";
    } catch (\Exception $e) {
        echo "<h3 style='color:red'>❌ Email Failed</h3>";
        echo "Error: " . $e->getMessage();
    }
    echo "</div>";
} elseif ($mode == 'phpinfo') {
    phpinfo();
}
