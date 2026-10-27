@extends('portal.layout')

@section('title', 'Invoice Saya')
@section('content')

<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Invoice Saya</h1>
    <p class="text-sm text-gray-500 mt-1">Daftar semua invoice pembayaran umrah Anda</p>
</div>

@if ($invoices->count() > 0)
    <div class="space-y-4">
        @foreach ($invoices as $invoice)
            <a href="{{ route('portal.invoices.show', $invoice) }}" class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:border-primary-200 hover:shadow-md transition">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="text-sm font-semibold text-gray-900">{{ $invoice->invoice_number }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($invoice->status === 'paid') bg-green-100 text-green-700
                                @elseif($invoice->status === 'pending') bg-amber-100 text-amber-700
                                @elseif($invoice->status === 'overdue') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-700
                                @endif
                            ">{{ ucfirst($invoice->status) }}</span>
                        </div>
                        <p class="text-sm text-gray-500">Booking: {{ $invoice->booking?->booking_number ?? '-' }}</p>
                        <p class="text-sm text-gray-500">Paket: {{ $invoice->booking?->package?->name ?? '-' }}</p>
                        @if ($invoice->due_date)
                            <p class="text-xs text-gray-400 mt-1">Jatuh tempo: {{ $invoice->due_date->format('d M Y') }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                        <div class="flex items-center justify-end gap-2 mt-1">
                            <span class="text-xs text-green-600">Dibayar: Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                            <span class="text-xs text-primary-600 font-medium">&rarr;</span>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $invoices->links() }}
    </div>
@else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <h2 class="text-base font-semibold text-gray-900 mb-1">Belum ada invoice</h2>
        <p class="text-sm text-gray-500">Anda belum memiliki invoice pembayaran.</p>
    </div>
@endif

@endsection
