@extends('layouts.marketing')

@section('title', $settings['site_name'] . ' - ' . $settings['site_tagline'])

@section('content')
{{-- Dynamic Banner Slider --}}
@if($sliders->isNotEmpty())
<div class="relative min-h-[90vh] overflow-hidden banner-slider">
    @php $bgClasses = ['from-white via-gray-50 to-white', 'from-emerald-50 via-white to-teal-50', 'from-sky-50 via-white to-blue-50', 'from-amber-50 via-white to-orange-50']; @endphp

    @foreach($sliders as $i => $slide)
    <div class="banner-slide absolute inset-0 flex items-center bg-gradient-to-br {{ $bgClasses[$i % count($bgClasses)] }} {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}">
        @if($slide->image)
        <div class="absolute right-0 top-0 w-full md:w-1/2 h-full opacity-30 md:opacity-100">
            <img src="{{ asset('storage/' . $slide->image) }}" alt="{{ strip_tags($slide->title) }}" loading="lazy" width="800" height="600" class="absolute inset-0 w-full h-full object-cover" onerror="this.parentElement.remove()">
            <div class="absolute inset-0 bg-gradient-to-r from-white via-white/80 to-transparent md:from-white md:via-white/40 md:to-transparent"></div>
        </div>
        @endif
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full">
            <div class="max-w-3xl relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-navy-50 border border-navy-100 rounded-full mb-6">
                    <span class="w-2 h-2 bg-navy-600 rounded-full animate-pulse"></span>
                    <span class="text-sm text-navy-700 font-semibold">{{ $settings['hero_badge'] ?? 'Terpercaya Sejak 2015' }}</span>
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-navy-900 leading-tight tracking-tight">{!! strip_tags($slide->title, '<br><strong><em><span><b><i>') !!}</h1>
                @if($slide->subtitle)
                <p class="mt-6 text-lg text-navy-700/70 leading-relaxed max-w-xl">{{ $slide->subtitle }}</p>
                @endif
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    @if($slide->link_url)
                    <a href="{{ $slide->link_url }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-gold-500 to-gold-600 text-white font-bold rounded-xl hover:from-gold-400 hover:to-gold-500 transition shadow-xl shadow-gold-500/25 text-lg">
                        {{ $slide->link_text ?? 'Selengkapnya' }}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    @else
                    <a href="{{ route('packages') }}#pesan" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-gold-500 to-gold-600 text-white font-bold rounded-xl hover:from-gold-400 hover:to-gold-500 transition shadow-xl shadow-gold-500/25 text-lg">
                        {{ $settings['hero_cta_primary'] ?? 'Lihat Paket Umrah' }}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    @endif
                    <a href="/kontak" class="inline-flex items-center px-8 py-4 bg-navy-50 border-2 border-navy-200 text-navy-700 font-semibold rounded-xl hover:bg-navy-100 transition">
                        {{ $settings['hero_cta_secondary'] ?? 'Konsultasi Gratis' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @if($sliders->count() > 1)
    <button onclick="bannerPrev()" aria-label="Slide sebelumnya" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-white/80 border border-gray-200 rounded-full hidden md:flex items-center justify-center text-navy-700 hover:bg-white transition shadow-md">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button onclick="bannerNext()" aria-label="Slide berikutnya" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-white/80 border border-gray-200 rounded-full hidden md:flex items-center justify-center text-navy-700 hover:bg-white transition shadow-md">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>
    <div class="absolute bottom-24 left-1/2 -translate-x-1/2 z-20 flex gap-2" role="tablist" aria-label="Pilih slide">
        @for($j = 0; $j < $sliders->count(); $j++)
        <button onclick="bannerGo({{ $j }})" aria-label="Ke slide {{ $j + 1 }}" aria-current="{{ $j === 0 ? 'true' : 'false' }}" class="banner-dot w-3 h-3 rounded-full transition-all duration-300 bg-navy-300 hover:bg-navy-400 {{ $j === 0 ? '!bg-navy-700 !w-8' : '' }}" data-index="{{ $j }}"></button>
        @endfor
    </div>
    @endif
</div>

@push('scripts')
{{-- Slider logic dibundel di resources/js/app.js (initBannerSlider). Inline dihapus agar tidak double timer. --}}
@endpush
@else
{{-- Fallback static slider if no dynamic slides --}}
<div class="relative min-h-[90vh] overflow-hidden">
    <div class="absolute inset-0 flex items-center bg-gradient-to-br from-white via-gray-50 to-white">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full">
            <div class="max-w-3xl relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-navy-50 border border-navy-100 rounded-full mb-6">
                    <span class="w-2 h-2 bg-navy-600 rounded-full animate-pulse"></span>
                    <span class="text-sm text-navy-700 font-semibold">{{ $settings['hero_badge'] ?? 'Terpercaya Sejak 2015' }}</span>
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-navy-900 leading-tight tracking-tight">{!! $settings['hero_title'] !!}</h1>
                <p class="mt-6 text-lg text-navy-700/70 leading-relaxed max-w-xl">{{ $settings['hero_subtitle'] }}</p>
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('packages') }}#pesan" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-gold-500 to-gold-600 text-white font-bold rounded-xl hover:from-gold-400 hover:to-gold-500 transition shadow-xl shadow-gold-500/25 text-lg">
                        {{ $settings['hero_cta_primary'] ?? 'Lihat Paket Umrah' }}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="/kontak" class="inline-flex items-center px-8 py-4 bg-navy-50 border-2 border-navy-200 text-navy-700 font-semibold rounded-xl hover:bg-navy-100 transition">
                        {{ $settings['hero_cta_secondary'] ?? 'Konsultasi Gratis' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Trust Strip --}}
