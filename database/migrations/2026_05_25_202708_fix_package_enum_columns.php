<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite: enum = string biasa, cukup normalisasi data
            DB::table('packages')->where('type', 'haji_reguler')->update(['type' => 'haji']);
            DB::table('packages')->where('type', 'haji_furoda')->update(['type' => 'haji']);
            DB::table('packages')->where('category', 'ekonomi')->update(['category' => 'reguler']);
            DB::table('packages')->where('category', 'standard')->update(['category' => 'reguler']);
            DB::table('packages')->where('category', 'vvip')->update(['category' => 'vip']);
            return;
        }
        // Step 1: Expand type ENUM to include all values (old + new)
        DB::statement("ALTER TABLE packages MODIFY COLUMN type ENUM('umrah', 'haji_reguler', 'haji_plus', 'haji_furoda', 'haji', 'umrah_plus', 'ziarah') NOT NULL");
        DB::table('packages')->where('type', 'haji_reguler')->update(['type' => 'haji']);
        DB::table('packages')->where('type', 'haji_furoda')->update(['type' => 'haji']);
        DB::statement("ALTER TABLE packages MODIFY COLUMN type ENUM('umrah', 'haji', 'umrah_plus', 'haji_plus', 'ziarah') NOT NULL");
        DB::statement("ALTER TABLE packages MODIFY COLUMN category ENUM('ekonomi', 'standard', 'vip', 'vvip', 'reguler', 'eksklusif') NOT NULL");
        DB::table('packages')->where('category', 'ekonomi')->update(['category' => 'reguler']);
        DB::table('packages')->where('category', 'standard')->update(['category' => 'reguler']);
        DB::table('packages')->where('category', 'vvip')->update(['category' => 'vip']);
        DB::statement("ALTER TABLE packages MODIFY COLUMN category ENUM('reguler', 'vip', 'eksklusif') NOT NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        // Expand type to include old values
        DB::statement("ALTER TABLE packages MODIFY COLUMN type ENUM('umrah', 'haji', 'umrah_plus', 'haji_plus', 'ziarah', 'haji_reguler', 'haji_furoda') NOT NULL");
        DB::table('packages')->where('type', 'haji')->update(['type' => 'haji_reguler']);
        DB::table('packages')->where('type', 'umrah_plus')->update(['type' => 'umrah']);
        DB::table('packages')->where('type', 'ziarah')->update(['type' => 'umrah']);
        DB::statement("ALTER TABLE packages MODIFY COLUMN type ENUM('umrah', 'haji_reguler', 'haji_plus', 'haji_furoda') NOT NULL");
        DB::statement("ALTER TABLE packages MODIFY COLUMN category ENUM('reguler', 'vip', 'eksklusif', 'ekonomi', 'standard', 'vvip') NOT NULL");
        DB::table('packages')->where('category', 'reguler')->update(['category' => 'ekonomi']);
        DB::table('packages')->where('category', 'eksklusif')->update(['category' => 'vip']);
        DB::statement("ALTER TABLE packages MODIFY COLUMN category ENUM('ekonomi', 'standard', 'vip', 'vvip') NOT NULL");
    }
};
