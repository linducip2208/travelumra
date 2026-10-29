<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Berhasil — Travel Umrah</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 antialiased">

<header class="bg-white border-b border-gray-200">
    <div class="max-w-3xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/></svg>
            </div>
            <span class="font-bold text-gray-900">Travel Umrah</span>
        </a>
    </div>
</header>

<main class="max-w-2xl mx-auto px-4 py-10">
    @if (session('gateway_error'))
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6 text-center">{{ session('gateway_error') }}</div>
    @endif
    @if (session('simulator'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6 text-center">Pembayaran simulasi berhasil! (Testing mode)</div>
    @endif
    {{-- Success Banner --}}
    <div class="text-center mb-8">
        <div class="inline-flex w-16 h-16 rounded-full bg-green-100 items-center justify-center mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Booking Berhasil!</h1>
        <p class="text-gray-500 mt-2">No. Booking: <span class="font-mono font-bold text-indigo-600">{{ $booking->booking_number }}</span></p>
    </div>

    {{-- Booking Detail --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-4">Detail Pemesanan</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Paket</span><span class="font-medium">{{ $booking->package->name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Jemaah</span><span class="font-medium">{{ $booking->pilgrim->full_name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Tipe Kamar</span><span class="font-medium capitalize">{{ $booking->room_type }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Keberangkatan</span><span class="font-medium">{{ $booking->package->departure_date?->format('d M Y') }}</span></div>
            <hr class="border-gray-100">
            <div class="flex justify-between"><span class="text-gray-500">Total</span><span class="font-bold text-indigo-600 text-lg">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Menunggu Pembayaran</span></div>
        </div>
    </div>

    @if ($invoice)
    {{-- Invoice Info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-4">Invoice</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">No. Invoice</span><span class="font-mono font-medium">{{ $invoice->invoice_number }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Paket</span><span class="font-medium">{{ $booking->package->name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Jatuh Tempo</span><span class="font-medium">{{ $invoice->due_date->format('d M Y') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Jumlah</span><span class="font-bold">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Belum Dibayar</span></div>
        </div>
    </div>
    @endif

    {{-- Payment Options --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-4">Pilih Metode Pembayaran</h3>

        <div class="space-y-3">
            {{-- Bank Transfer --}}
            <div class="border border-gray-200 rounded-xl p-4 bg-gradient-to-r from-blue-50 to-transparent">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 text-sm">Transfer Bank</h4>
                        <p class="text-xs text-gray-500 mt-1">Transfer ke rekening kami, lalu upload bukti transfer.</p>
                        <div class="mt-3 bg-white rounded-lg border border-gray-200 p-3 text-xs space-y-1">
                            <div class="flex justify-between"><span class="text-gray-500">Bank</span><span class="font-semibold">Bank Syariah Indonesia (BSI)</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">No. Rekening</span><span class="font-mono font-semibold">7123456789</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Atas Nama</span><span class="font-semibold">PT Travel Umrah Berkah</span></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Datang ke Kantor --}}
            <div class="border border-gray-200 rounded-xl p-4 bg-gradient-to-r from-amber-50 to-transparent">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 text-sm">Datang Langsung ke Kantor</h4>
                        <p class="text-xs text-gray-500 mt-1">Kunjungi kantor kami untuk pembayaran tunai atau konsultasi langsung.</p>
                        <div class="mt-3 bg-white rounded-lg border border-gray-200 p-3 text-xs space-y-1">
                            <div class="flex justify-between"><span class="text-gray-500">Alamat</span><span class="font-semibold">Jl. KH Hasyim Ashari No. 125, Tanah Abang, Jakarta Pusat</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Jam Operasional</span><span class="font-semibold">Senin-Jumat 09:00-18:00, Sabtu 09:00-15:00</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Telepon</span><span class="font-semibold">021-63881234</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Portal Link --}}
    <div class="bg-indigo-600 rounded-xl p-6 text-center text-white">
        <h3 class="font-semibold text-lg mb-2">Pantau Status Booking Anda</h3>
        <p class="text-indigo-200 text-sm mb-4">Login ke portal jemaah untuk upload bukti pembayaran dan cek status.</p>
        <div class="bg-indigo-700 rounded-lg p-4 mb-4 text-left text-sm space-y-1 max-w-sm mx-auto">
            <div class="flex justify-between"><span class="text-indigo-300">No. Registrasi</span><span class="font-mono font-bold text-white">{{ $booking->pilgrim->registration_number }}</span></div>
            <div class="flex justify-between"><span class="text-indigo-300">No. Telepon</span><span class="font-semibold text-white">{{ $booking->pilgrim->phone }}</span></div>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('portal.login') }}" class="inline-flex items-center justify-center px-6 py-2.5 bg-white text-indigo-700 font-semibold rounded-lg hover:bg-indigo-50 transition text-sm">
                Login Portal Jemaah
            </a>
            <a href="/" class="inline-flex items-center justify-center px-6 py-2.5 border border-indigo-400 text-white rounded-lg hover:bg-indigo-500 transition text-sm">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</main>

<footer class="border-t border-gray-200 mt-16 py-8 text-center text-sm text-gray-400">
    &copy; {{ date('Y') }} Travel Umrah. Seluruh hak cipta dilindungi.
</footer>

</body>
</html>
