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
            $table->foreignId('item_rate_id')->nullable()->after('ticket_id')->constrained('item_rates')->onDelete('cascade');
            $table->integer('quantity')->nullable()->after('item_rate_id');
            $table->decimal('total', 10, 2)->nullable()->after('rate');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_lines', function (Blueprint $table) {
            $table->dropForeign(['item_rate_id']);
            $table->dropColumn(['item_rate_id', 'quantity', 'total']);
        });
    }
};
