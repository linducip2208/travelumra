<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();
            $table->string('model')->nullable();
            $table->integer('capacity')->default(45);
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('company')->nullable();
            $table->date('last_service_date')->nullable();
            $table->date('next_service_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
