<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Audit log tracks security-sensitive events:
     * - Login attempts (success/failure)
     * - Ticket verifications
     * - Payment transactions
     * - Authorization failures
     * - Data modifications
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Who did it
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type')->nullable(); // Customer, User, Checker
            $table->string('user_name')->nullable(); // For deleted users

            // What happened
            $table->string('event')->index(); // login, logout, verify_ticket, payment, etc.
            $table->string('action'); // success, failure, attempt
            $table->text('description')->nullable();

            // Context
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable(); // Additional data

            // Related records
            $table->string('auditable_type')->nullable(); // Ticket, Booking, Payment
            $table->unsignedBigInteger('auditable_id')->nullable();

            // Security level
            $table->enum('severity', ['info', 'warning', 'critical'])->default('info');

            $table->timestamps();

            // Indexes for fast searching
            $table->index(['event', 'action']);
            $table->index('created_at');
            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
