@extends('portal.layout')

@section('title', 'Dashboard')
@section('content')

<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ $pilgrim->full_name }}</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-gray-900">{{ $activeBookingsCount }}</div>
        <div class="text-xs text-gray-500 mt-0.5">Booking Aktif</div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-gray-900">{{ $invoicesCount }}</div>
        <div class="text-xs text-gray-500 mt-0.5">Total Invoice</div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-gray-900">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
        <div class="text-xs text-gray-500 mt-0.5">Total Pembayaran</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-900">Booking Terbaru</h2>
            <a href="{{ route('portal.bookings') }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Lihat semua</a>
        </div>
        @if ($bookings->count() > 0)
            <div class="space-y-3">
                @foreach ($bookings as $booking)
                    <a href="{{ route('portal.bookings.show', $booking) }}" class="block p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50/30 transition">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-semibold text-gray-900">{{ $booking->booking_number }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($booking->status === 'confirmed') bg-green-100 text-green-700
                                @elseif($booking->status === 'pending') bg-amber-100 text-amber-700
                                @elseif($booking->status === 'cancelled') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-700
                                @endif
                            ">{{ ucfirst($booking->status) }}</span>
                        </div>
                        <p class="text-sm text-gray-600">{{ $booking->package?->name ?? 'Tanpa paket' }}</p>
                        @if ($booking->package?->departure_date)
                            <p class="text-xs text-gray-400 mt-1">Keberangkatan: {{ $booking->package->departure_date->format('d M Y') }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400">Belum ada booking.</p>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-900">Invoice Terbaru</h2>
            <a href="{{ route('portal.invoices') }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Lihat semua</a>
        </div>
        @if ($invoices->count() > 0)
            <div class="space-y-3">
                @foreach ($invoices as $invoice)
                    <a href="{{ route('portal.invoices.show', $invoice) }}" class="block p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50/30 transition">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-semibold text-gray-900">{{ $invoice->invoice_number }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($invoice->status === 'paid') bg-green-100 text-green-700
                                @elseif($invoice->status === 'pending') bg-amber-100 text-amber-700
                                @elseif($invoice->status === 'overdue') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-700
                                @endif
                            ">{{ ucfirst($invoice->status) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-600">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                            <span class="text-xs text-gray-400">Dibayar: Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400">Belum ada invoice.</p>
        @endif
    </div>
</div>

@endsection
