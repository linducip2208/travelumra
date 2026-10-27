@extends('portal.layout')

@section('title', 'Detail Invoice')
@section('content')

<div class="mb-6">
    <a href="{{ route('portal.invoices') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary-600 transition mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke daftar invoice
    </a>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $invoice->invoice_number }}</h1>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold mt-1 inline-block
                @if($invoice->status === 'paid') bg-green-100 text-green-700
                @elseif($invoice->status === 'pending') bg-amber-100 text-amber-700
                @elseif($invoice->status === 'overdue') bg-red-100 text-red-700
                @else bg-gray-100 text-gray-700
                @endif
            ">{{ ucfirst($invoice->status) }}</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Rincian Invoice</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Booking</span>
                    <a href="{{ route('portal.bookings.show', $invoice->booking) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">
                        {{ $invoice->booking?->booking_number ?? '-' }}
                    </a>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Paket</span>
                    <span class="text-sm font-medium text-gray-900">{{ $invoice->booking?->package?->name ?? '-' }}</span>
                </div>
                @if ($invoice->due_date)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Jatuh Tempo</span>
                    <span class="text-sm font-medium text-gray-900">{{ $invoice->due_date->format('d M Y') }}</span>
                </div>
                @endif
                <hr class="border-gray-100">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Total</span>
                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Telah Dibayar</span>
                    <span class="text-sm font-medium text-green-600">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                </div>
                <hr class="border-gray-100">
                <div class="flex justify-between">
                    <span class="text-sm font-semibold text-gray-900">Sisa</span>
                    <span class="text-sm font-bold text-red-600">Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Riwayat Pembayaran</h2>
            @if ($invoice->payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                                <th class="text-left py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Metode</th>
                                <th class="text-right py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="text-right py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="text-right py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->payments as $payment)
                                <tr class="border-b border-gray-50">
                                    <td class="py-2.5 font-medium text-gray-900">{{ $payment->payment_number }}</td>
                                    <td class="py-2.5 text-gray-600">{{ $payment->payment_method }}</td>
                                    <td class="py-2.5 text-right text-gray-900 font-medium">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    <td class="py-2.5 text-right text-gray-500">{{ $payment->payment_date?->format('d M Y') }}</td>
                                    <td class="py-2.5 text-right">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                            @if($payment->status === 'confirmed') bg-green-100 text-green-700
                                            @elseif($payment->status === 'pending') bg-amber-100 text-amber-700
                                            @elseif($payment->status === 'rejected') bg-red-100 text-red-700
                                            @else bg-gray-100 text-gray-700
                                            @endif
                                        ">{{ ucfirst($payment->status) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-400">Belum ada pembayaran.</p>
            @endif
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Upload Bukti Bayar</h2>

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any() && request()->is('*/upload-payment'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('portal.invoices.upload-payment', $invoice) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pembayaran</label>
                    <input type="number" name="amount" id="amount" required min="1" step="1"
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-200 focus:border-primary-500 outline-none transition"
                        placeholder="Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}"
                        value="{{ old('amount', (string) $invoice->remaining_amount) }}">
                </div>

                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                    <select name="payment_method" id="payment_method" required
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-200 focus:border-primary-500 outline-none transition">
                        <option value="">Pilih metode</option>
                        <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Tunai</option>
                        <option value="qris" {{ old('payment_method') === 'qris' ? 'selected' : '' }}>QRIS</option>
                        <option value="virtual_account" {{ old('payment_method') === 'virtual_account' ? 'selected' : '' }}>Virtual Account</option>
                    </select>
                </div>

                <div>
                    <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}"
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-200 focus:border-primary-500 outline-none transition"
                        placeholder="contoh: BCA">
                </div>

                <div>
                    <label for="bank_account" class="block text-sm font-medium text-gray-700 mb-1">No. Rekening</label>
                    <input type="text" name="bank_account" id="bank_account" value="{{ old('bank_account') }}"
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-200 focus:border-primary-500 outline-none transition"
                        placeholder="Nomor rekening pengirim">
                </div>

                <div>
                    <label for="bank_holder" class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik Rekening</label>
                    <input type="text" name="bank_holder" id="bank_holder" value="{{ old('bank_holder') }}"
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-200 focus:border-primary-500 outline-none transition"
                        placeholder="Nama di rekening">
                </div>

                <div>
                    <label for="proof_file" class="block text-sm font-medium text-gray-700 mb-1">Bukti Transfer</label>
                    <input type="file" name="proof_file" id="proof_file" required accept=".jpg,.jpeg,.png,.pdf"
                        class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, atau PDF (max 2MB)</p>
                </div>

                <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 transition shadow-lg shadow-primary-200">
                    Upload Bukti Pembayaran
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
