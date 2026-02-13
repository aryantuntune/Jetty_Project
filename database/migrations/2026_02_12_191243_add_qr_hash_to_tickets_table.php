<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add qr_hash column to tickets table for secure QR code generation
     * This replaces the insecure sequential ticket ID with a cryptographic hash
     */
    public function up(): void
    {
        // Ensure pgcrypto extension is available (required for digest function)
        DB::statement('CREATE EXTENSION IF NOT EXISTS pgcrypto');

        Schema::table('tickets', function (Blueprint $table) {
            // Add qr_hash column (64 characters for SHA-256 hex)
            $table->string('qr_hash', 64)->nullable()->after('id');

            // Add unique index for fast lookups when scanning QR codes
            $table->unique('qr_hash');
        });

        // Generate QR hashes for existing tickets using PostgreSQL syntax
        $secret = config('app.qr_secret', config('app.key'));

        DB::statement("
            UPDATE tickets
            SET qr_hash = encode(digest(
                id::text
                || '|'
                || EXTRACT(EPOCH FROM created_at)::text
                || '|'
                || COALESCE(booking_id, 0)::text
                || '|'
                || ?
            , 'sha256'), 'hex')
            WHERE qr_hash IS NULL
        ", [$secret]);

        // After generating hashes, make column NOT NULL
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('qr_hash', 64)->nullable(false)->change();
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
