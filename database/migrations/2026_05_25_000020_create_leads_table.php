<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->foreignId('interested_package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->enum('source', ['website', 'instagram', 'facebook', 'wa_ads', 'referral', 'event', 'walk_in']);
            $table->enum('status', ['new', 'contacted', 'interested', 'follow_up', 'converted', 'closed_lost']);
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->date('follow_up_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
