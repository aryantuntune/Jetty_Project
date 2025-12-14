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
        Schema::table('bookings', function (Blueprint $table) {
            // Add ferry_id if it doesn't exist
            if (!Schema::hasColumn('bookings', 'ferry_id')) {
                $table->unsignedBigInteger('ferry_id')->after('customer_id');
            }

            // Add booking_date if it doesn't exist
            if (!Schema::hasColumn('bookings', 'booking_date')) {
                $table->date('booking_date')->after('to_branch');
            }

            // Add departure_time if it doesn't exist
            if (!Schema::hasColumn('bookings', 'departure_time')) {
                $table->time('departure_time')->after('booking_date');
            }

            // Add qr_code if it doesn't exist
            if (!Schema::hasColumn('bookings', 'qr_code')) {
                $table->string('qr_code')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'ferry_id')) {
                $table->dropColumn('ferry_id');
            }
            if (Schema::hasColumn('bookings', 'booking_date')) {
                $table->dropColumn('booking_date');
            }
            if (Schema::hasColumn('bookings', 'departure_time')) {
                $table->dropColumn('departure_time');
            }
            if (Schema::hasColumn('bookings', 'qr_code')) {
                $table->dropColumn('qr_code');
            }
        });
    }
};
