<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProgrammaticSeoController extends Controller
{
    protected array $validCategories = ['reguler', 'vip', 'eksklusif'];

    protected function settings(): array
    {
        return [
            'site_brand_name' => \App\Helpers\SettingsHelper::get('site_brand_name', 'Travel Umrah'),
            'site_name' => \App\Helpers\SettingsHelper::get('site_name', 'Travel Umrah & Haji'),
        ];
    }

    protected function renderSeoResult(string $title, string $description, $packages, array $jsonld, string $content): \Illuminate\View\View
    {
        return view('marketing.seo-result', [
            'title' => $title,
            'description' => $description,
            'packages' => $packages,
            'jsonld' => $jsonld,
            'content' => $content,
            'settings' => $this->settings(),
        ]);
    }
    protected array $monthNames = [
        1 => 'januari', 2 => 'februari', 3 => 'maret', 4 => 'april',
        5 => 'mei', 6 => 'juni', 7 => 'juli', 8 => 'agustus',
        9 => 'september', 10 => 'oktober', 11 => 'november', 12 => 'desember',
    ];

    public function packageByMonth($bulan, $tahun)
    {
        $monthNum = $this->resolveMonth($bulan);
        if (!$monthNum || $tahun < 2020 || $tahun > (date('Y') + 2)) {
            abort(404);
        }

        $monthName = $this->monthNames[$monthNum];

        $packages = Package::where('is_active', true)
            ->whereMonth('departure_date', $monthNum)
            ->whereYear('departure_date', $tahun)
            ->orderBy('departure_date')
            ->take(20)
            ->get();

        $title = "Paket Umrah {$monthName} {$tahun} - Jadwal Keberangkatan Lengkap";
        $description = "Daftar lengkap paket umrah keberangkatan bulan {$monthName} {$tahun}. Pilih dari berbagai pilihan maskapai, hotel, dan harga yang sesuai dengan budget Anda.";

        $content = $this->generateMonthContent($packages, $monthName, $tahun);

        $jsonld = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $title,
            'description' => $description,
            'itemListElement' => $packages->map(fn($p, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'item' => $this->packageSchema($p),
            ])->toArray(),
        ];

        return $this->renderSeoResult($title, $description, $packages, $jsonld, $content);
    }

    public function bestPackages($tahun)
    {
        if ($tahun < 2020 || $tahun > (date('Y') + 2)) {
            abort(404);
        }

        $packages = Package::where('is_active', true)
            ->whereYear('departure_date', $tahun)
            ->withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->take(10)
            ->get();

        $title = "10 Paket Umrah Terbaik {$tahun} - Pilihan Jamaah Terbanyak";
        $description = "Rekomendasi 10 paket umrah terbaik tahun {$tahun} berdasarkan jumlah peminat terbanyak. Bandingkan harga, fasilitas, dan hotel pilihan jamaah.";

        $content = $this->generateBestContent($packages, $tahun);

        $jsonld = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $title,
            'description' => $description,
            'itemListElement' => $packages->map(fn($p, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'item' => $this->packageSchema($p),
            ])->toArray(),
        ];

        return $this->renderSeoResult($title, $description, $packages, $jsonld, $content);
    }

    public function packageByCategory($kategori)
    {
        $kategori = strtolower($kategori);
        if (!in_array($kategori, $this->validCategories)) {
            // Try as airline name
            return $this->byAirline($kategori);
        }

        $categoryLabel = [
            'ekonomi' => 'Ekonomi',
            'standard' => 'Standard',
            'vip' => 'VIP',
            'vvip' => 'VVIP',
        ][$kategori];

        $packages = Package::where('is_active', true)
            ->where('category', $kategori)
            ->orderBy('departure_date')
            ->take(20)
            ->get();

        $title = "Paket Umrah {$categoryLabel} - Harga & Fasilitas Lengkap";
        $description = "Koleksi paket umrah kategori {$categoryLabel} dengan pilihan hotel, maskapai, dan durasi bervariasi. Cocok untuk berbagai kebutuhan dan budget.";

        $content = $this->generateCategoryContent($packages, $categoryLabel);

        $jsonld = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $title,
            'description' => $description,
            'itemListElement' => $packages->map(fn($p, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'item' => $this->packageSchema($p),
            ])->toArray(),
        ];

        return $this->renderSeoResult($title, $description, $packages, $jsonld, $content);
    }

    public function packageByCity($kota)
    {
        $kota = str_replace('-', ' ', strtolower($kota));
        $kotaTitle = ucwords($kota);

        $packages = Package::where('is_active', true)
            ->where(function ($q) use ($kota) {
                $q->where('departure_airport', 'like', "%{$kota}%")
                  ->orWhere('departure_airport', 'like', '%' . strtoupper(substr($kota, 0, 3)) . '%');
            })
            ->orderBy('departure_date')
            ->take(20)
            ->get();

        $title = "Paket Umrah dari {$kotaTitle} - Jadwal Keberangkatan Terbaru";
        $description = "Cari paket umrah dengan keberangkatan dari {$kotaTitle}. Tersedia berbagai pilihan maskapai dan hotel dengan harga kompetitif.";

        $content = $this->generateCityContent($packages, $kotaTitle);

        $jsonld = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $title,
            'description' => $description,
            'itemListElement' => $packages->map(fn($p, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'item' => $this->packageSchema($p),
            ])->toArray(),
        ];

        return $this->renderSeoResult($title, $description, $packages, $jsonld, $content);
    }

    public function compare($slugA, $slugB)
    {
        $packageA = Package::where('slug', $slugA)->where('is_active', true)->firstOrFail();
        $packageB = Package::where('slug', $slugB)->where('is_active', true)->firstOrFail();

        $title = "{$packageA->name} vs {$packageB->name} - Perbandingan Paket Umrah";
        $description = "Bandingkan {$packageA->name} dan {$packageB->name} secara detail: harga, hotel, maskapai, durasi, dan fasilitas. Bantu Anda memilih paket umrah terbaik.";

        $content = view('marketing.compare-content', compact('packageA', 'packageB'))->render();

        $jsonld = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => "Apa perbedaan {$packageA->name} dan {$packageB->name}?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $this->generateCompareAnswer($packageA, $packageB),
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => "Mana yang lebih murah antara {$packageA->name} dan {$packageB->name}?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $this->generatePriceAnswer($packageA, $packageB),
                    ],
                ],
            ],
        ];

        return view('marketing.compare', [
            'title' => $title,
            'description' => $description,
            'packageA' => $packageA,
            'packageB' => $packageB,
            'jsonld' => $jsonld,
            'content' => $content,
            'settings' => $this->settings(),
        ]);
    }

    public function alternatives($slug)
    {
        $package = Package::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $alternatives = Package::where('is_active', true)
            ->where('id', '!=', $package->id)
            ->where(function ($q) use ($package) {
                $q->where('type', $package->type)
                  ->orWhere('category', $package->category);
            })
            ->orderBy('departure_date')
            ->take(20)
            ->get();

        $title = "Alternatif {$package->name} - Paket Umrah Serupa";
        $description = "Mencari alternatif dari {$package->name}? Lihat daftar paket umrah serupa dengan tipe dan kategori yang sama. Bandingkan harga dan fasilitas.";

        $content = $this->generateAlternativesContent($package, $alternatives);

        $jsonld = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $title,
            'description' => $description,
            'itemListElement' => $alternatives->map(fn($p, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'item' => $this->packageSchema($p),
            ])->toArray(),
        ];

        return $this->renderSeoResult($title, $description, $packages, $jsonld, $content);
    }

    public function budgetUmrah()
    {
        $packages = Package::where('is_active', true)
            ->where('type', 'umrah')
            ->where('price_quad', '>', 0)
            ->orderBy('price_quad')
            ->take(20)
            ->get();

        $title = "Travel Umrah Murah - Paket Hemat Harga Terjangkau";
        $description = "Temukan paket umrah murah dengan harga terjangkau tanpa mengorbankan kenyamanan. Berbagai pilihan keberangkatan sepanjang tahun.";

        $content = $this->generateBudgetContent($packages);

        $jsonld = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Berapa harga paket umrah termurah saat ini?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $packages->isNotEmpty()
                            ? 'Harga paket umrah termurah saat ini mulai dari Rp ' . number_format($packages->first()->price_quad, 0, ',', '.') . ' (quad). Harga dapat berubah sewaktu-waktu tergantung musim dan ketersediaan.'
                            : 'Silakan hubungi kami untuk informasi harga paket umrah terbaru.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Apa saja yang termasuk dalam paket umrah murah?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Paket umrah murah umumnya sudah termasuk tiket pesawat PP, visa umrah, hotel di Makkah dan Madinah, transportasi antar kota, dan manasik umrah. Beberapa paket mungkin tidak termasuk makan atau memiliki fasilitas hotel yang lebih sederhana.',
                    ],
                ],
            ],
        ];

        return $this->renderSeoResult($title, $description, $packages, $jsonld, $content);
    }

    public function hajiPlusYear($tahun)
    {
        if ($tahun < 2020 || $tahun > (date('Y') + 3)) {
            abort(404);
        }

        $packages = Package::where('is_active', true)
            ->where('type', 'haji_plus')
            ->whereYear('departure_date', $tahun)
            ->orderBy('departure_date')
            ->take(20)
            ->get();

        $title = "Paket Haji Plus {$tahun} - Daftar & Biaya Terbaru";
        $description = "Informasi lengkap paket haji plus tahun {$tahun}: biaya, fasilitas, hotel, maskapai, dan jadwal keberangkatan. Daftar sekarang sebelum kuota penuh.";

        $content = $this->generateHajiPlusContent($packages, $tahun);

        $jsonld = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $title,
            'description' => $description,
            'itemListElement' => $packages->map(fn($p, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'item' => $this->packageSchema($p),
            ])->toArray(),
        ];

        return $this->renderSeoResult($title, $description, $packages, $jsonld, $content);
    }

    public function byAirline($maskapai)
    {
        $maskapai = str_replace('-', ' ', strtolower($maskapai));
        $maskapaiTitle = ucwords($maskapai);

        $packages = Package::where('is_active', true)
            ->where('airline', 'like', "%{$maskapai}%")
            ->orderBy('departure_date')
            ->take(20)
            ->get();

        $title = "Paket Umrah {$maskapaiTitle} - Jadwal & Harga Terbaru";
        $description = "Paket umrah menggunakan maskapai {$maskapaiTitle}. Lihat jadwal keberangkatan, harga, dan fasilitas lengkap untuk perjalanan ibadah Anda.";

        $content = $this->generateAirlineContent($packages, $maskapaiTitle);

        $jsonld = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $title,
            'description' => $description,
            'itemListElement' => $packages->map(fn($p, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'item' => $this->packageSchema($p),
            ])->toArray(),
        ];

        return $this->renderSeoResult($title, $description, $packages, $jsonld, $content);
    }

    public function sitemap()
    {
        $packages = Package::where('is_active', true)->orderBy('updated_at', 'desc')->get();
        $baseUrl = config('app.url');

        $urls = [];

        // Home
        $urls[] = ['loc' => "{$baseUrl}", 'priority' => '1.0', 'changefreq' => 'daily'];

        // Static pages
        $urls[] = ['loc' => "{$baseUrl}/paket-umrah", 'priority' => '0.9', 'changefreq' => 'daily'];
        $urls[] = ['loc' => "{$baseUrl}/blog", 'priority' => '0.8', 'changefreq' => 'weekly'];
        $urls[] = ['loc' => "{$baseUrl}/faq", 'priority' => '0.6', 'changefreq' => 'monthly'];
        $urls[] = ['loc' => "{$baseUrl}/tentang-kami", 'priority' => '0.7', 'changefreq' => 'monthly'];
        $urls[] = ['loc' => "{$baseUrl}/kontak", 'priority' => '0.7', 'changefreq' => 'monthly'];

        // Blog posts
        $blogPosts = \App\Models\BlogPost::where('is_published', true)->orderBy('updated_at', 'desc')->get();
        foreach ($blogPosts as $post) {
            $urls[] = [
                'loc' => "{$baseUrl}/blog/{$post->slug}",
                'lastmod' => $post->updated_at->toDateString(),
                'priority' => '0.7',
                'changefreq' => 'monthly',
            ];
        }

        // Dynamic pages
        $pages = \App\Models\Page::where('is_published', true)->orderBy('updated_at', 'desc')->get();
        foreach ($pages as $page) {
            $urls[] = [
                'loc' => "{$baseUrl}/halaman/{$page->slug}",
                'lastmod' => $page->updated_at->toDateString(),
                'priority' => '0.6',
                'changefreq' => 'monthly',
            ];
        }

        // Individual packages
        foreach ($packages as $package) {
            $urls[] = [
                'loc' => "{$baseUrl}/paket-umrah/{$package->slug}",
                'lastmod' => $package->updated_at->toDateString(),
                'priority' => '0.8',
                'changefreq' => 'weekly',
            ];
        }

        // pSEO routes
        $months = range(1, 12);
        $years = range(date('Y') - 1, date('Y') + 1);

        foreach ($years as $year) {
            foreach ($months as $month) {
                $urls[] = [
                    'loc' => "{$baseUrl}/s/paket-umrah-" . $this->monthNames[$month] . "-{$year}",
                    'priority' => '0.5',
                    'changefreq' => 'monthly',
                ];
            }
        }

        foreach ($years as $year) {
            $urls[] = [
                'loc' => "{$baseUrl}/s/best-umrah-packages-{$year}",
                'priority' => '0.6',
                'changefreq' => 'monthly',
            ];
        }

        foreach ($this->validCategories as $cat) {
            $urls[] = [
                'loc' => "{$baseUrl}/s/umrah-{$cat}",
                'priority' => '0.6',
                'changefreq' => 'monthly',
            ];
        }

        $urls[] = ['loc' => "{$baseUrl}/s/travel-umrah-murah", 'priority' => '0.7', 'changefreq' => 'weekly'];

        foreach ($years as $year) {
            $urls[] = [
                'loc' => "{$baseUrl}/s/haji-plus-{$year}",
                'priority' => '0.5',
                'changefreq' => 'monthly',
            ];
        }

        // Departure cities from existing packages
        $cities = Package::where('is_active', true)
            ->whereNotNull('departure_airport')
            ->pluck('departure_airport')
            ->unique()
            ->map(fn($c) => Str::slug(strtolower($c)))
            ->filter();

        foreach ($cities as $city) {
            $urls[] = [
                'loc' => "{$baseUrl}/s/umrah-dari-{$city}",
                'priority' => '0.5',
                'changefreq' => 'monthly',
            ];
        }

        // Comparison and alternative pages from existing packages
        $slugs = $packages->pluck('slug');
        foreach ($slugs as $slug) {
            $urls[] = [
                'loc' => "{$baseUrl}/s/alternatives-to-{$slug}",
                'priority' => '0.4',
                'changefreq' => 'monthly',
            ];
        }

        $content = view('marketing.sitemap-xml', compact('urls'))->render();

        return response($content, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots()
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\$\n";
        $content .= "Allow: /docs\n";
        $content .= "Allow: /blog\n";
        $content .= "Allow: /faq\n";
        $content .= "Allow: /halaman/\n";
        $content .= "Allow: /paket-umrah\n";
        $content .= "Allow: /tentang-kami\n";
        $content .= "Allow: /kontak\n";
        $content .= "Allow: /s/\n";
        $content .= "Disallow: /admin\n";
        $content .= "Disallow: /api\n";
        $content .= "Sitemap: " . config('app.url') . "/sitemap/sitemap.xml\n";

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }

    // ─── Helper Methods ───

    protected function resolveMonth($bulan): ?int
    {
        $bulan = strtolower($bulan);
        $search = array_search($bulan, $this->monthNames);
        if ($search !== false) {
            return $search;
        }
        if (is_numeric($bulan) && $bulan >= 1 && $bulan <= 12) {
            return (int) $bulan;
        }
        return null;
    }

    protected function packageSchema(Package $package): array
    {
        return [
            '@type' => 'Product',
            'name' => $package->name,
            'description' => strip_tags(substr($this->packageExcerpt($package), 0, 160)),
            'offers' => [
                '@type' => 'Offer',
                'price' => $package->price_quad > 0 ? $package->price_quad : $package->price_triple,
                'priceCurrency' => 'IDR',
                'availability' => $package->available_count > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => route('package.detail', $package->slug),
            ],
        ];
    }

    protected function packageExcerpt(Package $package): string
    {
        $parts = [];
        if ($package->airline) {
            $parts[] = "Maskapai: {$package->airline}";
        }
        if ($package->duration_days) {
            $parts[] = "Durasi: {$package->duration_days} hari";
        }
        if ($package->makkah_hotel_name) {
            $parts[] = "Hotel Makkah: {$package->makkah_hotel_name} ({$package->makkah_hotel_stars}★)";
        }
        if ($package->madinah_hotel_name) {
            $parts[] = "Hotel Madinah: {$package->madinah_hotel_name} ({$package->madinah_hotel_stars}★)";
        }
        return implode('. ', $parts) . '.';
    }

    protected function priceRange(Package $p): string
    {
        if ($p->price_quad > 0) {
            return 'Rp ' . number_format($p->price_quad, 0, ',', '.');
        }
        if ($p->price_triple > 0) {
            return 'Rp ' . number_format($p->price_triple, 0, ',', '.');
        }
        if ($p->price_double > 0) {
            return 'Rp ' . number_format($p->price_double, 0, ',', '.');
        }
        return 'Rp ' . number_format($p->price_single, 0, ',', '.');
    }

    // ─── Content Generators (300+ words) ───

    protected function generateMonthContent($packages, $monthName, $tahun): string
    {
        $count = $packages->count();
        $airlines = $packages->pluck('airline')->filter()->unique()->implode(', ');
        $hotels = $packages->pluck('makkah_hotel_name')->filter()->unique()->take(5)->implode(', ');

        $monthNum = array_search($monthName, $this->monthNames);
        $weatherNote = ($monthNum !== false && ($monthNum >= 11 || $monthNum <= 2))
            ? 'Bulan ini termasuk musim dingin di Arab Saudi, sehingga cuaca lebih sejuk dan nyaman untuk beribadah. '
            : 'Cuaca di Arab Saudi pada bulan ini cukup hangat, pastikan Anda menjaga kondisi fisik dengan baik. ';

        return "Mencari paket umrah untuk keberangkatan bulan {$monthName} {$tahun}? Anda berada di tempat yang tepat. "
            . "Kami menyediakan {$count} paket umrah pilihan yang siap berangkat pada {$monthName} {$tahun}. "
            . "Setiap paket telah kami kurasi dengan cermat untuk memastikan kenyamanan dan kekhusyukan ibadah Anda di Tanah Suci.\n\n"
            . "Keberangkatan di bulan {$monthName} memiliki keunikan tersendiri. "
            . $weatherNote
            . "Paket-paket yang tersedia mencakup berbagai pilihan maskapai penerbangan termasuk {$airlines}. "
            . "Untuk akomodasi, Anda dapat memilih hotel-hotel terbaik di Makkah seperti {$hotels}, dengan berbagai pilihan jarak dari Masjidil Haram.\n\n"
            . "Setiap paket umrah kami sudah termasuk tiket pesawat pulang-pergi, visa umrah, akomodasi hotel di Makkah dan Madinah, "
            . "transportasi antar kota, makan sesuai program, manasik umrah, bimbingan ibadah oleh mutawwif berpengalaman, "
            . "serta perlengkapan umrah. Kami juga menyediakan layanan tambahan seperti pembuatan paspor, vaksin meningitis, dan asuransi perjalanan.\n\n"
            . "Untuk mendapatkan harga terbaik, kami sarankan melakukan pemesanan minimal 2-3 bulan sebelum keberangkatan. "
            . "Kuota setiap paket terbatas, jadi segera amankan tempat Anda. Hubungi tim kami untuk konsultasi gratis "
            . "dan dapatkan rekomendasi paket yang paling sesuai dengan kebutuhan dan budget Anda.\n\n"
            . "Tim kami siap membantu Anda dari awal pendaftaran hingga kembali ke Tanah Air. "
            . "Dengan pengalaman lebih dari " . (date('Y') - 2015) . " tahun melayani jamaah Indonesia, "
            . "kami berkomitmen memberikan pelayanan terbaik untuk perjalanan ibadah Anda.";
    }

    protected function generateBestContent($packages, $tahun): string
    {
        $count = $packages->count();

        $content = "Berikut adalah daftar {$count} paket umrah terbaik tahun {$tahun} yang paling banyak diminati oleh jamaah. "
            . "Pemeringkatan didasarkan pada jumlah peminat dan booking yang telah dikonfirmasi, "
            . "sehingga Anda bisa mendapatkan gambaran paket mana yang paling populer dan terpercaya.\n\n";

        foreach ($packages as $i => $p) {
            $content .= ($i + 1) . ". {$p->name} - {$this->priceRange($p)} "
                . ($p->airline ? "(Maskapai: {$p->airline})" : '')
                . ($p->duration_days ? ", Durasi: {$p->duration_days} hari" : '')
                . ". " . ($p->makkah_hotel_name ? "Hotel Makkah: {$p->makkah_hotel_name} {$p->makkah_hotel_stars}★." : '')
                . ($p->madinah_hotel_name ? " Hotel Madinah: {$p->madinah_hotel_name} {$p->madinah_hotel_stars}★." : '')
                . "\n\n";
        }

        $content .= "Setiap paket di atas telah melalui proses kurasi ketat oleh tim kami untuk memastikan kualitas layanan terbaik. "
            . "Kami memilih hotel-hotel dengan lokasi strategis, maskapai penerbangan ternama, dan mutawwif berpengalaman "
            . "yang akan membimbing ibadah Anda selama di Tanah Suci.\n\n"
            . "Harga yang tercantum di atas adalah harga per jamaah untuk kamar quad (4 orang). "
            . "Tersedia juga opsi kamar triple, double, dan single dengan penyesuaian harga. "
            . "Hubungi konsultan kami untuk informasi detail dan ketersediaan terbaru.";

        return $content;
    }

    protected function generateCategoryContent($packages, $categoryLabel): string
    {
        $count = $packages->count();
        $featureDesc = match ($categoryLabel) {
            'Ekonomi' => 'harga terjangkau dengan fasilitas standar yang tetap nyaman',
            'Standard' => 'keseimbangan antara harga dan fasilitas premium',
            'VIP' => 'fasilitas premium dan hotel bintang 4-5 dekat Masjidil Haram',
            'VVIP' => 'layanan eksklusif, hotel bintang 5 dengan lokasi terbaik',
            default => 'berbagai pilihan fasilitas dan harga',
        };

        return "Paket umrah kategori {$categoryLabel} menawarkan {$featureDesc}. "
            . "Saat ini tersedia {$count} paket umrah kategori {$categoryLabel} yang siap diberangkatkan.\n\n"
            . "Kategori {$categoryLabel} sangat cocok bagi Anda yang menginginkan {$featureDesc}. "
            . "Setiap paket sudah mencakup tiket pesawat, visa, hotel, transportasi, makan, dan bimbingan ibadah. "
            . "Yang membedakan antar kategori adalah level hotel, jarak ke Masjidil Haram, jenis maskapai, dan layanan tambahan.\n\n"
            . "Untuk kategori {$categoryLabel}, Anda akan mendapatkan fasilitas yang sesuai dengan standar kenyamanan yang diharapkan. "
            . "Tim kami akan membantu Anda memilih paket yang paling tepat sesuai preferensi dan anggaran yang dimiliki.\n\n"
            . "Segera booking paket umrah {$categoryLabel} pilihan Anda sebelum kuota habis. "
            . "Hubungi kami untuk informasi lebih detail tentang masing-masing paket dan dapatkan penawaran terbaik.";
    }

    protected function generateCityContent($packages, $kotaTitle): string
    {
        $count = $packages->count();

        return "Berencana berangkat umrah dari {$kotaTitle}? Kami menyediakan {$count} paket umrah dengan keberangkatan "
            . "dari {$kotaTitle} yang bisa Anda pilih sesuai jadwal dan budget.\n\n"
            . "Keberangkatan dari {$kotaTitle} memberikan kemudahan bagi jamaah yang berdomisili di sekitar wilayah {$kotaTitle} "
            . "dan sekitarnya. Anda tidak perlu repot melakukan perjalanan jauh ke Jakarta untuk memulai perjalanan ibadah.\n\n"
            . "Setiap paket sudah termasuk penerbangan dari {$kotaTitle} menuju Jeddah atau Madinah, "
            . "akomodasi hotel, transportasi darat, dan bimbingan ibadah oleh mutawwif profesional.\n\n"
            . "Kami bekerja sama dengan berbagai maskapai penerbangan yang melayani rute dari {$kotaTitle}, "
            . "sehingga Anda memiliki banyak pilihan jadwal dan harga. Konsultasikan kebutuhan Anda dengan tim kami "
            . "untuk mendapatkan rekomendasi paket terbaik dari {$kotaTitle}.";
    }

    protected function generateBudgetContent($packages): string
    {
        $count = $packages->count();
        $cheapest = $packages->first();
        $price = $this->priceRange($cheapest);

        return "Mencari travel umrah murah dengan harga terjangkau? Anda berada di halaman yang tepat. "
            . "Kami menyediakan {$count} paket umrah hemat yang tetap mengutamakan kenyamanan dan kekhusyukan ibadah Anda.\n\n"
            . "Paket umrah termurah kami saat ini adalah {$cheapest->name} dengan harga mulai dari {$price} per orang (quad). "
            . "Harga ini sudah termasuk tiket pesawat pulang-pergi, visa umrah, hotel di Makkah dan Madinah, "
            . "transportasi darat, dan bimbingan manasik.\n\n"
            . "Mengapa paket umrah kami bisa lebih terjangkau? Kami bekerja langsung dengan maskapai penerbangan "
            . "dan hotel untuk mendapatkan harga korporat terbaik. Kami juga mengoptimalkan jadwal keberangkatan "
            . "di luar peak season untuk memberikan harga yang lebih ekonomis tanpa mengurangi kualitas layanan.\n\n"
            . "Meskipun hemat, semua paket umrah murah kami tetap memenuhi standar kenyamanan dan keamanan. "
            . "Hotel yang kami pilih memiliki lokasi strategis, bersih, dan nyaman. Mutawwif kami berpengalaman "
            . "dan akan membimbing Anda selama menjalankan ibadah.\n\n"
            . "Tips memilih paket umrah murah: (1) Pesan jauh-jauh hari untuk mendapatkan early bird discount, "
            . "(2) Pilih keberangkatan di luar peak season seperti Ramadhan atau liburan sekolah, "
            . "(3) Pilih kamar quad untuk harga per orang yang lebih hemat, (4) Bandingkan beberapa paket "
            . "sebelum memutuskan. Hubungi tim kami sekarang untuk konsultasi gratis!";
    }

    protected function generateHajiPlusContent($packages, $tahun): string
    {
        $count = $packages->count();

        return "Informasi lengkap paket haji plus tahun {$tahun}. Saat ini tersedia {$count} paket haji plus "
            . "yang bisa Anda pilih untuk menunaikan ibadah haji dengan nyaman dan tenang.\n\n"
            . "Haji plus adalah program haji yang diselenggarakan oleh travel agent resmi (PIHK) dengan masa tunggu "
            . "yang lebih singkat dibandingkan haji reguler. Keunggulan haji plus antara lain: "
            . "waktu tunggu lebih pendek (biasanya 3-7 tahun), fasilitas lebih baik, "
            . "hotel lebih dekat ke Masjidil Haram, dan layanan lebih personal.\n\n"
            . "Setiap paket haji plus {$tahun} sudah termasuk: tiket pesawat PP, visa haji, "
            . "akomodasi hotel di Makkah dan Madinah, transportasi darat, makan 3x sehari, "
            . "bimbingan manasik haji, mutawwif berpengalaman, dan perlengkapan haji.\n\n"
            . "Pendaftaran haji plus memerlukan dokumen: KTP, Kartu Keluarga, Paspor (minimal berlaku 2 tahun), "
            . "Akta Lahir, Buku Nikah (bagi pasangan suami istri), dan pas foto. "
            . "Segera daftarkan diri Anda karena kuota terbatas. Hubungi tim kami untuk informasi "
            . "lebih detail tentang persyaratan, biaya, dan prosedur pendaftaran haji plus {$tahun}.";
    }

    protected function generateAirlineContent($packages, $maskapaiTitle): string
    {
        $count = $packages->count();

        return "Paket umrah menggunakan maskapai {$maskapaiTitle} menjadi pilihan favorit banyak jamaah. "
            . "Saat ini tersedia {$count} paket umrah dengan maskapai {$maskapaiTitle} yang siap berangkat.\n\n"
            . "Mengapa memilih {$maskapaiTitle}? Maskapai {$maskapaiTitle} dikenal dengan pelayanan yang baik, "
            . "ketepatan waktu, dan kenyamanan selama penerbangan. Dengan armada modern dan kru profesional, "
            . "perjalanan ibadah Anda akan terasa lebih nyaman.\n\n"
            . "Setiap paket umrah dengan maskapai {$maskapaiTitle} sudah termasuk tiket pesawat pulang-pergi, "
            . "bagasi sesuai ketentuan maskapai, dan layanan selama penerbangan. "
            . "Kombinasikan dengan pilihan hotel dan durasi yang sesuai dengan kebutuhan Anda.\n\n"
            . "Pilih paket umrah {$maskapaiTitle} terbaik untuk perjalanan ibadah Anda. "
            . "Hubungi tim kami untuk informasi jadwal terbaru dan penawaran spesial.";
    }

    protected function generateAlternativesContent($source, $alternatives): string
    {
        $count = $alternatives->count();

        return "Sedang mencari alternatif dari {$source->name}? Kami telah menyusun {$count} paket umrah alternatif "
            . "yang memiliki karakteristik serupa dengan {$source->name}.\n\n"
            . "{$source->name} adalah paket umrah dengan tipe " . ucfirst(str_replace('_', ' ', $source->type))
            . ", kategori " . ucfirst($source->category)
            . ", durasi {$source->duration_days} hari"
            . ($source->airline ? ", menggunakan maskapai {$source->airline}" : '')
            . ". Jika Anda mencari opsi lain dengan spesifikasi serupa, berikut alternatif yang bisa dipertimbangkan.\n\n"
            . "Bandingkan harga, fasilitas hotel, maskapai, dan jadwal keberangkatan dari masing-masing alternatif. "
            . "Setiap paket memiliki keunggulan masing-masing yang bisa disesuaikan dengan preferensi Anda.\n\n"
            . "Tim kami siap membantu Anda membandingkan dan memilih paket terbaik. "
            . "Hubungi kami untuk konsultasi gratis dan dapatkan penawaran khusus.";
    }

    protected function generateCompareAnswer($a, $b): string
    {
        return "{$a->name} adalah paket dengan durasi {$a->duration_days} hari, "
            . ($a->airline ? "maskapai {$a->airline}, " : '')
            . "hotel Makkah {$a->makkah_hotel_name} ({$a->makkah_hotel_stars}★) jarak {$a->makkah_hotel_distance_meters}m, "
            . "harga mulai {$this->priceRange($a)}. "
            . "Sementara {$b->name} adalah paket dengan durasi {$b->duration_days} hari, "
            . ($b->airline ? "maskapai {$b->airline}, " : '')
            . "hotel Makkah {$b->makkah_hotel_name} ({$b->makkah_hotel_stars}★) jarak {$b->makkah_hotel_distance_meters}m, "
            . "harga mulai {$this->priceRange($b)}.";
    }

    protected function generatePriceAnswer($a, $b): string
    {
        $priceA = $a->price_quad > 0 ? $a->price_quad : ($a->price_triple > 0 ? $a->price_triple : $a->price_double);
        $priceB = $b->price_quad > 0 ? $b->price_quad : ($b->price_triple > 0 ? $b->price_triple : $b->price_double);

        if ($priceA < $priceB) {
            return "{$a->name} lebih terjangkau dengan harga mulai {$this->priceRange($a)} dibandingkan {$b->name} yang dibanderol mulai {$this->priceRange($b)}. Selisih harga sekitar Rp " . number_format($priceB - $priceA, 0, ',', '.') . ".";
        } elseif ($priceB < $priceA) {
            return "{$b->name} lebih terjangkau dengan harga mulai {$this->priceRange($b)} dibandingkan {$a->name} yang dibanderol mulai {$this->priceRange($a)}. Selisih harga sekitar Rp " . number_format($priceA - $priceB, 0, ',', '.') . ".";
        }
        return "Kedua paket memiliki harga yang hampir sama. {$a->name} dibanderol mulai {$this->priceRange($a)} dan {$b->name} mulai {$this->priceRange($b)}. Perbedaan terdapat pada fasilitas hotel, maskapai, dan durasi.";
    }
}
