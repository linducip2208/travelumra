<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal Jamaah - {{ config('app.name', 'Travel Umrah') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900">Portal Jamaah</h1>
            <p class="text-sm text-gray-500 mt-1">Masuk untuk melihat booking dan invoice Anda</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-100 p-8">
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('portal.login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Registrasi</label>
                    <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number') }}" required autofocus
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-200 focus:border-primary-500 outline-none transition"
                        placeholder="contoh: JMA-YYYYMM-XXXX" autocomplete="off">
                </div>

                <div>
                    <label for="passport_or_phone" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Paspor <span class="text-gray-400 font-normal">atau</span> Telepon</label>
                    <input type="text" name="passport_or_phone" id="passport_or_phone" value="{{ old('passport_or_phone') }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-200 focus:border-primary-500 outline-none transition"
                        placeholder="Nomor paspor atau nomor telepon Anda" autocomplete="off">
                </div>

                <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 transition shadow-lg shadow-primary-200">
                    Kirim Kode OTP
                </button>
                <p class="text-xs text-gray-400 text-center">Kode OTP 6 digit dikirim via WhatsApp ke nomor terdaftar.</p>
            </form>
        </div>
    </div>

</body>
</html>
