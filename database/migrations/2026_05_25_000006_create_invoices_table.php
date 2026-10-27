<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 20)->unique();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('pilgrim_id')->constrained('pilgrims')->cascadeOnDelete();
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2);
            $table->enum('status', ['unpaid', 'partial', 'paid', 'overdue', 'cancelled']);
            $table->date('due_date');
            $table->text('notes')->nullable();
            $table->string('payment_link_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
