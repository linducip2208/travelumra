<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clusters', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('muthawif_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('capacity')->default(45);
            $table->integer('booked_count')->default(0);
            $table->enum('status', ['scheduled', 'departed', 'completed']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clusters');
    }
};
