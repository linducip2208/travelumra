<?php

namespace App\Http\Controllers;

class DocsController extends Controller
{
    public function index()
    {
        return view('marketing.docs', [
            'seo' => $this->seoMeta(),
            'demoAccounts' => $this->demoAccounts(),
            'phases' => $this->phases(),
            'features' => $this->features(),
            'pseo' => $this->pseoRoutes(),
            'glossary' => $this->glossary(),
        ]);
    }

    protected function seoMeta(): array
    {
        return [
            'title' => 'Dokumentasi & Tutorial — Sistem Manajemen Travel Umrah',
            'description' => 'Panduan lengkap 12 fase alur bisnis travel umrah: setup awal, pendaftaran jamaah, booking, keuangan, visa, kloter, keberangkatan, monitoring muthawif, hingga kepulangan. Dilengkapi akun demo semua role.',
            'og_title' => 'Dokumentasi Lengkap Sistem Manajemen Travel Umrah',
            'og_description' => 'Tutorial step-by-step dari setup hingga monitoring jamaah di Tanah Suci. 33+ modul, 12 fase alur bisnis, 6 role staff.',
        ];
    }

    protected function demoAccounts(): array
    {
        return [
            ['role' => 'Super Admin', 'email' => 'admin@travelumrah.com', 'password' => 'password', 'scope' => 'Full akses semua fitur: manajemen user, pengaturan sistem, semua data operasional', 'color' => 'indigo'],
            ['role' => 'Admin', 'email' => 'admin2@travelumrah.com', 'password' => 'password', 'scope' => 'CRUD semua data operasional (kecuali user & pengaturan sistem)', 'color' => 'blue'],
            ['role' => 'Finance', 'email' => 'finance@travelumrah.com', 'password' => 'password', 'scope' => 'Invoice, payment confirmation, expense management, laporan keuangan', 'color' => 'green'],
            ['role' => 'Branch Manager', 'email' => 'bm_bdg@travelumrah.com', 'password' => 'password', 'scope' => 'Kelola data cabang sendiri: jamaah, booking, pembayaran, laporan cabang', 'color' => 'amber'],
            ['role' => 'Agent', 'email' => 'agent_jkt@travelumrah.com', 'password' => 'password', 'scope' => 'Input jamaah, booking, lead follow-up, upload bukti pembayaran', 'color' => 'violet'],
            ['role' => 'Muthawif', 'email' => 'muthawif@travelumrah.com', 'password' => 'password', 'scope' => 'Lihat kloter & jamaah, absensi, laporan harian via API mobile (Sanctum)', 'color' => 'cyan'],
        ];
    }

