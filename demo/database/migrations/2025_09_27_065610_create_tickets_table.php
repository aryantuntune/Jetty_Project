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
        Schema::create('tickets', function (Blueprint $table) {
          $table->id();
            $table->integer('branch_id');
            $table->integer('ferry_boat_id');
            $table->string('payment_mode');
            $table->dateTime('ferry_time');
            $table->decimal('discount_pct', 8, 2)->nullable();
            $table->decimal('discount_rs', 12, 2)->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};