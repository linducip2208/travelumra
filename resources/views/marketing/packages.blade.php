@extends('layouts.marketing')

@section('title', $settings['packages_title'] . ' - ' . $settings['site_brand_name'])
@section('meta_desc', 'Daftar lengkap paket umrah dengan berbagai pilihan maskapai, hotel, dan harga. Temukan paket umrah yang sesuai dengan kebutuhan Anda.')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900">{{ $settings['packages_title'] }}</h1>
            <p class="mt-3 text-gray-500 max-w-2xl mx-auto">{{ $settings['packages_subtitle'] }}</p>
        </div>

        @if($packages->isEmpty())
            <div class="text-center py-20 text-gray-400">
                <p class="text-xl">Belum ada paket umrah tersedia.</p>
                <p class="mt-2">Silakan hubungi kami untuk informasi lebih lanjut.</p>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($packages as $p)
                <a href="{{ route('package.detail', $p->slug) }}" class="group bg-white rounded-2xl border border-gray-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">
                    @if($p->banner_image)
                    <div class="h-48 bg-cover bg-center" style="background-image: url('{{ asset('storage/' . $p->banner_image) }}')"></div>
                    @else
                    <div class="h-48 bg-cover bg-center relative" style="background-image: url('{{ asset('marketing/default-package.jpg') }}')">
                        <div class="absolute inset-0 bg-gradient-to-t from-navy-900/50 to-transparent"></div>
                    </div>
                    @endif
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-semibold px-2 py-1 bg-indigo-50 text-indigo-700 rounded-md uppercase">{{ $p->type }}</span>
                            <span class="text-xs font-semibold px-2 py-1 bg-gray-100 text-gray-600 rounded-md uppercase">{{ $p->category }}</span>
                            @if($p->available_count <= 5 && $p->available_count > 0)
                            <span class="text-xs font-bold px-2 py-1 bg-red-50 text-red-600 rounded-md">⚡ {{ $p->available_count }} slot</span>
                            @endif
                        </div>
                        <h3 class="font-bold text-gray-900 group-hover:text-indigo-600 transition line-clamp-2">{{ $p->name }}</h3>
                        <div class="mt-3 space-y-1 text-sm text-gray-500 flex-1">
                            @if($p->airline)<div class="flex items-center gap-2">✈️ {{ $p->airline }}</div>@endif
                            <div class="flex items-center gap-2">📅 {{ $p->departure_date?->format('d M Y') }}</div>
                            <div class="flex items-center gap-2">🕌 {{ $p->makkah_hotel_stars }}★ Hotel · {{ $p->duration_days }} Hari</div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-gray-400">Mulai</span>
                                <div class="text-lg font-extrabold text-indigo-600">Rp {{ number_format(min(array_filter([$p->price_quad, $p->price_triple, $p->price_double, $p->price_single], fn($v) => $v > 0)) ?: $p->price_quad, 0, ',', '.') }}</div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg group-hover:bg-indigo-700 transition">Pesan</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="mt-10">{{ $packages->links() }}</div>
        @endif
    </div>
</div>
@endsection