    protected function phases(): array
    {
        return [
            ['id' => 'fase1', 'num' => 1, 'color' => 'indigo', 'label' => 'Setup Awal', 'title' => 'Setup Awal — Cabang, Staff & Konfigurasi', 'desc' => 'Super Admin mengkonfigurasi data dasar: cabang, staff dengan 6 role, pengaturan sistem, dan payment provider.', 'image' => '20-admin-branches.png', 'imageUrl' => 'admin/branches', 'steps' => [
                ['title' => 'Data Cabang', 'detail' => 'Daftarkan kantor pusat dan cabang. Setiap cabang memiliki kuota haji, kuota umrah, komisi referral, dan rekening bank sendiri.'],
                ['title' => 'Data Staff', 'detail' => 'Tambah user dengan 6 pilihan role: Super Admin, Admin, Finance, Branch Manager, Agent, dan Muthawif.'],
                ['title' => 'Pengaturan Sistem', 'detail' => 'Isi brand name, bank info, threshold approval pembayaran, komisi default, dan konfigurasi lainnya di menu Settings.'],
                ['title' => 'Payment Provider', 'detail' => 'Setup payment gateway — pilih dari 13 preset Indonesia (Midtrans, Xendit, Tripay, dll) atau isi manual. API key dienkripsi AES-256-CBC.'],
            ], 'roles' => [
                ['name' => 'Super Admin'], ['name' => 'Admin'], ['name' => 'Finance'],
            ]],
            ['id' => 'fase2', 'num' => 2, 'color' => 'emerald', 'label' => 'Pendaftaran Jamaah', 'title' => 'Pendaftaran Jamaah — Biodata, Dokumen & Verifikasi', 'desc' => 'Setiap calon jamaah didaftarkan dengan data lengkap: identitas, paspor, kondisi medis, kontak darurat, dan dokumen pendukung.', 'image' => '12-admin-pilgrims.png', 'imageUrl' => 'admin/pilgrims', 'steps' => [
                ['title' => 'Input Biodata Lengkap', 'detail' => 'Nama, NIK, KK, tempat/tanggal lahir, gender, status nikah, golongan darah, alamat, HP, email.'],
                ['title' => 'Data Paspor', 'detail' => 'Nomor paspor, tanggal terbit, expiry, kantor imigrasi. Validasi minimal 6 bulan sebelum keberangkatan.'],
                ['title' => 'Kondisi Medis', 'detail' => 'Vaksin meningitis, vaksin COVID-19, riwayat penyakit, alergi, obat rutin.'],
                ['title' => 'Kontak Darurat & Mahram', 'detail' => 'Kontak darurat (nama, hubungan, HP). Jamaah wanita: data mahram.'],
                ['title' => 'Upload & Verifikasi Dokumen', 'detail' => 'Upload: KTP, KK, paspor, pas foto, buku nikah, sertifikat vaksin, SK sehat. Admin verifikasi.'],
            ]],
            ['id' => 'fase3', 'num' => 3, 'color' => 'violet', 'label' => 'Paket & Produk', 'title' => 'Paket Perjalanan — Umrah, Haji Plus & Fasilitas', 'desc' => 'Buat paket perjalanan lengkap dengan maskapai, hotel Makkah & Madinah, itinerary, harga 4 tier, kuota, dan fasilitas.', 'image' => '11-admin-packages.png', 'imageUrl' => 'admin/packages', 'steps' => [
                ['title' => 'Buat Paket Umrah/Haji Plus', 'detail' => 'Nama, kode, slug, tipe, kategori, maskapai, bandara, tanggal berangkat & pulang, durasi.'],
                ['title' => 'Hotel Makkah & Madinah', 'detail' => 'Nama hotel, bintang (1-5), jarak ke masjid. Tipe kamar: single, double, triple, quad.'],
                ['title' => 'Harga & Kuota', 'detail' => '4 tier harga, total kuota, booked, available (auto-calculated). Include/exclude list.'],
                ['title' => 'Itinerary & Banner', 'detail' => 'Rencana perjalanan harian, banner marketing, gallery images, sort order.'],
            ]],
            ['id' => 'fase4', 'num' => 4, 'color' => 'amber', 'label' => 'CRM & Lead', 'title' => 'CRM — Lead Capture, Follow-up & Broadcast', 'desc' => 'Pengunjung yang mengisi form kontak otomatis tercatat sebagai lead. Agent follow-up hingga konversi.', 'image' => '16-admin-leads.png', 'imageUrl' => 'admin/leads', 'steps' => [
                ['title' => 'Lead Capture Otomatis', 'detail' => 'Form kontak website, API endpoint POST /api/leads, input manual. 7 source channel.'],
                ['title' => 'Follow-up Tracking', 'detail' => 'Status: new → contacted → interested → follow_up → deal/lost. Activity log per lead.'],
                ['title' => 'Konversi ke Jamaah + Booking', 'detail' => 'Lead deal → buat Pilgrim + Booking dalam satu workflow. Track conversion rate.'],
                ['title' => 'Broadcast Massal', 'detail' => 'Kirim pesan ke jamaah/kloter via WhatsApp/email. Template pesan, tracking status kirim.'],
            ]],
            ['id' => 'fase5', 'num' => 5, 'color' => 'cyan', 'label' => 'Booking', 'title' => 'Booking & Transaksi — 12 Status Workflow', 'desc' => 'Booking dengan memilih jamaah, paket, tipe kamar, dan menghitung total biaya. 12 status workflow.', 'image' => '13-admin-bookings.png', 'imageUrl' => 'admin/bookings', 'steps' => [
                ['title' => 'Buat Booking Baru', 'detail' => 'Pilih jamaah, paket, tipe kamar, agent, cabang. Nomor otomatis: BKG-YYYYMM-XXXX.'],
                ['title' => '12 Status Workflow', 'detail' => 'pending → dp_paid → confirmed → documents_complete → visa_requested → visa_processing → visa_approved → ready_to_depart → departed → returned. Negatif: cancelled, refunded, expired.'],
                ['title' => 'Promo Code', 'detail' => 'Diskon percentage/fixed, batas pemakaian, masa berlaku, minimal booking amount.'],
                ['title' => 'Komisi Referral', 'detail' => 'Komisi otomatis per cabang. Tracking total komisi per jamaah referral.'],
            ]],
            ['id' => 'fase6', 'num' => 6, 'color' => 'rose', 'label' => 'Keuangan', 'title' => 'Keuangan — Invoice, Pembayaran & Approval', 'desc' => 'Invoice otomatis per booking, pembayaran bertahap dengan upload bukti transfer, verifikasi finance.', 'image' => '14-admin-invoices.png', 'imageUrl' => 'admin/invoices', 'steps' => [
                ['title' => 'Invoice Otomatis', 'detail' => 'Nomor: INV-YYYYMM-XXXX. Status: unpaid → partial → paid → overdue.'],
                ['title' => 'Upload Pembayaran', 'detail' => 'Upload bukti transfer → payment baru (PMT-XXXX). Multi-payment per invoice.'],
                ['title' => 'Verifikasi & Approval', 'detail' => 'Finance verifikasi payment. Threshold approval untuk nominal besar.'],
                ['title' => 'Manajemen Expense', 'detail' => 'Catat expense operasional per booking/cabang. Approval workflow.'],
            ]],
            ['id' => 'fase7', 'num' => 7, 'color' => 'teal', 'label' => 'Kloter & Visa', 'title' => 'Kloter & Visa — Pengelompokan Jamaah & Pengajuan Visa', 'desc' => 'Jamaah dikelompokkan dalam kloter dengan muthawif. Visa diajukan per jamaah dengan tracking status.', 'image' => '15-admin-clusters.png', 'imageUrl' => 'admin/clusters', 'steps' => [
                ['title' => 'Buat Kloter', 'detail' => 'Kode kloter, nama, paket, kapasitas. Assign muthawif. Status: scheduled → departed → completed.'],
                ['title' => 'Assign Jamaah ke Kloter', 'detail' => 'Assign booking ke kloter. Set seat number, kamar hotel, grup katering.'],
                ['title' => 'Pengajuan Visa', 'detail' => 'Visa per jamaah. Status: draft → submitted → processing → approved/rejected.'],
                ['title' => 'Update Status Booking', 'detail' => 'Visa approved → booking auto-update. Documents complete → status update otomatis.'],
            ]],
            ['id' => 'fase8', 'num' => 8, 'color' => 'orange', 'label' => 'Pra-Keberangkatan', 'title' => 'Pra-Keberangkatan — Bus, Penerbangan, Perlengkapan & Check-in', 'desc' => 'Seminggu sebelum berangkat: assign bus, jadwalkan flight, distribusi perlengkapan, check-in jamaah.', 'image' => '23-admin-buses.png', 'imageUrl' => 'admin/buses', 'steps' => [
                ['title' => 'Bus Assignment', 'detail' => 'Assign armada bus ke kloter. Rute: embarkasi → bandara, Madinah → Makkah.'],
                ['title' => 'Flight Scheduling', 'detail' => 'Flight number, maskapai, tanggal/jam, gate, terminal. Terkait kloter.'],
                ['title' => 'Distribusi Perlengkapan', 'detail' => 'Koper, ihram, tas paspor, name tag. Tracking: allocated → picked_up → returned.'],
                ['title' => 'Check-in & Broadcast', 'detail' => 'Verifikasi dokumen, timbang koper, boarding pass. Broadcast info keberangkatan.'],
            ]],
            ['id' => 'fase9', 'num' => 9, 'color' => 'sky', 'label' => 'Keberangkatan', 'title' => 'Keberangkatan & Monitoring Muthawif (API Mobile)', 'desc' => 'Kloter berangkat → booking auto-update ke departed. Muthawif pakai API mobile untuk absensi & laporan.', 'image' => '10-admin-dashboard.png', 'imageUrl' => 'admin', 'steps' => [
                ['title' => 'Departure', 'detail' => 'Status booking auto-update. Dashboard real-time: jamaah departed, kloter in transit.'],
                ['title' => 'Absensi Jamaah', 'detail' => 'POST /api/muthawif/attendance — muthawif absen jamaah harian. Status: present/absent/sick.'],
                ['title' => 'Laporan Harian', 'detail' => 'POST /api/muthawif/reports — laporan kondisi jamaah, cuaca, kejadian penting.'],
                ['title' => 'Laporan Darurat', 'detail' => 'POST /api/muthawif/emergency — medical emergency, lost person, insiden.'],
            ], 'apiEndpoints' => [
                ['method' => 'GET', 'path' => '/api/muthawif/cluster', 'desc' => 'Detail kloter + list jamaah'],
                ['method' => 'POST', 'path' => '/api/muthawif/attendance', 'desc' => 'Absensi harian per jamaah'],
                ['method' => 'POST', 'path' => '/api/muthawif/reports', 'desc' => 'Laporan harian muthawif'],
                ['method' => 'POST', 'path' => '/api/muthawif/emergency', 'desc' => 'Laporan darurat/insiden'],
            ]],
            ['id' => 'fase10', 'num' => 10, 'color' => 'green', 'label' => 'Kepulangan & Pasca', 'title' => 'Kepulangan — Return, Klaim Asuransi & Loyalty Points', 'desc' => 'Kloter pulang → status returned. Proses klaim asuransi, refund, dan loyalty points.', 'image' => '30-admin-insurances.png', 'imageUrl' => 'admin/insurances', 'steps' => [
                ['title' => 'Return & Rekonsiliasi', 'detail' => 'Status booking → returned. Perlengkapan wajib dikembalikan. Rekonsiliasi expense vs budget.'],
                ['title' => 'Klaim Asuransi', 'detail' => 'Medical, lost baggage, cancellation. Status: submitted → reviewed → approved/paid/rejected.'],
                ['title' => 'Refund Booking', 'detail' => 'Booking cancelled → refund dengan nominal disetujui. Approval workflow.'],
                ['title' => 'Loyalty Points', 'detail' => '1 point per 1 juta rupiah. Points bisa ditukar diskon booking berikutnya.'],
            ]],
            ['id' => 'fase11', 'num' => 11, 'color' => 'fuchsia', 'label' => 'Laporan', 'title' => 'Laporan — Penjualan, Keuangan & Operasional', 'desc' => 'Tiga halaman laporan dengan date filter, chart interaktif (Chart.js), summary cards, export PDF.', 'image' => '34-admin-laporan-penjualan.png', 'imageUrl' => 'admin/laporan-penjualan', 'steps' => [
                ['title' => 'Laporan Penjualan', 'detail' => 'Revenue chart, top packages, top agents, conversion rate. Group by harian/mingguan/bulanan.'],
                ['title' => 'Laporan Keuangan', 'detail' => 'P&L, AR/AP aging, expense by category, revenue vs expense comparison.'],
                ['title' => 'Laporan Operasional', 'detail' => 'Kloter utilization, visa approval rate, equipment status, bus occupancy rate.'],
            ]],
            ['id' => 'fase12', 'num' => 12, 'color' => 'slate', 'label' => 'Konten & SEO', 'title' => 'Konten & Marketing — Blog, CMS & Programmatic SEO', 'desc' => 'Kelola blog, halaman statis, FAQ, slider, testimonial. PSEO menghasilkan ratusan halaman SEO otomatis.', 'image' => '01-home.png', 'imageUrl' => '/', 'steps' => [
                ['title' => 'Blog & Kategori', 'detail' => 'Artikel dengan kategori, SEO meta, featured image, scheduled publish.'],
                ['title' => 'Halaman CMS & FAQ', 'detail' => 'Halaman custom (tentang kami, syarat ketentuan). FAQ dengan kategori & sort order.'],
                ['title' => 'Slider Banner & Testimonial', 'detail' => 'Slider homepage, testimonial jamaah dengan rating bintang.'],
                ['title' => 'Programmatic SEO (9 Route)', 'detail' => 'Ratusan halaman SEO otomatis: JSON-LD schema, meta tags, konten 300+ kata, auto-include sitemap.'],
            ]],
        ];
    }

