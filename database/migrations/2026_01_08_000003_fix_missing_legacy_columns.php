<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Check and add no_of_units
            if (!Schema::hasColumn('tickets', 'no_of_units')) {
                $table->integer('no_of_units')->nullable()->after('total_amount');
            }
            // Check and add dest_branch_id
            if (!Schema::hasColumn('tickets', 'dest_branch_id')) {
                $table->unsignedInteger('dest_branch_id')->nullable()->after('branch_id');
            }
            // Check and add created_by
            if (!Schema::hasColumn('tickets', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'no_of_units')) {
                $table->dropColumn('no_of_units');
            }
            if (Schema::hasColumn('tickets', 'dest_branch_id')) {
                $table->dropColumn('dest_branch_id');
            }
            if (Schema::hasColumn('tickets', 'created_by')) {
                $table->dropColumn('created_by');
            }
        });
    }
};
