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
        Schema::table('tickets', function (Blueprint $table) {
            // Add user_id if it doesn't exist
            if (!Schema::hasColumn('tickets', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('total_amount');
            }

            // Add ferry_type if it doesn't exist
            if (!Schema::hasColumn('tickets', 'ferry_type')) {
                $table->string('ferry_type')->nullable()->after('user_id');
            }

            // Add customer_name if it doesn't exist
            if (!Schema::hasColumn('tickets', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('ferry_type');
            }

            // Add customer_mobile if it doesn't exist
            if (!Schema::hasColumn('tickets', 'customer_mobile')) {
                $table->string('customer_mobile')->nullable()->after('customer_name');
            }

            // Add guest_id if it doesn't exist
            if (!Schema::hasColumn('tickets', 'guest_id')) {
                $table->unsignedBigInteger('guest_id')->nullable()->after('customer_mobile');
            }

            // Add verified_at if it doesn't exist
            if (!Schema::hasColumn('tickets', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('guest_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'user_id')) {
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('tickets', 'ferry_type')) {
                $table->dropColumn('ferry_type');
            }
            if (Schema::hasColumn('tickets', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
            if (Schema::hasColumn('tickets', 'customer_mobile')) {
                $table->dropColumn('customer_mobile');
            }
            if (Schema::hasColumn('tickets', 'guest_id')) {
                $table->dropColumn('guest_id');
            }
            if (Schema::hasColumn('tickets', 'verified_at')) {
                $table->dropColumn('verified_at');
            }
        });
    }
};