    protected function features(): array
    {
        return [
            ['group' => 'Master Data', 'groupColor' => 'indigo', 'items' => [
                ['name' => 'Data Jamaah', 'desc' => 'Biodata lengkap, paspor, medis, kontak darurat, mahram. Upload & verifikasi dokumen.', 'image' => '12-admin-pilgrims.png', 'url' => 'admin/pilgrims'],
                ['name' => 'Manajemen Cabang', 'desc' => 'Hierarki pusat-cabang, kuota haji/umrah, komisi referral, rekening bank per cabang.', 'image' => '20-admin-branches.png', 'url' => 'admin/branches'],
                ['name' => 'Manajemen Staff', 'desc' => '6 role: Super Admin, Admin, Finance, Branch Manager, Agent, Muthawif. Dashboard per-role.', 'image' => '10-admin-dashboard.png', 'url' => 'admin/users'],
            ]],
            ['group' => 'Penjualan', 'groupColor' => 'blue', 'items' => [
                ['name' => 'Paket Umrah & Haji', 'desc' => 'Maskapai, hotel Makkah/Madinah, 4 tier harga, kuota, itinerary, include/exclude list.', 'image' => '11-admin-packages.png', 'url' => 'admin/packages'],
                ['name' => 'Booking', 'desc' => '12 status workflow, nomor otomatis, komisi referral, promo code, auto-calculate.', 'image' => '13-admin-bookings.png', 'url' => 'admin/bookings'],
                ['name' => 'Promo Code', 'desc' => 'Diskon percentage/fixed, batas pemakaian, masa berlaku, minimal booking.', 'image' => '33-admin-promo-codes.png', 'url' => 'admin/promo-codes'],
            ]],
            ['group' => 'Keuangan', 'groupColor' => 'green', 'items' => [
                ['name' => 'Invoice', 'desc' => 'Invoice otomatis per booking. Status: unpaid → partial → paid → overdue.', 'image' => '14-admin-invoices.png', 'url' => 'admin/invoices'],
                ['name' => 'Pembayaran', 'desc' => 'Upload bukti transfer, verifikasi finance, approval threshold, multi-payment.', 'image' => '25-admin-payments.png', 'url' => 'admin/payments'],
                ['name' => 'Expense', 'desc' => 'Biaya operasional per booking/cabang, approval workflow, rekonsiliasi.', 'image' => '26-admin-expenses.png', 'url' => 'admin/expenses'],
            ]],
            ['group' => 'Operasional', 'groupColor' => 'cyan', 'items' => [
                ['name' => 'Kloter', 'desc' => 'Kelompokkan jamaah, assign muthawif, seat, kamar, katering. 3 status workflow.', 'image' => '15-admin-clusters.png', 'url' => 'admin/clusters'],
                ['name' => 'Visa', 'desc' => 'Pengajuan per jamaah. Status: draft → submitted → processing → approved/rejected.', 'image' => '17-admin-visas.png', 'url' => 'admin/visas'],
                ['name' => 'Hotel & Kamar', 'desc' => 'Hotel Makkah/Madinah, bintang, jarak ke masjid. Kamar single/double/triple/quad.', 'image' => '22-admin-hotels.png', 'url' => 'admin/hotels'],
                ['name' => 'Bus & Penerbangan', 'desc' => 'Armada bus + assignment rute. Flight scheduling per kloter.', 'image' => '23-admin-buses.png', 'url' => 'admin/buses'],
                ['name' => 'Perlengkapan', 'desc' => 'Koper, ihram, name tag. Distribusi & tracking allocated → picked_up → returned.', 'image' => '24-admin-equipment.png', 'url' => 'admin/equipment'],
                ['name' => 'Asuransi & Klaim', 'desc' => 'Medical, lost baggage, cancellation. Klaim: submitted → reviewed → approved/paid.', 'image' => '30-admin-insurances.png', 'url' => 'admin/insurances'],
                ['name' => 'Check-In & Itinerary', 'desc' => 'Verifikasi dokumen, timbang koper. Jadwal harian per kloter via mobile API.', 'image' => '15-admin-clusters.png', 'url' => 'admin/clusters'],
            ]],
            ['group' => 'CRM', 'groupColor' => 'orange', 'items' => [
                ['name' => 'Lead Management', 'desc' => 'Capture otomatis, follow-up 5 status, activity log, konversi ke jamaah + booking.', 'image' => '16-admin-leads.png', 'url' => 'admin/leads'],
                ['name' => 'Broadcast', 'desc' => 'Pesan massal WhatsApp/email ke jamaah/kloter. Template, tracking status kirim.', 'image' => '27-admin-broadcasts.png', 'url' => 'admin/broadcasts'],
                ['name' => 'Loyalty Points', 'desc' => '1 point / 1 juta, redeem diskon booking. Tracking earned, redeemed, balance.', 'image' => '28-admin-loyalty-points.png', 'url' => 'admin/loyalty-points'],
            ]],
            ['group' => 'Dokumen', 'groupColor' => 'yellow', 'items' => [
                ['name' => 'Dokumen Jamaah', 'desc' => 'Upload & verifikasi: KTP, KK, paspor, foto, buku nikah, sertifikat vaksin, SK sehat.', 'image' => '12-admin-pilgrims.png', 'url' => 'admin/pilgrim-documents'],
            ]],
            ['group' => 'Laporan', 'groupColor' => 'rose', 'items' => [
                ['name' => 'Laporan Penjualan', 'desc' => 'Revenue chart, top packages, top agents. Filter date, group by, export PDF.', 'image' => '34-admin-laporan-penjualan.png', 'url' => 'admin/laporan-penjualan'],
                ['name' => 'Laporan Keuangan', 'desc' => 'P&L, AR/AP aging, expense by category. Export PDF.', 'image' => '35-admin-laporan-keuangan.png', 'url' => 'admin/laporan-keuangan'],
                ['name' => 'Laporan Operasional', 'desc' => 'Kloter utilization, visa approval rate, equipment status. Export PDF.', 'image' => '36-admin-laporan-operasional.png', 'url' => 'admin/laporan-operasional'],
            ]],
            ['group' => 'Konten', 'groupColor' => 'violet', 'items' => [
                ['name' => 'Blog & Artikel', 'desc' => 'Artikel dengan kategori, SEO meta, featured image, scheduled publish.', 'image' => '31-admin-blog-posts.png', 'url' => 'admin/blog-posts'],
                ['name' => 'Slider Banner', 'desc' => 'Slider homepage dengan gambar, judul, CTA, sort order.', 'image' => '39-admin-sliders.png', 'url' => 'admin/sliders'],
                ['name' => 'Testimonial', 'desc' => 'Testimonial jamaah: foto, rating bintang. Social proof homepage.', 'image' => '38-admin-testimonials.png', 'url' => 'admin/testimonials'],
                ['name' => 'FAQ & Halaman', 'desc' => 'FAQ dengan kategori. Halaman CMS custom (tentang kami, kebijakan privasi).', 'image' => '37-admin-faqs.png', 'url' => 'admin/faqs'],
            ]],
            ['group' => 'Integrasi', 'groupColor' => 'slate', 'items' => [
                ['name' => 'Payment Gateway', 'desc' => '13 preset Indonesia siap pilih (Midtrans, Xendit, Tripay, dll). API key terenkripsi.', 'image' => '10-admin-dashboard.png', 'url' => 'admin/payment-providers'],
            ]],
            ['group' => 'Pengaturan', 'groupColor' => 'gray', 'items' => [
                ['name' => 'System Settings', 'desc' => 'Brand name, bank info, approval threshold, komisi default, currency rate.', 'image' => '32-admin-settings.png', 'url' => 'admin/settings'],
                ['name' => 'Audit Log', 'desc' => 'Tracking semua aktivitas user: create, update, delete, login. Filter per modul.', 'image' => '10-admin-dashboard.png', 'url' => 'admin/audit-logs'],
            ]],
            ['group' => 'Portal Jamaah', 'groupColor' => 'emerald', 'items' => [
                ['name' => 'Self-Service Portal', 'desc' => '/portal: login, dashboard, my bookings, my invoices, upload bukti pembayaran.', 'image' => '40-portal-login.png', 'url' => '/portal/login'],
            ]],
        ];
    }

