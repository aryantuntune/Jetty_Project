<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('branch_id')->unique();
            $table->string('branch_name');
            $table->string('branch_address')->nullable();
            $table->string('branch_phone')->nullable();
            $table->integer('dest_branch_id')->nullable();
            $table->string('dest_branch_name')->nullable();
            $table->integer('ferry_boat_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
