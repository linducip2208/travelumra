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

            <!-- Content Section -->
            @if($packages->isNotEmpty())
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                @foreach($packages as $p)
                <a href="{{ route('package.detail', $p->slug) }}" class="group bg-white rounded-2xl border border-gray-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">
                    @if($p->banner_image)
                    <div class="h-48 bg-cover bg-center" style="background-image: url('{{ asset('storage/' . $p->banner_image) }}')"></div>
                    @else
                    <div class="h-48 bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center">
                        <svg class="w-12 h-12 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    @endif
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-semibold px-2 py-1 bg-indigo-50 text-indigo-700 rounded-md uppercase">{{ $p->type }}</span>
                            <span class="text-xs font-semibold px-2 py-1 bg-gray-100 text-gray-600 rounded-md uppercase">{{ $p->category }}</span>
                        </div>
                        <h3 class="font-bold text-gray-900 group-hover:text-indigo-600 transition line-clamp-2">{{ $p->name }}</h3>
                        <div class="mt-3 space-y-1 text-sm text-gray-500 flex-1">
                            @if($p->airline)<div class="flex items-center gap-2"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> {{ $p->airline }}</div>@endif
                            <div class="flex items-center gap-2"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> {{ $p->departure_date->format('d M Y') }}</div>
                            <div class="flex items-center gap-2"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> {{ $p->duration_days }} Hari</div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-gray-400">Mulai</span>
                                <div class="text-lg font-extrabold text-indigo-600">
                                    Rp {{ number_format($p->price_quad > 0 ? $p->price_quad : ($p->price_triple > 0 ? $p->price_triple : $p->price_double), 0, ',', '.') }}
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-indigo-600 group-hover:translate-x-1 transition-transform">Detail &rarr;</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="text-center py-16 text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                <p class="text-lg">Tidak ada paket tersedia saat ini.</p>
            </div>
            @endif

            <!-- Long-form Content -->
            <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-10 prose prose-gray max-w-none text-gray-600 leading-relaxed whitespace-pre-line text-sm">
                {!! nl2br(e($content)) !!}
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
