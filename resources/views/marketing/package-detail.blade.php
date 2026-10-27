@extends('layouts.marketing')

@section('title', $package->name . ' - ' . $settings['site_brand_name'])
@section('meta_desc', 'Paket ' . $package->name . ' — ' . $package->airline . ', berangkat ' . $package->departure_date?->format('d M Y') . ', durasi ' . $package->duration_days . ' hari.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="/" class="hover:text-indigo-600">Beranda</a>
        <span>/</span>
        <a href="{{ route('packages') }}" class="hover:text-indigo-600">Paket Umrah</a>
        <span>/</span>
        <span class="text-gray-600">{{ $package->name }}</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        @if($package->banner_image)
        <div class="h-64 sm:h-80 bg-cover bg-center" style="background-image: url('{{ asset('storage/' . $package->banner_image) }}')"></div>
        @else
        <div class="h-64 sm:h-80 bg-cover bg-center relative" style="background-image: url('{{ asset('marketing/default-package.jpg') }}')">
            <div class="absolute inset-0 bg-gradient-to-t from-navy-900/60 to-transparent"></div>
        </div>
        @endif

        <div class="p-6 sm:p-8">
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="text-xs font-semibold px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg uppercase">{{ $package->type }}</span>
                <span class="text-xs font-semibold px-3 py-1 bg-gray-100 text-gray-600 rounded-lg uppercase">{{ $package->category }}</span>
                @if($package->available_count > 0)
                <span class="text-xs font-semibold px-3 py-1 bg-green-50 text-green-700 rounded-lg">Tersedia {{ $package->available_count }} slot</span>
                @else
                <span class="text-xs font-semibold px-3 py-1 bg-red-50 text-red-700 rounded-lg">Penuh</span>
                @endif
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ $package->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Kode: {{ $package->code }}</p>

            <div class="mt-6 p-5 bg-gradient-to-br from-indigo-50 to-violet-50 rounded-2xl border border-indigo-100">
                <h3 class="text-sm font-semibold text-indigo-800 mb-3 uppercase tracking-wider">Harga Paket</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @if($package->price_quad > 0)
                    <div class="bg-white rounded-xl p-3 text-center border border-indigo-100"><div class="text-xs text-gray-500 mb-1">Quad (4 org)</div><div class="text-base font-extrabold text-indigo-700">Rp {{ number_format($package->price_quad, 0, ',', '.') }}</div><div class="text-xs text-gray-400">/orang</div></div>
                    @endif
                    @if($package->price_triple > 0)
                    <div class="bg-white rounded-xl p-3 text-center border border-indigo-100"><div class="text-xs text-gray-500 mb-1">Triple (3 org)</div><div class="text-base font-extrabold text-indigo-700">Rp {{ number_format($package->price_triple, 0, ',', '.') }}</div><div class="text-xs text-gray-400">/orang</div></div>
                    @endif
                    @if($package->price_double > 0)
                    <div class="bg-white rounded-xl p-3 text-center border border-indigo-100"><div class="text-xs text-gray-500 mb-1">Double (2 org)</div><div class="text-base font-extrabold text-indigo-700">Rp {{ number_format($package->price_double, 0, ',', '.') }}</div><div class="text-xs text-gray-400">/orang</div></div>
                    @endif
                    @if($package->price_single > 0)
                    <div class="bg-white rounded-xl p-3 text-center border border-indigo-100"><div class="text-xs text-gray-500 mb-1">Single (1 org)</div><div class="text-base font-extrabold text-indigo-700">Rp {{ number_format($package->price_single, 0, ',', '.') }}</div><div class="text-xs text-gray-400">/orang</div></div>
                    @endif
                </div>
            </div>

            <div class="mt-8 grid sm:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900">Detail Perjalanan</h3>
                    <div class="space-y-3">
                        @if($package->airline)
                        <div class="flex items-center gap-3 text-sm"><span class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">✈️</span><div><span class="text-gray-400 block text-xs">Maskapai</span><span class="font-medium">{{ $package->airline }}</span></div></div>
                        @endif
                        <div class="flex items-center gap-3 text-sm"><span class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">📅</span><div><span class="text-gray-400 block text-xs">Keberangkatan</span><span class="font-medium">{{ $package->departure_date?->format('d M Y') }}</span></div></div>
                        <div class="flex items-center gap-3 text-sm"><span class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">📅</span><div><span class="text-gray-400 block text-xs">Kepulangan</span><span class="font-medium">{{ $package->return_date?->format('d M Y') }}</span></div></div>
                        <div class="flex items-center gap-3 text-sm"><span class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">⏱️</span><div><span class="text-gray-400 block text-xs">Durasi</span><span class="font-medium">{{ $package->duration_days }} Hari</span></div></div>
                        @if($package->departure_airport)
                        <div class="flex items-center gap-3 text-sm"><span class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">🛫</span><div><span class="text-gray-400 block text-xs">Bandara</span><span class="font-medium">{{ $package->departure_airport }}</span></div></div>
                        @endif
                    </div>
                </div>
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900">Akomodasi Hotel</h3>
                    <div class="space-y-3">
                        @if($package->makkah_hotel_name)
                        <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                            <div class="text-xs font-semibold text-amber-700 uppercase tracking-wide mb-1">Hotel Makkah</div>
                            <div class="font-semibold text-gray-900">{{ $package->makkah_hotel_name }}</div>
                            <div class="text-sm text-gray-600 mt-1">{{ str_repeat('★', $package->makkah_hotel_stars ?? 0) }}@if($package->makkah_hotel_distance_meters) &middot; {{ $package->makkah_hotel_distance_meters }}m dari Masjidil Haram @endif</div>
                            @if($package->makkah_hotel_room_type)<div class="text-xs text-gray-500 mt-1">Kamar: {{ $package->makkah_hotel_room_type }}</div>@endif
                        </div>
                        @endif
                        @if($package->madinah_hotel_name)
                        <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                            <div class="text-xs font-semibold text-emerald-700 uppercase tracking-wide mb-1">Hotel Madinah</div>
                            <div class="font-semibold text-gray-900">{{ $package->madinah_hotel_name }}</div>
                            <div class="text-sm text-gray-600 mt-1">{{ str_repeat('★', $package->madinah_hotel_stars ?? 0) }}@if($package->madinah_hotel_distance_meters) &middot; {{ $package->madinah_hotel_distance_meters }}m dari Masjid Nabawi @endif</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($package->includes && is_array($package->includes) && count($package->includes))
            <div class="mt-8 grid sm:grid-cols-2 gap-6">
                <div class="bg-green-50 rounded-2xl p-5 border border-green-100">
                    <h3 class="font-semibold text-green-800 flex items-center gap-2 mb-3">✅ Termasuk</h3>
                    <ul class="space-y-2">
                        @foreach($package->includes as $item)
                        <li class="flex items-start gap-2 text-sm text-green-700">✓ {{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                @if($package->excludes && is_array($package->excludes) && count($package->excludes))
                <div class="bg-red-50 rounded-2xl p-5 border border-red-100">
                    <h3 class="font-semibold text-red-800 flex items-center gap-2 mb-3">✕ Tidak Termasuk</h3>
                    <ul class="space-y-2">
                        @foreach($package->excludes as $item)
                        <li class="flex items-start gap-2 text-sm text-red-700">✕ {{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            @endif

            @if($package->terms)
            <div class="mt-8 p-5 bg-gray-50 rounded-2xl border border-gray-100">
                <h3 class="font-semibold text-gray-900 mb-2">Syarat & Ketentuan</h3>
                <div class="text-sm text-gray-600">{{ $package->terms }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-8 bg-gradient-to-br from-navy-800 to-brand-800 rounded-2xl p-6 sm:p-8 text-white" id="form-booking">
        <h2 class="text-2xl font-extrabold mb-6">Pesan Paket Ini</h2>
        <form action="{{ route('booking.store', $package->slug) }}" method="POST" class="space-y-6">
            @csrf

            {{-- Room Type --}}
            <div>
                <label class="block text-sm font-semibold mb-3 text-indigo-200">Pilih Tipe Kamar</label>
                <div class="grid grid-cols-2 gap-3">
                    @php $types = ['quad' => 'Quad (4 org)', 'triple' => 'Triple (3 org)', 'double' => 'Double (2 org)', 'single' => 'Single (1 org)']; @endphp
                    @foreach ($types as $key => $label)
                        @php $priceField = 'price_' . $key; $price = $package->$priceField ?? $package->price_quad; @endphp
                        @if($price > 0)
                        <label class="relative border-2 border-white/20 rounded-xl p-3 cursor-pointer hover:border-white/40 transition has-[:checked]:border-white has-[:checked]:bg-white/10">
                            <div class="flex items-start gap-3">
                                <input type="radio" name="room_type" value="{{ $key }}" {{ $key === 'quad' ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-indigo-600 border-white/30 focus:ring-indigo-500 bg-white/20">
                                <div>
                                    <div class="text-sm font-semibold">{{ $label }}</div>
                                    <div class="text-lg font-bold">Rp {{ number_format($price, 0, ',', '.') }}</div>
                                    <div class="text-xs text-indigo-300">/orang</div>
                                </div>
                            </div>
                        </label>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Personal Data --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1 text-indigo-200">Nama Lengkap *</label>
                    <input type="text" name="full_name" required class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2.5 text-white placeholder-white/40 text-sm focus:ring-2 focus:ring-white/30 focus:border-white/40 outline-none" placeholder="Nama sesuai KTP">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-indigo-200">No. Telepon / WA *</label>
                    <input type="text" name="phone" required class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2.5 text-white placeholder-white/40 text-sm focus:ring-2 focus:ring-white/30 focus:border-white/40 outline-none" placeholder="0812xxxxxx">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-indigo-200">NIK (KTP) *</label>
                    <input type="text" name="nik" required minlength="16" maxlength="16" inputmode="numeric" class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2.5 text-white placeholder-white/40 text-sm focus:ring-2 focus:ring-white/30 focus:border-white/40 outline-none" placeholder="16 digit NIK">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-indigo-200">Email</label>
                    <input type="email" name="email" class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2.5 text-white placeholder-white/40 text-sm focus:ring-2 focus:ring-white/30 focus:border-white/40 outline-none" placeholder="email@example.com">
                </div>
            </div>

            {{-- Payment Method --}}
            <div>
                <label class="block text-sm font-semibold mb-3 text-indigo-200">Metode Pembayaran</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @php $providers = app(\App\Services\PaymentGatewayService::class)->getAvailableProviders(); @endphp
                    @if(!empty($providers))
                    <label class="relative border-2 border-white/20 rounded-xl p-3 cursor-pointer hover:border-white/40 transition has-[:checked]:border-white has-[:checked]:bg-white/10">
                        <input type="radio" name="payment_method" value="gateway" {{ old('payment_method') === 'gateway' ? 'checked' : '' }} class="sr-only peer">
                        <div class="text-sm font-semibold">💳 Online</div>
                        <div class="text-xs text-indigo-300 mt-1">Transfer/QRIS/E-Wallet</div>
                    </label>
                    @endif
                    <label class="relative border-2 border-white/20 rounded-xl p-3 cursor-pointer hover:border-white/40 transition has-[:checked]:border-white has-[:checked]:bg-white/10">
                        <input type="radio" name="payment_method" value="transfer" {{ old('payment_method', 'transfer') === 'transfer' ? 'checked' : '' }} class="sr-only peer">
                        <div class="text-sm font-semibold">🏦 Transfer Manual</div>
                        <div class="text-xs text-indigo-300 mt-1">Upload bukti transfer</div>
                    </label>
                    <label class="relative border-2 border-white/20 rounded-xl p-3 cursor-pointer hover:border-white/40 transition has-[:checked]:border-white has-[:checked]:bg-white/10">
                        <input type="radio" name="payment_method" value="office" {{ old('payment_method') === 'office' ? 'checked' : '' }} class="sr-only peer">
                        <div class="text-sm font-semibold">🏢 Kantor</div>
                        <div class="text-xs text-indigo-300 mt-1">Bayar tunai di kantor</div>
                    </label>
                </div>

                @if(!empty($providers))
                <div id="gateway_options" class="mt-3 space-y-2">
                <select name="payment_provider_id" class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2.5 text-white text-sm focus:ring-2 focus:ring-white/30 focus:border-white/40 outline-none">
                    <option value="" class="text-gray-900">Pilih Gateway</option>
                    @foreach ($providers as $prov)
                        <option value="{{ $prov->id }}" class="text-gray-900">{{ $prov->name }}</option>
                    @endforeach
                </select>
                <select name="payment_channel" class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2.5 text-white text-sm focus:ring-2 focus:ring-white/30 focus:border-white/40 outline-none">
                    <option value="BRIVA" class="text-gray-900">BRI Virtual Account</option>
                    <option value="BCAVA" class="text-gray-900">BCA Virtual Account</option>
                    <option value="BNIVA" class="text-gray-900">BNI Virtual Account</option>
                    <option value="MANDIRIVA" class="text-gray-900">Mandiri Virtual Account</option>
                    <option value="BSIVA" class="text-gray-900">BSI Virtual Account</option>
                </select>
                </div>
                @endif
            </div>

            <button type="submit" class="w-full bg-white text-indigo-700 font-bold py-3.5 rounded-xl hover:bg-indigo-50 transition shadow-lg text-lg">
                Pesan Sekarang →
            </button>
            <p class="text-center text-xs text-indigo-300">Dengan memesan, Anda menyetujui syarat & ketentuan yang berlaku.</p>
        </form>
    </div>

    {{-- Related --}}
    @if($related->isNotEmpty())
    <div class="mt-16">
        <h2 class="text-2xl font-extrabold text-gray-900 mb-6">Paket Serupa</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($related as $r)
            <a href="{{ route('package.detail', $r->slug) }}" class="group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all overflow-hidden">
                <div class="p-4">
                    <h3 class="font-semibold text-sm text-gray-900 group-hover:text-indigo-600 transition line-clamp-2">{{ $r->name }}</h3>
                    <div class="mt-2 text-xs text-gray-500">{{ $r->duration_days }} Hari &middot; {{ $r->departure_date?->format('d M Y') }}</div>
                    <div class="mt-2 text-sm font-bold text-indigo-600">Rp {{ number_format($r->price_quad > 0 ? $r->price_quad : $r->price_triple, 0, ',', '.') }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
{{-- Gateway toggle dibundel di resources/js/app.js (initGatewayToggle). --}}
@endpush
