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
        // Bookings foreign keys
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('customer_id', 'fk_bookings_customer')
                ->references('id')->on('customers')
                ->onDelete('cascade');

            $table->foreign('ferry_id', 'fk_bookings_ferry')
                ->references('id')->on('ferryboats')
                ->onDelete('restrict');

            $table->foreign('from_branch', 'fk_bookings_from_branch')
                ->references('id')->on('branches')
                ->onDelete('restrict');

            $table->foreign('to_branch', 'fk_bookings_to_branch')
                ->references('id')->on('branches')
                ->onDelete('restrict');
        });

        // Tickets foreign keys
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreign('branch_id', 'fk_tickets_branch')
                ->references('id')->on('branches')
                ->onDelete('restrict');

            $table->foreign('ferry_boat_id', 'fk_tickets_ferry')
                ->references('id')->on('ferryboats')
                ->onDelete('restrict');

            // Only add if user_id column exists
            if (Schema::hasColumn('tickets', 'user_id')) {
                $table->foreign('user_id', 'fk_tickets_user')
                    ->references('id')->on('users')
                    ->onDelete('set null');
            }

            // Only add if guest_id column exists
            if (Schema::hasColumn('tickets', 'guest_id')) {
                $table->foreign('guest_id', 'fk_tickets_guest')
                    ->references('id')->on('guests')
                    ->onDelete('set null');
            }
        });

        // Ticket lines foreign keys
        Schema::table('ticket_lines', function (Blueprint $table) {
            $table->foreign('ticket_id', 'fk_ticket_lines_ticket')
                ->references('id')->on('tickets')
                ->onDelete('cascade');  // Delete lines when ticket deleted

            // Only add if user_id column exists
            if (Schema::hasColumn('ticket_lines', 'user_id')) {
                $table->foreign('user_id', 'fk_ticket_lines_user')
                    ->references('id')->on('users')
                    ->onDelete('set null');
            }
        });

        // Item rates foreign keys
        Schema::table('item_rates', function (Blueprint $table) {
            $table->foreign('branch_id', 'fk_item_rates_branch')
                ->references('id')->on('branches')
                ->onDelete('cascade');

            $table->foreign('item_category_id', 'fk_item_rates_category')
                ->references('id')->on('item_categories')
                ->onDelete('restrict');
        });

        // Ferry boats foreign keys
        Schema::table('ferryboats', function (Blueprint $table) {
            if (Schema::hasColumn('ferryboats', 'branch_id')) {
                $table->foreign('branch_id', 'fk_ferryboats_branch')
                    ->references('id')->on('branches')
                    ->onDelete('restrict');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign('fk_bookings_customer');
            $table->dropForeign('fk_bookings_ferry');
            $table->dropForeign('fk_bookings_from_branch');
            $table->dropForeign('fk_bookings_to_branch');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign('fk_tickets_branch');
            $table->dropForeign('fk_tickets_ferry');
            if (Schema::hasColumn('tickets', 'user_id')) {
                $table->dropForeign('fk_tickets_user');
            }
            if (Schema::hasColumn('tickets', 'guest_id')) {
                $table->dropForeign('fk_tickets_guest');
            }
        });

        Schema::table('ticket_lines', function (Blueprint $table) {
            $table->dropForeign('fk_ticket_lines_ticket');
            if (Schema::hasColumn('ticket_lines', 'user_id')) {
                $table->dropForeign('fk_ticket_lines_user');
            }
        });

        Schema::table('item_rates', function (Blueprint $table) {
            $table->dropForeign('fk_item_rates_branch');
            $table->dropForeign('fk_item_rates_category');
        });

        Schema::table('ferryboats', function (Blueprint $table) {
            if (Schema::hasColumn('ferryboats', 'branch_id')) {
                $table->dropForeign('fk_ferryboats_branch');
            }
        });
    }
};
