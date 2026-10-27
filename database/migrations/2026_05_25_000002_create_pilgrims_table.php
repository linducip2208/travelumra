<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilgrims', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number', 20)->unique();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('full_name');
            $table->string('father_name')->nullable();
            $table->string('nik', 16)->unique()->nullable();
            $table->string('kk_number', 16)->nullable();
            $table->string('passport_number')->nullable();
            $table->date('passport_issued_date')->nullable();
            $table->date('passport_expiry_date')->nullable();
            $table->string('passport_issuer')->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->enum('marital_status', ['single', 'married', 'widowed'])->nullable();
            $table->string('blood_type', 3)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('emergency_name')->nullable();
            $table->string('emergency_relation')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->foreignId('mahram_id')->nullable()->constrained('pilgrims')->nullOnDelete();
            $table->text('medical_notes')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medications')->nullable();
            $table->boolean('has_meningitis_vaccine')->default(false);
            $table->date('meningitis_vaccine_date')->nullable();
            $table->boolean('has_covid_vaccine')->default(false);
            $table->string('source')->nullable();
            $table->foreignId('referral_pilgrim_id')->nullable()->constrained('pilgrims')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilgrims');
    }
};
