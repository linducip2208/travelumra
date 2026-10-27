<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilgrim_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('visa_number')->nullable();
            $table->enum('type', ['umrah', 'haji', 'ziarah']);
            $table->enum('status', [
                'not_submitted', 'submitted', 'in_review',
                'approved', 'rejected', 'issued', 'expired'
            ]);
            $table->date('submission_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('embassy')->nullable();
            $table->string('mofa_number')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visas');
    }
};
