<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = [
        'route_id',
        'branch_id',
        'sequence',
    ];

    /**
     * Get the branch for this route entry
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    /**
     * Get all branches for a specific route_id
     */
    public static function getBranchesForRoute($routeId)
    {
        return static::where('route_id', $routeId)
            ->with('branch')
            ->orderBy('sequence')
            ->get()
            ->pluck('branch');
    }

    /**
     * Get destination branches for a given branch
     * (finds all other branches on the same route)
     */
    public static function getDestinationsForBranch($branchId)
    {
        // Find route_ids that include this branch
        $routeIds = static::where('branch_id', $branchId)->pluck('route_id');

        // Get all other branches on those routes
        // IMPORTANT: ->values() after ->unique() resets keys to sequential 0,1,2...
        // Without it, PHP serializes as {"0": x, "3": y} (object) instead of [x, y] (array)
        // which causes React Error #310 on the frontend.
        return static::whereIn('route_id', $routeIds)
            ->where('branch_id', '!=', $branchId)
            ->with('branch:id,branch_name')
            ->get()
            ->pluck('branch')
            ->filter()  // Remove any null branches
            ->unique('id')
            ->values()  // ← CRITICAL: reset to sequential keys for JSON array serialization
            ->map(fn($b) => ['id' => $b->id, 'branch_name' => $b->branch_name]);
    }
}
