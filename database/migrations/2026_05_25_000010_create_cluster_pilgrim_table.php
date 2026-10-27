<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cluster_pilgrim', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cluster_id')->constrained('clusters')->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('seat_number')->nullable();
            $table->string('room_number')->nullable();
            $table->string('catering_group')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cluster_pilgrim');
    }
};
