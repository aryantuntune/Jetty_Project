<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_ticket_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('ticket_date')->index();
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->unsignedBigInteger('ferry_boat_id')->nullable()->index();
            $table->string('payment_mode', 50)->nullable();
            $table->string('ferry_type', 50)->nullable();

            // Pre-aggregated stats
            $table->unsignedInteger('ticket_count')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('total_levy', 15, 2)->default(0);

            $table->timestamps();

            // Unique constraint for upsert
            $table->unique(['ticket_date', 'branch_id', 'ferry_boat_id', 'payment_mode', 'ferry_type'], 'daily_summary_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_ticket_summaries');
    }
};
