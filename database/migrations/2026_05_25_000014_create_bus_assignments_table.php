<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained('buses')->cascadeOnDelete();
            $table->foreignId('cluster_id')->constrained('clusters')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('route')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_assignments');
    }
};
