<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->string('type')->default('main');
            $table->foreignId('parent_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_holder')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('quota_haji')->default(0);
            $table->integer('quota_umrah')->default(0);
            $table->integer('quota_used')->default(0);
            $table->decimal('referral_commission_pct', 5, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            // Kolom branch_id sudah ada di create_users_table; tambah FK bila belum ada (MySQL).
            // SQLite diabaikan agar testing tetap jalan.
            try {
                $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            } catch (\Throwable $e) {
                // FK mungkin sudah ada — abaikan.
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
        });

        Schema::dropIfExists('branches');
    }
};
