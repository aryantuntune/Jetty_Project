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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'ticket_id')) {
                $table->string('ticket_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('bookings', 'booking_source')) {
                $table->string('booking_source')->default('web')->after('status');
            }
            if (!Schema::hasColumn('bookings', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('booking_source');
            }
            if (!Schema::hasColumn('bookings', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'ticket_id')) {
                $table->dropColumn('ticket_id');
            }
            if (Schema::hasColumn('bookings', 'booking_source')) {
                $table->dropColumn('booking_source');
            }
            if (Schema::hasColumn('bookings', 'verified_at')) {
                $table->dropColumn('verified_at');
            }
            if (Schema::hasColumn('bookings', 'verified_by')) {
                $table->dropColumn('verified_by');
            }
        });
    }
};
