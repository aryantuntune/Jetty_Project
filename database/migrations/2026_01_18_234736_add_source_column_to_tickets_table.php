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
            $table->string('source', 20)->default('web')->after('user_id');
            $table->string('payment_id')->nullable()->after('source');
            $table->string('qr_code')->nullable()->after('payment_id');
            $table->string('status', 20)->default('confirmed')->after('qr_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['source', 'payment_id', 'qr_code', 'status']);
        });
    }
};
