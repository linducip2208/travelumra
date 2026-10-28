<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan {{ $package->name }} — Travel Umrah</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 antialiased">

<header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/></svg>
            </div>
            <span class="font-bold text-gray-900">Travel Umrah</span>
        </a>
        <a href="/paket-umrah" class="text-sm text-gray-500 hover:text-indigo-600">← Kembali ke Paket</a>
    </div>
</header>

<main class="max-w-3xl mx-auto px-4 py-10">
    <div class="mb-8">
        <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Form Pemesanan</span>
        <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $package->name }}</h1>
        <p class="text-gray-500 mt-1">{{ $package->airline }} · {{ $package->departure_date?->format('d M Y') }} · {{ $package->duration_days }} Hari</p>
    </div>

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6">{{ session('error') }}</div>
    @endif

    <form action="{{ route('booking.store', $package->slug) }}" method="POST" class="space-y-8">
        @csrf

        {{-- Package Summary --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Ringkasan Paket</h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><span class="text-gray-500">Maskapai</span><p class="font-medium">{{ $package->airline }}</p></div>
                <div><span class="text-gray-500">Berangkat</span><p class="font-medium">{{ $package->departure_date?->format('d M Y') }}</p></div>
                <div><span class="text-gray-500">Kembali</span><p class="font-medium">{{ $package->return_date?->format('d M Y') }}</p></div>
                <div><span class="text-gray-500">Durasi</span><p class="font-medium">{{ $package->duration_days }} Hari</p></div>
            </div>
        </div>

        {{-- Room Type --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">Pilih Tipe Kamar</h3>
            <x-room-selector :package="$package" />
        </div>

        {{-- Personal Data --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">Data Diri Jemaah</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="field_full_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                    <input type="text" id="field_full_name" name="full_name" value="{{ old('full_name') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                    @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="field_title" class="block text-sm font-medium text-gray-700 mb-1">Gelar</label>
                    <select id="field_title" name="title" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                        <option value="">—</option>
                        <option value="Bpk." {{ old('title') === 'Bpk.' ? 'selected' : '' }}>Bpk.</option>
                        <option value="Ibu" {{ old('title') === 'Ibu' ? 'selected' : '' }}>Ibu</option>
                        <option value="Sdri." {{ old('title') === 'Sdri.' ? 'selected' : '' }}>Sdri.</option>
                    </select>
                </div>
                <div>
                    <label for="field_gender" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin *</label>
                    <select id="field_gender" name="gender" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="field_marital_status" class="block text-sm font-medium text-gray-700 mb-1">Status Nikah</label>
                    <select id="field_marital_status" name="marital_status" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                        <option value="">—</option>
                        <option value="single">Lajang</option>
                        <option value="married">Menikah</option>
                        <option value="widowed">Duda/Janda</option>
                        <option value="divorced">Cerai</option>
                    </select>
                </div>
                <div>
                    <label for="field_birth_place" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" id="field_birth_place" name="birth_place" value="{{ old('birth_place') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                </div>
                <div>
                    <label for="field_birth_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" id="field_birth_date" name="birth_date" value="{{ old('birth_date') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                </div>
                <div>
                    <label for="field_phone" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon / WA *</label>
                    <input type="text" id="field_phone" name="phone" value="{{ old('phone') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="field_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="field_email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="field_nik" class="block text-sm font-medium text-gray-700 mb-1">NIK (KTP) *</label>
                    <input type="text" id="field_nik" name="nik" value="{{ old('nik') }}" required maxlength="16" minlength="16" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                    @error('nik') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="field_passport_number" class="block text-sm font-medium text-gray-700 mb-1">No. Paspor</label>
                    <input type="text" id="field_passport_number" name="passport_number" value="{{ old('passport_number') }}" maxlength="20" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                </div>
                <div class="col-span-2">
                    <label for="field_address" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea id="field_address" name="address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">{{ old('address') }}</textarea>
                </div>
                <div>
                    <label for="field_city" class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                    <input type="text" id="field_city" name="city" value="{{ old('city') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                </div>
                <div>
                    <label for="field_province" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                    <input type="text" id="field_province" name="province" value="{{ old('province') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                </div>
                <div>
                    <label for="field_postal_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                    <input type="text" id="field_postal_code" name="postal_code" value="{{ old('postal_code') }}" maxlength="10" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                </div>
            </div>
        </div>

        {{-- Payment Method --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">Metode Pembayaran</h3>

            @if (!empty($providers))
            <label class="relative border-2 border-gray-200 rounded-xl p-4 mb-3 cursor-pointer hover:border-indigo-300 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 block">
                <div class="flex items-start gap-3">
                    <input type="radio" name="payment_method" value="gateway" checked class="mt-0.5 w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-gray-900">Bayar Online (Payment Gateway)</div>
                        <p class="text-xs text-gray-500 mt-1">QRIS, transfer bank, e-wallet — diproses otomatis via Tripay.</p>
                        <div id="payment_gateway_options" class="mt-3 space-y-2">
                            <div>
                                <label for="field_payment_provider_id" class="text-xs font-medium text-gray-600">Gateway</label>
                                <select id="field_payment_provider_id" name="payment_provider_id" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                                    @foreach ($providers as $prov)
                                        <option value="{{ $prov->id }}" {{ old('payment_provider_id') == $prov->id ? 'selected' : '' }}>{{ $prov->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="field_payment_channel" class="text-xs font-medium text-gray-600">Channel Pembayaran</label>
                                <select id="field_payment_channel" name="payment_channel" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                                    <option value="BRIVA" selected>BRI Virtual Account</option>
                                    <option value="BCAVA">BCA Virtual Account</option>
                                    <option value="BNIVA">BNI Virtual Account</option>
                                    <option value="MANDIRIVA">Mandiri Virtual Account</option>
                                    <option value="BSIVA">BSI Virtual Account</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </label>
            @endif

            <label class="relative border-2 border-gray-200 rounded-xl p-4 mb-3 cursor-pointer hover:border-indigo-300 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 block">
                <div class="flex items-start gap-3">
                    <input type="radio" name="payment_method" value="transfer" class="mt-0.5 w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">Transfer Bank Manual</div>
                        <p class="text-xs text-gray-500 mt-1">Transfer ke rekening BSI, lalu upload bukti transfer.</p>
                    </div>
                </div>
            </label>

            <label class="relative border-2 border-gray-200 rounded-xl p-4 cursor-pointer hover:border-indigo-300 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 block">
                <div class="flex items-start gap-3">
                    <input type="radio" name="payment_method" value="office" class="mt-0.5 w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">Bayar di Kantor</div>
                        <p class="text-xs text-gray-500 mt-1">Datang langsung ke kantor kami untuk pembayaran tunai.</p>
                    </div>
                </div>
            </label>

            @error('payment_method') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
            @error('payment_provider_id') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
        </div>

        {{-- Submit --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-4">Dengan menekan tombol "Pesan Sekarang", Anda menyetujui syarat & ketentuan yang berlaku.</p>
            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold py-3 rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all">
                Pesan Sekarang
            </button>
        </div>
    </form>
</main>

<footer class="border-t border-gray-200 mt-16 py-8 text-center text-sm text-gray-400">
    &copy; {{ date('Y') }} Travel Umrah. Seluruh hak cipta dilindungi.
</footer>

</body>
</html>