@extends('layouts.marketing')

@section('title', ($settings['contact_title'] ?? 'Hubungi Kami') . ' - ' . ($settings['site_brand_name'] ?? 'Travel Umrah'))

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
                    <a href="/tentang-kami" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Tentang Kami</a>
                    <a href="/kontak" class="text-sm font-medium text-indigo-600">Kontak</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900">{{ $settings['contact_title'] }}</h1>
                <p class="mt-3 text-gray-500 max-w-2xl mx-auto">{{ $settings['contact_subtitle'] }}</p>
            </div>

            @if(session('success'))
            <div class="mb-8 p-4 bg-green-50 border border-green-200 rounded-2xl text-green-800 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif

            <div class="grid md:grid-cols-5 gap-8">
                <div class="md:col-span-3 bg-white rounded-2xl shadow-lg p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-6">{{ $settings['contact_form_title'] }}</h2>
                    <form method="POST" action="{{ route('contact.submit') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                            <input type="text" name="name" id="name" required maxlength="255"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition"
                                placeholder="Nama Anda">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon *</label>
                            <input type="text" name="phone" id="phone" required maxlength="20"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition"
                                placeholder="08xxxxxxxxxx">
                            @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="email" maxlength="255"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition"
                                placeholder="email@contoh.com">
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Pesan *</label>
                            <textarea name="message" id="message" required maxlength="2000" rows="5"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition resize-none"
                                placeholder="Tulis pesan atau pertanyaan Anda..."></textarea>
                            @error('message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit"
                            class="w-full py-3.5 bg-gradient-to-r from-indigo-500 to-violet-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-violet-700 transition shadow-lg shadow-indigo-200">
                            {{ $settings['contact_form_btn'] }}
                        </button>
                    </form>
                </div>

                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Informasi Kontak</h3>
                        <ul class="space-y-4 text-sm">
                            <li class="flex items-start gap-3">
                                <span class="w-9 h-9 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </span>
                                <div>
                                    <div class="text-gray-400 text-xs uppercase tracking-wide">Telepon</div>
                                    <div class="font-medium text-gray-900">{{ $settings['contact_phone'] }}</div>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-9 h-9 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                </span>
                                <div>
                                    <div class="text-gray-400 text-xs uppercase tracking-wide">WhatsApp</div>
                                    <div class="font-medium text-gray-900">{{ $settings['contact_whatsapp'] }}</div>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-9 h-9 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </span>
                                <div>
                                    <div class="text-gray-400 text-xs uppercase tracking-wide">Email</div>
                                    <div class="font-medium text-gray-900">{{ $settings['contact_email'] }}</div>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-9 h-9 bg-rose-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </span>
                                <div>
                                    <div class="text-gray-400 text-xs uppercase tracking-wide">Alamat</div>
                                    <div class="font-medium text-gray-900">{{ $settings['contact_address'] }}</div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="font-semibold text-gray-900 mb-3">Jam Operasional</h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex justify-between"><span>Senin - Jumat</span><span class="font-medium">{{ $settings['contact_hours_weekday'] }}</span></li>
                            <li class="flex justify-between"><span>Sabtu</span><span class="font-medium">{{ $settings['contact_hours_saturday'] }}</span></li>
                            <li class="flex justify-between"><span>Minggu</span><span class="font-medium text-gray-400">Tutup</span></li>
                        </ul>
                    </div>
                </div>
            </div>
    </div>
</main>
@endsection
