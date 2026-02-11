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
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_id')->unique();
                $table->unsignedBigInteger('customer_id');
                $table->unsignedBigInteger('ferry_id')->nullable();
                $table->unsignedBigInteger('from_branch');
                $table->unsignedBigInteger('to_branch');
                $table->date('booking_date');
                $table->time('departure_time')->nullable();
                $table->json('items')->nullable();
                $table->decimal('total_amount', 10, 2);
                $table->string('payment_id')->nullable();
                $table->string('booking_source')->default('web');
                $table->string('status')->default('pending');
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
