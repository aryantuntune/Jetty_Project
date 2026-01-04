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
        Schema::create('item_rates', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id');
            $table->string('item_name', 150);
            $table->unsignedBigInteger('item_category_id')->nullable();
            $table->decimal('item_rate', 10, 2)->default(0);
            $table->decimal('item_lavy', 10, 2)->default(0);
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->date('starting_date');
            $table->date('ending_date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('route_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_rates');
    }
};