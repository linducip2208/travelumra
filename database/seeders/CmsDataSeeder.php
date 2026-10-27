<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Slider;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class CmsDataSeeder extends Seeder
{
    public function run(): void
    {
        $category = BlogCategory::create(['name' => 'Tips Umrah', 'slug' => 'tips-umrah', 'sort_order' => 1]);
        $category2 = BlogCategory::create(['name' => 'Persiapan Haji', 'slug' => 'persiapan-haji', 'sort_order' => 2]);
        $category3 = BlogCategory::create(['name' => 'Info Visa', 'slug' => 'info-visa', 'sort_order' => 3]);

        BlogPost::create([
            'blog_category_id' => $category->id,
            'user_id' => 1,
            'title' => '10 Tips Umrah Pertama Kali untuk Jamaah Pemula',
            'slug' => '10-tips-umrah-pertama-kali',
            'excerpt' => 'Panduan lengkap bagi Anda yang baru pertama kali akan menunaikan ibadah umrah.',
            'content' => '<p>Menunaikan ibadah umrah untuk pertama kali adalah momen yang sangat istimewa. Berikut tips penting:</p><h3>1. Persiapkan Dokumen</h3><p>Pastikan paspor berlaku minimal 8 bulan. Siapkan pas foto 4x6 latar putih 6 lembar.</p><h3>2. Vaksinasi Meningitis</h3><p>Syarat wajib dari pemerintah Arab Saudi. Lakukan minimal 14 hari sebelum keberangkatan.</p><h3>3. Pelajari Manasik</h3><p>Ikuti bimbingan manasik. Pahami urutan: ihram, tawaf, sai, tahalul.</p><h3>4. Jaga Kesehatan</h3><p>Mulai olahraga ringan 2-3 bulan sebelum keberangkatan untuk stamina prima.</p>',
            'is_published' => true,
            'published_at' => now()->subDays(5),
            'meta_title' => '10 Tips Umrah Pertama Kali - Panduan Jamaah Pemula',
            'meta_description' => 'Panduan lengkap umrah pertama kali: persiapan dokumen, vaksinasi, manasik, tips kesehatan.',
        ]);

        BlogPost::create([
            'blog_category_id' => $category->id,
            'user_id' => 1,
            'title' => 'Perbedaan Umrah Reguler vs VIP: Mana yang Cocok untuk Anda?',
            'slug' => 'perbedaan-umrah-reguler-vs-vip',
            'excerpt' => 'Memilih antara paket umrah reguler atau VIP? Simak perbandingan lengkapnya.',
            'content' => '<h3>Umrah Reguler</h3><p>Hotel bintang 3-4, jarak 400-800m dari Masjidil Haram. Budget-friendly.</p><h3>Umrah VIP</h3><p>Hotel bintang 5, jarak <300m, konsumsi full board, pendampingan muthawwif 24 jam.</p><p>Pilih reguler untuk budget terbatas, VIP untuk kenyamanan maksimal.</p>',
            'is_published' => true,
            'published_at' => now()->subDays(3),
            'meta_title' => 'Umrah Reguler vs VIP - Perbandingan Lengkap',
            'meta_description' => 'Bingung pilih umrah reguler atau VIP? Simak perbandingan fasilitas dan harga.',
        ]);

        BlogPost::create([
            'blog_category_id' => $category2->id,
            'user_id' => 1,
            'title' => 'Syarat dan Prosedur Pendaftaran Haji Plus 2026',
            'slug' => 'syarat-prosedur-haji-plus-2026',
            'excerpt' => 'Informasi lengkap tentang syarat dokumen, prosedur, dan biaya Haji Plus 2026.',
            'content' => '<h3>Syarat Dokumen</h3><ul><li>KTP asli dan fotokopi</li><li>KK</li><li>Paspor berlaku min 12 bulan</li><li>Akta kelahiran</li><li>Surat keterangan sehat</li><li>Sertifikat vaksin meningitis</li></ul><h3>Biaya</h3><p>Rp 170-220 juta, sudah termasuk tiket PP, hotel, konsumsi, visa, muthawwif.</p>',
            'is_published' => true,
            'published_at' => now()->subDays(7),
            'meta_title' => 'Syarat Pendaftaran Haji Plus 2026 - Dokumen & Biaya',
            'meta_description' => 'Simak syarat lengkap Haji Plus 2026: dokumen, prosedur, biaya.',
        ]);

        Page::create([
            'title' => 'Syarat & Ketentuan', 'slug' => 'syarat-dan-ketentuan',
            'content' => '<h3>Syarat & Ketentuan</h3><h4>1. Pemesanan</h4><p>Dapat dilakukan via website, WhatsApp, atau datang ke cabang. Wajib menyertakan data diri lengkap.</p><h4>2. Pembayaran</h4><p>DP 50% saat booking, pelunasan H-30 sebelum keberangkatan.</p><h4>3. Pembatalan</h4><p>H-30+: biaya admin 25%. H-14 s/d H-29: 50%. < H-13: tidak dapat dikembalikan.</p>',
            'is_published' => true,
            'meta_title' => 'Syarat & Ketentuan', 'meta_description' => 'Baca syarat dan ketentuan pemesanan paket umrah.',
        ]);

        Page::create([
            'title' => 'Kebijakan Privasi', 'slug' => 'kebijakan-privasi',
            'content' => '<h3>Kebijakan Privasi</h3><p>Kami menghargai privasi Anda. Data yang kami kumpulkan: nama, telepon, email, KTP/paspor untuk keperluan booking.</p><p>Data Anda dilindungi dengan langkah keamanan teknis dan tidak dibagikan ke pihak ketiga tanpa izin.</p>',
            'is_published' => true,
            'meta_title' => 'Kebijakan Privasi', 'meta_description' => 'Baca kebijakan privasi kami.',
        ]);

        $faqs = [
            ['question' => 'Apa saja dokumen yang diperlukan untuk umrah?', 'answer' => '<p>Paspor (berlaku min 8 bulan), KTP, KK, pas foto 4x6 latar putih 6 lembar, sertifikat vaksin meningitis.</p>', 'category' => 'Pendaftaran', 'sort_order' => 1],
            ['question' => 'Berapa lama proses pembuatan visa umrah?', 'answer' => '<p>5-10 hari kerja setelah dokumen lengkap. Daftar minimal 1 bulan sebelum keberangkatan.</p>', 'category' => 'Visa & Dokumen', 'sort_order' => 1],
            ['question' => 'Apakah bisa mencicil pembayaran?', 'answer' => '<p>Ya, tersedia cicilan hingga 3x. DP 50%, sisanya dicicil hingga H-30 sebelum keberangkatan.</p>', 'category' => 'Pembayaran', 'sort_order' => 1],
            ['question' => 'Bagaimana kebijakan pembatalan?', 'answer' => '<p>H-30+: biaya admin 25%. H-14 s/d H-29: 50%. < H-13: tidak dapat dikembalikan.</p>', 'category' => 'Pembayaran', 'sort_order' => 2],
            ['question' => 'Apakah hotel sudah termasuk paket?', 'answer' => '<p>Ya, semua paket sudah termasuk hotel di Makkah dan Madinah. Detail ada di halaman paket.</p>', 'category' => 'Hotel & Akomodasi', 'sort_order' => 1],
            ['question' => 'Berapa jarak hotel dari Masjidil Haram?', 'answer' => '<p>VIP: 100-300m, Reguler: 400-800m. Detail tercantum di masing-masing paket.</p>', 'category' => 'Hotel & Akomodasi', 'sort_order' => 2],
            ['question' => 'Perlengkapan apa yang harus dibawa?', 'answer' => '<p>Kain ihram (pria), mukena (wanita), sandal, obat pribadi, Al-Quran kecil, pakaian 9-12 hari. Jangan bawa cairan >100ml di kabin.</p>', 'category' => 'Perlengkapan', 'sort_order' => 1],
            ['question' => 'Apa perbedaan umrah reguler dan VIP?', 'answer' => '<p>Reguler: hotel bintang 3-4, jarak 400-800m. VIP: hotel bintang 5, jarak <300m, full board, layanan eksklusif.</p>', 'category' => 'Umum', 'sort_order' => 1],
        ];
        foreach ($faqs as $f) {
            Faq::create([...$f, 'is_published' => true]);
        }

        $testimonials = [
            ['name' => 'Ahmad Fauzi', 'role' => 'Umrah Ramadhan 2025', 'content' => 'Alhamdulillah, perjalanan umrah bersama UmrahKita sangat berkesan. Pelayanan prima, hotel dekat masjid, muthawwif sangat membantu.', 'rating' => 5, 'sort_order' => 1],
            ['name' => 'Siti Nurhaliza', 'role' => 'Umrah Syawal 2025', 'content' => 'Pertama kali umrah dan langsung jatuh cinta dengan pelayanannya. Semua diurus sampai tuntas. Muthawwifnya sabar banget.', 'rating' => 5, 'sort_order' => 2],
            ['name' => 'H. Bambang Sutrisno', 'role' => 'Haji Plus 2025', 'content' => 'Haji Plus bersama UmrahKita luar biasa. Hotel Fairmont depan Masjidil Haram. Makanan enak, pelayanan VIP.', 'rating' => 5, 'sort_order' => 3],
            ['name' => 'Rina Anggraini', 'role' => 'Umrah Keluarga', 'content' => 'Berangkat sekeluarga 5 orang. Anak-anak senang, orang tua nyaman. Hotel bersih, transportasi nyaman. Recommended!', 'rating' => 4, 'sort_order' => 4],
        ];
        foreach ($testimonials as $t) {
            Testimonial::create([...$t, 'is_published' => true]);
        }

        $sliders = [
            ['title' => 'Perjalanan <span class="text-navy-700">Umrah & Haji</span> Aman dan Nyaman', 'subtitle' => 'Layanan profesional, hotel strategis, bimbingan ibadah lengkap dari tim berpengalaman.', 'link_url' => '/paket-umrah', 'link_text' => 'Lihat Paket Umrah', 'sort_order' => 1],
            ['title' => 'Paket <span class="text-navy-700">Umrah Ramadhan</span> 2026', 'subtitle' => 'Hotel bintang 5 dekat Masjidil Haram. Daftar sekarang sebelum kehabisan!', 'link_url' => '/paket-umrah/umrah-ramadhan-2026', 'link_text' => 'Pesan Sekarang', 'sort_order' => 2],
            ['title' => 'Perjalanan <span class="text-navy-700">Haji Plus</span> Eksklusif', 'subtitle' => 'Fasilitas VIP, hotel Fairmont Makkah, muthawwif 24 jam. Kuota terbatas!', 'link_url' => '/paket-umrah/haji-plus-2026', 'link_text' => 'Info Haji Plus', 'sort_order' => 3],
        ];
        foreach ($sliders as $s) {
            Slider::create([...$s, 'is_active' => true]);
        }

        $this->command->info('CMS seeded: 3 categories, 3 posts, 2 pages, 8 FAQs, 4 testimonials, 3 sliders');
    }
}
