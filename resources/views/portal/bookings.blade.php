@extends('portal.layout')

@section('title', 'Booking Saya')
@section('content')

<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Booking Saya</h1>
    <p class="text-sm text-gray-500 mt-1">Daftar semua booking umrah Anda</p>
</div>

@if ($bookings->count() > 0)
    <div class="space-y-4">
        @foreach ($bookings as $booking)
            <a href="{{ route('portal.bookings.show', $booking) }}" class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:border-primary-200 hover:shadow-md transition">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="text-sm font-semibold text-gray-900">{{ $booking->booking_number }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($booking->status === 'confirmed') bg-green-100 text-green-700
                                @elseif($booking->status === 'pending') bg-amber-100 text-amber-700
                                @elseif($booking->status === 'cancelled') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-700
                                @endif
                            ">{{ ucfirst($booking->status) }}</span>
                        </div>
                        <p class="text-sm text-gray-700 font-medium">{{ $booking->package?->name ?? 'Tanpa paket' }}</p>
                        @if ($booking->package?->departure_date)
                            <p class="text-xs text-gray-400 mt-1">Keberangkatan: {{ $booking->package->departure_date->format('d M Y') }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        <span class="text-xs text-primary-600 font-medium">Lihat detail &rarr;</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $bookings->links() }}
    </div>
@else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <h2 class="text-base font-semibold text-gray-900 mb-1">Belum ada booking</h2>
        <p class="text-sm text-gray-500">Anda belum memiliki booking umrah.</p>
    </div>
@endif

@endsection
