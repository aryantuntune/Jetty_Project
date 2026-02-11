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
        // NOTE: Tickets table foreign keys skipped due to type incompatibility
        // tickets.branch_id is integer(), branches.id is bigInteger()
        // Fixing this requires ALTER COLUMN which risks data loss in production

        // Bookings foreign keys - wrapped in try-catch for type mismatches
        if (!$this->foreignKeyExists('bookings', 'fk_bookings_customer')) {
            try {
                Schema::table('bookings', function (Blueprint $table) {
                    $table->foreign('customer_id', 'fk_bookings_customer')
                        ->references('id')->on('customers')
                        ->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Skip - type mismatch
            }
        }

        if (!$this->foreignKeyExists('bookings', 'fk_bookings_ferry')) {
            try {
                Schema::table('bookings', function (Blueprint $table) {
                    $table->foreign('ferry_id', 'fk_bookings_ferry')
                        ->references('id')->on('ferryboats')
                        ->onDelete('restrict');
                });
            } catch (\Exception $e) {
                // Skip - type mismatch
            }
        }

        if (!$this->foreignKeyExists('bookings', 'fk_bookings_from_branch')) {
            try {
                Schema::table('bookings', function (Blueprint $table) {
                    $table->foreign('from_branch', 'fk_bookings_from_branch')
                        ->references('id')->on('branches')
                        ->onDelete('restrict');
                });
            } catch (\Exception $e) {
                // Skip - type mismatch
            }
        }

        if (!$this->foreignKeyExists('bookings', 'fk_bookings_to_branch')) {
            try {
                Schema::table('bookings', function (Blueprint $table) {
                    $table->foreign('to_branch', 'fk_bookings_to_branch')
                        ->references('id')->on('branches')
                        ->onDelete('restrict');
                });
            } catch (\Exception $e) {
                // Skip - type mismatch
            }
        }

        // SKIP tickets foreign keys - column types incompatible

        // Ticket lines foreign keys
        if (!$this->foreignKeyExists('ticket_lines', 'fk_ticket_lines_ticket')) {
            try {
                Schema::table('ticket_lines', function (Blueprint $table) {
                    $table->foreign('ticket_id', 'fk_ticket_lines_ticket')
                        ->references('id')->on('tickets')
                        ->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Skip - type mismatch
            }
        }

        if (Schema::hasColumn('ticket_lines', 'user_id') && !$this->foreignKeyExists('ticket_lines', 'fk_ticket_lines_user')) {
            try {
                Schema::table('ticket_lines', function (Blueprint $table) {
                    $table->foreign('user_id', 'fk_ticket_lines_user')
                        ->references('id')->on('users')
                        ->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Skip - type mismatch
            }
        }

        // Item rates foreign keys
        if (!$this->foreignKeyExists('item_rates', 'fk_item_rates_branch')) {
            try {
                Schema::table('item_rates', function (Blueprint $table) {
                    $table->foreign('branch_id', 'fk_item_rates_branch')
                        ->references('id')->on('branches')
                        ->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Skip - type mismatch
            }
        }

        if (!$this->foreignKeyExists('item_rates', 'fk_item_rates_category')) {
            try {
                Schema::table('item_rates', function (Blueprint $table) {
                    $table->foreign('item_category_id', 'fk_item_rates_category')
                        ->references('id')->on('item_categories')
                        ->onDelete('restrict');
                });
            } catch (\Exception $e) {
                // Skip - type mismatch
            }
        }

        // Ferry boats foreign keys
        if (Schema::hasColumn('ferryboats', 'branch_id') && !$this->foreignKeyExists('ferryboats', 'fk_ferryboats_branch')) {
            try {
                Schema::table('ferryboats', function (Blueprint $table) {
                    $table->foreign('branch_id', 'fk_ferryboats_branch')
                        ->references('id')->on('branches')
                        ->onDelete('restrict');
                });
            } catch (\Exception $e) {
                // Skip - type mismatch
            }
        }
    }

    /**
     * Check if foreign key exists (PostgreSQL and MySQL compatible)
     */
    private function foreignKeyExists($table, $name): bool
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL query - uses current schema
            $result = DB::select("
                SELECT constraint_name
                FROM information_schema.table_constraints
                WHERE table_schema = 'public'
                AND table_name = ?
                AND constraint_name = ?
                AND constraint_type = 'FOREIGN KEY'
            ", [$table, $name]);
        } else {
            // MySQL query
            $result = DB::select("
                SELECT CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND CONSTRAINT_NAME = ?
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            ", [$table, $name]);
        }

        return count($result) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('bookings', function (Blueprint $table) {
                if ($this->foreignKeyExists('bookings', 'fk_bookings_customer')) {
                    $table->dropForeign('fk_bookings_customer');
                }
                if ($this->foreignKeyExists('bookings', 'fk_bookings_ferry')) {
                    $table->dropForeign('fk_bookings_ferry');
                }
                if ($this->foreignKeyExists('bookings', 'fk_bookings_from_branch')) {
                    $table->dropForeign('fk_bookings_from_branch');
                }
                if ($this->foreignKeyExists('bookings', 'fk_bookings_to_branch')) {
                    $table->dropForeign('fk_bookings_to_branch');
                }
            });
        } catch (\Exception $e) {
            // Skip
        }

        try {
            Schema::table('ticket_lines', function (Blueprint $table) {
                if ($this->foreignKeyExists('ticket_lines', 'fk_ticket_lines_ticket')) {
                    $table->dropForeign('fk_ticket_lines_ticket');
                }
                if ($this->foreignKeyExists('ticket_lines', 'fk_ticket_lines_user')) {
                    $table->dropForeign('fk_ticket_lines_user');
                }
            });
        } catch (\Exception $e) {
            // Skip
        }

        try {
            Schema::table('item_rates', function (Blueprint $table) {
                if ($this->foreignKeyExists('item_rates', 'fk_item_rates_branch')) {
                    $table->dropForeign('fk_item_rates_branch');
                }
                if ($this->foreignKeyExists('item_rates', 'fk_item_rates_category')) {
                    $table->dropForeign('fk_item_rates_category');
                }
            });
        } catch (\Exception $e) {
            // Skip
        }

        try {
            Schema::table('ferryboats', function (Blueprint $table) {
                if ($this->foreignKeyExists('ferryboats', 'fk_ferryboats_branch')) {
                    $table->dropForeign('fk_ferryboats_branch');
                }
            });
        } catch (\Exception $e) {
            // Skip
        }
    }
};
