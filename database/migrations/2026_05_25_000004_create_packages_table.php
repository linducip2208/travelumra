<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['umrah', 'haji', 'umrah_plus', 'haji_plus', 'ziarah']);
            $table->enum('category', ['reguler', 'vip', 'eksklusif']);
            $table->string('airline')->nullable();
            $table->string('departure_airport')->nullable();
            $table->date('departure_date');
            $table->date('return_date');
            $table->integer('duration_days');
            $table->string('makkah_hotel_name')->nullable();
            $table->integer('makkah_hotel_stars')->nullable();
            $table->integer('makkah_hotel_distance_meters')->nullable();
            $table->string('makkah_hotel_room_type')->nullable();
            $table->string('madinah_hotel_name')->nullable();
            $table->integer('madinah_hotel_stars')->nullable();
            $table->integer('madinah_hotel_distance_meters')->nullable();
            $table->decimal('price_single', 15, 2)->default(0);
            $table->decimal('price_double', 15, 2)->default(0);
            $table->decimal('price_triple', 15, 2)->default(0);
            $table->decimal('price_quad', 15, 2)->default(0);
            $table->integer('quota')->default(0);
            $table->integer('booked_count')->default(0);
            $table->integer('available_count')->default(0);
            $table->text('itinerary')->nullable();
            $table->text('includes')->nullable();
            $table->text('excludes')->nullable();
            $table->text('terms')->nullable();
            $table->string('banner_image')->nullable();
            $table->json('gallery_images')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
