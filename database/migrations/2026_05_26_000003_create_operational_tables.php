<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_id')->constrained('visas')->cascadeOnDelete();
            $table->string('document_type');
            $table->string('file_path');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('refund_number')->unique();
            $table->decimal('total_paid', 15, 2);
            $table->decimal('refund_amount', 15, 2);
            $table->decimal('cancellation_fee', 15, 2)->default(0);
            $table->integer('cancellation_fee_pct')->default(0);
            $table->string('reason');
            $table->string('status')->default('pending');
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cluster_id')->nullable()->constrained('clusters')->nullOnDelete();
            $table->string('flight_number');
            $table->string('airline');
            $table->string('departure_airport');
            $table->string('arrival_airport');
            $table->datetime('departure_time');
            $table->datetime('arrival_time');
            $table->string('terminal')->nullable();
            $table->string('gate')->nullable();
            $table->string('status')->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('galleriable_type');
            $table->unsignedBigInteger('galleriable_id');
            $table->string('file_path');
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index(['galleriable_type', 'galleriable_id']);
        });

        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('policy_number')->unique();
            $table->string('provider');
            $table->string('coverage_type');
            $table->decimal('premium', 15, 2);
            $table->decimal('coverage_amount', 15, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurance_id')->constrained('insurances')->cascadeOnDelete();
            $table->string('claim_number')->unique();
            $table->string('incident_type');
            $table->text('description');
            $table->date('incident_date');
            $table->decimal('claim_amount', 15, 2);
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->string('status')->default('submitted');
            $table->text('resolution')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency', 5);
            $table->decimal('rate', 15, 4);
            $table->date('effective_date');
            $table->timestamps();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('currency', 3)->default('IDR')->after('total_price');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
        Schema::dropIfExists('currency_rates');
        Schema::dropIfExists('insurance_claims');
        Schema::dropIfExists('insurances');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('flights');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('visa_documents');
    }
};
