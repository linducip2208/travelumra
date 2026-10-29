<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">
    <meta property="og:title" content="{{ $seo['og_title'] }}">
    <meta property="og:description" content="{{ $seo['og_description'] }}">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="{{ config('app.url') }}/docs">
    @vite(['resources/css/app.css'])
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900|jetbrains-mono:400,500,600,700" rel="stylesheet" />
    <style>
        html{scroll-behavior:smooth;scroll-padding-top:6rem}
        .gradient-text{background:linear-gradient(135deg,#6366f1 0%,#a855f7 50%,#ec4899 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .glass{background:rgba(255,255,255,0.7);backdrop-filter:blur(20px)}
        .card-hover{transition:all 0.3s cubic-bezier(0.4,0,0.2,1)}
        .card-hover:hover{transform:translateY(-4px);box-shadow:0 20px 40px -12px rgba(0,0,0,0.15)}
        .browser-mock{border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;background:#fff;box-shadow:0 4px 24px rgba(0,0,0,0.08)}
        .browser-mock-header{background:#f1f5f9;padding:8px 14px;display:flex;align-items:center;gap:6px;border-bottom:1px solid #e2e8f0}
        .browser-mock-dot{width:10px;height:10px;border-radius:50%}.bd-red{background:#ef4444}.bd-yellow{background:#f59e0b}.bd-green{background:#10b981}
        .browser-mock-url{margin-left:8px;background:#fff;border-radius:6px;padding:2px 10px;font-size:10px;font-family:'JetBrains Mono',monospace;color:#64748b;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .status-badge{font-size:10px;padding:3px 8px;border-radius:6px;font-weight:600;letter-spacing:0.03em;text-transform:uppercase}
        .sb-gray{background:#e5e7eb;color:#374151}.sb-blue{background:#dbeafe;color:#1d4ed8}.sb-indigo{background:#e0e7ff;color:#4338ca}
        .sb-violet{background:#ede9fe;color:#6d28d9}.sb-amber{background:#fef3c7;color:#b45309}.sb-orange{background:#ffedd5;color:#c2410c}
        .sb-cyan{background:#cffafe;color:#0e7490}.sb-teal{background:#ccfbf1;color:#0f766e}.sb-green{background:#dcfce7;color:#15803d}
        .sb-red{background:#fee2e2;color:#b91c1c}.sb-yellow{background:#fef9c3;color:#a16207}.sb-rose{background:#ffe4e6;color:#be123c}
        ::-webkit-scrollbar{width:8px}::-webkit-scrollbar-track{background:#f1f5f9}::-webkit-scrollbar-thumb{background:#c7d2fe;border-radius:4px}
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

<header class="fixed top-0 left-0 right-0 z-50 glass border-b border-white/20 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="/" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <span class="text-lg font-extrabold text-slate-800">Travel Umrah <span class="text-indigo-500 font-bold">Docs</span></span>
            </a>
            <nav class="hidden md:flex items-center gap-1 text-sm font-medium">
                <a href="/" class="px-3 py-2 text-slate-500 hover:text-slate-800 rounded-lg hover:bg-white/50 transition">Beranda</a>
                <a href="/docs" class="px-3 py-2 text-indigo-600 bg-indigo-50 rounded-lg font-semibold">Dokumentasi</a>
                <a href="/paket-umrah" class="px-3 py-2 text-slate-500 hover:text-slate-800 rounded-lg hover:bg-white/50 transition">Paket</a>
                <a href="/admin/login" class="ml-2 px-4 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-600 hover:to-indigo-700 transition shadow-md">Login Admin</a>
            </nav>
        </div>
    </div>
</header>

<main class="pt-16">

{{-- Hero --}}
<section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-indigo-950 to-violet-950">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-violet-500/10 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-5xl mx-auto px-4 py-20 lg:py-28 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 rounded-full border border-white/10 mb-8">
            <span class="relative flex h-2.5 w-2.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span></span>
            <span class="text-sm text-white/80 font-medium">12 Fase Tutorial — Alur Bisnis Lengkap</span>
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-6xl font-black text-white tracking-tight">Dokumentasi &amp; <span class="gradient-text">Tutorial</span></h1>
        <p class="mt-6 text-lg sm:text-xl text-white/60 max-w-3xl mx-auto">Panduan lengkap <strong class="text-white/90">Sistem Manajemen Travel Umrah</strong> — 12 fase dari setup hingga monitoring jamaah di Tanah Suci, 33+ modul, 6 role staff, API mobile, customer portal, dan programmatic SEO.</p>
        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <a href="#fase1" class="px-6 py-3 bg-white text-slate-900 text-sm font-bold rounded-xl hover:bg-white/90 transition shadow-xl flex items-center gap-2">Mulai Tutorial</a>
            <a href="#akun" class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-violet-600 text-white text-sm font-bold rounded-xl hover:from-indigo-600 hover:to-violet-700 transition shadow-xl flex items-center gap-2">Akun Demo</a>
        </div>
        <div class="mt-12 grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-2xl mx-auto">
            @foreach([['33+', 'Modul Admin'], ['12', 'Fase Bisnis'], ['6', 'Role Staff'], ['9', 'Route SEO']] as $stat)
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-4 text-center">
                <div class="text-2xl sm:text-3xl font-black text-white">{{ $stat[0] }}</div>
                <div class="text-xs text-white/50 mt-1 font-medium">{{ $stat[1] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Jump Nav --}}
<nav class="sticky top-16 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="flex items-center gap-3 overflow-x-auto">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap">Lompat ke</span>
            @foreach($phases as $p)
            <a href="#{{ $p['id'] }}" class="whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-semibold transition bg-{{ $p['color'] }}-50 text-{{ $p['color'] }}-700 hover:bg-{{ $p['color'] }}-100 flex items-center gap-1.5">
                <span class="w-5 h-5 rounded-full bg-{{ $p['color'] }}-200 text-{{ $p['color'] }}-700 flex items-center justify-center text-[10px] font-bold">{{ $p['num'] }}</span>{{ $p['label'] }}
            </a>
            @endforeach
            <span class="text-slate-300">|</span>
            <a href="#akun" class="whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">Akun Demo</a>
            <a href="#fitur" class="whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Fitur Lengkap</a>
        </div>
    </div>
</nav>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

{{-- Tutorial 12 Fase --}}
@foreach($phases as $phase)
<section id="{{ $phase['id'] }}" class="mb-24 scroll-mt-28">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-8">
        <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-gradient-to-br from-{{ $phase['color'] }}-400 to-{{ $phase['color'] }}-600 flex items-center justify-center shadow-lg">
            <span class="text-2xl font-black text-white">{{ $phase['num'] }}</span>
        </div>
        <div>
            <span class="text-xs font-bold text-{{ $phase['color'] }}-600 uppercase tracking-widest">{{ $phase['label'] }}</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-0.5">{{ $phase['title'] }}</h2>
            <p class="mt-2 text-slate-500 max-w-3xl">{{ $phase['desc'] }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-5 gap-8">
        <div class="lg:col-span-2 {{ $phase['num'] % 2 == 0 ? 'lg:order-last' : '' }}">
            <div class="browser-mock card-hover">
                <div class="browser-mock-header">
                    <span class="browser-mock-dot bd-red"></span><span class="browser-mock-dot bd-yellow"></span><span class="browser-mock-dot bd-green"></span>
                    <span class="browser-mock-url">{{ $phase['imageUrl'] }}</span>
                </div>
                <img src="/marketing/screens/{{ $phase['image'] }}" alt="{{ $phase['title'] }}" class="w-full" loading="lazy" onerror="this.style.display='none'">
            </div>
        </div>
        <div class="lg:col-span-3 space-y-4">
            @foreach($phase['steps'] as $idx => $step)
            <div class="relative pl-10 card-hover bg-white rounded-xl border border-slate-200/60 p-5 shadow-sm">
                <div class="absolute left-4 top-5 w-6 h-6 rounded-full bg-{{ $phase['color'] }}-100 border-2 border-{{ $phase['color'] }}-200 flex items-center justify-center text-[11px] font-bold text-{{ $phase['color'] }}-700">{{ $idx + 1 }}</div>
                <h4 class="font-bold text-slate-800 text-sm">{{ $step['title'] }}</h4>
                <p class="text-sm text-slate-500 mt-1.5 leading-relaxed">{{ $step['detail'] }}</p>
            </div>
            @endforeach

            @if(isset($phase['roles']))
            <div class="bg-{{ $phase['color'] }}-50 rounded-xl border border-{{ $phase['color'] }}-100 p-4">
                <p class="text-xs font-bold text-{{ $phase['color'] }}-800 mb-2 uppercase tracking-wider">Role yang Terlibat</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($phase['roles'] as $role)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-lg text-xs font-semibold text-{{ $phase['color'] }}-700 shadow-sm">{{ $role['name'] }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if(isset($phase['apiEndpoints']))
            <div class="bg-slate-900 rounded-xl border border-slate-700 p-5">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">API Endpoints (Muthawif Mobile)</p>
                <div class="space-y-2">
                    @foreach($phase['apiEndpoints'] as $ep)
                    <div class="flex items-center gap-3 text-sm">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono {{ $ep['method'] === 'GET' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ $ep['method'] }}</span>
                        <code class="text-green-400 text-xs font-mono">{{ $ep['path'] }}</code>
                        <span class="text-slate-500 text-xs">{{ $ep['desc'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    @if($phase['num'] === 5)
    <div class="mt-8 bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6 overflow-x-auto">
        <h4 class="text-sm font-bold text-slate-800 mb-4">12 Status Booking — Complete Workflow</h4>
        <div class="flex flex-wrap items-center gap-1.5 text-[10px] font-mono font-bold min-w-max">
            <span class="status-badge sb-gray">pending</span><span class="text-slate-300">&rarr;</span>
            <span class="status-badge sb-blue">dp_paid</span><span class="text-slate-300">&rarr;</span>
            <span class="status-badge sb-indigo">confirmed</span><span class="text-slate-300">&rarr;</span>
            <span class="status-badge sb-violet">documents_complete</span><span class="text-slate-300">&rarr;</span>
            <span class="status-badge sb-amber">visa_requested</span><span class="text-slate-300">&rarr;</span>
            <span class="status-badge sb-orange">visa_processing</span><span class="text-slate-300">&rarr;</span>
            <span class="status-badge sb-cyan">visa_approved</span><span class="text-slate-300">&rarr;</span>
            <span class="status-badge sb-teal">ready_to_depart</span><span class="text-slate-300">&rarr;</span>
            <span class="status-badge sb-green">departed</span><span class="text-slate-300">&rarr;</span>
            <span class="status-badge sb-green">returned</span>
        </div>
        <div class="flex gap-2 mt-3 text-[10px] font-mono font-bold">
            <span class="status-badge sb-red">cancelled</span><span class="status-badge sb-yellow">refunded</span><span class="status-badge sb-gray">expired</span>
        </div>
        <p class="text-xs text-slate-400 mt-3">Workflow otomatis — status berubah berdasarkan pembayaran, dokumen, visa, dan departure.</p>
    </div>
    @endif
</section>
@endforeach

{{-- Akun Demo --}}
<section id="akun" class="mb-24 scroll-mt-28">
    <div class="text-center mb-10">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold uppercase tracking-wider">Akses</span>
        <h2 class="mt-3 text-3xl font-extrabold text-slate-900">Akun Demo Admin</h2>
        <p class="mt-3 text-slate-500 max-w-2xl mx-auto">Gunakan akun berikut untuk login ke panel admin. Setiap role memiliki <strong>dashboard widget</strong> dan <strong>hak akses</strong> yang berbeda.</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-[11px]">Role</th>
                        <th class="text-left px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-[11px]">Email</th>
                        <th class="text-left px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-[11px]">Password</th>
                        <th class="text-left px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-[11px]">Cakupan Akses</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($demoAccounts as $acc)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4"><span class="px-3 py-1.5 bg-{{ $acc['color'] }}-100 text-{{ $acc['color'] }}-700 rounded-lg text-[11px] font-bold uppercase tracking-wider">{{ $acc['role'] }}</span></td>
                        <td class="px-6 py-4 font-mono text-xs font-semibold text-slate-700">{{ $acc['email'] }}</td>
                        <td class="px-6 py-4 font-mono text-xs font-semibold text-slate-500">{{ $acc['password'] }}</td>
                        <td class="px-6 py-4 text-xs text-slate-500">{{ $acc['scope'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6 grid sm:grid-cols-2 gap-4">
        <div class="bg-gradient-to-br from-indigo-50 to-violet-50 rounded-2xl border border-indigo-100 p-6">
            <div class="flex items-center gap-3"><div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center"><svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg></div><div><span class="font-bold text-indigo-800 text-sm">Admin Panel</span><br><code class="text-xs font-mono text-indigo-600 bg-white px-2 py-0.5 rounded">{{ config('app.url') }}/admin</code></div></div>
        </div>
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl border border-emerald-100 p-6">
            <div class="flex items-center gap-3"><div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div><div><span class="font-bold text-emerald-800 text-sm">Portal Jamaah</span><br><code class="text-xs font-mono text-emerald-600 bg-white px-2 py-0.5 rounded">{{ config('app.url') }}/portal</code></div></div>
        </div>
    </div>
</section>

{{-- Fitur Lengkap --}}
<section id="fitur" class="mb-24 scroll-mt-28">
    <div class="text-center mb-12">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-slate-200 text-slate-700 rounded-full text-xs font-bold uppercase tracking-wider">Fitur</span>
        <h2 class="mt-3 text-3xl font-extrabold text-slate-900">Fitur Lengkap — Dikelompokkan per Menu</h2>
        <p class="mt-3 text-slate-500 max-w-3xl mx-auto">33+ modul diorganisir dalam 12 navigation group mengikuti <strong>alur bisnis</strong> travel umrah.</p>
    </div>
    @foreach($features as $fg)
    <div class="mb-16">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-3 h-3 rounded-full bg-{{ $fg['groupColor'] }}-500"></div>
            <h3 class="text-lg font-extrabold text-slate-800 uppercase tracking-wide">{{ $fg['group'] }}</h3>
            <span class="text-xs text-slate-400 font-medium">{{ count($fg['items']) }} modul</span>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($fg['items'] as $item)
            <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm p-5 card-hover">
                <div class="browser-mock mb-3">
                    <div class="browser-mock-header">
                        <span class="browser-mock-dot bd-red"></span><span class="browser-mock-dot bd-yellow"></span><span class="browser-mock-dot bd-green"></span>
                        <span class="browser-mock-url text-[9px]">{{ $item['url'] }}</span>
                    </div>
                    <img src="/marketing/screens/{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full" loading="lazy" onerror="this.style.display='none'">
                </div>
                <h4 class="font-bold text-slate-800 text-sm">{{ $item['name'] }}</h4>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">{{ $item['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</section>

{{-- Glosarium --}}
<section class="mb-24">
    <div class="text-center mb-10">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-amber-100 text-amber-700 rounded-full text-xs font-bold uppercase tracking-wider">Glosarium</span>
        <h2 class="mt-3 text-3xl font-extrabold text-slate-900">Istilah Penting Travel Umrah</h2>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($glossary as $g)
        <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm p-5 card-hover">
            <h5 class="font-bold text-slate-800 text-sm">{{ $g['term'] }}</h5>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">{{ $g['def'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- CTA --}}
<div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-950 to-violet-950 rounded-3xl p-10 sm:p-16 text-center">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-violet-500/10 rounded-full blur-3xl"></div>
    </div>
    <div class="relative">
        <h2 class="text-2xl sm:text-4xl font-black text-white">Siap Mencoba Semua Fitur?</h2>
        <p class="mt-4 text-white/60 max-w-2xl mx-auto">Login dengan akun demo dan eksplorasi 33+ modul operasional travel umrah.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <a href="/admin/login" class="px-8 py-4 bg-white text-slate-900 font-bold rounded-xl hover:bg-white/90 transition shadow-2xl flex items-center gap-2">Login Admin Panel</a>
            <a href="/paket-umrah" class="px-8 py-4 bg-white/5 border border-white/20 text-white font-bold rounded-xl hover:bg-white/10 transition flex items-center gap-2">Lihat Paket Umrah</a>
        </div>
    </div>
</div>

</div>
</main>

<footer class="bg-slate-950 text-slate-400 py-12">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <div class="flex items-center justify-center gap-3 mb-6">
            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-lg flex items-center justify-center"><svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></div>
            <span class="text-lg font-bold text-white">{{ config('app.name', 'Travel Umrah') }}</span>
        </div>
        <div class="flex flex-wrap justify-center gap-6 text-sm mb-4">
            <a href="/" class="hover:text-white transition">Beranda</a>
            <a href="/paket-umrah" class="hover:text-white transition">Paket</a>
            <a href="/blog" class="hover:text-white transition">Blog</a>
            <a href="/faq" class="hover:text-white transition">FAQ</a>
            <a href="/kontak" class="hover:text-white transition">Kontak</a>
        </div>
        <p class="text-xs">&copy; {{ date('Y') }} {{ config('app.name', 'Travel Umrah') }}. {{ config('app.url') }}/docs</p>
    </div>
</footer>

</body>
</html>
