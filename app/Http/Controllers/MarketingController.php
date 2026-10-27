<?php

namespace App\Http\Controllers;

use App\Helpers\SettingsHelper;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Package;
use App\Models\Slider;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    private function loadCmsSettings(): array
    {
        return [
            'site_name' => SettingsHelper::get('site_name', 'Travel Umrah & Haji'),
            'site_brand_name' => SettingsHelper::get('site_brand_name', 'UmrahKita'),
            'site_tagline' => SettingsHelper::get('site_tagline', 'Travel Umrah & Haji Terpercaya'),
            'hero_title' => SettingsHelper::get('hero_title', 'Perjalanan Umrah & Haji Anda, <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-violet-300">Aman dan Nyaman</span>'),
            'hero_subtitle' => SettingsHelper::get('hero_subtitle', 'Wujudkan impian beribadah ke Tanah Suci bersama kami. Layanan profesional, hotel strategis, bimbingan ibadah lengkap dari tim berpengalaman.'),
            'hero_badge' => SettingsHelper::get('hero_badge', 'Terpercaya sejak 2015'),
            'hero_cta_primary' => SettingsHelper::get('hero_cta_primary', 'Lihat Paket Umrah'),
            'hero_cta_secondary' => SettingsHelper::get('hero_cta_secondary', 'Konsultasi Gratis'),
            'trust_1_title' => SettingsHelper::get('trust_1_title', 'Keluarga'),
            'trust_1_desc' => SettingsHelper::get('trust_1_desc', 'Paket nyaman untuk seluruh anggota keluarga'),
            'trust_2_title' => SettingsHelper::get('trust_2_title', 'Lansia'),
            'trust_2_desc' => SettingsHelper::get('trust_2_desc', 'Fasilitas khusus dan pendampingan lansia'),
            'trust_3_title' => SettingsHelper::get('trust_3_title', 'Pertama Kali'),
            'trust_3_desc' => SettingsHelper::get('trust_3_desc', 'Bimbingan intensif untuk jamaah pemula'),
            'trust_4_title' => SettingsHelper::get('trust_4_title', 'Grup & Perusahaan'),
            'trust_4_desc' => SettingsHelper::get('trust_4_desc', 'Paket rombongan dengan harga spesial'),
            'section_packages_title' => SettingsHelper::get('section_packages_title', 'Paket Umrah Pilihan'),
            'section_packages_subtitle' => SettingsHelper::get('section_packages_subtitle', 'Pilihan paket umrah terbaik dengan berbagai opsi keberangkatan dan fasilitas premium'),
            'cta_title' => SettingsHelper::get('cta_title', 'Siap Berangkat ke Tanah Suci?'),
            'cta_subtitle' => SettingsHelper::get('cta_subtitle', 'Konsultasikan rencana perjalanan umrah atau haji Anda dengan tim kami. Gratis, tanpa komitmen.'),
            'cta_btn_primary' => SettingsHelper::get('cta_btn_primary', 'Pilih Paket Umrah'),
            'cta_btn_secondary' => SettingsHelper::get('cta_btn_secondary', 'Hubungi Kami'),
            'header_btn_text' => SettingsHelper::get('header_btn_text', 'Lihat Paket'),
            'stat_packages_label' => SettingsHelper::get('stat_packages_label', 'Paket Umrah & Haji'),
            'stat_pilgrims_label' => SettingsHelper::get('stat_pilgrims_label', 'Jamaah Berangkat'),
            'stat_branches_label' => SettingsHelper::get('stat_branches_label', 'Cabang di Indonesia'),
            'stat_years_label' => SettingsHelper::get('stat_years_label', 'Tahun Pengalaman'),
            'about_title' => SettingsHelper::get('about_title', 'Tentang Kami'),
            'about_subtitle' => SettingsHelper::get('about_subtitle', 'Mengenal lebih dekat travel umrah dan haji terpercaya Anda'),
            'about_text' => SettingsHelper::get('about_text', '<p>UmrahKita adalah biro perjalanan umrah dan haji yang telah melayani jamaah Indonesia sejak tahun 2015. Kami berkomitmen untuk memberikan pelayanan terbaik dalam setiap perjalanan ibadah, mulai dari persiapan keberangkatan hingga kepulangan ke Tanah Air.</p><p>Dengan tim yang berpengalaman dan jaringan yang luas di Arab Saudi, kami memastikan setiap jamaah mendapatkan pengalaman ibadah yang khusyuk, aman, dan nyaman. Kepuasan dan keselamatan jamaah adalah prioritas utama kami.</p>'),
            'vision_text' => SettingsHelper::get('vision_text', 'Menjadi travel umrah dan haji pilihan utama masyarakat Indonesia dengan pelayanan profesional, amanah, dan berorientasi pada kenyamanan jamaah.'),
            'mission_1' => SettingsHelper::get('mission_1', 'Menyediakan paket umrah dan haji dengan harga kompetitif dan fasilitas berkualitas.'),
            'mission_2' => SettingsHelper::get('mission_2', 'Memberikan bimbingan ibadah yang lengkap oleh mutawwif berpengalaman.'),
            'mission_3' => SettingsHelper::get('mission_3', 'Menjalin kemitraan dengan hotel dan maskapai terbaik untuk kenyamanan jamaah.'),
            'mission_4' => SettingsHelper::get('mission_4', 'Memberikan pelayanan prima dengan prinsip amanah dan transparan.'),
            'footer_about' => SettingsHelper::get('footer_about', 'Travel umrah dan haji terpercaya melayani jamaah Indonesia sejak 2015.'),
            'footer_copyright' => SettingsHelper::get('footer_copyright', 'Seluruh hak cipta dilindungi.'),
            'contact_phone' => SettingsHelper::get('contact_phone', '021-xxxx-xxxx'),
            'contact_whatsapp' => SettingsHelper::get('contact_whatsapp', '08xxxxxxxxxx'),
            'contact_email' => SettingsHelper::get('contact_email', 'info@umrahkita.id'),
            'contact_address' => SettingsHelper::get('contact_address', 'Jl. Contoh No. 123, Jakarta Pusat'),
            'contact_hours_weekday' => SettingsHelper::get('contact_hours_weekday', '09:00 - 18:00 WIB'),
            'contact_hours_saturday' => SettingsHelper::get('contact_hours_saturday', '09:00 - 15:00 WIB'),
            'contact_title' => SettingsHelper::get('contact_title', 'Hubungi Kami'),
            'contact_subtitle' => SettingsHelper::get('contact_subtitle', 'Konsultasikan rencana perjalanan umrah atau haji Anda. Tim kami siap membantu, gratis tanpa komitmen.'),
            'contact_form_title' => SettingsHelper::get('contact_form_title', 'Kirim Pesan'),
            'contact_form_btn' => SettingsHelper::get('contact_form_btn', 'Kirim Pesan'),
            'packages_title' => SettingsHelper::get('packages_title', 'Paket Umrah'),
            'packages_subtitle' => SettingsHelper::get('packages_subtitle', 'Temukan paket umrah dan haji yang sesuai dengan kebutuhan dan budget Anda'),
        ];
    }

    public function home()
    {
        $packages = Package::where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $stats = [
            'packages' => Package::where('is_active', true)->count(),
            'pilgrims' => \App\Models\Pilgrim::count(),
            'branches' => \App\Models\Branch::where('is_active', true)->count(),
            'years' => date('Y') - 2015,
        ];

        $settings = $this->loadCmsSettings();
        $sliders = Slider::where('is_active', true)->orderBy('sort_order')->get();
        $testimonials = Testimonial::where('is_published', true)->orderBy('sort_order')->get();

        return view('marketing.home', compact('packages', 'stats', 'settings', 'sliders', 'testimonials'));
    }

    public function packages()
    {
        $packages = Package::where('is_active', true)
            ->orderBy('departure_date')
            ->paginate(12);

        $settings = $this->loadCmsSettings();

        return view('marketing.packages', compact('packages', 'settings'));
    }

    public function packageDetail($slug)
    {
        $package = Package::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $related = Package::where('is_active', true)
            ->where('id', '!=', $package->id)
            ->where('type', $package->type)
            ->take(4)
            ->get();

        $settings = $this->loadCmsSettings();
        $providers = app(\App\Services\PaymentGatewayService::class)->getAvailableProviders();

        return view('marketing.package-detail', compact('package', 'related', 'settings', 'providers'));
    }

    public function about()
    {
        $settings = $this->loadCmsSettings();

        return view('marketing.about', compact('settings'));
    }

    public function contact()
    {
        $settings = $this->loadCmsSettings();

        return view('marketing.contact', compact('settings'));
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        \App\Models\Lead::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'source' => 'website',
            'status' => 'new',
            'notes' => $validated['message'],
        ]);

        return back()->with('success', 'Pesan Anda telah terkirim. Tim kami akan menghubungi Anda segera.');
    }

    public function blog()
    {
        $posts = BlogPost::with(['category', 'author'])
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        $settings = $this->loadCmsSettings();

        return view('marketing.blog', compact('posts', 'settings'));
    }

    public function blogShow($slug)
    {
        $post = BlogPost::with(['category', 'author'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $relatedPosts = BlogPost::where('is_published', true)
            ->where('id', '!=', $post->id)
            ->where('blog_category_id', $post->blog_category_id)
            ->latest('published_at')
            ->take(3)
            ->get();

        $settings = $this->loadCmsSettings();

        return view('marketing.blog-detail', compact('post', 'relatedPosts', 'settings'));
    }

    public function faq()
    {
        $faqs = Faq::where('is_published', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        $settings = $this->loadCmsSettings();

        return view('marketing.faq', compact('faqs', 'settings'));
    }

    public function page($slug)
    {
        $page = Page::where('slug', $slug)->where('is_published', true)->firstOrFail();
        $settings = $this->loadCmsSettings();

        return view('marketing.page', compact('page', 'settings'));
    }
}
