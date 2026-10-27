<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number', 20)->unique();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignId('pilgrim_id')->constrained('pilgrims')->cascadeOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->enum('room_type', ['single', 'double', 'triple', 'quad'])->default('quad');
            $table->decimal('package_price', 15, 2);
            $table->decimal('additional_cost', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2);
            $table->enum('status', [
                'pending', 'dp_paid', 'confirmed', 'documents_complete',
                'visa_processing', 'visa_approved', 'visa_rejected',
                'ready_to_depart', 'departed', 'returned', 'cancelled', 'refunded',
            ])->default('pending');
            $table->foreignId('referral_by')->nullable()->constrained('pilgrims')->nullOnDelete();
            $table->decimal('referral_commission', 15, 2)->default(0);
            $table->date('confirmed_date')->nullable();
            $table->date('cancelled_date')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
