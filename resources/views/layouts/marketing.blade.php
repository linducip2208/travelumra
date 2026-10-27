<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Travel Umrah')</title>
    <meta name="description" content="@yield('meta_desc', 'Travel umrah dan haji terpercaya. Paket lengkap, hotel strategis, bimbingan profesional.')">
    <meta property="og:title" content="@yield('og_title', 'Travel Umrah')">
    <meta property="og:description" content="@yield('og_desc', 'Travel umrah dan haji terpercaya. Paket lengkap, hotel strategis, bimbingan profesional.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="{{ url()->current() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#f0fdfa', 100: '#ccfbf1', 200: '#99f6e4', 300: '#5eead4',
                            400: '#2dd4bf', 500: '#0d9488', 600: '#0f766e', 700: '#115e59',
                            800: '#134e4a', 900: '#042f2e',
                        },
                        navy: {
                            50: '#f0f4fd', 100: '#e0e8f9', 200: '#b9cdf2', 300: '#8daee8',
                            400: '#608bdc', 500: '#3b6dcb', 600: '#1e3a8a', 700: '#1a2f6e',
                            800: '#162556', 900: '#0f1a3d',
                        },
                        gold: {
                            400: '#fbbf24', 500: '#f59e0b', 600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-white text-gray-900 font-sans antialiased" x-data="{
    mobileMenu: false,
    showPopup: false,
    init() {
        if (!localStorage.getItem('sales_popup_closed')) {
            this.showPopup = true;
            setTimeout(() => { this.showPopup = false; localStorage.setItem('sales_popup_closed', '1'); }, 10000);
        }
    },
    closePopup() { this.showPopup = false; localStorage.setItem('sales_popup_closed', '1'); }
}">

{{-- Sales Popup --}}
<div x-show="showPopup" x-transition.opacity.duration.500ms class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none;">
    <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm" @click="closePopup()"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden animate-bounce-in">
        <button @click="closePopup()" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-600 transition z-10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 px-8 py-10 text-center">
            <div class="w-16 h-16 mx-auto bg-white/20 rounded-2xl flex items-center justify-center mb-4 backdrop-blur-sm">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            </div>
            <h2 class="text-2xl font-black text-white leading-tight">JUAL SOURCE CODE</h2>
            <p class="text-white/80 mt-1 text-sm font-medium">&amp; Terima Jasa Customisasi Aplikasi</p>
        </div>
        <div class="px-8 py-6 space-y-4">
            <div class="flex items-center gap-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl border border-green-200 p-4">
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center shadow-lg shadow-green-500/25 flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-green-700 font-semibold uppercase tracking-wider">WhatsApp</p>
                    <p class="text-xl font-black text-green-800 tracking-tight">0812-9605-2010</p>
                </div>
            </div>
            <div class="space-y-2 text-sm text-slate-600">
                <div class="flex items-start gap-2"><span class="w-5 h-5 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">1</span><span>Source code siap pakai — tinggal deploy</span></div>
                <div class="flex items-start gap-2"><span class="w-5 h-5 bg-violet-100 text-violet-600 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">2</span><span>Custom fitur sesuai kebutuhan bisnis Anda</span></div>
                <div class="flex items-start gap-2"><span class="w-5 h-5 bg-fuchsia-100 text-fuchsia-600 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">3</span><span>Rebranding & white-label siap jual kembali</span></div>
            </div>
            <a href="https://wa.me/6281296052010?text=Halo%2C%20saya%20tertarik%20dengan%20source%20code%20travel%20umrah" class="block w-full py-3.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold text-center rounded-xl hover:from-green-600 hover:to-emerald-700 transition shadow-lg shadow-green-500/25 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Chat WhatsApp Sekarang
            </a>
            <p class="text-center text-[10px] text-slate-400">Popup ini muncul sekali saja. Klik <button @click="closePopup()" class="underline hover:text-slate-600 font-semibold">tutup</button> untuk menutup.</p>
        </div>
    </div>
</div>

<style>
@keyframes bounceIn{0%{opacity:0;transform:scale(0.8)}50%{transform:scale(1.03)}100%{opacity:1;transform:scale(1)}}
.animate-bounce-in{animation:bounceIn 0.5s ease-out}
</style>
{{-- End Popup --}}

    {{-- Header --}}
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center gap-2 shrink-0">
                    <div class="w-9 h-9 bg-gradient-to-br from-navy-600 to-brand-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <span class="text-lg font-bold text-navy-700">{{ $settings['site_brand_name'] ?? 'Travel Umrah' }}</span>
                </a>

                {{-- Desktop Nav --}}
                <nav class="hidden md:flex items-center gap-8">
                    <a href="/" class="text-sm font-medium {{ request()->is('/') ? 'text-brand-600' : 'text-gray-600 hover:text-brand-600' }} transition">Beranda</a>
                    <a href="{{ route('packages') }}" class="text-sm font-medium {{ request()->is('paket-umrah*') ? 'text-brand-600' : 'text-gray-600 hover:text-brand-600' }} transition">Paket Umrah</a>
                    <a href="{{ route('blog') }}" class="text-sm font-medium {{ request()->is('blog*') ? 'text-brand-600' : 'text-gray-600 hover:text-brand-600' }} transition">Blog</a>
                    <a href="{{ route('faq') }}" class="text-sm font-medium {{ request()->is('faq*') ? 'text-brand-600' : 'text-gray-600 hover:text-brand-600' }} transition">FAQ</a>
                    <a href="/tentang-kami" class="text-sm font-medium {{ request()->is('tentang-kami') ? 'text-brand-600' : 'text-gray-600 hover:text-brand-600' }} transition">Tentang</a>
                    <a href="/kontak" class="text-sm font-medium {{ request()->is('kontak') ? 'text-brand-600' : 'text-gray-600 hover:text-brand-600' }} transition">Kontak</a>
                </nav>

                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ route('portal.login') }}" class="text-sm text-gray-500 hover:text-brand-600 transition">Login Jemaah</a>
                    <a href="{{ route('packages') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-navy-600 to-brand-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition">
                        Pesan Sekarang
                    </a>
                </div>

                {{-- Mobile Hamburger --}}
                <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-gray-600 hover:text-brand-600 transition" aria-label="Menu">
                    <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenu" x-transition @click.outside="mobileMenu = false" class="md:hidden bg-white border-b border-gray-100 shadow-lg">
            <nav class="px-4 py-4 space-y-2">
                <a href="/" class="block px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-brand-600 rounded-lg hover:bg-gray-50">Beranda</a>
                <a href="{{ route('packages') }}" class="block px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-brand-600 rounded-lg hover:bg-gray-50">Paket Umrah</a>
                <a href="{{ route('blog') }}" class="block px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-brand-600 rounded-lg hover:bg-gray-50">Blog</a>
                <a href="{{ route('faq') }}" class="block px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-brand-600 rounded-lg hover:bg-gray-50">FAQ</a>
                <a href="{{ route('packages') }}" class="block px-3 py-2.5 text-sm font-bold text-gold-600 rounded-lg hover:bg-gold-50">Pesan Sekarang</a>
                <a href="/tentang-kami" class="block px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-brand-600 rounded-lg hover:bg-gray-50">Tentang Kami</a>
                <a href="/kontak" class="block px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-brand-600 rounded-lg hover:bg-gray-50">Kontak</a>
                <hr class="border-gray-100">
                <a href="{{ route('portal.login') }}" class="block px-3 py-2.5 text-sm font-medium text-gray-500 hover:text-brand-600 rounded-lg hover:bg-gray-50">Login Jemaah</a>
            </nav>
        </div>
    </header>

    <main class="pt-16">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-navy-900 text-gray-300 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-gold-500 to-gold-400 rounded-lg flex items-center justify-center">
                            <span class="text-white text-sm font-bold">🕋</span>
                        </div>
                        <span class="text-lg font-bold text-white">{{ $settings['site_brand_name'] ?? 'Travel Umrah' }}</span>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed">{{ $settings['footer_about'] ?? 'Travel umrah dan haji terpercaya melayani jamaah Indonesia.' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Menu</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/" class="hover:text-white transition">Beranda</a></li>
                        <li><a href="{{ route('packages') }}" class="hover:text-white transition">Paket Umrah</a></li>
                        <li><a href="{{ route('blog') }}" class="hover:text-white transition">Blog</a></li>
                        <li><a href="{{ route('faq') }}" class="hover:text-white transition">FAQ</a></li>
                        <li><a href="/tentang-kami" class="hover:text-white transition">Tentang Kami</a></li>
                        <li><a href="/docs" class="hover:text-white transition">Dokumentasi</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Layanan</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('packages') }}" class="hover:text-white transition">Pesan Paket</a></li>
                        <li><a href="{{ route('portal.login') }}" class="hover:text-white transition">Portal Jemaah</a></li>
                        <li><a href="/kontak" class="hover:text-white transition">Konsultasi Gratis</a></li>
                        <li><a href="/sitemap/sitemap.xml" class="hover:text-white transition">Sitemap</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Kontak</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center gap-2"><span>📍</span> {{ $settings['contact_address'] ?? 'Jakarta Pusat' }}</li>
                        <li class="flex items-center gap-2"><span>📞</span> {{ $settings['contact_phone'] ?? '021-xxxx-xxxx' }}</li>
                        <li class="flex items-center gap-2"><span>✉️</span> {{ $settings['contact_email'] ?? 'info@travelumrah.com' }}</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} {{ $settings['site_brand_name'] ?? 'Travel Umrah' }}. {{ $settings['footer_copyright'] ?? 'Seluruh hak cipta dilindungi.' }}</p>
            </div>
        </div>
    </footer>

    {{-- WhatsApp Floating Button --}}
    @php $wa = $settings['contact_whatsapp'] ?? '08xxxxxxxxxx'; $waClean = preg_replace('/[^0-9]/', '', $wa); @endphp
    <a href="https://wa.me/{{ $waClean }}?text=Assalamualaikum%2C%20saya%20mau%20tanya%20soal%20paket%20umrah"
       target="_blank"
       class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all hover:scale-110"
       aria-label="Chat WhatsApp">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </a>

    @stack('scripts')
</body>
</html>
