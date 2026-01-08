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
        Schema::table('houseboat_rooms', function (Blueprint $table) {
            $table->json('gallery_images')->nullable()->after('image_url');
        });

        // Seed data for gallery
        DB::table('houseboat_rooms')->where('name', 'Deluxe Room')->update([
            'gallery_images' => json_encode([
                '/images/houseboat/deluxe_room_main.jpg',
                '/images/houseboat/hero_banner_2.jpg',
                '/images/houseboat/about_deck.jpg'
            ])
        ]);

        DB::table('houseboat_rooms')->where('name', 'VIP Suite with Deck')->update([
            'gallery_images' => json_encode([
                '/images/houseboat/vip_suite_main.jpg',
                '/images/houseboat/hero_banner_1.jpg',
                '/images/houseboat/hero_banner_3.jpg'
            ])
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('houseboat_rooms', function (Blueprint $table) {
            $table->dropColumn('gallery_images');
        });
    }
};
