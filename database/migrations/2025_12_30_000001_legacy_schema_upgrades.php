<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Schema upgrades to match legacy system's robust patterns:
     * 1. Composite ticket IDs (branch_id + ticket_date + ticket_no)
     * 2. Vehicles master table
     * 3. Active flags (soft delete) on all master tables
     * 4. Additional fields from legacy system
     */
    public function up(): void
    {
        // =====================================================
        // 1. CREATE VEHICLES MASTER TABLE
        // =====================================================
        if (!Schema::hasTable('vehicles')) {
            Schema::create('vehicles', function (Blueprint $table) {
                $table->id();
                $table->string('vehicle_name', 30);
                $table->unsignedBigInteger('item_category_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->index('vehicle_name');
                $table->index('item_category_id');
            });
        }

        // =====================================================
        // 2. ADD ACTIVE FLAGS TO ALL MASTER TABLES
        // =====================================================

        // Branches
        if (!Schema::hasColumn('branches', 'is_active')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('user_id');
                $table->string('branch_address', 150)->nullable()->after('branch_name');
                $table->string('branch_phone', 30)->nullable()->after('branch_address');
                $table->unsignedInteger('dest_branch_id')->nullable()->after('branch_phone');
                $table->string('dest_branch_name', 30)->nullable()->after('dest_branch_id');
                $table->unsignedBigInteger('ferry_boat_id')->nullable()->after('dest_branch_name');
                $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            });
        }

        // Ferryboats
        if (!Schema::hasColumn('ferryboats', 'is_active')) {
            Schema::table('ferryboats', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('user_id');
                $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            });
        }

        // Ferry Schedules
        if (!Schema::hasColumn('ferry_schedules', 'is_active')) {
            Schema::table('ferry_schedules', function (Blueprint $table) {
                $table->time('schedule_time')->nullable()->after('minute');
                $table->boolean('is_active')->default(true)->after('schedule_time');
                $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            });
        }

        // Item Categories
        if (!Schema::hasColumn('item_categories', 'is_active')) {
            Schema::table('item_categories', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('location_id');
                $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            });
        }

        // Item Rates - add legacy fields
        if (!Schema::hasColumn('item_rates', 'is_active')) {
            Schema::table('item_rates', function (Blueprint $table) {
                $table->string('item_short_name', 50)->nullable()->after('item_name');
                $table->string('item_name_regional', 200)->nullable()->after('item_short_name');
                $table->decimal('item_surcharge_pct', 5, 2)->default(0)->after('item_lavy');
                $table->decimal('levy_surcharge_pct', 5, 2)->default(0)->after('item_surcharge_pct');
                $table->decimal('space_units', 10, 2)->default(1)->after('levy_surcharge_pct');
                $table->boolean('is_fixed_rate')->default(false)->after('space_units');
                $table->boolean('is_vehicle')->default(false)->after('is_fixed_rate');
                $table->boolean('is_active')->default(true)->after('is_vehicle');
                $table->unsignedBigInteger('created_by')->nullable()->after('user_id');
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            });
        }

        // Guest Categories
        if (!Schema::hasColumn('guest_categories', 'is_active')) {
            Schema::table('guest_categories', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('user_id');
                $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            });
        }

        // Guests - add legacy fields
        if (!Schema::hasColumn('guests', 'is_active')) {
            Schema::table('guests', function (Blueprint $table) {
                $table->string('address', 150)->nullable()->after('name');
                $table->string('phone', 30)->nullable()->after('address');
                $table->string('designation', 30)->nullable()->after('phone');
                $table->string('remark', 50)->nullable()->after('designation');
                $table->boolean('is_active')->default(true)->after('branch_id');
                $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            });
        }

        // =====================================================
        // 3. UPGRADE TICKETS TABLE - COMPOSITE KEY PATTERN
        // =====================================================
        if (!Schema::hasColumn('tickets', 'ticket_date')) {
            Schema::table('tickets', function (Blueprint $table) {
                // Add legacy ticket identification fields
                $table->date('ticket_date')->nullable()->after('id');
                $table->unsignedBigInteger('ticket_no')->nullable()->after('ticket_date');

                // Add missing legacy fields
                $table->decimal('total_levy', 14, 2)->default(0)->after('total_amount');
                $table->decimal('total_surcharge', 14, 2)->default(0)->after('total_levy');
                $table->decimal('net_amount', 14, 2)->default(0)->after('total_surcharge');
                $table->decimal('received_amount', 14, 2)->nullable()->after('net_amount');
                $table->decimal('balance_amount', 14, 2)->nullable()->after('received_amount');
                $table->decimal('pending_amount', 14, 2)->nullable()->after('balance_amount');

                // Destination info (routes)
                $table->unsignedInteger('dest_branch_id')->nullable()->after('branch_id');
                $table->string('dest_branch_name', 30)->nullable()->after('dest_branch_id');

                // Additional info
                $table->integer('no_of_units')->nullable()->after('pending_amount');
                $table->unsignedBigInteger('customer_id')->nullable()->after('guest_id');

                // Audit fields
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();

                // Create unique index for legacy ticket identification
                $table->unique(['branch_id', 'ticket_date', 'ticket_no'], 'tickets_legacy_unique');
            });
        }

        // =====================================================
        // 4. UPGRADE TICKET_LINES TABLE
        // =====================================================
        if (!Schema::hasColumn('ticket_lines', 'ticket_date')) {
            Schema::table('ticket_lines', function (Blueprint $table) {
                // Add legacy composite key reference
                $table->unsignedInteger('branch_id')->nullable()->after('ticket_id');
                $table->date('ticket_date')->nullable()->after('branch_id');
                $table->unsignedBigInteger('ticket_no')->nullable()->after('ticket_date');

                // Add missing fields from legacy
                $table->decimal('surcharge_pct', 5, 2)->default(0)->after('levy');
                $table->decimal('levy_surcharge_pct', 5, 2)->default(0)->after('surcharge_pct');
                $table->unsignedBigInteger('vehicle_id')->nullable()->after('vehicle_no');

                // Unit tracking
                $table->integer('unit_no')->nullable()->after('vehicle_id');
                $table->integer('unit_sr_no')->nullable()->after('unit_no');

                // Audit
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();

                // Index for legacy lookup
                $table->index(['branch_id', 'ticket_date', 'ticket_no'], 'ticket_lines_legacy_idx');
            });
        }

        // =====================================================
        // 5. CREATE PAYMENT MODES TABLE (from legacy)
        // =====================================================
        if (!Schema::hasTable('payment_modes')) {
            Schema::create('payment_modes', function (Blueprint $table) {
                $table->id();
                $table->integer('payment_mode_id')->unique();
                $table->string('payment_mode_name', 50);
                $table->char('is_active', 1)->default('Y');
                $table->timestamps();
            });

            // Insert default payment modes
            DB::table('payment_modes')->insert([
                ['payment_mode_id' => 1, 'payment_mode_name' => 'Cash', 'is_active' => 'Y', 'created_at' => now(), 'updated_at' => now()],
                ['payment_mode_id' => 2, 'payment_mode_name' => 'Card', 'is_active' => 'Y', 'created_at' => now(), 'updated_at' => now()],
                ['payment_mode_id' => 3, 'payment_mode_name' => 'UPI', 'is_active' => 'Y', 'created_at' => now(), 'updated_at' => now()],
                ['payment_mode_id' => 4, 'payment_mode_name' => 'Credit', 'is_active' => 'Y', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // =====================================================
        // 6. CREATE LEGACY ID MAPPING TABLES (for migration tracking)
        // =====================================================
        if (!Schema::hasTable('legacy_id_mappings')) {
            Schema::create('legacy_id_mappings', function (Blueprint $table) {
                $table->id();
                $table->string('table_name', 50);
                $table->string('legacy_id', 50);
                $table->unsignedBigInteger('new_id');
                $table->timestamps();

                $table->unique(['table_name', 'legacy_id']);
                $table->index(['table_name', 'new_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new tables
        Schema::dropIfExists('legacy_id_mappings');
        Schema::dropIfExists('payment_modes');
        Schema::dropIfExists('vehicles');

        // Remove added columns from ticket_lines
        Schema::table('ticket_lines', function (Blueprint $table) {
            $table->dropIndex('ticket_lines_legacy_idx');
            $table->dropColumn([
                'branch_id',
                'ticket_date',
                'ticket_no',
                'surcharge_pct',
                'levy_surcharge_pct',
                'vehicle_id',
                'unit_no',
                'unit_sr_no',
                'created_by',
                'updated_by'
            ]);
        });

        // Remove added columns from tickets
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropUnique('tickets_legacy_unique');
            $table->dropColumn([
                'ticket_date',
                'ticket_no',
                'total_levy',
                'total_surcharge',
                'net_amount',
                'received_amount',
                'balance_amount',
                'pending_amount',
                'dest_branch_id',
                'dest_branch_name',
                'no_of_units',
                'customer_id',
                'created_by',
                'updated_by'
            ]);
        });

        // Remove is_active and other columns from master tables
        $tables = ['branches', 'ferryboats', 'ferry_schedules', 'item_categories', 'item_rates', 'guest_categories', 'guests'];
        foreach ($tables as $tableName) {
            if (Schema::hasColumn($tableName, 'is_active')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn(['is_active', 'created_by', 'updated_by']);
                });
            }
        }
    }
};
