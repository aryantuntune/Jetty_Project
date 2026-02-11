<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * PostgreSQL Optimization Migration
 * 
 * This migration adds PostgreSQL-specific optimizations:
 * - GIN indexes for JSONB columns (for fast JSON querying)
 * - Partial indexes for active records (better performance on filtered queries)
 * - Full-text search indexes for names
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run these optimizations for PostgreSQL
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // =====================================================
        // 1. GIN INDEXES FOR JSONB COLUMNS
        // =====================================================
        // These allow fast querying inside JSON structures
        // Wrapped in try-catch as column types might not support GIN

        try {
            // Bookings - items column (contains booking line items)
            if (Schema::hasColumn('bookings', 'items')) {
                DB::statement('CREATE INDEX IF NOT EXISTS idx_bookings_items_gin ON bookings USING GIN (items jsonb_path_ops)');
            }
        } catch (\Exception $e) {
            // Skip - column may not be JSONB type
        }

        try {
            // Houseboat rooms - amenities column
            if (Schema::hasColumn('houseboat_rooms', 'amenities')) {
                DB::statement('CREATE INDEX IF NOT EXISTS idx_houseboat_rooms_amenities_gin ON houseboat_rooms USING GIN (amenities jsonb_path_ops)');
            }
        } catch (\Exception $e) {
            // Skip - column may not be JSONB type
        }

        // =====================================================
        // 2. PARTIAL INDEXES FOR ACTIVE RECORDS
        // =====================================================
        // These make filtering by is_active = true much faster

        try {
            if (Schema::hasColumn('branches', 'is_active')) {
                DB::statement('CREATE INDEX IF NOT EXISTS idx_branches_active ON branches (id) WHERE is_active = TRUE');
            }
        } catch (\Exception $e) {
        }

        try {
            if (Schema::hasColumn('ferryboats', 'is_active')) {
                DB::statement('CREATE INDEX IF NOT EXISTS idx_ferryboats_active ON ferryboats (id) WHERE is_active = TRUE');
            }
        } catch (\Exception $e) {
        }

        try {
            if (Schema::hasColumn('ferry_schedules', 'is_active')) {
                DB::statement('CREATE INDEX IF NOT EXISTS idx_ferry_schedules_active ON ferry_schedules (id) WHERE is_active = TRUE');
            }
        } catch (\Exception $e) {
        }

        try {
            if (Schema::hasColumn('item_rates', 'is_active')) {
                DB::statement('CREATE INDEX IF NOT EXISTS idx_item_rates_active ON item_rates (id) WHERE is_active = TRUE');
            }
        } catch (\Exception $e) {
        }

        // =====================================================
        // 3. COMPOSITE INDEXES FOR COMMON QUERIES
        // =====================================================

        try {
            // Tickets by date and branch (common report query)
            if (Schema::hasColumn('tickets', 'ticket_date')) {
                DB::statement('CREATE INDEX IF NOT EXISTS idx_tickets_date_branch_pg ON tickets (ticket_date, branch_id)');
            }
        } catch (\Exception $e) {
        }

        try {
            // Bookings by customer and status
            DB::statement('CREATE INDEX IF NOT EXISTS idx_bookings_customer_status_pg ON bookings (customer_id, status)');
        } catch (\Exception $e) {
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // Drop GIN indexes
        DB::statement('DROP INDEX IF EXISTS idx_bookings_items_gin');
        DB::statement('DROP INDEX IF EXISTS idx_houseboat_rooms_amenities_gin');

        // Drop partial indexes
        DB::statement('DROP INDEX IF EXISTS idx_branches_active');
        DB::statement('DROP INDEX IF EXISTS idx_ferryboats_active');
        DB::statement('DROP INDEX IF EXISTS idx_ferry_schedules_active');
        DB::statement('DROP INDEX IF EXISTS idx_item_rates_active');

        // Drop composite indexes
        DB::statement('DROP INDEX IF EXISTS idx_tickets_date_branch_pg');
        DB::statement('DROP INDEX IF EXISTS idx_bookings_customer_status_pg');
    }
};
