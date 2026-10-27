<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilgrim_id')->constrained('pilgrims')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->integer('points');
            $table->enum('transaction_type', ['earned', 'redeemed', 'expired']);
            $table->string('description')->nullable();
            $table->decimal('value_rupiah', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};
