<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - Portal Jamaah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            <h1 class="text-lg font-bold text-gray-900">Verifikasi OTP</h1>
            <p class="text-sm text-gray-500 mt-1">Masukkan 6 digit kode dari WhatsApp. Berlaku 5 menit.</p>
            @if ($errors->any())
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">{{ $errors->first() }}</div>
            @endif
            <form action="{{ route('portal.otp.verify') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="otp" class="block text-sm font-medium text-gray-700 mb-1.5">Kode OTP</label>
                    <input type="text" name="otp" id="otp" required autofocus inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm tracking-widest text-center focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 outline-none"
                        placeholder="••••••" autocomplete="one-time-code">
                </div>
                <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">Verifikasi</button>
            </form>
            <form action="{{ route('portal.login') }}" method="GET" class="mt-3">
                <button class="w-full text-sm text-gray-500 hover:text-gray-700">Kirim ulang / kembali</button>
            </form>
        </div>
    </div>
</body>
</html>
