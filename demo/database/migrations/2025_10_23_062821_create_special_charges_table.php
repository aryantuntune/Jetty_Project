<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('special_charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->decimal('special_charge', 10, 2);
            $table->timestamps();

           
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('special_charges');
    }
};