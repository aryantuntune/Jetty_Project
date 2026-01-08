<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Adds ticket_date column that was missing from initial migration
     * but is used throughout the codebase for date filtering
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'ticket_date')) {
                $table->date('ticket_date')->nullable()->after('ferry_time');
            }
            if (!Schema::hasColumn('tickets', 'ticket_no')) {
                $table->unsignedBigInteger('ticket_no')->nullable()->after('ticket_date');
            }
        });

        Schema::table('ticket_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_lines', 'ticket_date')) {
                $table->date('ticket_date')->nullable()->after('ticket_id');
            }
        });

        // Populate ticket_date from created_at for existing records
        \DB::statement('UPDATE tickets SET ticket_date = DATE(created_at) WHERE ticket_date IS NULL');
        \DB::statement('UPDATE ticket_lines tl JOIN tickets t ON tl.ticket_id = t.id SET tl.ticket_date = t.ticket_date WHERE tl.ticket_date IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'ticket_date')) {
                $table->dropColumn('ticket_date');
            }
            if (Schema::hasColumn('tickets', 'ticket_no')) {
                $table->dropColumn('ticket_no');
            }
        });

        Schema::table('ticket_lines', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_lines', 'ticket_date')) {
                $table->dropColumn('ticket_date');
            }
        });
    }
};
