<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_rates', function (Blueprint $table) {
            $table->id();
            $table->string('item_name', 150);
            $table->string('item_short_name', 50)->nullable();
            $table->string('item_name_regional', 150)->nullable();
            $table->unsignedBigInteger('item_category_id')->nullable();
            $table->decimal('item_rate', 10, 2)->default(0);
            $table->decimal('item_lavy', 10, 2)->default(0);
            $table->decimal('item_surcharge_pct', 5, 2)->default(0);
            $table->decimal('levy_surcharge_pct', 5, 2)->default(0);
            $table->integer('space_units')->default(1);
            $table->boolean('is_fixed_rate')->default(false);
            $table->boolean('is_vehicle')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->date('starting_date');
            $table->date('ending_date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_rates');
    }
};
