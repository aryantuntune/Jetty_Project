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
        // Only add column if customers table exists and doesn't have profile_image
        if (Schema::hasTable('customers') && !Schema::hasColumn('customers', 'profile_image')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('profile_image')
                    ->nullable()
                    ->after('mobile');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('customers', 'profile_image')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('profile_image');
            });
        }
    }
};
