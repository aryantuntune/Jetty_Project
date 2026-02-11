<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Adds qr_hash column for secure QR code verification
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Add qr_hash column - 64 chars for SHA256 hex output
            $table->string('qr_hash', 64)->nullable()->after('id');

            // Add unique index for fast lookups
            $table->unique('qr_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropUnique(['qr_hash']);
            $table->dropColumn('qr_hash');
        });
    }
};
