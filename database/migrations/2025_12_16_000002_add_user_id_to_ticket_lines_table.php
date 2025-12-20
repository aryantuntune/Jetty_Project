<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ticket_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_lines', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('vehicle_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_lines', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_lines', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};
