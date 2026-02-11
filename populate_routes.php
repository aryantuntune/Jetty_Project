<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Current Branches ===\n";
$branches = DB::table('branches')->select('id', 'branch_name', 'dest_branch_id', 'dest_branch_name')->get();
foreach ($branches as $b) {
    echo "{$b->id}: {$b->branch_name} -> dest: " . ($b->dest_branch_name ?? 'NULL') . "\n";
}

echo "\n=== Current Routes ===\n";
$routes = DB::table('routes')
    ->join('branches', 'routes.branch_id', '=', 'branches.id')
    ->select('routes.route_id', 'branches.id as branch_id', 'branches.branch_name', 'routes.sequence')
    ->orderBy('routes.route_id')
    ->orderBy('routes.sequence')
    ->get();

$groupedRoutes = [];
foreach ($routes as $r) {
    $groupedRoutes[$r->route_id][] = $r->branch_name;
}

foreach ($groupedRoutes as $routeId => $branchNames) {
    echo "Route {$routeId}: " . implode(' <-> ', $branchNames) . "\n";
}

echo "\n=== Populating destination branches ===\n";

// For each route, set destination branches
foreach ($groupedRoutes as $routeId => $branchNames) {
    if (count($branchNames) >= 2) {
        // Get the branch IDs for this route
        $routeBranches = DB::table('routes')
            ->where('route_id', $routeId)
            ->join('branches', 'routes.branch_id', '=', 'branches.id')
            ->orderBy('routes.sequence')
            ->select('branches.id', 'branches.branch_name')
            ->get();

        if (count($routeBranches) >= 2) {
            $firstBranch = $routeBranches[0];
            $secondBranch = $routeBranches[1];

            // Update first branch with second as destination
            DB::table('branches')
                ->where('id', $firstBranch->id)
                ->update([
                        'dest_branch_id' => $secondBranch->id,
                        'dest_branch_name' => $secondBranch->branch_name,
                        'updated_at' => now(),
                    ]);

            // Update second branch with first as destination
            DB::table('branches')
                ->where('id', $secondBranch->id)
                ->update([
                        'dest_branch_id' => $firstBranch->id,
                        'dest_branch_name' => $firstBranch->branch_name,
                        'updated_at' => now(),
                    ]);

            echo "Route $routeId: Set {$firstBranch->branch_name} <-> {$secondBranch->branch_name}\n";
        }
    }
}

echo "\n=== Updated Branches ===\n";
$branches = DB::table('branches')->select('id', 'branch_name', 'dest_branch_id', 'dest_branch_name')->get();
foreach ($branches as $b) {
    echo "{$b->id}: {$b->branch_name} -> dest: " . ($b->dest_branch_name ?? 'NULL') . "\n";
}

echo "\nDone!\n";
