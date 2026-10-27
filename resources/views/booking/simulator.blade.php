@extends('layouts.marketing')

@section('title', 'Simulator Pembayaran')

@section('content')
<div class="pt-20 pb-20 min-h-screen bg-gray-50">
    <div class="max-w-lg mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
            <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Simulator Pembayaran</h1>
            <p class="text-gray-500 mb-2">Mode testing — tidak ada transaksi real</p>

            <div class="bg-gray-50 rounded-xl p-4 mb-6 text-left text-sm">
                <div class="flex justify-between py-1"><span class="text-gray-500">Invoice</span><span class="font-medium">{{ $invoice->invoice_number }}</span></div>
                <div class="flex justify-between py-1"><span class="text-gray-500">Jamaah</span><span class="font-medium">{{ $invoice->booking->pilgrim->full_name }}</span></div>
                <div class="flex justify-between py-1"><span class="text-gray-500">Paket</span><span class="font-medium">{{ $invoice->booking->package->name }}</span></div>
                <div class="flex justify-between py-1 border-t mt-2 pt-2"><span class="text-gray-500">Total</span><span class="font-bold text-lg text-indigo-600">Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</span></div>
            </div>

            <form method="POST" action="{{ route('payment.simulator.confirm', $invoice->id) }}">
                @csrf
                <div class="space-y-3 mb-6">
                    <label class="flex items-center gap-3 p-3 border-2 border-indigo-200 rounded-xl cursor-pointer hover:border-indigo-400 bg-indigo-50">
                        <input type="radio" name="method" value="bca" checked class="text-indigo-600">
                        <span class="text-sm font-medium">BCA Virtual Account</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-400">
                        <input type="radio" name="method" value="bri" class="text-indigo-600">
                        <span class="text-sm font-medium">BRI Virtual Account</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-400">
                        <input type="radio" name="method" value="qris" class="text-indigo-600">
                        <span class="text-sm font-medium">QRIS</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-4 bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold rounded-xl hover:from-indigo-500 hover:to-violet-500 transition shadow-lg shadow-indigo-500/25 text-lg">
                    Bayar Sekarang (Simulasi)
                </button>
            </form>

            <p class="text-xs text-gray-400 mt-4">Ini adalah simulator pembayaran untuk testing.<br>Tidak ada uang yang ditransfer.</p>
        </div>
    </div>
</div>
@endsection
