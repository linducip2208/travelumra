<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use App\Models\PaymentProvider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production') && ! $this->command->confirm('Seed DEMO users ke PRODUCTION? (password default!)', false)) {
            $this->command->warn('DemoDataSeeder dibatalkan di production.');
            return;
        }
        $this->command->info('Seeding 30 demo users...');

        $branch = Branch::first() ?? Branch::create([
            'name' => 'Pusat', 'code' => 'JKT-01', 'type' => 'main',
            'city' => 'Jakarta Pusat', 'province' => 'DKI Jakarta',
            'phone' => '021-63881234', 'is_active' => true,
            'quota_haji' => 200, 'quota_umrah' => 500,
        ]);

        $roles = ['super_admin','admin','finance','branch_manager','agent','muthawif'];
        $existingCount = User::count();

        $users = [
            ['name'=>'Super Admin','email'=>'superadmin@travelumrah.com','role'=>'super_admin'],
            ['name'=>'Admin Pusat','email'=>'admin@travelumrah.com','role'=>'admin'],
            ['name'=>'Finance Manager','email'=>'finance@travelumrah.com','role'=>'finance'],
            ['name'=>'Finance Staff 1','email'=>'finance1@travelumrah.com','role'=>'finance'],
            ['name'=>'Finance Staff 2','email'=>'finance2@travelumrah.com','role'=>'finance'],
            ['name'=>'Branch Manager JKT','email'=>'bm_jkt@travelumrah.com','role'=>'branch_manager'],
            ['name'=>'Branch Manager BDG','email'=>'bm_bdg@travelumrah.com','role'=>'branch_manager'],
            ['name'=>'Branch Manager SBY','email'=>'bm_sby@travelumrah.com','role'=>'branch_manager'],
            ['name'=>'Branch Manager MDN','email'=>'bm_mdn@travelumrah.com','role'=>'branch_manager'],
            ['name'=>'Agent Ahmad','email'=>'agent.ahmad@travelumrah.com','role'=>'agent'],
            ['name'=>'Agent Budi','email'=>'agent.budi@travelumrah.com','role'=>'agent'],
            ['name'=>'Agent Citra','email'=>'agent.citra@travelumrah.com','role'=>'agent'],
            ['name'=>'Agent Dewi','email'=>'agent.dewi@travelumrah.com','role'=>'agent'],
            ['name'=>'Agent Eko','email'=>'agent.eko@travelumrah.com','role'=>'agent'],
            ['name'=>'Agent Fajar','email'=>'agent.fajar@travelumrah.com','role'=>'agent'],
            ['name'=>'Agent Gita','email'=>'agent.gita@travelumrah.com','role'=>'agent'],
            ['name'=>'Agent Hendra','email'=>'agent.hendra@travelumrah.com','role'=>'agent'],
            ['name'=>'Agent Indah','email'=>'agent.indah@travelumrah.com','role'=>'agent'],
            ['name'=>'Agent Joko','email'=>'agent.joko@travelumrah.com','role'=>'agent'],
            ['name'=>'Agent Kartika','email'=>'agent.kartika@travelumrah.com','role'=>'agent'],
            ['name'=>'Muthawif Ustadz Ahmad','email'=>'muthawif.ahmad@travelumrah.com','role'=>'muthawif'],
            ['name'=>'Muthawif Ustadz Hamid','email'=>'muthawif.hamid@travelumrah.com','role'=>'muthawif'],
            ['name'=>'Muthawif Ustadz Syarif','email'=>'muthawif.syarif@travelumrah.com','role'=>'muthawif'],
            ['name'=>'Muthawif Ustadz Rahman','email'=>'muthawif.rahman@travelumrah.com','role'=>'muthawif'],
            ['name'=>'Muthawif Ustadz Malik','email'=>'muthawif.malik@travelumrah.com','role'=>'muthawif'],
            ['name'=>'Muthawif Ustadz Ibrahim','email'=>'muthawif.ibrahim@travelumrah.com','role'=>'muthawif'],
            ['name'=>'Muthawif Ustadz Yusuf','email'=>'muthawif.yusuf@travelumrah.com','role'=>'muthawif'],
            ['name'=>'Muthawif Ustadz Ismail','email'=>'muthawif.ismail@travelumrah.com','role'=>'muthawif'],
            ['name'=>'Muthawif Ustadz Idris','email'=>'muthawif.idris@travelumrah.com','role'=>'muthawif'],
            ['name'=>'Muthawif Ustadz Zain','email'=>'muthawif.zain@travelumrah.com','role'=>'muthawif'],
        ];

        $created = 0;
        foreach ($users as $u) {
            if (User::where('email', $u['email'])->exists()) continue;
            User::create([
                'name' => $u['name'],
                'email' => $u['email'],
                'password' => 'password',
                'branch_id' => $branch->id,
                'role' => $u['role'],
                'phone' => '08' . fake()->numerify('##########'),
                'is_active' => true,
                'is_muthawif' => $u['role'] === 'muthawif',
                'muthawif_certification' => $u['role'] === 'muthawif' ? 'Sertifikasi Kemenag RI' : null,
                'muthawif_experience_years' => $u['role'] === 'muthawif' ? rand(3, 15) : 0,
            ]);
            $created++;
        }

        $this->command->info("Users: {$created} baru (total: " . User::count() . ")");

        // Payment Gateway - Simulator (default, no API needed)
        if (!PaymentProvider::where('code', 'simulator')->exists()) {
            PaymentProvider::create([
                'name' => 'Simulator (Testing)',
                'code' => 'simulator',
                'api_format' => 'simulator',
                'base_url' => null,
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ]);
            $this->command->info('Payment Gateway: Simulator (default, tanpa API)');
        }

        // Payment Gateway - Tripay Sandbox (dari ENV, JANGAN hardcode secret)
        // Isi via: TRIPAY_API_KEY, TRIPAY_PRIVATE_KEY, TRIPAY_MERCHANT_CODE
        if (!PaymentProvider::where('code', 'tripay-sandbox')->exists()) {
            $tripayKey = env('TRIPAY_API_KEY');
            $tripayPrivate = env('TRIPAY_PRIVATE_KEY');
            $tripayMerchant = env('TRIPAY_MERCHANT_CODE');
            if ($tripayKey && $tripayPrivate && $tripayMerchant) {
                PaymentProvider::create([
                    'name' => 'Tripay Sandbox',
                    'code' => 'tripay-sandbox',
                    'api_format' => 'redirect_tripay',
                    'base_url' => 'https://tripay.co.id/api-sandbox',
                    'api_key_encrypted' => encrypt($tripayKey),
                    'server_key_encrypted' => encrypt($tripayKey),
                    'merchant_id' => $tripayMerchant,
                    'extra_config' => ['private_key' => encrypt($tripayPrivate)],
                    'is_active' => true,
                    'is_default' => false,
                    'sort_order' => 2,
                ]);
                $this->command->info('Payment Gateway: Tripay Sandbox (dari ENV)');
            } else {
                $this->command->warn('Tripay Sandbox dilewati: set TRIPAY_API_KEY/TRIPAY_PRIVATE_KEY/TRIPAY_MERCHANT_CODE dulu.');
            }
        }

        // Payment Gateway - Midtrans Sandbox (dari ENV)
        if (!PaymentProvider::where('code', 'midtrans-sandbox')->exists()) {
            $midServer = env('MIDTRANS_SERVER_KEY');
            $midClient = env('MIDTRANS_CLIENT_KEY');
            if ($midServer && $midClient) {
                PaymentProvider::create([
                    'name' => 'Midtrans Sandbox',
                    'code' => 'midtrans-sandbox',
                    'api_format' => 'redirect_snap',
                    'base_url' => 'https://app.sandbox.midtrans.com',
                    'server_key_encrypted' => encrypt($midServer),
                    'client_key_encrypted' => encrypt($midClient),
                    'is_active' => false,
                    'is_default' => false,
                    'sort_order' => 3,
                ]);
                $this->command->info('Payment Gateway: Midtrans Sandbox (dari ENV)');
            } else {
                $this->command->warn('Midtrans Sandbox dilewati: set MIDTRANS_SERVER_KEY/MIDTRANS_CLIENT_KEY dulu.');
            }
        }
    }
}
