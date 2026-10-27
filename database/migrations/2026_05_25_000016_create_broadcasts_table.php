<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->enum('channel', ['whatsapp', 'email', 'push_notification', 'all']);
            $table->json('recipient_ids')->nullable();
            $table->foreignId('cluster_id')->nullable()->constrained('clusters')->nullOnDelete();
            $table->enum('status', ['draft', 'scheduled', 'sent', 'failed']);
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('recipient_count')->default(0);
            $table->integer('sent_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcasts');
    }
};
