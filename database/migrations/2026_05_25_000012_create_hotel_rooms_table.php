<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('room_number');
            $table->enum('type', ['double', 'triple', 'quad']);
            $table->integer('floor')->nullable();
            $table->string('block')->nullable();
            $table->boolean('is_available')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_rooms');
    }
};
