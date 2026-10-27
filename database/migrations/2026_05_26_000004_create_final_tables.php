<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type')->default('percentage');
            $table->decimal('value', 10, 2);
            $table->decimal('min_booking_amount', 15, 2)->nullable();
            $table->decimal('max_discount', 15, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('booking_promo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('promo_code_id')->constrained('promo_codes')->cascadeOnDelete();
            $table->decimal('discount_amount', 15, 2);
            $table->timestamps();
        });

        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('cluster_id')->nullable()->constrained('clusters')->nullOnDelete();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('itinerary_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cluster_id')->constrained('clusters')->cascadeOnDelete();
            $table->integer('day_number');
            $table->date('date');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
        });

        Schema::create('itinerary_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_day_id')->constrained('itinerary_days')->cascadeOnDelete();
            $table->string('time')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location_type')->default('makkah');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('itinerary_activities');
        Schema::dropIfExists('itinerary_days');
        Schema::dropIfExists('check_ins');
        Schema::dropIfExists('booking_promo');
        Schema::dropIfExists('promo_codes');
    }
};
