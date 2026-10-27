<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignId('cluster_id')->nullable()->constrained('clusters')->nullOnDelete();
            $table->integer('day_number');
            $table->date('date');
            $table->string('time')->nullable();
            $table->string('activity');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itineraries');
    }
};
