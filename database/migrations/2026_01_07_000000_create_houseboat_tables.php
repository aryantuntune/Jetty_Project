<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('houseboat_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->integer('capacity_adults')->default(2);
            $table->integer('capacity_kids')->default(0);
            $table->integer('total_rooms')->default(1);
            $table->json('amenities')->nullable(); // stored as JSON array ["AC", "Breakfast", ...]
            $table->string('image_url')->nullable();
            $table->timestamps();
        });

        Schema::create('houseboat_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('houseboat_rooms')->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('guests_adults')->default(1);
            $table->integer('guests_children')->default(0);
            $table->integer('room_count')->default(1);
            $table->decimal('total_amount', 10, 2);
            // Use string instead of enum for PostgreSQL compatibility
            $table->string('status', 20)->default('pending');
            $table->string('booking_reference')->unique(); // For tracking
            $table->timestamps();
        });

        // Add CHECK constraint for status values (PostgreSQL compatible)
        DB::statement("ALTER TABLE houseboat_bookings ADD CONSTRAINT houseboat_bookings_status_check CHECK (status IN ('pending', 'confirmed', 'cancelled', 'completed'))");

        // Insert initial seed data
        DB::table('houseboat_rooms')->insert([
            [
                'name' => 'Deluxe Room',
                'price' => 6000.00,
                'description' => 'AP (Room with Breakfast, Lunch, High Tea, Dinner). Experience the luxury of staying on the tranquil waters with all modern amenities.',
                'capacity_adults' => 2,
                'capacity_kids' => 1,
                'total_rooms' => 3,
                'amenities' => json_encode(['King Bed', 'AC', 'Sea View', 'Attached Bathroom']),
                'image_url' => '/images/houseboat/deluxe_room_main.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'VIP Suite with Deck',
                'price' => 8000.00,
                'description' => 'Luxury redefined. Enjoy your morning coffee on your private deck with panoramic views of the sea. Includes all meals.',
                'capacity_adults' => 2,
                'capacity_kids' => 2,
                'total_rooms' => 2,
                'amenities' => json_encode(['King Bed', 'Private Deck', 'AC', 'Bathtub', 'Sea View']),
                'image_url' => '/images/houseboat/vip_suite_main.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('houseboat_bookings');
        Schema::dropIfExists('houseboat_rooms');
    }
};
