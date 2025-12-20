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
        Schema::create('ticket_lines', function (Blueprint $table) {
            $table->id();
            $table->integer('ticket_id');
            $table->string('item_id')->nullable();
            $table->string('item_name');
            $table->decimal('qty', 12, 2);
            $table->decimal('rate', 12, 2);
            $table->decimal('levy', 12, 2);
            $table->decimal('amount', 12, 2);
            $table->string('vehicle_name')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_lines');
    }
};