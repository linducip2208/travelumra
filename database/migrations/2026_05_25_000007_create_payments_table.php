<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number', 20)->unique();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_holder')->nullable();
            $table->date('payment_date');
            $table->string('proof_file')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'refunded']);
            $table->text('rejection_reason')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
