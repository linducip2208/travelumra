<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_pilgrim', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->foreignId('pilgrim_id')->constrained('pilgrims')->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->enum('status', ['allocated', 'picked_up']);
            $table->date('pickup_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_pilgrim');
    }
};
