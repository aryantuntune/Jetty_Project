<?php
/**
 * VPS Debug Dashboard v5.0 - Intelligent Error Detection
 * Features:
 * - Smart log parsing (Backend/DB/Auth errors separated)
 * - API endpoint tester
 * - Data integrity checker
 * - Relationship validator
 */

define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

ini_set('display_errors', 1);
error_reporting(E_ALL);

$mode = $_GET['mode'] ?? 'dashboard';
$action = $_GET['action'] ?? null;

// CSS
echo "<style>
:root { --bg: #0d1117; --card: #161b22; --border: #30363d; --text: #c9d1d9; --accent: #58a6ff; --success: #3fb950; --warning: #d29922; --danger: #f85149; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; background: var(--bg); color: var(--text); padding: 20px; line-height: 1.5; }
h1 { color: var(--accent); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
h2 { color: var(--accent); margin-bottom: 15px; font-size: 1.1rem; border-bottom: 1px solid var(--border); padding-bottom: 8px; }
.nav { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; padding: 15px; background: var(--card); border-radius: 8px; border: 1px solid var(--border); }
.nav a { padding: 8px 16px; background: var(--border); color: var(--text); text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 500; transition: all 0.2s; }
.nav a:hover, .nav a.active { background: var(--accent); color: #000; }
.card { background: var(--card); border: 1px solid var(--border); border-radius: 8px; padding: 20px; margin-bottom: 15px; }
.grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; }
table { width: 100%; border-collapse: collapse; font-size: 13px; }
th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid var(--border); }
th { background: rgba(88, 166, 255, 0.1); color: var(--accent); font-weight: 600; }
.ok { color: var(--success); } .warn { color: var(--warning); } .error { color: var(--danger); }
pre { background: #0d1117; padding: 15px; border-radius: 6px; overflow-x: auto; font-size: 12px; max-height: 400px; overflow-y: auto; border: 1px solid var(--border); }
.log-entry { padding: 8px 12px; margin: 4px 0; border-radius: 4px; font-family: monospace; font-size: 12px; }
.log-error { background: rgba(248, 81, 73, 0.1); border-left: 3px solid var(--danger); }
.log-warning { background: rgba(210, 153, 34, 0.1); border-left: 3px solid var(--warning); }
.log-info { background: rgba(88, 166, 255, 0.1); border-left: 3px solid var(--accent); }
.badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
.badge-success { background: var(--success); color: #000; }
.badge-danger { background: var(--danger); color: #fff; }
.badge-warning { background: var(--warning); color: #000; }
input, select { padding: 8px 12px; border: 1px solid var(--border); background: var(--bg); color: var(--text); border-radius: 6px; font-size: 13px; }
button { padding: 8px 16px; background: var(--accent); color: #000; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
button:hover { opacity: 0.9; }
.test-result { margin-top: 15px; padding: 15px; border-radius: 6px; }
.test-pass { background: rgba(63, 185, 80, 0.1); border: 1px solid var(--success); }
.test-fail { background: rgba(248, 81, 73, 0.1); border: 1px solid var(--danger); }
</style>";

echo "<h1>🔬 VPS Debug Dashboard v5.0</h1>";

// Navigation
$modes = ['dashboard' => '📊 Overview', 'logs' => '📜 Smart Logs', 'live-logs' => '🔴 Live Logs', 'cache-tools' => '🧹 Cache Tools', 'api-test' => '🔌 API Tester', 'integrity' => '🔗 Data Integrity', 'routes-check' => '🛤️ Routes Debug'];
echo "<div class='nav'>";
foreach ($modes as $key => $label) {
    $class = ($mode === $key) ? 'active' : '';
    echo "<a href='?mode=$key' class='$class'>$label</a>";
}
echo "</div>";

// ===== DASHBOARD =====
if ($mode === 'dashboard') {
    echo "<div class='grid'>";

    // DB Connection
    echo "<div class='card'><h2>🗄️ Database</h2>";
    try {
        \DB::connection()->getPdo();
        echo "<p class='ok'>✅ Connected: " . \DB::connection()->getDatabaseName() . "</p>";
    } catch (\Exception $e) {
        echo "<p class='error'>❌ " . $e->getMessage() . "</p>";
    }
    echo "</div>";

    // Auth Test
    echo "<div class='card'><h2>🔐 Authentication</h2>";
    if (\Auth::attempt(['email' => 'superadmin@gmail.com', 'password' => 'admin123'])) {
        echo "<p class='ok'>✅ Login works for superadmin@gmail.com</p>";
    } else {
        $user = \App\Models\User::where('email', 'superadmin@gmail.com')->first();
        echo $user ? "<p class='error'>❌ User exists but password wrong</p>" : "<p class='error'>❌ User not found - run db:seed</p>";
    }
    echo "</div>";

    echo "</div>"; // grid

    // Critical Tables
    echo "<div class='card'><h2>📋 Critical Tables</h2><table><tr><th>Table</th><th>Rows</th><th>Status</th><th>Notes</th></tr>";
    $checks = [
        'users' => ['min' => 1, 'note' => 'Admin accounts'],
        'branches' => ['min' => 1, 'note' => 'Jetty locations'],
        'item_rates' => ['min' => 1, 'note' => 'Ticket prices'],
        'item_categories' => ['min' => 1, 'note' => 'Item types'],
        'ferryboats' => ['min' => 1, 'note' => 'Ferry vessels'],
        'ferry_schedules' => ['min' => 1, 'note' => 'Time slots'],
        'routes' => ['min' => 1, 'note' => 'Branch connections'],
    ];
    foreach ($checks as $table => $cfg) {
        try {
            $count = \DB::table($table)->count();
            $status = $count >= $cfg['min'] ? "<span class='badge badge-success'>OK</span>" : "<span class='badge badge-danger'>EMPTY</span>";
            echo "<tr><td>$table</td><td>$count</td><td>$status</td><td>{$cfg['note']}</td></tr>";
        } catch (\Exception $e) {
            echo "<tr><td>$table</td><td colspan='3'><span class='error'>TABLE MISSING</span></td></tr>";
        }
    }
    echo "</table></div>";

    // Quick Health Score
    echo "<div class='card'><h2>🏥 Health Score</h2>";
    $score = 0;
    $total = 7;
    try {
        if (\DB::connection()->getPdo())
            $score++;
    } catch (\Exception $e) {
    }
    try {
        if (\App\Models\User::count() > 0)
            $score++;
    } catch (\Exception $e) {
    }
    try {
        if (\DB::table('branches')->count() > 0)
            $score++;
    } catch (\Exception $e) {
    }
    try {
        if (\DB::table('item_rates')->count() > 0)
            $score++;
    } catch (\Exception $e) {
    }
    try {
        if (\DB::table('ferryboats')->count() > 0)
            $score++;
    } catch (\Exception $e) {
    }
    try {
        if (\DB::table('routes')->count() > 0)
            $score++;
    } catch (\Exception $e) {
    }
    try {
        if (\DB::table('ferry_schedules')->count() > 0)
            $score++;
    } catch (\Exception $e) {
    }
    $pct = round(($score / $total) * 100);
    $color = $pct >= 80 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
    echo "<div style='font-size: 48px; font-weight: bold;' class='$color'>$pct%</div>";
    echo "<p>$score / $total checks passed</p></div>";
}

// ===== SMART LOGS =====
elseif ($mode === 'logs') {
    echo "<div class='card'><h2>📜 Intelligent Log Analysis</h2>";
    $logFile = storage_path('logs/laravel.log');

    if (!file_exists($logFile)) {
        echo "<p class='warn'>No log file found</p>";
    } else {
        $lines = file($logFile);
        $recent = array_slice($lines, -200); // Last 200 lines

        $errors = ['db' => [], 'auth' => [], 'route' => [], 'general' => []];
        $currentBlock = '';

        foreach ($recent as $line) {
            if (preg_match('/\[(\d{4}-\d{2}-\d{2}[^\]]+)\]/', $line, $m)) {
                $currentBlock = $line;
            } else {
                $currentBlock .= $line;
            }

            // Categorize
            if (stripos($currentBlock, 'SQLSTATE') !== false || stripos($currentBlock, 'PDOException') !== false) {
                if (!in_array(substr($currentBlock, 0, 100), array_map(fn($x) => substr($x, 0, 100), $errors['db']))) {
                    $errors['db'][] = $currentBlock;
                }
            } elseif (stripos($currentBlock, 'auth') !== false || stripos($currentBlock, 'login') !== false || stripos($currentBlock, 'Unauthenticated') !== false) {
                if (!in_array(substr($currentBlock, 0, 100), array_map(fn($x) => substr($x, 0, 100), $errors['auth']))) {
                    $errors['auth'][] = $currentBlock;
                }
            } elseif (stripos($currentBlock, 'Route') !== false || stripos($currentBlock, '404') !== false || stripos($currentBlock, 'NotFoundHttpException') !== false) {
                if (!in_array(substr($currentBlock, 0, 100), array_map(fn($x) => substr($x, 0, 100), $errors['route']))) {
                    $errors['route'][] = $currentBlock;
                }
            } elseif (stripos($currentBlock, 'ERROR') !== false || stripos($currentBlock, 'Exception') !== false) {
                if (!in_array(substr($currentBlock, 0, 100), array_map(fn($x) => substr($x, 0, 100), $errors['general']))) {
                    $errors['general'][] = $currentBlock;
                }
            }
        }

        // Display categorized
        $categories = [
            'db' => ['🗄️ Database Errors', 'danger'],
            'auth' => ['🔐 Auth Errors', 'warning'],
            'route' => ['🛤️ Route Errors', 'warning'],
            'general' => ['⚠️ General Errors', 'danger']
        ];

        foreach ($categories as $key => [$title, $type]) {
            $count = count($errors[$key]);
            if ($count > 0) {
                echo "<h3 style='margin: 15px 0 10px;'>$title <span class='badge badge-$type'>$count</span></h3>";
                foreach (array_slice($errors[$key], -5) as $err) { // Last 5 of each
                    echo "<div class='log-entry log-error'>" . htmlspecialchars(substr($err, 0, 500)) . "</div>";
                }
            }
        }

        if (array_sum(array_map('count', $errors)) === 0) {
            echo "<p class='ok'>✅ No recent errors detected!</p>";
        }
    }
    echo "</div>";
}

// ===== API TESTER =====
elseif ($mode === 'api-test') {
    echo "<div class='card'><h2>🔌 API Endpoint Tester</h2>";
    echo "<p style='margin-bottom:15px;'>Test critical endpoints to find which one is failing</p>";

    $endpoints = [
        ['GET', '/booking/to-branches/1', 'Customer: Get destinations for branch 1'],
        ['GET', '/ajax/item-rate-lookup?q=1&branch_id=1', 'Admin: Lookup item rate'],
        ['GET', '/booking/items?branch_id=1', 'Customer: Get items for booking'],
    ];

    echo "<table><tr><th>Method</th><th>Endpoint</th><th>Description</th><th>Action</th></tr>";
    foreach ($endpoints as $i => [$method, $url, $desc]) {
        echo "<tr><td>$method</td><td><code>$url</code></td><td>$desc</td>";
        echo "<td><a href='?mode=api-test&action=test&endpoint=$i' class='nav a'>Test</a></td></tr>";
    }
    echo "</table>";

    // Run test
    if ($action === 'test' && isset($_GET['endpoint'])) {
        $idx = (int) $_GET['endpoint'];
        if (isset($endpoints[$idx])) {
            [$method, $url, $desc] = $endpoints[$idx];
            echo "<div class='test-result'><h3>Testing: $url</h3>";

            try {
                $request = \Illuminate\Http\Request::create($url, $method);
                $response = app()->handle($request);
                $status = $response->getStatusCode();
                $content = $response->getContent();

                $class = $status === 200 ? 'test-pass' : 'test-fail';
                echo "<div class='$class' style='margin-top:10px;'>";
                echo "<strong>Status:</strong> $status<br>";
                echo "<strong>Response:</strong><pre>" . htmlspecialchars(substr($content, 0, 2000)) . "</pre>";
                echo "</div>";
            } catch (\Exception $e) {
                echo "<div class='test-fail'><strong>Exception:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
            }
            echo "</div>";
        }
    }
    echo "</div>";
}

// ===== DATA INTEGRITY =====
elseif ($mode === 'integrity') {
    echo "<div class='card'><h2>🔗 Data Integrity Checker</h2>";
    echo "<p style='margin-bottom:15px;'>Validates relationships between tables</p>";

    $issues = [];

    // Check: Do all branches have routes?
    try {
        $branchesWithoutRoutes = \DB::table('branches')
            ->leftJoin('routes', 'branches.id', '=', 'routes.branch_id')
            ->whereNull('routes.id')
            ->pluck('branches.branch_name');
        if ($branchesWithoutRoutes->count() > 0) {
            $issues[] = ['warn', 'Branches without routes: ' . $branchesWithoutRoutes->implode(', ')];
        }
    } catch (\Exception $e) {
        $issues[] = ['error', 'Routes table error: ' . $e->getMessage()];
    }

    // Check: Do all branches have ferry boats?
    try {
        $branchesWithoutBoats = \DB::table('branches')
            ->leftJoin('ferryboats', 'branches.id', '=', 'ferryboats.branch_id')
            ->whereNull('ferryboats.id')
            ->pluck('branches.branch_name');
        if ($branchesWithoutBoats->count() > 0) {
            $issues[] = ['warn', 'Branches without ferryboats: ' . $branchesWithoutBoats->implode(', ')];
        }
    } catch (\Exception $e) {
        $issues[] = ['error', 'Ferryboats table error: ' . $e->getMessage()];
    }

    // Check: Item rates with valid starting_date
    try {
        $futureRates = \DB::table('item_rates')->where('starting_date', '>', now())->count();
        $activeRates = \DB::table('item_rates')
            ->where('starting_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('ending_date')->orWhere('ending_date', '>=', now());
            })
            ->count();
        if ($activeRates === 0) {
            $issues[] = ['error', "No active item rates! All rates have future start dates or expired."];
        } else {
            $issues[] = ['ok', "Active item rates: $activeRates (Future: $futureRates)"];
        }
    } catch (\Exception $e) {
        $issues[] = ['error', 'Item rates error: ' . $e->getMessage()];
    }

    // Check: Ferry schedules exist
    try {
        $schedulesPerBranch = \DB::table('ferry_schedules')
            ->selectRaw('branch_id, COUNT(*) as cnt')
            ->groupBy('branch_id')
            ->pluck('cnt', 'branch_id');
        $branchesWithSchedules = $schedulesPerBranch->count();
        $totalBranches = \DB::table('branches')->count();
        if ($branchesWithSchedules < $totalBranches) {
            $issues[] = ['warn', "Only $branchesWithSchedules of $totalBranches branches have schedules"];
        } else {
            $issues[] = ['ok', "All $totalBranches branches have ferry schedules"];
        }
    } catch (\Exception $e) {
        $issues[] = ['error', 'Schedules error: ' . $e->getMessage()];
    }

    echo "<table><tr><th>Status</th><th>Check Result</th></tr>";
    foreach ($issues as [$status, $msg]) {
        $badge = $status === 'ok' ? 'success' : ($status === 'warn' ? 'warning' : 'danger');
        $icon = $status === 'ok' ? '✅' : ($status === 'warn' ? '⚠️' : '❌');
        echo "<tr><td><span class='badge badge-$badge'>$icon</span></td><td>$msg</td></tr>";
    }
    echo "</table></div>";
}

// ===== ROUTES DEBUG =====
elseif ($mode === 'routes-check') {
    echo "<div class='card'><h2>🛤️ Routes & Destinations Debug</h2>";

    // Show all routes data
    echo "<h3 style='margin:15px 0 10px;'>Routes Table Contents</h3>";
    try {
        $routes = \DB::table('routes')
            ->join('branches', 'routes.branch_id', '=', 'branches.id')
            ->select('routes.route_id', 'routes.branch_id', 'branches.branch_name')
            ->orderBy('routes.route_id')
            ->get();

        if ($routes->isEmpty()) {
            echo "<p class='error'>❌ Routes table is EMPTY! Run: php artisan db:seed --force</p>";
        } else {
            echo "<table><tr><th>Route ID</th><th>Branch ID</th><th>Branch Name</th></tr>";
            foreach ($routes as $r) {
                echo "<tr><td>{$r->route_id}</td><td>{$r->branch_id}</td><td>{$r->branch_name}</td></tr>";
            }
            echo "</table>";
        }
    } catch (\Exception $e) {
        echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    // Test getToBranches logic
    echo "<h3 style='margin:15px 0 10px;'>Test: Get Destinations for Branch</h3>";
    $testBranchId = $_GET['test_branch'] ?? 1;
    echo "<form method='get' style='margin-bottom:15px;'><input type='hidden' name='mode' value='routes-check'>";
    echo "<label>Branch ID: <input type='number' name='test_branch' value='$testBranchId' style='width:80px;'></label> ";
    echo "<button type='submit'>Test</button></form>";

    try {
        $routeIds = \DB::table('routes')->where('branch_id', $testBranchId)->pluck('route_id');
        echo "<p>Route IDs for branch $testBranchId: " . ($routeIds->isEmpty() ? '<span class="error">NONE</span>' : $routeIds->implode(', ')) . "</p>";

        if ($routeIds->isNotEmpty()) {
            $toBranchIds = \DB::table('routes')
                ->whereIn('route_id', $routeIds)
                ->where('branch_id', '!=', $testBranchId)
                ->distinct()
                ->pluck('branch_id');

            $destinations = \App\Models\Branch::whereIn('id', $toBranchIds)->get(['id', 'branch_name']);
            echo "<p>Destinations: ";
            if ($destinations->isEmpty()) {
                echo "<span class='error'>NONE FOUND</span>";
            } else {
                foreach ($destinations as $d) {
                    echo "<span class='badge badge-success'>{$d->branch_name}</span> ";
                }
            }
            echo "</p>";
        }
    } catch (\Exception $e) {
        echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    echo "</div>";
}

// ===== LIVE LOGS =====
elseif ($mode === 'live-logs') {
    $autoRefresh = isset($_GET['auto']) && $_GET['auto'] === '1';

    echo "<div class='card'>";
    echo "<h2>🔴 Live Log Stream</h2>";
    echo "<div style='margin-bottom:15px;'>";
    echo "<a href='?mode=live-logs&auto=1' class='nav a' style='" . ($autoRefresh ? "background:var(--success);color:#000;" : "") . "'>▶ Auto-Refresh ON</a> ";
    echo "<a href='?mode=live-logs' class='nav a' style='" . (!$autoRefresh ? "background:var(--danger);color:#fff;" : "") . "'>⏸ Auto-Refresh OFF</a> ";
    echo "<a href='?mode=live-logs&clear=1' class='nav a' style='background:var(--warning);color:#000;'>🗑️ Clear Logs</a>";
    echo "</div>";

    // Clear logs if requested
    if (isset($_GET['clear']) && $_GET['clear'] === '1') {
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
            echo "<p class='ok'>✅ Log file cleared!</p>";
        }
    }

    // Auto-refresh script
    if ($autoRefresh) {
        echo "<script>setTimeout(function(){ location.reload(); }, 3000);</script>";
        echo "<p style='color:var(--success);'>🔄 Refreshing every 3 seconds...</p>";
    }

    // Show last 50 log lines in real-time format
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $lines = file($logFile);
        $recent = array_slice($lines, -50);

        echo "<div style='background:#0d1117;border:1px solid var(--border);border-radius:6px;padding:10px;font-family:monospace;font-size:11px;max-height:500px;overflow-y:auto;'>";
        foreach ($recent as $line) {
            $line = htmlspecialchars(trim($line));
            if (stripos($line, 'ERROR') !== false || stripos($line, 'Exception') !== false) {
                echo "<div style='color:var(--danger);'>$line</div>";
            } elseif (stripos($line, 'WARNING') !== false || stripos($line, 'WARN') !== false) {
                echo "<div style='color:var(--warning);'>$line</div>";
            } elseif (stripos($line, 'INFO') !== false) {
                echo "<div style='color:var(--accent);'>$line</div>";
            } else {
                echo "<div style='color:var(--text);opacity:0.7;'>$line</div>";
            }
        }
        echo "</div>";

        // Stats
        $fileSize = filesize($logFile);
        $lineCount = count($lines);
        echo "<p style='margin-top:10px;font-size:12px;opacity:0.7;'>📊 Total lines: $lineCount | File size: " . round($fileSize / 1024, 1) . " KB</p>";
    } else {
        echo "<p class='warn'>No log file found at: $logFile</p>";
    }

    echo "</div>";
}

// ===== CACHE TOOLS =====
elseif ($mode === 'cache-tools') {
    echo "<div class='card'><h2>🧹 Cache Management Tools</h2>";

    // Git Info - Add safe.directory first to avoid ownership errors
    $basePath = base_path();
    shell_exec('git config --global --add safe.directory ' . $basePath . ' 2>&1');

    echo "<h3 style='margin:15px 0 10px;'>📦 Git Status</h3>";
    $gitBranch = trim(shell_exec('cd ' . $basePath . ' && git branch --show-current 2>&1') ?? 'unknown');
    $gitStatus = trim(shell_exec('cd ' . $basePath . ' && git status --porcelain 2>&1') ?? '');
    $gitLog = trim(shell_exec('cd ' . $basePath . ' && git log -1 --oneline 2>&1') ?? '');

    echo "<table>";
    echo "<tr><td><strong>Branch:</strong></td><td>$gitBranch</td></tr>";
    echo "<tr><td><strong>Last Commit:</strong></td><td>$gitLog</td></tr>";
    echo "<tr><td><strong>Uncommitted Changes:</strong></td><td>" . ($gitStatus ? "<span class='error'>YES - " . substr(htmlspecialchars($gitStatus), 0, 100) . "</span>" : "<span class='ok'>Clean</span>") . "</td></tr>";
    echo "</table>";

    // View File Version Check
    echo "<h3 style='margin:15px 0 10px;'>📄 View Version Check</h3>";
    $viewFile = resource_path('views/tickets/create.blade.php');
    if (file_exists($viewFile)) {
        $viewContent = file_get_contents($viewFile);
        if (preg_match('/VIEW VERSION: ([^\-\}]+)/', $viewContent, $m)) {
            echo "<p class='ok'>✅ Ticket Entry View Version: <strong>{$m[1]}</strong></p>";
        } else {
            echo "<p class='warn'>⚠️ No version marker found in view file</p>";
        }
        echo "<p style='font-size:12px;opacity:0.7;'>File modified: " . date('Y-m-d H:i:s', filemtime($viewFile)) . "</p>";
    } else {
        echo "<p class='error'>❌ View file not found!</p>";
    }

    // Compiled Views Count
    $compiledViewsDir = storage_path('framework/views');
    $compiledCount = is_dir($compiledViewsDir) ? count(glob($compiledViewsDir . '/*.php')) : 0;
    echo "<p>Compiled views in cache: <strong>$compiledCount</strong></p>";

    // Action Buttons
    echo "<h3 style='margin:15px 0 10px;'>🔧 Actions</h3>";
    echo "<div style='display:flex;gap:10px;flex-wrap:wrap;margin-bottom:10px;'>";
    echo "<a href='?mode=cache-tools&action=git-pull' style='background:var(--accent);color:#000;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:600;'>📥 Git Pull</a>";
    echo "<a href='?mode=cache-tools&action=migrate' style='background:var(--success);color:#000;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:600;'>🗄️ Run Migrations</a>";
    echo "<a href='?mode=cache-tools&action=clear-all' style='background:var(--danger);color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:600;'>💥 Clear ALL Caches</a>";
    echo "</div>";
    echo "<div style='display:flex;gap:10px;flex-wrap:wrap;'>";
    echo "<a href='?mode=cache-tools&action=clear-views' style='background:var(--warning);color:#000;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:600;'>🗑️ Delete Compiled Views</a>";
    echo "<a href='?mode=cache-tools&action=seed' style='background:#9333ea;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:600;'>🌱 Run Seeders</a>";
    echo "</div>";

    // Handle Actions
    if ($action === 'git-pull') {
        echo "<div class='test-result test-pass' style='margin-top:15px;'>";
        echo "<h4>Git Pull Result:</h4><pre>";
        // Add safe.directory first
        shell_exec('git config --global --add safe.directory ' . base_path());
        $output = shell_exec('cd ' . base_path() . ' && git pull origin master 2>&1');
        echo htmlspecialchars($output);
        echo "</pre></div>";
    }

    if ($action === 'clear-views') {
        echo "<div class='test-result test-pass' style='margin-top:15px;'>";
        $count = 0;
        $files = glob(storage_path('framework/views') . '/*.php');
        foreach ($files as $file) {
            if (unlink($file))
                $count++;
        }
        echo "<h4>✅ Deleted $count compiled view files</h4>";
        echo "</div>";
    }

    if ($action === 'clear-all') {
        echo "<div class='test-result test-pass' style='margin-top:15px;'>";
        echo "<h4>Cache Clear Results:</h4><pre>";

        // Delete compiled views
        $files = glob(storage_path('framework/views') . '/*.php');
        foreach ($files as $file) {
            @unlink($file);
        }
        echo "✅ Compiled views deleted\n";

        // Delete cache files
        $cacheFiles = glob(storage_path('framework/cache/data') . '/*');
        foreach ($cacheFiles as $file) {
            @unlink($file);
        }
        echo "✅ Cache files deleted\n";

        // Try artisan commands
        $commands = ['config:clear', 'view:clear', 'cache:clear', 'route:clear'];
        foreach ($commands as $cmd) {
            $result = shell_exec('cd ' . base_path() . ' && php artisan ' . $cmd . ' 2>&1');
            echo "artisan $cmd: " . trim($result) . "\n";
        }

        echo "</pre></div>";
    }

    if ($action === 'migrate') {
        echo "<div class='test-result test-pass' style='margin-top:15px;'>";
        echo "<h4>Migration Result:</h4><pre>";
        $output = shell_exec('cd ' . base_path() . ' && php artisan migrate --force 2>&1');
        echo htmlspecialchars($output);
        echo "</pre></div>";
    }

    if ($action === 'seed') {
        echo "<div class='test-result test-pass' style='margin-top:15px;'>";
        echo "<h4>Seeder Result:</h4><pre>";
        $output = shell_exec('cd ' . base_path() . ' && php artisan db:seed --force 2>&1');
        echo htmlspecialchars($output);
        echo "</pre></div>";
    }

    echo "</div>";
}
