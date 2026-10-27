<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah status pending_approval via DB statement (MySQL) — aman untuk fresh migrate.
        try {
            \Illuminate\Support\Facades\DB::statement(
                "ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','pending_approval','dp_paid','confirmed','documents_complete','visa_processing','visa_approved','visa_rejected','ready_to_depart','departed','returned','cancelled','refunded') DEFAULT 'pending'"
            );
        } catch (\Throwable $e) {
            // SQLite/testing: lewati (kolom string biasa).
        }

        Schema::table('bookings', function (Blueprint $table) {
            $table->index('status');
            $table->index('package_id');
            $table->index('pilgrim_id');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->index('status');
            $table->index('due_date');
            $table->index('pilgrim_id');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->index('status');
            $table->index('payment_date');
            $table->index('invoice_id');
        });
        Schema::table('pilgrims', function (Blueprint $table) {
            $table->index('phone');
            $table->index('email');
        });
        Schema::table('packages', function (Blueprint $table) {
            $table->index('slug');
            $table->index('is_active');
            $table->index('is_featured');
        });
        Schema::table('leads', function (Blueprint $table) {
            $table->index('status');
            $table->index('assigned_to');
        });
        Schema::table('clusters', function (Blueprint $table) {
            $table->index('status');
        });
    }

    public function down(): void
    {
        // Index drop dibiarkan (no-op aman).
    }
};
