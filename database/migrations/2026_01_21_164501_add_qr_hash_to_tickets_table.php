<?php

use Illuminate\Database\Migrations\Migration;

/**
 * DEPRECATED: This migration has been superseded by
 * 2026_02_12_191243_add_qr_hash_to_tickets_table.php
 * which also populates existing ticket hashes.
 *
 * This file is intentionally empty to prevent duplicate column creation.
 */
return new class extends Migration {
    public function up(): void
    {
        // Handled by 2026_02_12 migration
    }

    public function down(): void
    {
        // Handled by 2026_02_12 migration
    }
};
