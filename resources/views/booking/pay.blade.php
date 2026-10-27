<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran — Travel Umrah</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ $isProduction ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com' }}/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<div class="max-w-lg mx-auto px-4 py-16 text-center">
    <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm">
        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 mb-2">Pembayaran</h2>
        <p class="text-gray-500 text-sm mb-2">{{ $booking->package->name }}</p>
        <p class="text-2xl font-bold text-indigo-600 mb-6">Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</p>

        <button id="pay-button" class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold py-3 rounded-xl hover:shadow-lg transition">
            Bayar Sekarang
        </button>

        <p class="text-xs text-gray-400 mt-4">Anda akan dialihkan ke halaman pembayaran aman.</p>
    </div>

    <a href="{{ route('booking.success', $booking->booking_number) }}" class="inline-block mt-4 text-sm text-gray-500 hover:text-indigo-600">
        ← Kembali
    </a>
</div>

<script type="text/javascript">
    var payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
        window.snap.pay('{{ $snapToken }}', {
            onSuccess: function(result) {
                window.location.href = '{{ route('payment.finish', $invoice->id) }}?' + new URLSearchParams(result).toString();
            },
            onPending: function(result) {
                window.location.href = '{{ route('payment.finish', $invoice->id) }}?' + new URLSearchParams(result).toString();
            },
            onError: function(result) {
                window.location.href = '{{ route('payment.finish', $invoice->id) }}?' + new URLSearchParams(result).toString();
            },
            onClose: function() {
                window.location.href = '{{ route('booking.success', $booking->booking_number) }}';
            }
        });
    });
</script>

</body>
</html>
