<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('api_format')->default('redirect_snap');
            $table->string('base_url')->nullable();
            $table->text('api_key_encrypted')->nullable();
            $table->text('server_key_encrypted')->nullable();
            $table->text('client_key_encrypted')->nullable();
            $table->text('merchant_id')->nullable();
            $table->json('extra_config')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_providers');
    }
};
