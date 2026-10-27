@extends('layouts.marketing')

@section('title', ($settings['about_title'] ?? 'Tentang Kami') . ' - ' . ($settings['site_brand_name'] ?? 'Travel Umrah'))

@section('content')
<main class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <span class="text-lg font-bold text-gray-900">{{ $settings['site_brand_name'] }}</span>
                </a>
                <nav class="hidden md:flex items-center gap-6">
                    <a href="/" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Beranda</a>
                    <a href="/paket-umrah" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Paket Umrah</a>
                    <a href="{{ route('packages') }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Pesan</a>
                    <a href="/tentang-kami" class="text-sm font-medium text-indigo-600">Tentang Kami</a>
                    <a href="/kontak" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Kontak</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900">{{ $settings['about_title'] }}</h1>
                <p class="mt-3 text-gray-500 max-w-2xl mx-auto">{{ $settings['about_subtitle'] }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-8 sm:p-10 space-y-8">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Siapa Kami</h2>
                    <div class="text-gray-600 leading-relaxed space-y-3">
                        {!! $settings['about_text'] !!}
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Visi</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $settings['vision_text'] }}</p>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Misi</h2>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3 text-gray-600">
                            <svg class="w-5 h-5 text-indigo-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ $settings['mission_1'] }}
                        </li>
                        <li class="flex items-start gap-3 text-gray-600">
                            <svg class="w-5 h-5 text-indigo-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ $settings['mission_2'] }}
                        </li>
                        <li class="flex items-start gap-3 text-gray-600">
                            <svg class="w-5 h-5 text-indigo-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ $settings['mission_3'] }}
                        </li>
                        <li class="flex items-start gap-3 text-gray-600">
                            <svg class="w-5 h-5 text-indigo-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ $settings['mission_4'] }}
                        </li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Mengapa Memilih Kami</h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="bg-indigo-50 rounded-xl p-5 border border-indigo-100">
                            <h3 class="font-semibold text-indigo-800 text-sm mb-1">{{ $settings['why_1_title'] ?? 'Berpengalaman' }}</h3>
                            <p class="text-sm text-indigo-600">{{ $settings['why_1_desc'] ?? 'Pengalaman sejak 2015 dengan ribuan jamaah puas.' }}</p>
                        </div>
                        <div class="bg-green-50 rounded-xl p-5 border border-green-100">
                            <h3 class="font-semibold text-green-800 text-sm mb-1">{{ $settings['why_2_title'] ?? 'Hotel Strategis' }}</h3>
                            <p class="text-sm text-green-600">{{ $settings['why_2_desc'] ?? 'Hotel dekat Masjidil Haram dan Masjid Nabawi.' }}</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-5 border border-amber-100">
                            <h3 class="font-semibold text-amber-800 text-sm mb-1">{{ $settings['why_3_title'] ?? 'Bimbingan Lengkap' }}</h3>
                            <p class="text-sm text-amber-600">{{ $settings['why_3_desc'] ?? 'Manasik dan pendampingan oleh mutawwif berpengalaman.' }}</p>
                        </div>
                        <div class="bg-violet-50 rounded-xl p-5 border border-violet-100">
                            <h3 class="font-semibold text-violet-800 text-sm mb-1">{{ $settings['why_4_title'] ?? 'Harga Kompetitif' }}</h3>
                            <p class="text-sm text-violet-600">{{ $settings['why_4_desc'] ?? 'Berbagai pilihan paket sesuai budget Anda.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</main>
@endsection
