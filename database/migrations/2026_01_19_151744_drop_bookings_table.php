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
        Schema::dropIfExists('bookings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Bookings table has been replaced by tickets table
        // No rollback - recreating would require the full schema
    }
};