    protected function pseoRoutes(): array
    {
        return [
            ['path' => '/s/paket-umrah-{bulan}-{tahun}', 'desc' => 'Paket per bulan keberangkatan. Contoh: /s/paket-umrah-ramadhan-2026'],
            ['path' => '/s/best-umrah-packages-{tahun}', 'desc' => 'Top 10 paket umrah terpopuler tahun ini — ItemList JSON-LD'],
            ['path' => '/s/umrah-{kategori}', 'desc' => 'Filter kategori: ekonomi, standard, vip, vvip'],
            ['path' => '/s/umrah-dari-{kota}', 'desc' => 'Filter kota keberangkatan: jakarta, surabaya, medan'],
            ['path' => '/s/compare/{a}-vs-{b}', 'desc' => 'Head-to-head comparison 2 paket + FAQPage schema'],
            ['path' => '/s/alternatives-to-{slug}', 'desc' => 'Alternatif paket serupa dalam budget yang mirip'],
            ['path' => '/s/travel-umrah-murah', 'desc' => 'Koleksi paket umrah budget/ekonomi terbaik'],
            ['path' => '/s/haji-plus-{tahun}', 'desc' => 'Daftar lengkap paket haji plus per tahun'],
            ['path' => '/s/{maskapai}', 'desc' => 'Filter maskapai: saudia-airlines, garuda-indonesia, lion-air'],
        ];
    }

