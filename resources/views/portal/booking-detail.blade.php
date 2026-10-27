@extends('portal.layout')

@section('title', 'Detail Booking')
@section('content')

<div class="mb-6">
    <a href="{{ route('portal.bookings') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary-600 transition mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke daftar booking
    </a>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $booking->booking_number }}</h1>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold mt-1 inline-block
                @if($booking->status === 'confirmed') bg-green-100 text-green-700
                @elseif($booking->status === 'pending') bg-amber-100 text-amber-700
                @elseif($booking->status === 'cancelled') bg-red-100 text-red-700
                @else bg-gray-100 text-gray-700
                @endif
            ">{{ ucfirst($booking->status) }}</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Detail Paket</h2>
            @if ($booking->package)
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Nama Paket</span>
                        <span class="text-sm font-medium text-gray-900">{{ $booking->package->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Maskapai</span>
                        <span class="text-sm font-medium text-gray-900">{{ $booking->package->airline }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Keberangkatan</span>
                        <span class="text-sm font-medium text-gray-900">{{ $booking->package->departure_date?->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Kepulangan</span>
                        <span class="text-sm font-medium text-gray-900">{{ $booking->package->return_date?->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Durasi</span>
                        <span class="text-sm font-medium text-gray-900">{{ $booking->package->duration_days }} Hari</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Bandara</span>
                        <span class="text-sm font-medium text-gray-900">{{ $booking->package->departure_airport }}</span>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-400">Paket tidak tersedia.</p>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Rincian Biaya</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Harga Paket</span>
                    <span class="text-sm font-medium text-gray-900">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</span>
                </div>
                @if ($booking->additional_cost > 0)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Biaya Tambahan</span>
                    <span class="text-sm font-medium text-gray-900">Rp {{ number_format($booking->additional_cost, 0, ',', '.') }}</span>
                </div>
                @endif
                @if ($booking->discount > 0)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Diskon</span>
                    <span class="text-sm font-medium text-green-600">-Rp {{ number_format($booking->discount, 0, ',', '.') }}</span>
                </div>
                @endif
                <hr class="border-gray-100">
                <div class="flex justify-between">
                    <span class="text-sm font-semibold text-gray-900">Total</span>
                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Telah Dibayar</span>
                    <span class="text-sm font-medium text-green-600">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span>
                </div>
                <hr class="border-gray-100">
                <div class="flex justify-between">
                    <span class="text-sm font-semibold text-gray-900">Sisa</span>
                    <span class="text-sm font-bold text-red-600">Rp {{ number_format($booking->total_price - $booking->paid_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Invoice</h2>
            @if ($booking->invoices->count() > 0)
                <div class="space-y-3">
                    @foreach ($booking->invoices as $inv)
                        <a href="{{ route('portal.invoices.show', $inv) }}" class="block p-3 bg-gray-50 rounded-xl border border-gray-100 hover:border-primary-200 transition">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-semibold text-gray-900">{{ $inv->invoice_number }}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    @if($inv->status === 'paid') bg-green-100 text-green-700
                                    @elseif($inv->status === 'pending') bg-amber-100 text-amber-700
                                    @else bg-gray-100 text-gray-700
                                    @endif
                                ">{{ ucfirst($inv->status) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</span>
                                <span>Dibayar: Rp {{ number_format($inv->paid_amount, 0, ',', '.') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400">Belum ada invoice.</p>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Pembayaran</h2>
            @php
                $allPayments = $booking->invoices->flatMap->payments;
            @endphp
            @if ($allPayments->count() > 0)
                <div class="space-y-2">
                    @foreach ($allPayments as $payment)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">{{ $payment->payment_number }}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                @if($payment->status === 'confirmed') bg-green-100 text-green-700
                                @elseif($payment->status === 'pending') bg-amber-100 text-amber-700
                                @elseif($payment->status === 'rejected') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-700
                                @endif
                            ">{{ ucfirst($payment->status) }}</span>
                        </div>
                        <p class="text-xs text-gray-400">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400">Belum ada pembayaran.</p>
            @endif
        </div>
    </div>
</div>

@endsection
