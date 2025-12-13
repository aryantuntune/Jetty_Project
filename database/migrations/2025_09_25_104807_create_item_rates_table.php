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
            $table->string('item_name', 150);
            $table->unsignedBigInteger('item_category_id')->nullable();
            $table->decimal('item_rate', 10, 2)->default(0);
            $table->decimal('item_lavy', 10, 2)->default(0);
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->date('starting_date');
            $table->date('ending_date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('item_category_id')->references('id')->on('item_categories')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
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