    protected function glossary(): array
    {
        return [
            ['term' => 'Muthawif', 'def' => 'Pembimbing ibadah umrah/haji yang mendampingi jamaah selama di Tanah Suci.'],
            ['term' => 'Kloter', 'def' => 'Kelompok terbang — sekelompok jamaah yang berangkat bersama dalam satu penerbangan.'],
            ['term' => 'Mahram', 'def' => 'Pendamping wajib bagi jamaah wanita (suami, ayah, saudara laki-laki, anak laki-laki).'],
            ['term' => 'DP', 'def' => 'Down Payment / Uang Muka — pembayaran awal untuk mengamankan slot booking (30-50%).'],
            ['term' => 'Manifest', 'def' => 'Daftar resmi penumpang kloter: nama, paspor, seat number, info kontak.'],
            ['term' => 'Ihram', 'def' => 'Pakaian khusus saat ibadah. Laki-laki: 2 lembar kain putih. Perempuan: pakaian sopan.'],
            ['term' => 'Tier Kamar', 'def' => 'Level okupansi: Single (1), Double (2), Triple (3), Quad (4). Harga berbeda per tier.'],
            ['term' => 'Approval Threshold', 'def' => 'Batas nominal pembayaran yang harus disetujui manager/atasan.'],
            ['term' => 'Programmatic SEO', 'def' => 'SEO otomatis — ratusan halaman dari database dengan JSON-LD dan meta tags.'],
            ['term' => 'P&L', 'def' => 'Profit & Loss — laporan laba rugi pendapatan vs pengeluaran dalam periode tertentu.'],
            ['term' => 'AR/AP Aging', 'def' => 'Analisis umur piutang dan hutang untuk identifikasi pembayaran overdue.'],
            ['term' => 'Sanctum', 'def' => 'Laravel Sanctum — autentikasi API token untuk mobile app muthawif.'],
        ];
    }
}
