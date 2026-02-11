<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Bookings table indexes
        if (!$this->indexExists('bookings', 'idx_bookings_customer')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->index('customer_id', 'idx_bookings_customer');
            });
        }
        if (!$this->indexExists('bookings', 'idx_bookings_status')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->index('status', 'idx_bookings_status');
            });
        }
        if (!$this->indexExists('bookings', 'idx_bookings_date')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->index('booking_date', 'idx_bookings_date');
            });
        }
        if (!$this->indexExists('bookings', 'idx_bookings_customer_status')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->index(['customer_id', 'status'], 'idx_bookings_customer_status');
            });
        }

        // Tickets table indexes
        if (!$this->indexExists('tickets', 'idx_tickets_branch')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->index('branch_id', 'idx_tickets_branch');
            });
        }
        if (!$this->indexExists('tickets', 'idx_tickets_ferry')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->index('ferry_boat_id', 'idx_tickets_ferry');
            });
        }
        if (!$this->indexExists('tickets', 'idx_tickets_created')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->index('created_at', 'idx_tickets_created');
            });
        }
        if (!$this->indexExists('tickets', 'idx_tickets_branch_date')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->index(['branch_id', 'created_at'], 'idx_tickets_branch_date');
            });
        }
        if (Schema::hasColumn('tickets', 'user_id') && !$this->indexExists('tickets', 'idx_tickets_user')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->index('user_id', 'idx_tickets_user');
            });
        }

        // Ticket lines table indexes
        if (!$this->indexExists('ticket_lines', 'idx_ticket_lines_ticket')) {
            Schema::table('ticket_lines', function (Blueprint $table) {
                $table->index('ticket_id', 'idx_ticket_lines_ticket');
            });
        }
        if (!$this->indexExists('ticket_lines', 'idx_ticket_lines_vehicle_no')) {
            Schema::table('ticket_lines', function (Blueprint $table) {
                $table->index('vehicle_no', 'idx_ticket_lines_vehicle_no');
            });
        }

        // Item rates table indexes
        if (!$this->indexExists('item_rates', 'idx_item_rates_branch')) {
            Schema::table('item_rates', function (Blueprint $table) {
                $table->index('branch_id', 'idx_item_rates_branch');
            });
        }
        if (!$this->indexExists('item_rates', 'idx_item_rates_branch_dates')) {
            Schema::table('item_rates', function (Blueprint $table) {
                $table->index(['branch_id', 'starting_date', 'ending_date'], 'idx_item_rates_branch_dates');
            });
        }

        // Personal access tokens indexes (CRITICAL for auth performance)
        // Token lookup is on EVERY API request - this is the most critical index
        if (!$this->indexExists('personal_access_tokens', 'idx_tokens_token')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->index('token', 'idx_tokens_token');
            });
        }

        if (!$this->indexExists('personal_access_tokens', 'idx_tokens_expires')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->index('expires_at', 'idx_tokens_expires');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_customer');
            $table->dropIndex('idx_bookings_status');
            $table->dropIndex('idx_bookings_date');
            $table->dropIndex('idx_bookings_customer_status');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('idx_tickets_branch');
            $table->dropIndex('idx_tickets_ferry');
            $table->dropIndex('idx_tickets_created');
            $table->dropIndex('idx_tickets_branch_date');
            if (Schema::hasColumn('tickets', 'user_id')) {
                $table->dropIndex('idx_tickets_user');
            }
        });

        Schema::table('ticket_lines', function (Blueprint $table) {
            $table->dropIndex('idx_ticket_lines_ticket');
            $table->dropIndex('idx_ticket_lines_vehicle_no');
        });

        Schema::table('item_rates', function (Blueprint $table) {
            $table->dropIndex('idx_item_rates_branch');
            $table->dropIndex('idx_item_rates_branch_dates');
        });

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            if ($this->indexExists('personal_access_tokens', 'idx_tokens_token')) {
                $table->dropIndex('idx_tokens_token');
            }
            $table->dropIndex('idx_tokens_expires');
        });
    }

    /**
     * Check if index exists (PostgreSQL and MySQL compatible)
     */
    private function indexExists($table, $index): bool
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL uses pg_indexes catalog
            $indexes = DB::select(
                "SELECT indexname FROM pg_indexes WHERE tablename = ? AND indexname = ?",
                [$table, $index]
            );
        } else {
            // MySQL uses SHOW INDEX
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);
        }

        return count($indexes) > 0;
    }
};
