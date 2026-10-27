<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Jamaah') - {{ config('app.name', 'Travel Umrah') }}</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen">

    <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('portal.dashboard') }}" class="flex items-center gap-2">
                        <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900">Portal Jamaah</span>
                    </a>
                </div>
                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ route('portal.dashboard') }}" class="text-sm font-medium {{ request()->routeIs('portal.dashboard') ? 'text-primary-600' : 'text-gray-600 hover:text-primary-600' }} transition">Dashboard</a>
                    <a href="{{ route('portal.bookings') }}" class="text-sm font-medium {{ request()->routeIs('portal.bookings*') ? 'text-primary-600' : 'text-gray-600 hover:text-primary-600' }} transition">Booking</a>
                    <a href="{{ route('portal.invoices') }}" class="text-sm font-medium {{ request()->routeIs('portal.invoices*') ? 'text-primary-600' : 'text-gray-600 hover:text-primary-600' }} transition">Invoice</a>
                    <a href="{{ route('packages') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition">+ Pesan Paket</a>
                </nav>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500 hidden sm:block">{{ session('portal_pilgrim_name') }}</span>
                    <form action="{{ route('portal.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-700 transition">Keluar</button>
                    </form>
                    <button type="button" class="md:hidden p-2 -mr-2 text-gray-600 hover:text-gray-900" aria-label="Buka menu navigasi" onclick="document.getElementById('portal-mobile-nav').classList.toggle('hidden')">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
            <nav id="portal-mobile-nav" class="hidden md:hidden pb-4 flex flex-col gap-1" aria-label="Navigasi portal">
                <a href="{{ route('portal.dashboard') }}" class="px-2 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('portal.dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }}">Dashboard</a>
                <a href="{{ route('portal.bookings') }}" class="px-2 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('portal.bookings*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }}">Booking</a>
                <a href="{{ route('portal.invoices') }}" class="px-2 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('portal.invoices*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }}">Invoice</a>
                <a href="{{ route('packages') }}" class="px-2 py-2 text-sm font-medium rounded-lg text-indigo-600 hover:bg-indigo-50">+ Pesan Paket</a>
            </nav>
            </div>
        </div>
    </header>

    <main class="pt-20 pb-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <footer class="border-t border-gray-200 py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'Travel Umrah') }} &mdash; Portal Jamaah
        </div>
    </footer>

</body>
</html>
