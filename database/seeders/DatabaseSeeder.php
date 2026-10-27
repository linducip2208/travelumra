<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Branch;
use App\Models\Cluster;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\LoyaltyPoint;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Pilgrim;
use App\Models\Setting;
use App\Models\User;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Slider;
use App\Models\Testimonial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command->error('DatabaseSeeder penuh DILARANG di production (demo data + password default). Gunakan seeder spesifik.');
            return;
        }
        $this->command->info('Seeding database...');

        $branches = $this->seedBranches();
        $users = $this->seedUsers($branches);
        $pilgrims = $this->seedPilgrims($branches, $users);
        $packages = $this->seedPackages();
        $bookings = $this->seedBookings($packages, $pilgrims, $branches, $users);
        $this->seedInvoicesAndPayments($bookings, $pilgrims);
        $this->seedClusters($packages, $users, $bookings, $pilgrims);
        $this->seedLeads($packages, $users);
        $this->seedLoyaltyPoints($bookings, $pilgrims);
        $this->seedSettings();
        $this->seedCms();

        $this->command->info('Seeding complete!');
        $this->command->info('---');
        $this->command->info("Branches:   {$branches->count()}");
        $this->command->info("Users:      {$users->count()}");
        $this->command->info("Pilgrims:   {$pilgrims->count()}");
        $this->command->info("Packages:   {$packages->count()}");
        $this->command->info("Bookings:   ".count($bookings));
        $this->command->info("Clusters:   2");
        $this->command->info("Leads:      5");
    }

    private function seedBranches()
    {
        $data = [
            [
                'name' => 'Pusat',
                'code' => 'JKT-01',
                'type' => 'main',
                'parent_id' => null,
                'address' => 'Jl. KH Hasyim Ashari No. 125, Tanah Abang',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'phone' => '021-63881234',
                'email' => 'pusat@travelumrah.com',
                'bank_name' => 'Bank Syariah Indonesia',
                'bank_account' => '7123456789',
                'bank_holder' => 'PT Travel Umrah Berkah',
                'is_active' => true,
                'quota_haji' => 200,
                'quota_umrah' => 500,
                'quota_used' => 0,
                'referral_commission_pct' => 5.00,
            ],
            [
                'name' => 'Cabang Bandung',
                'code' => 'BDG-01',
                'type' => 'branch',
                'parent_id' => null,
                'address' => 'Jl. Soekarno Hatta No. 88, Kiaracondong',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'phone' => '022-20567890',
                'email' => 'bandung@travelumrah.com',
                'bank_name' => 'Bank Syariah Indonesia',
                'bank_account' => '7123456790',
                'bank_holder' => 'PT Travel Umrah Berkah',
                'is_active' => true,
                'quota_haji' => 50,
                'quota_umrah' => 150,
                'quota_used' => 0,
                'referral_commission_pct' => 5.00,
            ],
            [
                'name' => 'Cabang Surabaya',
                'code' => 'SBY-01',
                'type' => 'branch',
                'parent_id' => null,
                'address' => 'Jl. Diponegoro No. 150, Wonokromo',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'phone' => '031-50123456',
                'email' => 'surabaya@travelumrah.com',
                'bank_name' => 'Bank Syariah Indonesia',
                'bank_account' => '7123456791',
                'bank_holder' => 'PT Travel Umrah Berkah',
                'is_active' => true,
                'quota_haji' => 50,
                'quota_umrah' => 150,
                'quota_used' => 0,
                'referral_commission_pct' => 5.00,
            ],
        ];

        $branches = collect();
        foreach ($data as $item) {
            $branches->push(Branch::create($item));
        }

        $bandung = $branches[1];
        $bandung->parent_id = $branches[0]->id;
        $bandung->save();

        $surabaya = $branches[2];
        $surabaya->parent_id = $branches[0]->id;
        $surabaya->save();

        return $branches;
    }

    private function seedUsers($branches)
    {
        $users = collect();
        $pusat = $branches[0];
        $bandung = $branches[1];

        $data = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@travelumrah.com',
                'password' => 'password',
                'branch_id' => $pusat->id,
                'role' => 'super_admin',
                'phone' => '081234567890',
                'is_muthawif' => false,
                'muthawif_certification' => null,
                'muthawif_experience_years' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Admin',
                'email' => 'admin2@travelumrah.com',
                'password' => 'password',
                'branch_id' => $pusat->id,
                'role' => 'admin',
                'phone' => '081234567891',
                'is_muthawif' => false,
                'muthawif_certification' => null,
                'muthawif_experience_years' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Finance',
                'email' => 'finance@travelumrah.com',
                'password' => 'password',
                'branch_id' => $pusat->id,
                'role' => 'finance',
                'phone' => '081234567892',
                'is_muthawif' => false,
                'muthawif_certification' => null,
                'muthawif_experience_years' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Branch Manager Bandung',
                'email' => 'bm_bdg@travelumrah.com',
                'password' => 'password',
                'branch_id' => $bandung->id,
                'role' => 'branch_manager',
                'phone' => '081234567893',
                'is_muthawif' => false,
                'muthawif_certification' => null,
                'muthawif_experience_years' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Agent Jakarta',
                'email' => 'agent_jkt@travelumrah.com',
                'password' => 'password',
                'branch_id' => $pusat->id,
                'role' => 'agent',
                'phone' => '081234567894',
                'is_muthawif' => false,
                'muthawif_certification' => null,
                'muthawif_experience_years' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Muthawif Ahmad',
                'email' => 'muthawif@travelumrah.com',
                'password' => 'password',
                'branch_id' => $pusat->id,
                'role' => 'muthawif',
                'phone' => '081234567895',
                'is_muthawif' => true,
                'muthawif_certification' => 'Sertifikasi Muthawif Kemenag RI No. MTH-2020-0042',
                'muthawif_experience_years' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($data as $item) {
            $users->push(User::create($item));
        }

        return $users;
    }

    private function seedPilgrims($branches, $users)
    {
        $pusat = $branches[0];
        $bandung = $branches[1];
        $surabaya = $branches[2];
        $agent = $users[4];
        $superAdmin = $users[0];

        $pilgrims = collect();

        $data = [
            [
                'registration_number' => 'JMA-202605-0001',
                'title' => 'Bpk.',
                'full_name' => 'Ahmad Fauzi',
                'father_name' => 'Muhammad Yusuf',
                'nik' => '3174012304650001',
                'passport_number' => 'C1234567',
                'passport_issued_date' => '2024-03-15',
                'passport_expiry_date' => '2029-03-15',
                'passport_issuer' => 'Kantor Imigrasi Jakarta Pusat',
                'birth_place' => 'Jakarta',
                'birth_date' => '1965-04-23',
                'gender' => 'male',
                'marital_status' => 'married',
                'blood_type' => 'O',
                'phone' => '081298765001',
                'email' => 'ahmad.fauzi@email.com',
                'address' => 'Jl. Melati No. 12, Kemayoran',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'postal_code' => '10640',
                'emergency_name' => 'Siti Aminah',
                'emergency_relation' => 'Istri',
                'emergency_phone' => '081298765002',
                'has_meningitis_vaccine' => true,
                'meningitis_vaccine_date' => '2025-12-10',
                'has_covid_vaccine' => true,
                'source' => 'website',
                'branch_id' => $pusat->id,
                'agent_id' => $agent->id,
                'is_active' => true,

            ],
            [
                'registration_number' => 'JMA-202605-0002',
                'title' => 'Ibu',
                'full_name' => 'Siti Nurhaliza',
                'father_name' => 'Abdul Rahman',
                'nik' => '3275011005720002',
                'passport_number' => 'C2345678',
                'passport_issued_date' => '2024-05-20',
                'passport_expiry_date' => '2029-05-20',
                'passport_issuer' => 'Kantor Imigrasi Bekasi',
                'birth_place' => 'Bandung',
                'birth_date' => '1972-10-05',
                'gender' => 'female',
                'marital_status' => 'married',
                'blood_type' => 'A',
                'phone' => '081398765003',
                'email' => 'siti.nurhaliza@email.com',
                'address' => 'Jl. Cihampelas No. 45, Bandung',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40131',
                'emergency_name' => 'Ahmad Dahlan',
                'emergency_relation' => 'Suami',
                'emergency_phone' => '081398765004',
                'has_meningitis_vaccine' => true,
                'meningitis_vaccine_date' => '2026-01-05',
                'has_covid_vaccine' => true,
                'source' => 'referral',
                'branch_id' => $bandung->id,
                'agent_id' => $users[3]->id,
                'is_active' => true,            ],
            [
                'registration_number' => 'JMA-202605-0003',
                'title' => 'Bpk.',
                'full_name' => 'Rudi Hartono',
                'father_name' => 'Sutomo',
                'nik' => '3578012508800003',
                'passport_number' => 'C3456789',
                'passport_issued_date' => '2025-01-10',
                'passport_expiry_date' => '2030-01-10',
                'passport_issuer' => 'Kantor Imigrasi Surabaya',
                'birth_place' => 'Surabaya',
                'birth_date' => '1980-08-25',
                'gender' => 'male',
                'marital_status' => 'married',
                'blood_type' => 'B',
                'phone' => '081498765005',
                'email' => 'rudi.hartono@email.com',
                'address' => 'Jl. Ahmad Yani No. 78, Wonokromo',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60243',
                'emergency_name' => 'Dewi Sartika',
                'emergency_relation' => 'Istri',
                'emergency_phone' => '081498765006',
                'has_meningitis_vaccine' => false,
                'has_covid_vaccine' => true,
                'source' => 'walk_in',
                'branch_id' => $surabaya->id,
                'agent_id' => $agent->id,
                'is_active' => true,            ],
            [
                'registration_number' => 'JMA-202605-0004',
                'title' => 'Ibu',
                'full_name' => 'Mega Wati',
                'father_name' => 'Soekarno Putra',
                'nik' => '3174024109750004',
                'passport_number' => 'C4567890',
                'passport_issued_date' => '2024-08-05',
                'passport_expiry_date' => '2029-08-05',
                'passport_issuer' => 'Kantor Imigrasi Jakarta Selatan',
                'birth_place' => 'Medan',
                'birth_date' => '1975-09-01',
                'gender' => 'female',
                'marital_status' => 'married',
                'blood_type' => 'AB',
                'phone' => '081598765007',
                'email' => 'mega.wati@email.com',
                'address' => 'Jl. Sudirman No. 99, Kebayoran Baru',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12190',
                'emergency_name' => 'Andi Pratama',
                'emergency_relation' => 'Anak',
                'emergency_phone' => '081598765008',
                'has_meningitis_vaccine' => true,
                'meningitis_vaccine_date' => '2025-11-20',
                'has_covid_vaccine' => true,
                'source' => 'facebook',
                'branch_id' => $pusat->id,
                'agent_id' => $agent->id,
                'is_active' => true,

            ],
            [
                'registration_number' => 'JMA-202605-0005',
                'title' => 'Bpk.',
                'full_name' => 'Haji Bambang',
                'father_name' => 'Sumarno',
                'nik' => '3275025503600005',
                'passport_number' => 'C5678901',
                'passport_issued_date' => '2025-06-12',
                'passport_expiry_date' => '2030-06-12',
                'passport_issuer' => 'Kantor Imigrasi Bandung',
                'birth_place' => 'Yogyakarta',
                'birth_date' => '1960-03-15',
                'gender' => 'male',
                'marital_status' => 'married',
                'blood_type' => 'O',
                'phone' => '081698765009',
                'email' => 'haji.bambang@email.com',
                'address' => 'Jl. Dago No. 22, Coblong',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40135',
                'emergency_name' => 'Sri Rahayu',
                'emergency_relation' => 'Istri',
                'emergency_phone' => '081698765010',
                'has_meningitis_vaccine' => true,
                'meningitis_vaccine_date' => '2025-10-01',
                'has_covid_vaccine' => true,
                'source' => 'instagram',
                'branch_id' => $bandung->id,
                'agent_id' => $users[3]->id,
                'is_active' => true,            ],
            [
                'registration_number' => 'JMA-202605-0006',
                'title' => 'Bpk.',
                'full_name' => 'Doni Kusuma',
                'father_name' => 'Herman Kusuma',
                'nik' => '3578021007850006',
                'passport_number' => 'C6789012',
                'passport_issued_date' => '2025-02-18',
                'passport_expiry_date' => '2030-02-18',
                'passport_issuer' => 'Kantor Imigrasi Malang',
                'birth_place' => 'Malang',
                'birth_date' => '1985-07-10',
                'gender' => 'male',
                'marital_status' => 'single',
                'blood_type' => 'A',
                'phone' => '081798765011',
                'email' => 'doni.kusuma@email.com',
                'address' => 'Jl. Sudirman No. 55, Genteng',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60275',
                'emergency_name' => 'Herman Kusuma',
                'emergency_relation' => 'Ayah',
                'emergency_phone' => '081798765012',
                'has_meningitis_vaccine' => true,
                'meningitis_vaccine_date' => '2026-01-15',
                'has_covid_vaccine' => true,
                'source' => 'walk_in',
                'branch_id' => $surabaya->id,
                'agent_id' => $agent->id,
                'is_active' => true,

            ],
            [
                'registration_number' => 'JMA-202605-0007',
                'title' => 'Ibu',
                'full_name' => 'Nurul Huda',
                'father_name' => 'Muhammad Ali',
                'nik' => '3174034802880007',
                'passport_number' => 'C7890123',
                'passport_issued_date' => '2024-11-30',
                'passport_expiry_date' => '2029-11-30',
                'passport_issuer' => 'Kantor Imigrasi Tangerang',
                'birth_place' => 'Semarang',
                'birth_date' => '1988-02-28',
                'gender' => 'female',
                'marital_status' => 'married',
                'blood_type' => 'B',
                'phone' => '081898765013',
                'email' => 'nurul.huda@email.com',
                'address' => 'Jl. Gajah Mada No. 33, Cengkareng',
                'city' => 'Jakarta Barat',
                'province' => 'DKI Jakarta',
                'postal_code' => '11750',
                'emergency_name' => 'Hasan Basri',
                'emergency_relation' => 'Suami',
                'emergency_phone' => '081898765014',
                'has_meningitis_vaccine' => false,
                'has_covid_vaccine' => true,
                'source' => 'website',
                'branch_id' => $pusat->id,
                'agent_id' => $agent->id,
                'is_active' => true,

            ],
            [
                'registration_number' => 'JMA-202605-0008',
                'title' => 'Bpk.',
                'full_name' => 'Abdul Malik',
                'father_name' => 'Ibrahim',
                'nik' => '3174054207550008',
                'passport_number' => 'C8901234',
                'passport_issued_date' => '2025-07-22',
                'passport_expiry_date' => '2030-07-22',
                'passport_issuer' => 'Kantor Imigrasi Jakarta Timur',
                'birth_place' => 'Padang',
                'birth_date' => '1955-07-01',
                'gender' => 'male',
                'marital_status' => 'married',
                'blood_type' => 'O',
                'phone' => '081998765015',
                'email' => 'abdul.malik@email.com',
                'address' => 'Jl. Matraman No. 67, Matraman',
                'city' => 'Jakarta Timur',
                'province' => 'DKI Jakarta',
                'postal_code' => '13120',
                'emergency_name' => 'Fatimah Zahra',
                'emergency_relation' => 'Istri',
                'emergency_phone' => '081998765016',
                'has_meningitis_vaccine' => true,
                'meningitis_vaccine_date' => '2025-09-05',
                'has_covid_vaccine' => true,
                'source' => 'referral',
                'branch_id' => $pusat->id,
                'agent_id' => $agent->id,
                'is_active' => true,

            ],
            [
                'registration_number' => 'JMA-202605-0009',
                'title' => 'Ibu',
                'full_name' => 'Lina Marlina',
                'father_name' => 'Ujang Suryana',
                'nik' => '3275035109900009',
                'passport_number' => 'C9012345',
                'passport_issued_date' => '2025-09-10',
                'passport_expiry_date' => '2030-09-10',
                'passport_issuer' => 'Kantor Imigrasi Cimahi',
                'birth_place' => 'Garut',
                'birth_date' => '1990-09-15',
                'gender' => 'female',
                'marital_status' => 'single',
                'blood_type' => 'AB',
                'phone' => '082198765017',
                'email' => 'lina.marlina@email.com',
                'address' => 'Jl. Merdeka No. 11, Sumurbandung',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40111',
                'emergency_name' => 'Ujang Suryana',
                'emergency_relation' => 'Ayah',
                'emergency_phone' => '082198765018',
                'has_meningitis_vaccine' => true,
                'meningitis_vaccine_date' => '2026-02-01',
                'has_covid_vaccine' => true,
                'source' => 'instagram',
                'branch_id' => $bandung->id,
                'agent_id' => $users[3]->id,
                'is_active' => true,            ],
            [
                'registration_number' => 'JMA-202605-0010',
                'title' => 'Bpk.',
                'full_name' => 'Yusuf Mansur',
                'father_name' => 'Abdullah',
                'nik' => '3578032711720010',
                'passport_number' => 'C0123456',
                'passport_issued_date' => '2024-04-25',
                'passport_expiry_date' => '2029-04-25',
                'passport_issuer' => 'Kantor Imigrasi Surabaya',
                'birth_place' => 'Gresik',
                'birth_date' => '1972-11-27',
                'gender' => 'male',
                'marital_status' => 'married',
                'blood_type' => 'A',
                'phone' => '082298765019',
                'email' => 'yusuf.mansur@email.com',
                'address' => 'Jl. Raya Gresik No. 44, Kebomas',
                'city' => 'Gresik',
                'province' => 'Jawa Timur',
                'postal_code' => '61121',
                'emergency_name' => 'Aisyah',
                'emergency_relation' => 'Istri',
                'emergency_phone' => '082298765020',
                'has_meningitis_vaccine' => true,
                'meningitis_vaccine_date' => '2025-08-15',
                'has_covid_vaccine' => true,
                'source' => 'walk_in',
                'branch_id' => $surabaya->id,
                'agent_id' => $agent->id,
                'is_active' => true,

            ],
            [
                'registration_number' => 'JMA-202605-0011',
                'title' => 'Ibu',
                'full_name' => 'Ratna Dewi',
                'father_name' => 'Kartono',
                'nik' => '3174106519820011',
                'passport_number' => 'C1112223',
                'passport_issued_date' => '2025-03-01',
                'passport_expiry_date' => '2030-03-01',
                'passport_issuer' => 'Kantor Imigrasi Bogor',
                'birth_place' => 'Bogor',
                'birth_date' => '1982-01-05',
                'gender' => 'female',
                'marital_status' => 'widowed',
                'blood_type' => 'O',
                'phone' => '082398765021',
                'email' => 'ratna.dewi@email.com',
                'address' => 'Jl. Pajajaran No. 77, Bogor Tengah',
                'city' => 'Bogor',
                'province' => 'Jawa Barat',
                'postal_code' => '16121',
                'emergency_name' => 'Rina Kartika',
                'emergency_relation' => 'Anak',
                'emergency_phone' => '082398765022',
                'has_meningitis_vaccine' => true,
                'meningitis_vaccine_date' => '2026-01-20',
                'has_covid_vaccine' => true,
                'source' => 'website',
                'branch_id' => $pusat->id,
                'agent_id' => $agent->id,
                'is_active' => true,

            ],
            [
                'registration_number' => 'JMA-202605-0012',
                'title' => 'Bpk.',
                'full_name' => 'Taufik Hidayat',
                'father_name' => 'Ruslan',
                'nik' => '3174051706780012',
                'passport_number' => 'C2223334',
                'passport_issued_date' => '2025-05-18',
                'passport_expiry_date' => '2030-05-18',
                'passport_issuer' => 'Kantor Imigrasi Depok',
                'birth_place' => 'Depok',
                'birth_date' => '1978-06-17',
                'gender' => 'male',
                'marital_status' => 'married',
                'blood_type' => 'B',
                'phone' => '082498765023',
                'email' => 'taufik.hidayat@email.com',
                'address' => 'Jl. Margonda No. 90, Pancoran Mas',
                'city' => 'Depok',
                'province' => 'Jawa Barat',
                'postal_code' => '16424',
                'emergency_name' => 'Nurhayati',
                'emergency_relation' => 'Istri',
                'emergency_phone' => '082498765024',
                'has_meningitis_vaccine' => false,
                'has_covid_vaccine' => true,
                'source' => 'walk_in',
                'branch_id' => $pusat->id,
                'agent_id' => $agent->id,
                'is_active' => true,

            ],
            [
                'registration_number' => 'JMA-202605-0013',
                'title' => 'Ibu',
                'full_name' => 'Hana Sofia',
                'father_name' => 'Rahmat Hidayat',
                'nik' => '3275104501950013',
                'passport_number' => 'C3334445',
                'passport_issued_date' => '2025-11-08',
                'passport_expiry_date' => '2030-11-08',
                'passport_issuer' => 'Kantor Imigrasi Cirebon',
                'birth_place' => 'Cirebon',
                'birth_date' => '1995-01-15',
                'gender' => 'female',
                'marital_status' => 'single',
                'blood_type' => 'A',
                'phone' => '082598765025',
                'email' => 'hana.sofia@email.com',
                'address' => 'Jl. Siliwangi No. 12, Kejaksan',
                'city' => 'Cirebon',
                'province' => 'Jawa Barat',
                'postal_code' => '45122',
                'emergency_name' => 'Rahmat Hidayat',
                'emergency_relation' => 'Ayah',
                'emergency_phone' => '082598765026',
                'has_meningitis_vaccine' => true,
                'meningitis_vaccine_date' => '2026-02-10',
                'has_covid_vaccine' => true,
                'source' => 'facebook',
                'branch_id' => $bandung->id,
                'agent_id' => $users[3]->id,
                'is_active' => true,            ],
            [
                'registration_number' => 'JMA-202605-0014',
                'title' => 'Bpk.',
                'full_name' => 'Firman Syahputra',
                'father_name' => 'Syamsul Bahri',
                'nik' => '3578051209900014',
                'passport_number' => 'C4445556',
                'passport_issued_date' => '2025-04-22',
                'passport_expiry_date' => '2030-04-22',
                'passport_issuer' => 'Kantor Imigrasi Sidoarjo',
                'birth_place' => 'Sidoarjo',
                'birth_date' => '1990-09-12',
                'gender' => 'male',
                'marital_status' => 'single',
                'blood_type' => 'O',
                'phone' => '082698765027',
                'email' => 'firman.syahputra@email.com',
                'address' => 'Jl. Pahlawan No. 33, Sidoarjo',
                'city' => 'Sidoarjo',
                'province' => 'Jawa Timur',
                'postal_code' => '61213',
                'emergency_name' => 'Syamsul Bahri',
                'emergency_relation' => 'Ayah',
                'emergency_phone' => '082698765028',
                'has_meningitis_vaccine' => true,
                'meningitis_vaccine_date' => '2026-03-01',
                'has_covid_vaccine' => true,
                'source' => 'instagram',
                'branch_id' => $surabaya->id,
                'agent_id' => $agent->id,
                'is_active' => true,

            ],
            [
                'registration_number' => 'JMA-202605-0015',
                'title' => 'Bpk.',
                'full_name' => 'Zulkifli Hasan',
                'father_name' => 'Hasanuddin',
                'nik' => '3174036304680015',
                'passport_number' => 'C5556667',
                'passport_issued_date' => '2024-12-15',
                'passport_expiry_date' => '2029-12-15',
                'passport_issuer' => 'Kantor Imigrasi Jakarta Pusat',
                'birth_place' => 'Palembang',
                'birth_date' => '1968-04-03',
                'gender' => 'male',
                'marital_status' => 'married',
                'blood_type' => 'AB',
                'phone' => '082798765029',
                'email' => 'zulkifli.hasan@email.com',
                'address' => 'Jl. Veteran No. 56, Gambir',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'postal_code' => '10110',
                'emergency_name' => 'Nur Asiah',
                'emergency_relation' => 'Istri',
                'emergency_phone' => '082798765030',
                'has_meningitis_vaccine' => true,
                'meningitis_vaccine_date' => '2025-07-10',
                'has_covid_vaccine' => true,
                'source' => 'referral',
                'branch_id' => $pusat->id,
                'agent_id' => $agent->id,
                'is_active' => true,

            ],
        ];

        foreach ($data as $item) {
            $pilgrims->push(Pilgrim::create($item));
        }

        return $pilgrims;
    }

    private function seedPackages()
    {
        $packages = collect();

        $data = [
            [
                'code' => 'UMR-RAM2026',
                'name' => 'Umrah Ramadhan 2026',
                'slug' => 'umrah-ramadhan-2026',
                'type' => 'umrah',
                'category' => 'vip',
                'airline' => 'Saudia Airlines',
                'departure_airport' => 'CGK',
                'departure_date' => '2026-02-20',
                'return_date' => '2026-03-04',
                'duration_days' => 12,
                'makkah_hotel_name' => 'Swissotel Al Maqam Makkah',
                'makkah_hotel_stars' => 5,
                'makkah_hotel_distance_meters' => 300,
                'makkah_hotel_room_type' => 'Quad',
                'madinah_hotel_name' => 'Pullman Zamzam Madinah',
                'madinah_hotel_stars' => 5,
                'madinah_hotel_distance_meters' => 200,
                'price_single' => 52000000,
                'price_double' => 45000000,
                'price_triple' => 41000000,
                'price_quad' => 38500000,
                'quota' => 100,
                'booked_count' => 0,
                'available_count' => 100,
                'itinerary' => json_encode(['Hari 1: Keberangkatan CGK-JED', 'Hari 2-8: Ibadah di Makkah', 'Hari 9-11: Madinah', 'Hari 12: Kepulangan MED-CGK']),
                'includes' => json_encode(['Tiket pesawat PP', 'Hotel bintang 5', 'Visa umrah', 'Bus AC', 'Muthawif berpengalaman', 'Konsumsi 3x sehari']),
                'excludes' => json_encode(['Pengeluaran pribadi', 'Kelebihan bagasi', 'Asuransi perjalanan']),
                'terms' => 'Pembayaran DP 50% saat booking, pelunasan H-30 keberangkatan.',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'UMR-SYW2026',
                'name' => 'Umrah Syawal 2026',
                'slug' => 'umrah-syawal-2026',
                'type' => 'umrah',
                'category' => 'reguler',
                'airline' => 'Garuda Indonesia',
                'departure_airport' => 'CGK',
                'departure_date' => '2026-04-10',
                'return_date' => '2026-04-19',
                'duration_days' => 9,
                'makkah_hotel_name' => 'Movenpick Hajar Tower Makkah',
                'makkah_hotel_stars' => 4,
                'makkah_hotel_distance_meters' => 500,
                'makkah_hotel_room_type' => 'Quad',
                'madinah_hotel_name' => 'Al Haram Hotel Madinah',
                'madinah_hotel_stars' => 4,
                'madinah_hotel_distance_meters' => 400,
                'price_single' => 38000000,
                'price_double' => 33000000,
                'price_triple' => 30500000,
                'price_quad' => 28500000,
                'quota' => 80,
                'booked_count' => 0,
                'available_count' => 80,
                'itinerary' => json_encode(['Hari 1: Jakarta-Jeddah', 'Hari 2-6: Makkah', 'Hari 7-8: Madinah', 'Hari 9: Madinah-Jakarta']),
                'includes' => json_encode(['Tiket PP Garuda', 'Hotel bintang 4', 'Visa umrah', 'Transportasi darat', 'Muthawif', 'Makan 2x sehari']),
                'excludes' => json_encode(['Pengeluaran pribadi', 'Asuransi', 'Laundry']),
                'terms' => 'DP 50%, pelunasan H-21.',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'UMR-DES2026',
                'name' => 'Umrah Desember 2026',
                'slug' => 'umrah-desember-2026',
                'type' => 'umrah',
                'category' => 'reguler',
                'airline' => 'Lion Air',
                'departure_airport' => 'SUB',
                'departure_date' => '2026-12-15',
                'return_date' => '2026-12-24',
                'duration_days' => 9,
                'makkah_hotel_name' => 'Anjum Hotel Makkah',
                'makkah_hotel_stars' => 3,
                'makkah_hotel_distance_meters' => 800,
                'makkah_hotel_room_type' => 'Quad',
                'madinah_hotel_name' => 'Al Eiman Royal Hotel',
                'madinah_hotel_stars' => 3,
                'madinah_hotel_distance_meters' => 600,
                'price_single' => 34000000,
                'price_double' => 29000000,
                'price_triple' => 27000000,
                'price_quad' => 25000000,
                'quota' => 120,
                'booked_count' => 0,
                'available_count' => 120,
                'itinerary' => json_encode(['Hari 1: Surabaya-Jeddah', 'Hari 2-6: Makkah', 'Hari 7-8: Madinah', 'Hari 9: Pulang']),
                'includes' => json_encode(['Tiket PP Lion Air', 'Hotel bintang 3', 'Visa umrah', 'Bus', 'Muthawif']),
                'excludes' => json_encode(['Makan siang & malam', 'Laundry', 'Tips']),
                'terms' => 'DP 30%, pelunasan H-14.',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
            ],
            [
                'code' => 'HAJI-PLUS2026',
                'name' => 'Haji Plus 2026',
                'slug' => 'haji-plus-2026',
                'type' => 'haji_plus',
                'category' => 'vip',
                'airline' => 'Saudia Airlines',
                'departure_airport' => 'CGK',
                'departure_date' => '2026-06-05',
                'return_date' => '2026-06-30',
                'duration_days' => 25,
                'makkah_hotel_name' => 'Fairmont Makkah Clock Royal Tower',
                'makkah_hotel_stars' => 5,
                'makkah_hotel_distance_meters' => 100,
                'makkah_hotel_room_type' => 'Quad',
                'madinah_hotel_name' => 'The Oberoi Madinah',
                'madinah_hotel_stars' => 5,
                'madinah_hotel_distance_meters' => 150,
                'price_single' => 220000000,
                'price_double' => 200000000,
                'price_triple' => 190000000,
                'price_quad' => 180000000,
                'quota' => 40,
                'booked_count' => 0,
                'available_count' => 40,
                'itinerary' => json_encode(['Hari 1-2: Keberangkatan', 'Hari 3-15: Makkah (Haji)', 'Hari 16-23: Madinah', 'Hari 24-25: Kepulangan']),
                'includes' => json_encode(['Tiket PP Saudia', 'Hotel bintang 5', 'Visa haji', 'Bus AC eksklusif', 'Muthawif 24 jam', 'Konsumsi full board', 'Manasik haji', 'Perlengkapan haji']),
                'excludes' => json_encode(['Dam/hadyu', 'Pengeluaran pribadi', 'Kelebihan bagasi']),
                'terms' => 'DP 25%, cicilan 3x. Pelunasan H-60. Syarat: usia maks 65 tahun, kesehatan prima.',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 4,
            ],
            [
                'code' => 'UMR-BACK2026',
                'name' => 'Umrah Backpacker 2026',
                'slug' => 'umrah-backpacker-2026',
                'type' => 'umrah',
                'category' => 'reguler',
                'airline' => 'AirAsia',
                'departure_airport' => 'KNO',
                'departure_date' => '2026-07-20',
                'return_date' => '2026-07-29',
                'duration_days' => 9,
                'makkah_hotel_name' => 'Elaf Kinda Hotel',
                'makkah_hotel_stars' => 3,
                'makkah_hotel_distance_meters' => 1200,
                'makkah_hotel_room_type' => 'Quad',
                'madinah_hotel_name' => 'Al Mukhtara International Hotel',
                'madinah_hotel_stars' => 3,
                'madinah_hotel_distance_meters' => 900,
                'price_single' => 30000000,
                'price_double' => 26000000,
                'price_triple' => 24000000,
                'price_quad' => 22000000,
                'quota' => 150,
                'booked_count' => 0,
                'available_count' => 150,
                'itinerary' => json_encode(['Hari 1: Medan-KL-Jeddah', 'Hari 2-6: Makkah', 'Hari 7-8: Madinah', 'Hari 9: Pulang']),
                'includes' => json_encode(['Tiket PP AirAsia', 'Hotel bintang 3', 'Visa umrah', 'Transportasi', 'Muthawif']),
                'excludes' => json_encode(['Makan', 'Bagasi tambahan', 'Tips']),
                'terms' => 'DP 30%, pelunasan H-14.',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 5,
            ],
        ];

        foreach ($data as $item) {
            $packages->push(Package::create($item));
        }

        return $packages;
    }

    private function seedBookings($packages, $pilgrims, $branches, $users)
    {
        $pusat = $branches[0];
        $bandung = $branches[1];
        $surabaya = $branches[2];
        $agent = $users[4];
        $bmBandung = $users[3];

        $bookings = [];

        $pkgRamadhan = $packages[0];
        $pkgSyawal = $packages[1];
        $pkgDesember = $packages[2];
        $pkgHaji = $packages[3];
        $pkgBackpacker = $packages[4];

        $statuses = [
            ['status' => 'confirmed', 'confirmed_date' => '2026-01-10', 'paid_amount' => 38500000],
            ['status' => 'returned', 'confirmed_date' => '2026-01-15', 'paid_amount' => 38500000],
            ['status' => 'confirmed', 'confirmed_date' => '2026-03-01', 'paid_amount' => 28500000],
            ['status' => 'returned', 'confirmed_date' => '2026-03-10', 'paid_amount' => 28500000],
            ['status' => 'pending', 'confirmed_date' => null, 'paid_amount' => 5000000],
            ['status' => 'confirmed', 'confirmed_date' => '2026-05-15', 'paid_amount' => 12500000],
            ['status' => 'pending', 'confirmed_date' => null, 'paid_amount' => 10000000],
            ['status' => 'confirmed', 'confirmed_date' => '2026-04-20', 'paid_amount' => 90000000],
            ['status' => 'pending', 'confirmed_date' => null, 'paid_amount' => 5000000],
            ['status' => 'cancelled', 'confirmed_date' => '2026-02-01', 'paid_amount' => 5000000, 'cancelled_date' => '2026-03-20', 'cancellation_reason' => 'Alasan kesehatan'],
        ];

        $bookingData = [
            // Package 1 (Ramadhan) - 3 bookings
            [
                'package_id' => $pkgRamadhan->id,
                'pilgrim_id' => $pilgrims[0]->id,
                'room_type' => 'quad',
                'agent_id' => $agent->id,
                'branch_id' => $pusat->id,
                'package_price' => 38500000,
                'additional_cost' => 500000,
                'discount' => 0,
                'total_price' => 39000000,
                'referral_by' => $pilgrims[4]->id,
                'referral_commission' => 1925000,
                'notes' => 'Diet khusus vegetarian',
            ],
            [
                'package_id' => $pkgRamadhan->id,
                'pilgrim_id' => $pilgrims[4]->id,
                'room_type' => 'quad',
                'agent_id' => $agent->id,
                'branch_id' => $pusat->id,
                'package_price' => 38500000,
                'additional_cost' => 0,
                'discount' => 500000,
                'total_price' => 38000000,
                'referral_commission' => 0,
                'notes' => null,
            ],
            [
                'package_id' => $pkgRamadhan->id,
                'pilgrim_id' => $pilgrims[7]->id,
                'room_type' => 'quad',
                'agent_id' => $agent->id,
                'branch_id' => $pusat->id,
                'package_price' => 38500000,
                'additional_cost' => 1000000,
                'discount' => 0,
                'total_price' => 39500000,
                'referral_by' => $pilgrims[1]->id,
                'referral_commission' => 1925000,
                'notes' => 'Kursi roda',
            ],
            // Package 2 (Syawal) - 2 bookings
            [
                'package_id' => $pkgSyawal->id,
                'pilgrim_id' => $pilgrims[1]->id,
                'room_type' => 'quad',
                'agent_id' => $bmBandung->id,
                'branch_id' => $bandung->id,
                'package_price' => 28500000,
                'additional_cost' => 0,
                'discount' => 0,
                'total_price' => 28500000,
                'referral_commission' => 0,
                'notes' => null,
            ],
            [
                'package_id' => $pkgSyawal->id,
                'pilgrim_id' => $pilgrims[8]->id,
                'room_type' => 'quad',
                'agent_id' => $bmBandung->id,
                'branch_id' => $bandung->id,
                'package_price' => 28500000,
                'additional_cost' => 750000,
                'discount' => 0,
                'total_price' => 29250000,
                'referral_commission' => 0,
                'notes' => 'Perlu asuransi tambahan',
            ],
            // Package 3 (Desember) - 2 bookings
            [
                'package_id' => $pkgDesember->id,
                'pilgrim_id' => $pilgrims[2]->id,
                'room_type' => 'quad',
                'agent_id' => $agent->id,
                'branch_id' => $surabaya->id,
                'package_price' => 25000000,
                'additional_cost' => 0,
                'discount' => 500000,
                'total_price' => 24500000,
                'referral_by' => $pilgrims[0]->id,
                'referral_commission' => 1225000,
                'notes' => null,
            ],
            [
                'package_id' => $pkgDesember->id,
                'pilgrim_id' => $pilgrims[5]->id,
                'room_type' => 'quad',
                'agent_id' => $agent->id,
                'branch_id' => $surabaya->id,
                'package_price' => 25000000,
                'additional_cost' => 500000,
                'discount' => 0,
                'total_price' => 25500000,
                'referral_commission' => 0,
                'notes' => 'Permintaan kamar dekat lift',
            ],
            // Package 4 (Haji Plus) - 2 bookings
            [
                'package_id' => $pkgHaji->id,
                'pilgrim_id' => $pilgrims[10]->id,
                'room_type' => 'quad',
                'agent_id' => $agent->id,
                'branch_id' => $pusat->id,
                'package_price' => 180000000,
                'additional_cost' => 2500000,
                'discount' => 0,
                'total_price' => 182500000,
                'referral_by' => $pilgrims[11]->id,
                'referral_commission' => 9000000,
                'notes' => 'Bersama istri (booking terpisah)',
            ],
            [
                'package_id' => $pkgHaji->id,
                'pilgrim_id' => $pilgrims[14]->id,
                'room_type' => 'quad',
                'agent_id' => $agent->id,
                'branch_id' => $pusat->id,
                'package_price' => 180000000,
                'additional_cost' => 0,
                'discount' => 5000000,
                'total_price' => 175000000,
                'referral_commission' => 0,
                'notes' => 'Referral dari jamaah lama',
            ],
            // Package 5 (Backpacker) - 1 booking
            [
                'package_id' => $pkgBackpacker->id,
                'pilgrim_id' => $pilgrims[12]->id,
                'room_type' => 'quad',
                'agent_id' => $agent->id,
                'branch_id' => $pusat->id,
                'package_price' => 22000000,
                'additional_cost' => 0,
                'discount' => 2000000,
                'total_price' => 20000000,
                'referral_commission' => 0,
                'notes' => 'Mahasiswa, potongan khusus',
            ],
        ];

        foreach ($bookingData as $i => $data) {
            $data['booking_number'] = Booking::generateBookingNumber();
            $data['status'] = $statuses[$i]['status'];
            $data['confirmed_date'] = $statuses[$i]['confirmed_date'];
            $data['paid_amount'] = $statuses[$i]['paid_amount'];
            $data['remaining_amount'] = $data['total_price'] - $data['paid_amount'];

            if (isset($statuses[$i]['cancelled_date'])) {
                $data['cancelled_date'] = $statuses[$i]['cancelled_date'];
                $data['cancellation_reason'] = $statuses[$i]['cancellation_reason'];
            }

            $bookings[] = Booking::create($data);
        }

        return $bookings;
    }

    private function seedInvoicesAndPayments($bookings, $pilgrims)
    {
        foreach ($bookings as $i => $booking) {
            if ($booking->status === 'cancelled') {
                continue;
            }

            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'booking_id' => $booking->id,
                'pilgrim_id' => $booking->pilgrim_id,
                'total_amount' => $booking->total_price,
                'paid_amount' => $booking->paid_amount,
                'remaining_amount' => $booking->total_price - $booking->paid_amount,
                'status' => $booking->paid_amount >= $booking->total_price ? 'paid' : ($booking->paid_amount > 0 ? 'partial' : 'unpaid'),
                'due_date' => ($booking->status === 'confirmed') ? now()->addDays(rand(7, 30)) : now()->subDays(rand(10, 60)),
            ]);

            if ($booking->paid_amount > 0) {
                Payment::create([
                    'payment_number' => Payment::generatePaymentNumber(),
                    'invoice_id' => $invoice->id,
                    'booking_id' => $booking->id,
                    'amount' => $booking->paid_amount,
                    'payment_method' => 'transfer',
                    'bank_name' => 'BSI',
                    'bank_account' => '7123456789',
                    'bank_holder' => 'PT Travel Umrah Berkah',
                    'payment_date' => $booking->confirmed_date ?? now()->subDays(rand(5, 20))->format('Y-m-d'),
                    'status' => 'confirmed',
                    'notes' => 'Pembayaran diterima',
                ]);
            }
        }
    }

    private function seedClusters($packages, $users, $bookings, $pilgrims)
    {
        $muthawif = $users[5];

        $cluster1 = Cluster::create([
            'code' => 'CL-001-RAM',
            'name' => 'Kloter Ramadhan A',
            'package_id' => $packages[0]->id,
            'muthawif_id' => $muthawif->id,
            'capacity' => 50,
            'booked_count' => 3,
            'status' => 'completed',
        ]);

        $cluster2 = Cluster::create([
            'code' => 'CL-002-SYW',
            'name' => 'Kloter Syawal A',
            'package_id' => $packages[1]->id,
            'muthawif_id' => $muthawif->id,
            'capacity' => 40,
            'booked_count' => 2,
            'status' => 'completed',
        ]);

        foreach ([$bookings[0], $bookings[1], $bookings[2]] as $idx => $booking) {
            $cluster1->bookings()->attach($booking->id, [
                'seat_number' => 'A' . ($idx + 1),
                'room_number' => '10' . ($idx + 1),
                'catering_group' => 'A',
            ]);
        }

        foreach ([$bookings[3], $bookings[4]] as $idx => $booking) {
            $cluster2->bookings()->attach($booking->id, [
                'seat_number' => 'B' . ($idx + 1),
                'room_number' => '20' . ($idx + 1),
                'catering_group' => 'B',
            ]);
        }
    }

    private function seedLeads($packages, $users)
    {
        $agent = $users[4];
        $bmBandung = $users[3];

        $leads = [
            [
                'name' => 'Hendra Gunawan',
                'phone' => '081356789012',
                'email' => 'hendra.gunawan@email.com',
                'city' => 'Jakarta Utara',
                'interested_package_id' => $packages[3]->id,
                'source' => 'website',
                'status' => 'new',
                'assigned_to' => $agent->id,
                'follow_up_date' => now()->addDays(2)->format('Y-m-d'),
                'notes' => 'Tertarik Haji Plus, butuh info lebih lanjut soal fasilitas',
            ],
            [
                'name' => 'Dian Permata',
                'phone' => '081456789013',
                'email' => 'dian.permata@email.com',
                'city' => 'Tangerang',
                'interested_package_id' => $packages[0]->id,
                'source' => 'instagram',
                'status' => 'contacted',
                'assigned_to' => $agent->id,
                'follow_up_date' => now()->addDays(1)->format('Y-m-d'),
                'notes' => 'Sudah dihubungi via WA, minta brosur Umrah Ramadhan',
            ],
            [
                'name' => 'Budi Santoso',
                'phone' => '085678901234',
                'email' => null,
                'city' => 'Bandung',
                'interested_package_id' => $packages[1]->id,
                'source' => 'walk_in',
                'status' => 'new',
                'assigned_to' => $bmBandung->id,
                'follow_up_date' => now()->addDays(3)->format('Y-m-d'),
                'notes' => 'Datang langsung ke cabang Bandung, bawa KTP',
            ],
            [
                'name' => 'Rina Anggraini',
                'phone' => '082156789014',
                'email' => 'rina.anggraini@email.com',
                'city' => 'Surabaya',
                'interested_package_id' => $packages[2]->id,
                'source' => 'facebook',
                'status' => 'interested',
                'assigned_to' => $agent->id,
                'follow_up_date' => now()->addDays(5)->format('Y-m-d'),
                'notes' => 'Siap booking Umrah Desember untuk keluarga 4 orang',
            ],
            [
                'name' => 'Agus Salim',
                'phone' => '081289012345',
                'email' => 'agus.salim@email.com',
                'city' => 'Medan',
                'interested_package_id' => $packages[4]->id,
                'source' => 'website',
                'status' => 'new',
                'assigned_to' => null,
                'follow_up_date' => now()->addDays(1)->format('Y-m-d'),
                'notes' => 'Dari Medan, tertarik Umrah Backpacker via KNO',
            ],
        ];

        foreach ($leads as $lead) {
            Lead::create($lead);
        }
    }

    private function seedLoyaltyPoints($bookings, $pilgrims): void
    {
        $loyaltyData = [];

        foreach ($bookings as $booking) {
            if ($booking->status === 'confirmed' && $booking->referral_by) {
                $loyaltyData[] = [
                    'pilgrim_id' => $booking->referral_by,
                    'booking_id' => $booking->id,
                    'points' => 500,
                    'transaction_type' => 'earned',
                    'description' => "Referral booking {$booking->booking_number}",
                    'value_rupiah' => $booking->referral_commission,
                    'created_at' => $booking->confirmed_date ?? now(),
                    'updated_at' => now(),
                ];
            }
        }

        $redeemed = [
            [
                'pilgrim_id' => $pilgrims[4]->id,
                'booking_id' => null,
                'points' => 200,
                'transaction_type' => 'redeemed',
                'description' => 'Diskon tambahan paket Umrah',
                'value_rupiah' => 200000,
                'created_at' => '2026-02-15',
                'updated_at' => now(),
            ],
            [
                'pilgrim_id' => $pilgrims[0]->id,
                'booking_id' => null,
                'points' => 500,
                'transaction_type' => 'redeemed',
                'description' => 'Potongan harga Umrah berikutnya',
                'value_rupiah' => 500000,
                'created_at' => '2026-04-10',
                'updated_at' => now(),
            ],
        ];

        foreach (array_merge($loyaltyData, $redeemed) as $item) {
            LoyaltyPoint::create($item);
        }

        $this->command->info('Loyalty Points: ' . count($loyaltyData) . ' earned, ' . count($redeemed) . ' redeemed');
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Travel Umrah & Haji', 'type' => 'string', 'group' => 'marketing', 'label' => 'Nama Website', 'description' => 'Nama yang muncul di title tag dan header'],
            ['key' => 'site_brand_name', 'value' => 'UmrahKita', 'type' => 'string', 'group' => 'marketing', 'label' => 'Nama Brand', 'description' => 'Nama brand yang muncul di header, footer, dan logo teks'],
            ['key' => 'site_tagline', 'value' => 'Travel Umrah & Haji Terpercaya', 'type' => 'string', 'group' => 'marketing', 'label' => 'Tagline', 'description' => 'Tagline singkat perusahaan'],
            ['key' => 'hero_title', 'value' => 'Perjalanan Umrah & Haji Anda, <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-violet-300">Aman dan Nyaman</span>', 'type' => 'string', 'group' => 'marketing', 'label' => 'Judul Hero', 'description' => 'Judul utama di bagian hero landing page (boleh HTML span)'],
            ['key' => 'hero_subtitle', 'value' => 'Wujudkan impian beribadah ke Tanah Suci bersama kami. Layanan profesional, hotel strategis, bimbingan ibadah lengkap dari tim berpengalaman.', 'type' => 'string', 'group' => 'marketing', 'label' => 'Sub Judul Hero', 'description' => 'Teks deskripsi di bawah judul hero'],
            ['key' => 'hero_badge', 'value' => 'Terpercaya sejak 2015', 'type' => 'string', 'group' => 'marketing', 'label' => 'Badge Hero', 'description' => 'Teks badge kecil di atas judul hero (contoh: Terpercaya sejak 2015)'],
            ['key' => 'hero_cta_primary', 'value' => 'Lihat Paket Umrah', 'type' => 'string', 'group' => 'marketing', 'label' => 'CTA Utama Hero', 'description' => 'Teks tombol CTA utama di hero section'],
            ['key' => 'hero_cta_secondary', 'value' => 'Konsultasi Gratis', 'type' => 'string', 'group' => 'marketing', 'label' => 'CTA Kedua Hero', 'description' => 'Teks tombol CTA kedua di hero section'],
            ['key' => 'trust_1_title', 'value' => 'Keluarga', 'type' => 'string', 'group' => 'marketing', 'label' => 'Trust Strip 1 - Judul', 'description' => 'Judul trust strip pertama'],
            ['key' => 'trust_1_desc', 'value' => 'Paket nyaman untuk seluruh anggota keluarga', 'type' => 'string', 'group' => 'marketing', 'label' => 'Trust Strip 1 - Deskripsi', 'description' => 'Deskripsi trust strip pertama'],
            ['key' => 'trust_2_title', 'value' => 'Lansia', 'type' => 'string', 'group' => 'marketing', 'label' => 'Trust Strip 2 - Judul', 'description' => 'Judul trust strip kedua'],
            ['key' => 'trust_2_desc', 'value' => 'Fasilitas khusus dan pendampingan lansia', 'type' => 'string', 'group' => 'marketing', 'label' => 'Trust Strip 2 - Deskripsi', 'description' => 'Deskripsi trust strip kedua'],
            ['key' => 'trust_3_title', 'value' => 'Pertama Kali', 'type' => 'string', 'group' => 'marketing', 'label' => 'Trust Strip 3 - Judul', 'description' => 'Judul trust strip ketiga'],
            ['key' => 'trust_3_desc', 'value' => 'Bimbingan intensif untuk jamaah pemula', 'type' => 'string', 'group' => 'marketing', 'label' => 'Trust Strip 3 - Deskripsi', 'description' => 'Deskripsi trust strip ketiga'],
            ['key' => 'trust_4_title', 'value' => 'Grup & Perusahaan', 'type' => 'string', 'group' => 'marketing', 'label' => 'Trust Strip 4 - Judul', 'description' => 'Judul trust strip keempat'],
            ['key' => 'trust_4_desc', 'value' => 'Paket rombongan dengan harga spesial', 'type' => 'string', 'group' => 'marketing', 'label' => 'Trust Strip 4 - Deskripsi', 'description' => 'Deskripsi trust strip keempat'],
            ['key' => 'section_packages_title', 'value' => 'Paket Umrah Pilihan', 'type' => 'string', 'group' => 'marketing', 'label' => 'Judul Section Paket', 'description' => 'Judul section paket umrah di landing page'],
            ['key' => 'section_packages_subtitle', 'value' => 'Pilihan paket umrah terbaik dengan berbagai opsi keberangkatan dan fasilitas premium', 'type' => 'string', 'group' => 'marketing', 'label' => 'Sub Judul Section Paket', 'description' => 'Deskripsi di bawah judul section paket umrah'],
            ['key' => 'cta_title', 'value' => 'Siap Berangkat ke Tanah Suci?', 'type' => 'string', 'group' => 'marketing', 'label' => 'Judul CTA', 'description' => 'Judul besar di section call-to-action bawah landing page'],
            ['key' => 'cta_subtitle', 'value' => 'Konsultasikan rencana perjalanan umrah atau haji Anda dengan tim kami. Gratis, tanpa komitmen.', 'type' => 'string', 'group' => 'marketing', 'label' => 'Sub Judul CTA', 'description' => 'Deskripsi di section call-to-action bawah landing page'],
            ['key' => 'cta_btn_primary', 'value' => 'Pilih Paket Umrah', 'type' => 'string', 'group' => 'marketing', 'label' => 'Tombol CTA Utama', 'description' => 'Teks tombol utama di section CTA'],
            ['key' => 'cta_btn_secondary', 'value' => 'Hubungi Kami', 'type' => 'string', 'group' => 'marketing', 'label' => 'Tombol CTA Kedua', 'description' => 'Teks tombol kedua di section CTA'],
            ['key' => 'header_btn_text', 'value' => 'Lihat Paket', 'type' => 'string', 'group' => 'marketing', 'label' => 'Tombol Header', 'description' => 'Teks tombol CTA di header navigation'],
            ['key' => 'stat_packages_label', 'value' => 'Paket Umrah & Haji', 'type' => 'string', 'group' => 'marketing', 'label' => 'Label Stat Paket', 'description' => 'Label untuk stat jumlah paket'],
            ['key' => 'stat_pilgrims_label', 'value' => 'Jamaah Berangkat', 'type' => 'string', 'group' => 'marketing', 'label' => 'Label Stat Jamaah', 'description' => 'Label untuk stat jumlah jamaah'],
            ['key' => 'stat_branches_label', 'value' => 'Cabang di Indonesia', 'type' => 'string', 'group' => 'marketing', 'label' => 'Label Stat Cabang', 'description' => 'Label untuk stat jumlah cabang'],
            ['key' => 'stat_years_label', 'value' => 'Tahun Pengalaman', 'type' => 'string', 'group' => 'marketing', 'label' => 'Label Stat Tahun', 'description' => 'Label untuk stat tahun pengalaman'],
            ['key' => 'about_text', 'value' => '<p>UmrahKita adalah biro perjalanan umrah dan haji yang telah melayani jamaah Indonesia sejak tahun 2015. Kami berkomitmen untuk memberikan pelayanan terbaik dalam setiap perjalanan ibadah, mulai dari persiapan keberangkatan hingga kepulangan ke Tanah Air.</p><p>Dengan tim yang berpengalaman dan jaringan yang luas di Arab Saudi, kami memastikan setiap jamaah mendapatkan pengalaman ibadah yang khusyuk, aman, dan nyaman. Kepuasan dan keselamatan jamaah adalah prioritas utama kami.</p>', 'type' => 'string', 'group' => 'marketing', 'label' => 'Teks Tentang Kami', 'description' => 'Konten halaman Tentang Kami (boleh HTML paragraph)'],
            ['key' => 'about_title', 'value' => 'Tentang Kami', 'type' => 'string', 'group' => 'marketing', 'label' => 'Judul Halaman About', 'description' => 'Judul halaman Tentang Kami'],
            ['key' => 'about_subtitle', 'value' => 'Mengenal lebih dekat travel umrah dan haji terpercaya Anda', 'type' => 'string', 'group' => 'marketing', 'label' => 'Sub Judul About', 'description' => 'Deskripsi di bawah judul halaman Tentang Kami'],
            ['key' => 'vision_text', 'value' => 'Menjadi travel umrah dan haji pilihan utama masyarakat Indonesia dengan pelayanan profesional, amanah, dan berorientasi pada kenyamanan jamaah.', 'type' => 'string', 'group' => 'marketing', 'label' => 'Teks Visi', 'description' => 'Teks visi perusahaan di halaman Tentang Kami'],
            ['key' => 'mission_1', 'value' => 'Menyediakan paket umrah dan haji dengan harga kompetitif dan fasilitas berkualitas.', 'type' => 'string', 'group' => 'marketing', 'label' => 'Misi 1', 'description' => 'Misi perusahaan poin 1'],
            ['key' => 'mission_2', 'value' => 'Memberikan bimbingan ibadah yang lengkap oleh mutawwif berpengalaman.', 'type' => 'string', 'group' => 'marketing', 'label' => 'Misi 2', 'description' => 'Misi perusahaan poin 2'],
            ['key' => 'mission_3', 'value' => 'Menjalin kemitraan dengan hotel dan maskapai terbaik untuk kenyamanan jamaah.', 'type' => 'string', 'group' => 'marketing', 'label' => 'Misi 3', 'description' => 'Misi perusahaan poin 3'],
            ['key' => 'mission_4', 'value' => 'Memberikan pelayanan prima dengan prinsip amanah dan transparan.', 'type' => 'string', 'group' => 'marketing', 'label' => 'Misi 4', 'description' => 'Misi perusahaan poin 4'],
            ['key' => 'footer_about', 'value' => 'Travel umrah dan haji terpercaya melayani jamaah Indonesia sejak 2015.', 'type' => 'string', 'group' => 'marketing', 'label' => 'Teks Footer', 'description' => 'Deskripsi singkat di footer landing page'],
            ['key' => 'footer_copyright', 'value' => 'Seluruh hak cipta dilindungi.', 'type' => 'string', 'group' => 'marketing', 'label' => 'Teks Copyright Footer', 'description' => 'Teks copyright di bagian bawah footer'],
            ['key' => 'contact_title', 'value' => 'Hubungi Kami', 'type' => 'string', 'group' => 'contact', 'label' => 'Judul Halaman Kontak', 'description' => 'Judul halaman kontak'],
            ['key' => 'contact_subtitle', 'value' => 'Konsultasikan rencana perjalanan umrah atau haji Anda. Tim kami siap membantu, gratis tanpa komitmen.', 'type' => 'string', 'group' => 'contact', 'label' => 'Sub Judul Kontak', 'description' => 'Deskripsi di bawah judul halaman kontak'],
            ['key' => 'contact_form_title', 'value' => 'Kirim Pesan', 'type' => 'string', 'group' => 'contact', 'label' => 'Judul Form Kontak', 'description' => 'Judul form di halaman kontak'],
            ['key' => 'contact_form_btn', 'value' => 'Kirim Pesan', 'type' => 'string', 'group' => 'contact', 'label' => 'Tombol Form Kontak', 'description' => 'Teks tombol submit form kontak'],
            ['key' => 'contact_phone', 'value' => '021-xxxx-xxxx', 'type' => 'string', 'group' => 'contact', 'label' => 'Nomor Telepon', 'description' => 'Nomor telepon yang muncul di halaman kontak dan footer'],
            ['key' => 'contact_whatsapp', 'value' => '08xxxxxxxxxx', 'type' => 'string', 'group' => 'contact', 'label' => 'Nomor WhatsApp', 'description' => 'Nomor WhatsApp untuk kontak cepat'],
            ['key' => 'contact_email', 'value' => 'info@umrahkita.id', 'type' => 'string', 'group' => 'contact', 'label' => 'Email', 'description' => 'Alamat email yang muncul di halaman kontak dan footer'],
            ['key' => 'contact_address', 'value' => 'Jl. Contoh No. 123, Jakarta Pusat', 'type' => 'string', 'group' => 'contact', 'label' => 'Alamat', 'description' => 'Alamat kantor yang muncul di halaman kontak'],
            ['key' => 'contact_hours_weekday', 'value' => '09:00 - 18:00 WIB', 'type' => 'string', 'group' => 'contact', 'label' => 'Jam Operasional (Senin-Jumat)', 'description' => 'Jam operasional hari kerja'],
            ['key' => 'contact_hours_saturday', 'value' => '09:00 - 15:00 WIB', 'type' => 'string', 'group' => 'contact', 'label' => 'Jam Operasional (Sabtu)', 'description' => 'Jam operasional hari Sabtu'],
            ['key' => 'packages_title', 'value' => 'Paket Umrah', 'type' => 'string', 'group' => 'marketing', 'label' => 'Judul Halaman Paket', 'description' => 'Judul halaman daftar paket umrah'],
            ['key' => 'packages_subtitle', 'value' => 'Temukan paket umrah dan haji yang sesuai dengan kebutuhan dan budget Anda', 'type' => 'string', 'group' => 'marketing', 'label' => 'Sub Judul Halaman Paket', 'description' => 'Deskripsi di bawah judul halaman paket'],
            ['key' => 'banner_1_badge', 'value' => 'Terpercaya Sejak 2015', 'type' => 'string', 'group' => 'marketing', 'label' => 'Banner 1 - Badge', 'description' => 'Teks badge slide 1'],
            ['key' => 'banner_1_title', 'value' => 'Perjalanan Umrah & Haji Anda, <span class="text-brand-600">Aman dan Nyaman</span>', 'type' => 'string', 'group' => 'marketing', 'label' => 'Banner 1 - Judul', 'description' => 'Judul slide 1 (boleh HTML)'],
            ['key' => 'banner_1_subtitle', 'value' => 'Wujudkan impian beribadah ke Tanah Suci bersama kami. Layanan profesional, hotel strategis, bimbingan ibadah lengkap.', 'type' => 'string', 'group' => 'marketing', 'label' => 'Banner 1 - Deskripsi', 'description' => 'Deskripsi slide 1'],
            ['key' => 'banner_1_photo', 'value' => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=1920&q=80', 'type' => 'string', 'group' => 'marketing', 'label' => 'Banner 1 - URL Foto', 'description' => 'URL gambar slide 1 (dari Unsplash atau upload sendiri)'],
            ['key' => 'banner_2_badge', 'value' => '🔥 Promo Spesial', 'type' => 'string', 'group' => 'marketing', 'label' => 'Banner 2 - Badge', 'description' => 'Teks badge slide 2'],
            ['key' => 'banner_2_title', 'value' => 'Paket <span class="text-brand-700">Umrah Ramadhan</span> 2026', 'type' => 'string', 'group' => 'marketing', 'label' => 'Banner 2 - Judul', 'description' => 'Judul slide 2 (boleh HTML)'],
            ['key' => 'banner_2_subtitle', 'value' => 'Keberangkatan Februari 2026. Hotel bintang 5 dekat Masjidil Haram. Daftar sekarang sebelum kehabisan!', 'type' => 'string', 'group' => 'marketing', 'label' => 'Banner 2 - Deskripsi', 'description' => 'Deskripsi slide 2'],
            ['key' => 'banner_2_photo', 'value' => 'https://images.unsplash.com/photo-1564769625392-651b5ef12ab6?w=1920&q=80', 'type' => 'string', 'group' => 'marketing', 'label' => 'Banner 2 - URL Foto', 'description' => 'URL gambar slide 2'],
            ['key' => 'banner_3_photo', 'value' => 'https://images.unsplash.com/photo-1565552645632-d725f8bfc19a?w=1920&q=80', 'type' => 'string', 'group' => 'marketing', 'label' => 'Banner 3 - URL Foto', 'description' => 'URL gambar slide 3'],
            ['key' => 'banner_4_photo', 'value' => 'https://images.unsplash.com/photo-1580414057403-c0570ab15096?w=1920&q=80', 'type' => 'string', 'group' => 'marketing', 'label' => 'Banner 4 - URL Foto', 'description' => 'URL gambar slide 4'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }

        $this->command->info('Settings:    ' . count($settings));
    }

    private function seedCms(): void
    {
        $category = BlogCategory::create(['name' => 'Tips Umrah', 'slug' => 'tips-umrah', 'sort_order' => 1]);
        $category2 = BlogCategory::create(['name' => 'Persiapan Haji', 'slug' => 'persiapan-haji', 'sort_order' => 2]);
        $category3 = BlogCategory::create(['name' => 'Info Visa', 'slug' => 'info-visa', 'sort_order' => 3]);

        BlogPost::create([
            'blog_category_id' => $category->id,
            'user_id' => 1,
            'title' => '10 Tips Umrah Pertama Kali untuk Jamaah Pemula',
            'slug' => '10-tips-umrah-pertama-kali',
            'excerpt' => 'Panduan lengkap bagi Anda yang baru pertama kali akan menunaikan ibadah umrah. Simak persiapan yang perlu dilakukan.',
            'content' => '<p>Menunaikan ibadah umrah untuk pertama kali adalah momen yang sangat istimewa bagi setiap Muslim. Agar perjalanan Anda berjalan lancar dan khusyuk, berikut 10 tips penting yang perlu diperhatikan:</p><h3>1. Persiapkan Dokumen dengan Lengkap</h3><p>Pastikan paspor Anda masih berlaku minimal 8 bulan sebelum keberangkatan. Siapkan juga pas foto ukuran 4x6 dengan latar belakang putih sebanyak 6 lembar.</p><h3>2. Vaksinasi Meningitis</h3><p>Vaksin meningitis adalah syarat wajib dari pemerintah Arab Saudi. Lakukan vaksinasi minimal 14 hari sebelum keberangkatan.</p><h3>3. Pelajari Manasik Umrah</h3><p>Ikuti bimbingan manasik yang disediakan oleh travel agent Anda. Pahami urutan ibadah: ihram, tawaf, sai, dan tahalul.</p><h3>4. Jaga Kesehatan Fisik</h3><p>Umrah membutuhkan stamina yang prima. Mulai rutin berolahraga ringan seperti jalan kaki minimal 30 menit sehari, 2-3 bulan sebelum keberangkatan.</p><h3>5. Siapkan Dana yang Cukup</h3><p>Selain biaya paket, siapkan uang saku untuk keperluan pribadi dan oleh-oleh. Gunakan mata uang Riyal Saudi (SAR).</p>',
            'is_published' => true,
            'published_at' => now()->subDays(5),
            'meta_title' => '10 Tips Umrah Pertama Kali - Panduan Lengkap Jamaah Pemula',
            'meta_description' => 'Panduan lengkap umrah pertama kali: persiapan dokumen, vaksinasi, manasik, tips kesehatan, dan budget.',
        ]);

        BlogPost::create([
            'blog_category_id' => $category->id,
            'user_id' => 1,
            'title' => 'Perbedaan Umrah Reguler vs VIP: Mana yang Cocok untuk Anda?',
            'slug' => 'perbedaan-umrah-reguler-vs-vip',
            'excerpt' => 'Memilih antara paket umrah reguler atau VIP? Simak perbandingan lengkapnya dari segi fasilitas, harga, dan pengalaman.',
            'content' => '<p>Memilih paket umrah yang tepat adalah keputusan penting. Berikut perbandingan antara paket reguler dan VIP:</p><h3>Umrah Reguler</h3><p>Cocok untuk jamaah yang ingin menunaikan umrah dengan budget terbatas. Hotel bintang 3-4 dengan jarak 400-800 meter dari Masjidil Haram. Fasilitas standar dengan layanan yang tetap profesional.</p><h3>Umrah VIP</h3><p>Menawarkan pengalaman premium: hotel bintang 5 dengan jarak kurang dari 300 meter dari Masjidil Haram, ruangan lebih luas, konsumsi full board, dan pendampingan muthawwif 24 jam.</p><h3>Kesimpulan</h3><p>Pilih reguler jika budget terbatas dan Anda masih prima secara fisik. Pilih VIP jika Anda menginginkan kenyamanan maksimal, bersama orang tua/lansia, atau ingin pengalaman yang lebih eksklusif.</p>',
            'is_published' => true,
            'published_at' => now()->subDays(3),
            'meta_title' => 'Umrah Reguler vs VIP - Perbandingan Lengkap Fasilitas & Harga',
            'meta_description' => 'Bingung pilih umrah reguler atau VIP? Simak perbandingan hotel, jarak, fasilitas, dan harga.',
        ]);

        BlogPost::create([
            'blog_category_id' => $category2->id,
            'user_id' => 1,
            'title' => 'Syarat dan Prosedur Pendaftaran Haji Plus 2026',
            'slug' => 'syarat-prosedur-haji-plus-2026',
            'excerpt' => 'Informasi lengkap tentang syarat dokumen, prosedur pendaftaran, dan estimasi biaya Haji Plus tahun 2026.',
            'content' => '<p>Haji Plus adalah program haji yang diselenggarakan oleh travel agent resmi dengan masa tunggu yang lebih singkat dibandingkan haji reguler. Berikut informasi lengkapnya:</p><h3>Syarat Dokumen</h3><ul><li>KTP asli dan fotokopi</li><li>Kartu Keluarga</li><li>Paspor asli (berlaku minimal 12 bulan)</li><li>Akta kelahiran</li><li>Surat keterangan sehat dari dokter</li><li>Sertifikat vaksin meningitis</li></ul><h3>Estimasi Biaya</h3><p>Biaya Haji Plus tahun 2026 berkisar antara Rp 170-220 juta, tergantung fasilitas hotel dan maskapai. Sudah termasuk tiket PP, hotel, konsumsi, visa, dan muthawwif.</p>',
            'is_published' => true,
            'published_at' => now()->subDays(7),
            'meta_title' => 'Syarat Pendaftaran Haji Plus 2026 - Dokumen & Biaya Lengkap',
            'meta_description' => 'Simak syarat lengkap pendaftaran Haji Plus 2026: dokumen wajib, prosedur, estimasi biaya, dan masa tunggu.',
        ]);

        Page::create([
            'title' => 'Syarat & Ketentuan',
            'slug' => 'syarat-dan-ketentuan',
            'content' => '<h3>Syarat & Ketentuan Pemesanan</h3><p>Dengan melakukan pemesanan paket umrah/haji melalui website atau cabang kami, Anda dianggap telah membaca, memahami, dan menyetujui syarat dan ketentuan berikut:</p><h4>1. Pemesanan</h4><p>Pemesanan dapat dilakukan melalui website, WhatsApp, telepon, atau datang langsung ke cabang. Setiap pemesanan wajib menyertakan data diri lengkap sesuai KTP.</p><h4>2. Pembayaran</h4><p>Pembayaran dilakukan secara bertahap: DP 50% saat booking dan pelunasan H-30 sebelum keberangkatan. Pembayaran dapat dilakukan melalui transfer bank atau datang langsung ke kantor.</p><h4>3. Pembatalan</h4><p>Pembatalan yang dilakukan H-30 atau lebih sebelum keberangkatan dikenakan biaya administrasi 25%. Pembatalan H-14 s/d H-29 dikenakan 50%. Pembatalan H-13 atau kurang tidak dapat dikembalikan.</p>',
            'is_published' => true,
            'meta_title' => 'Syarat & Ketentuan Pemesanan',
            'meta_description' => 'Baca syarat dan ketentuan pemesanan paket umrah dan haji.',
        ]);

        Page::create([
            'title' => 'Kebijakan Privasi',
            'slug' => 'kebijakan-privasi',
            'content' => '<h3>Kebijakan Privasi</h3><p>Kami menghargai privasi Anda dan berkomitmen untuk melindungi data pribadi yang Anda berikan kepada kami.</p><h4>Data yang Kami Kumpulkan</h4><p>Kami mengumpulkan informasi seperti nama, nomor telepon, alamat email, dan data KTP/paspor yang diperlukan untuk proses pemesanan paket umrah/haji.</p><h4>Penggunaan Data</h4><p>Data Anda digunakan untuk: pemrosesan booking, penerbitan visa, komunikasi terkait perjalanan, dan pengiriman informasi promosi (jika Anda menyetujui).</p><h4>Keamanan Data</h4><p>Kami menerapkan langkah keamanan teknis dan organisasional untuk melindungi data pribadi Anda dari akses tidak sah.</p>',
            'is_published' => true,
            'meta_title' => 'Kebijakan Privasi',
            'meta_description' => 'Baca kebijakan privasi kami tentang pengumpulan dan penggunaan data pribadi.',
        ]);

        Faq::create(['question' => 'Apa saja dokumen yang diperlukan untuk umrah?', 'answer' => '<p>Dokumen yang diperlukan: Paspor asli (berlaku minimal 8 bulan), KTP, Kartu Keluarga, pas foto 4x6 latar putih 6 lembar, dan sertifikat vaksin meningitis.</p>', 'category' => 'Pendaftaran', 'sort_order' => 1, 'is_published' => true]);
        Faq::create(['question' => 'Berapa lama proses pembuatan visa umrah?', 'answer' => '<p>Proses pembuatan visa umrah biasanya memakan waktu 5-10 hari kerja setelah semua dokumen lengkap. Kami sarankan untuk mendaftar minimal 1 bulan sebelum keberangkatan.</p>', 'category' => 'Visa & Dokumen', 'sort_order' => 1, 'is_published' => true]);
        Faq::create(['question' => 'Apakah saya bisa mencicil pembayaran paket umrah?', 'answer' => '<p>Ya, kami menyediakan opsi cicilan hingga 3x untuk paket tertentu. DP awal 50% dan sisanya dapat dicicil sesuai kesepakatan hingga H-30 sebelum keberangkatan.</p>', 'category' => 'Pembayaran', 'sort_order' => 1, 'is_published' => true]);
        Faq::create(['question' => 'Bagaimana jika saya harus membatalkan keberangkatan?', 'answer' => '<p>Kebijakan pembatalan kami: H-30+ = biaya admin 25%, H-14 s/d H-29 = 50%, < H-13 = tidak dapat dikembalikan. Kami sarankan untuk memiliki asuransi perjalanan.</p>', 'category' => 'Pembayaran', 'sort_order' => 2, 'is_published' => true]);
        Faq::create(['question' => 'Apakah hotel sudah termasuk dalam paket?', 'answer' => '<p>Ya, semua paket umrah kami sudah termasuk akomodasi hotel di Makkah dan Madinah. Detail hotel (nama, bintang, jarak) tercantum di halaman masing-masing paket.</p>', 'category' => 'Hotel & Akomodasi', 'sort_order' => 1, 'is_published' => true]);
        Faq::create(['question' => 'Berapa jarak hotel dari Masjidil Haram?', 'answer' => '<p>Jarak hotel bervariasi tergantung paket. Paket VIP: 100-300 meter, Paket Reguler: 400-800 meter. Jarak spesifik tercantum di detail masing-masing paket.</p>', 'category' => 'Hotel & Akomodasi', 'sort_order' => 2, 'is_published' => true]);
        Faq::create(['question' => 'Perlengkapan apa yang harus saya bawa?', 'answer' => '<p>Barang wajib: kain ihram (pria), mukena (wanita), sandal jepit, obat-obatan pribadi, Al-Quran kecil, tas selempang, dan pakaian secukupnya untuk 9-12 hari. Jangan membawa cairan >100ml di tas kabin.</p>', 'category' => 'Perlengkapan', 'sort_order' => 1, 'is_published' => true]);
        Faq::create(['question' => 'Apa perbedaan umrah reguler dan VIP?', 'answer' => '<p>Umrah reguler menggunakan hotel bintang 3-4 dengan jarak 400-800m dari masjid. Umrah VIP menggunakan hotel bintang 5 dengan jarak <300m, kamar lebih luas, full board meal, dan layanan lebih eksklusif.</p>', 'category' => 'Umum', 'sort_order' => 1, 'is_published' => true]);

        Testimonial::create(['name' => 'Ahmad Fauzi', 'role' => 'Umrah Ramadhan 2025', 'content' => 'Alhamdulillah, perjalanan umrah bersama UmrahKita sangat berkesan. Pelayanan prima, hotel dekat masjid, muthawwif sangat membantu. InsyaAllah umrah berikutnya pakai travel ini lagi.', 'rating' => 5, 'sort_order' => 1, 'is_published' => true]);
        Testimonial::create(['name' => 'Siti Nurhaliza', 'role' => 'Umrah Syawal 2025', 'content' => 'Pertama kali umrah dan langsung jatuh cinta dengan pelayanannya. Daftar dari Bandung, semua diurus sampai tuntas. Muthawwifnya sabar banget ngebimbing jamaah lansia.', 'rating' => 5, 'sort_order' => 2, 'is_published' => true]);
        Testimonial::create(['name' => 'H. Bambang Sutrisno', 'role' => 'Haji Plus 2025', 'content' => 'Haji Plus bersama UmrahKita luar biasa. Hotel Fairmont persis di depan Masjidil Haram. Makanan enak, pelayanan VIP. Worth every rupiah!', 'rating' => 5, 'sort_order' => 3, 'is_published' => true]);
        Testimonial::create(['name' => 'Rina Anggraini', 'role' => 'Umrah Keluarga', 'content' => 'Berangkat umrah sekeluarga 5 orang. Anak-anak senang, orang tua nyaman. Hotel bersih, transportasi nyaman. Recommended banget buat yang mau umrah keluarga.', 'rating' => 4, 'sort_order' => 4, 'is_published' => true]);

        Slider::create(['title' => 'Perjalanan <span class="text-navy-700">Umrah & Haji</span> Aman dan Nyaman', 'subtitle' => 'Wujudkan impian beribadah ke Tanah Suci bersama kami. Layanan profesional, hotel strategis, bimbingan ibadah lengkap.', 'link_url' => '/paket-umrah', 'link_text' => 'Lihat Paket Umrah', 'sort_order' => 1, 'is_active' => true]);
        Slider::create(['title' => 'Paket <span class="text-navy-700">Umrah Ramadhan</span> 2026', 'subtitle' => 'Keberangkatan Februari 2026. Hotel bintang 5 dekat Masjidil Haram. Daftar sekarang sebelum kehabisan!', 'link_url' => '/paket-umrah/umrah-ramadhan-2026', 'link_text' => 'Pesan Sekarang', 'sort_order' => 2, 'is_active' => true]);
        Slider::create(['title' => 'Perjalanan <span class="text-navy-700">Haji Plus</span> Eksklusif', 'subtitle' => 'Fasilitas VIP, hotel Fairmont Makkah, muthawwif 24 jam. Kuota terbatas, daftar sekarang!', 'link_url' => '/paket-umrah/haji-plus-2026', 'link_text' => 'Info Haji Plus', 'sort_order' => 3, 'is_active' => true]);

        $this->command->info('CMS Data:    Blog categories: 3, Posts: 3, Pages: 2, FAQs: 8, Testimonials: 4, Sliders: 3');
    }
}
