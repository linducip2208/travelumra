<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url') }}{{ request()->getRequestUri() }}">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <link rel="canonical" href="{{ config('app.url') }}{{ request()->getRequestUri() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']},colors:{primary:{500:'#6366f1',600:'#4f46e5',700:'#4338ca'}}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script type="application/ld+json">{!! json_encode($jsonld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">

    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <span class="text-lg font-bold text-gray-900">{{ $settings['site_brand_name'] ?? 'Travel Umrah' }}</span>
                </a>
                <nav class="hidden md:flex items-center gap-6">
                    <a href="/" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Beranda</a>
                    <a href="/paket-umrah" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Paket Umrah</a>
                    <a href="{{ route('packages') }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Pesan</a>
                    <a href="/tentang-kami" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Tentang Kami</a>
                    <a href="/kontak" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Kontak</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-400 mb-6">
                <a href="/" class="hover:text-indigo-600 transition">Beranda</a>
                <span class="mx-2">/</span>
                <span class="text-gray-600">{{ $title }}</span>
            </nav>

            <div class="text-center mb-12">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900">{{ $title }}</h1>
                <p class="mt-3 text-gray-500 max-w-3xl mx-auto">{{ $description }}</p>
            </div>

            <!-- Comparison Cards -->
            <div class="grid md:grid-cols-2 gap-8 mb-12">
                @php $packages = [$packageA, $packageB]; @endphp
                @foreach($packages as $pkg)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 {{ $loop->first ? 'ring-2 ring-indigo-200' : '' }}">
                    <div class="bg-gradient-to-r from-indigo-500 to-violet-600 px-6 py-3">
                        <h2 class="text-white font-bold text-lg">{{ $pkg->name }}</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex flex-wrap gap-2">
                            <span class="text-xs font-semibold px-2 py-1 bg-indigo-50 text-indigo-700 rounded-md uppercase">{{ $pkg->type }}</span>
                            <span class="text-xs font-semibold px-2 py-1 bg-gray-100 text-gray-600 rounded-md uppercase">{{ $pkg->category }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs text-gray-400">Maskapai</div>
                                <div class="font-semibold">{{ $pkg->airline ?? '-' }}</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs text-gray-400">Durasi</div>
                                <div class="font-semibold">{{ $pkg->duration_days }} Hari</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs text-gray-400">Keberangkatan</div>
                                <div class="font-semibold">{{ $pkg->departure_date->format('d M Y') }}</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs text-gray-400">Kepulangan</div>
                                <div class="font-semibold">{{ $pkg->return_date->format('d M Y') }}</div>
                            </div>
                        </div>

                        @if($pkg->makkah_hotel_name)
                        <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                            <div class="text-xs font-semibold text-amber-700 uppercase tracking-wide mb-1">Hotel Makkah</div>
                            <div class="font-semibold text-gray-900">{{ $pkg->makkah_hotel_name }}</div>
                            <div class="text-sm text-gray-600">{{ str_repeat('★', $pkg->makkah_hotel_stars ?? 0) }} &middot; {{ $pkg->makkah_hotel_distance_meters ?? '-' }}m dari Masjidil Haram</div>
                        </div>
                        @endif

                        @if($pkg->madinah_hotel_name)
                        <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                            <div class="text-xs font-semibold text-emerald-700 uppercase tracking-wide mb-1">Hotel Madinah</div>
                            <div class="font-semibold text-gray-900">{{ $pkg->madinah_hotel_name }}</div>
                            <div class="text-sm text-gray-600">{{ str_repeat('★', $pkg->madinah_hotel_stars ?? 0) }} &middot; {{ $pkg->madinah_hotel_distance_meters ?? '-' }}m dari Masjid Nabawi</div>
                        </div>
                        @endif

                        <div class="bg-gradient-to-br from-indigo-50 to-violet-50 rounded-xl p-4 border border-indigo-100">
                            <div class="text-xs text-indigo-600 uppercase tracking-wide mb-2 font-semibold">Harga Mulai</div>
                            @if($pkg->price_quad > 0)
                            <div class="flex justify-between text-sm mb-1"><span class="text-gray-500">Quad (4)</span><span class="font-semibold">Rp {{ number_format($pkg->price_quad, 0, ',', '.') }}</span></div>
                            @endif
                            @if($pkg->price_triple > 0)
                            <div class="flex justify-between text-sm mb-1"><span class="text-gray-500">Triple (3)</span><span class="font-semibold">Rp {{ number_format($pkg->price_triple, 0, ',', '.') }}</span></div>
                            @endif
                            @if($pkg->price_double > 0)
                            <div class="flex justify-between text-sm mb-1"><span class="text-gray-500">Double (2)</span><span class="font-semibold">Rp {{ number_format($pkg->price_double, 0, ',', '.') }}</span></div>
                            @endif
                        </div>

                        <div class="text-center">
                            <a href="{{ route('package.detail', $pkg->slug) }}" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Comparison Content -->
            <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-10 prose prose-gray max-w-none text-gray-600 leading-relaxed text-sm">
                {!! $content !!}
            </div>

            <!-- CTA -->
            <div class="mt-12 text-center">
                <a href="/paket-umrah" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-500 to-violet-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-violet-700 transition shadow-lg shadow-indigo-200">
                    Lihat Semua Paket Umrah
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </main>

    <footer class="bg-gray-900 text-gray-400 py-10 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm">
            &copy; {{ date('Y') }} {{ $settings['site_brand_name'] ?? 'Travel Umrah' }}. Seluruh hak cipta dilindungi.
        </div>
    </footer>

</body>
</html>
