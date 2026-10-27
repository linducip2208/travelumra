<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->string('expense_number')->unique();
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->string('payment_method')->default('transfer');
            $table->string('recipient')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('approved');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('approval_thresholds', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->decimal('min_amount', 15, 2)->default(0);
            $table->decimal('max_amount', 15, 2)->nullable();
            $table->string('approver_role');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_thresholds');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
    }
};
