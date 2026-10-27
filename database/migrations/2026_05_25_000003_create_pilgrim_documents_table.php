<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilgrim_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilgrim_id')->constrained('pilgrims')->cascadeOnDelete();
            $table->string('type');
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->bigInteger('size')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('verification_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilgrim_documents');
    }
};