<section class="relative -mt-16 z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ([
                ['icon' => '👨‍👩‍👧‍👦', 'color' => 'indigo', 'title' => $settings['trust_1_title'] ?? 'Keluarga', 'desc' => $settings['trust_1_desc'] ?? 'Paket nyaman untuk seluruh anggota keluarga'],
                ['icon' => '🧓', 'color' => 'amber', 'title' => $settings['trust_2_title'] ?? 'Lansia', 'desc' => $settings['trust_2_desc'] ?? 'Fasilitas khusus dan pendampingan lansia'],
                ['icon' => '🕌', 'color' => 'green', 'title' => $settings['trust_3_title'] ?? 'Pertama Kali', 'desc' => $settings['trust_3_desc'] ?? 'Bimbingan intensif untuk jamaah pemula'],
                ['icon' => '🏢', 'color' => 'rose', 'title' => $settings['trust_4_title'] ?? 'Grup', 'desc' => $settings['trust_4_desc'] ?? 'Paket rombongan dengan harga spesial'],
            ] as $t)
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all text-center">
                <div class="text-3xl mb-3">{{ $t['icon'] }}</div>
                <h3 class="font-bold text-sm text-gray-900">{{ $t['title'] }}</h3>
                <p class="text-xs text-gray-500 mt-1">{{ $t['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Packages --}}
<section id="pesan" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-xs font-semibold text-brand-600 uppercase tracking-widest">Pilihan Terbaik</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-2">{{ $settings['section_packages_title'] ?? 'Paket Umrah Pilihan' }}</h2>
            <p class="mt-3 text-gray-500 max-w-xl mx-auto">{{ $settings['section_packages_subtitle'] ?? 'Pilihan paket umrah terbaik dengan berbagai opsi keberangkatan dan fasilitas premium' }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($packages as $p)
            <a href="{{ route('package.detail', $p->slug) }}" class="group bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all flex flex-col">
                <div class="h-52 relative overflow-hidden">
                    @if ($p->banner_image)
                        <img src="{{ asset('storage/' . $p->banner_image) }}" alt="{{ $p->name }}" loading="lazy" width="600" height="400" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <img src="{{ asset('marketing/default-package.jpg') }}" alt="{{ $p->name }}" loading="lazy" width="600" height="400" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-navy-900/70 to-transparent"></div>
                    @endif
                    <div class="absolute top-4 left-4 flex gap-2">
                        <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-xs font-bold text-navy-700 rounded-lg uppercase">{{ $p->type }}</span>
                        @if ($p->is_featured)
                        <span class="px-3 py-1 bg-amber-400 text-xs font-bold text-amber-900 rounded-lg">BEST SELLER</span>
                        @endif
                    </div>
                    @if ($p->available_count <= 5 && $p->available_count > 0)
                    <div class="absolute top-4 right-4 px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-lg animate-pulse">⚡ Sisa {{ $p->available_count }}</div>
                    @endif
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
                        <span>✈️ {{ $p->airline }}</span>
                        <span>🕌 {{ $p->makkah_hotel_stars }}★</span>
                        <span>📅 {{ $p->duration_days }}H</span>
                    </div>
                    <h3 class="font-bold text-gray-900 group-hover:text-brand-700 transition line-clamp-2 mb-3">{{ $p->name }}</h3>
                    <div class="mt-auto">
                        <div class="flex items-baseline gap-1 mb-3">
                            <span class="text-xs text-gray-400">Mulai dari</span>
                            <span class="text-xl font-extrabold text-gold-600">Rp {{ number_format($p->price_quad, 0, ',', '.') }}</span>
                            <span class="text-xs text-gray-400">/org</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">📅 {{ $p->departure_date?->format('d M Y') }}</span>
                            <span class="inline-flex items-center px-4 py-2 bg-navy-600 text-white text-xs font-semibold rounded-lg group-hover:bg-brand-600 transition">Pesan</span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('packages') }}" class="inline-flex items-center px-6 py-3 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-brand-300 hover:text-brand-600 transition">
                Lihat Semua Paket
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- Kelebihan Kami --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-xs font-semibold text-brand-600 uppercase tracking-widest">Mengapa Memilih Kami</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-2">Kelebihan Kami</h2>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach ([
                ['icon' => '🛡️', 'title' => 'Resmi & Terpercaya', 'desc' => 'Terdaftar resmi di Kemenag RI dengan izin operasional yang sah.'],
                ['icon' => '🏨', 'title' => 'Hotel Strategis', 'desc' => 'Hotel bintang 4-5 dekat Masjidil Haram dan Masjid Nabawi.'],
                ['icon' => '👨‍🏫', 'title' => 'Muthawwif Berpengalaman', 'desc' => 'Dibimbing oleh muthawwif tersertifikasi yang berpengalaman.'],
                ['icon' => '💎', 'title' => 'Harga Kompetitif', 'desc' => 'Berbagai pilihan paket dengan harga terbaik dan fasilitas premium.'],
                ['icon' => '📋', 'title' => 'Dokumen Lengkap', 'desc' => 'Bantuan pengurusan visa, paspor, dan dokumen perjalanan.'],
                ['icon' => '🕐', 'title' => 'Layanan 24 Jam', 'desc' => 'Tim support siap membantu kapanpun Anda butuhkan.'],
                ['icon' => '🍽️', 'title' => 'Katering Halal', 'desc' => 'Makanan halal 3x sehari dengan menu khas Indonesia.'],
                ['icon' => '🚌', 'title' => 'Transportasi Nyaman', 'desc' => 'Bus AC eksklusif untuk perjalanan ibadah Anda.'],
            ] as $kb)
            <div class="text-center group">
                <div class="w-16 h-16 bg-brand-50 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-brand-100 group-hover:scale-110 transition-all text-2xl">{{ $kb['icon'] }}</div>
                <h3 class="font-bold text-gray-900 mb-2">{{ $kb['title'] }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed">{{ $kb['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Stats + Testimonial --}}
<section class="py-20 bg-gradient-to-br from-navy-700 to-brand-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center mb-16">
            <div>
                <div class="text-5xl font-extrabold text-white">{{ number_format($stats['packages']) }}+</div>
                <div class="text-brand-200 text-sm mt-2 font-medium">{{ $settings['stat_packages_label'] ?? 'Paket Umrah & Haji' }}</div>
            </div>
            <div>
                <div class="text-5xl font-extrabold text-white">{{ number_format($stats['pilgrims']) }}+</div>
                <div class="text-brand-200 text-sm mt-2 font-medium">{{ $settings['stat_pilgrims_label'] ?? 'Jamaah Berangkat' }}</div>
            </div>
            <div>
                <div class="text-5xl font-extrabold text-white">{{ $stats['branches'] }}</div>
                <div class="text-brand-200 text-sm mt-2 font-medium">{{ $settings['stat_branches_label'] ?? 'Cabang di Indonesia' }}</div>
            </div>
            <div>
                <div class="text-5xl font-extrabold text-white">{{ $stats['years'] }}+</div>
                <div class="text-brand-200 text-sm mt-2 font-medium">{{ $settings['stat_years_label'] ?? 'Tahun Pengalaman' }}</div>
            </div>
        </div>

        @if($testimonials->isNotEmpty())
        <div id="testimonial-slider" class="relative">
            @foreach($testimonials as $i => $t)
            <div class="testimonial-item text-center {{ $i === 0 ? '' : 'hidden' }}" data-index="{{ $i }}">
                <p class="text-white/70 text-lg italic max-w-2xl mx-auto">"{{ $t->content }}"</p>
                <div class="mt-6">
                    @if($t->photo)
                    <img src="{{ asset('storage/' . $t->photo) }}" alt="Foto {{ $t->name }}" loading="lazy" width="64" height="64" class="w-16 h-16 rounded-full mx-auto mb-3 object-cover border-2 border-white/20">
                    @endif
                    <div class="text-white font-semibold">{{ $t->name }}</div>
                    @if($t->role)
                    <div class="text-indigo-300 text-sm">{{ $t->role }}</div>
                    @endif
                    <div class="flex justify-center gap-1 mt-2 text-amber-400">{{ str_repeat('★', $t->rating) }}{{ str_repeat('☆', 5 - $t->rating) }}</div>
                </div>
            </div>
            @endforeach
            @if($testimonials->count() > 1)
            <div class="flex justify-center gap-2 mt-8">
                @foreach($testimonials as $j => $t)
                <button onclick="testimonialGo({{ $j }})" class="testimonial-dot w-3 h-3 rounded-full transition-all duration-300 bg-white/30 hover:bg-white/50 {{ $j === 0 ? '!bg-white !w-8' : '' }}" data-index="{{ $j }}"></button>
                @endforeach
            </div>
            @endif
        </div>
        @push('scripts')
        {{-- Testimonial logic dibundel di resources/js/app.js (initTestimonials). --}}
        @endpush
        @else
        <div class="text-center">
            <p class="text-white/70 text-lg italic max-w-2xl mx-auto">"Alhamdulillah, perjalanan umrah bersama Travel Umrah sangat berkesan. Pelayanan prima, hotel dekat masjid, muthawwif sangat membantu."</p>
            <div class="mt-6">
                <div class="text-white font-semibold">Bpk. Ahmad Fauzi</div>
                <div class="text-indigo-300 text-sm">Jamaah Umrah Ramadhan 2025</div>
                <div class="flex justify-center gap-1 mt-2 text-amber-400">★★★★★</div>
            </div>
        </div>
        @endif
    </div>
</section>

{{-- Final CTA --}}
<section class="py-20 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">{{ $settings['cta_title'] ?? 'Siap Berangkat ke Tanah Suci?' }}</h2>
        <p class="mt-4 text-lg text-gray-500">{{ $settings['cta_subtitle'] ?? 'Konsultasikan rencana perjalanan umrah Anda dengan tim kami. Gratis, tanpa komitmen.' }}</p>
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('packages') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-gold-500 to-gold-600 text-white font-bold rounded-xl hover:from-gold-400 hover:to-gold-500 transition shadow-xl shadow-gold-500/25">
                {{ $settings['cta_btn_primary'] ?? 'Pilih Paket Umrah' }}
            </a>
            <a href="/kontak" class="inline-flex items-center px-8 py-4 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-indigo-300 hover:text-indigo-600 transition">
                {{ $settings['cta_btn_secondary'] ?? 'Hubungi Kami' }}
            </a>
        </div>
    </div>
</section>
@endsection
