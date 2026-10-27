<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('pilgrim_id')->nullable()->after('converted_at')->constrained('pilgrims')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pilgrim_id');
        });
    }
};
