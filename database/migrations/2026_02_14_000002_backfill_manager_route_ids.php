<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Backfill route_id for existing manager accounts.
 * Looks up each manager's branch_id, finds the route_id
 * that contains that branch, and sets it.
 */
return new class extends Migration {
    public function up(): void
    {
        // Get all managers (role_id = 3) that don't have a route_id set
        $managers = DB::table('users')
            ->where('role_id', 3)
            ->whereNull('route_id')
            ->get();

        foreach ($managers as $manager) {
            if (!$manager->branch_id)
                continue;

            // Find the route_id that contains this branch
            $routeEntry = DB::table('routes')
                ->where('branch_id', $manager->branch_id)
                ->first();

            if ($routeEntry) {
                DB::table('users')
                    ->where('id', $manager->id)
                    ->update(['route_id' => $routeEntry->route_id]);
            }
        }
    }

    public function down(): void
    {
        // Clear route_id for all managers
        DB::table('users')
            ->where('role_id', 3)
            ->update(['route_id' => null]);
    }
};
