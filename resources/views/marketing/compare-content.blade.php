<div class="space-y-6">
    <h2 class="text-xl font-bold text-gray-900">Perbandingan Detail: {{ $packageA->name }} vs {{ $packageB->name }}</h2>

    <p>Berikut adalah perbandingan mendetail antara dua paket umrah populer: <strong>{{ $packageA->name }}</strong> dan <strong>{{ $packageB->name }}</strong>. Kami akan membandingkan setiap aspek penting agar Anda dapat memilih paket yang paling sesuai dengan kebutuhan dan anggaran Anda.</p>

    <h3 class="text-lg font-bold text-gray-900 mt-6">Ringkasan Perbandingan</h3>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left p-3 border-b border-gray-200 font-semibold">Aspek</th>
                    <th class="text-left p-3 border-b border-gray-200 font-semibold text-indigo-700">{{ $packageA->name }}</th>
                    <th class="text-left p-3 border-b border-gray-200 font-semibold text-violet-700">{{ $packageB->name }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr>
                    <td class="p-3 text-gray-500">Tipe</td>
                    <td class="p-3 font-medium">{{ ucfirst(str_replace('_', ' ', $packageA->type)) }}</td>
                    <td class="p-3 font-medium">{{ ucfirst(str_replace('_', ' ', $packageB->type)) }}</td>
                </tr>
                <tr>
                    <td class="p-3 text-gray-500">Kategori</td>
                    <td class="p-3 font-medium">{{ ucfirst($packageA->category) }}</td>
                    <td class="p-3 font-medium">{{ ucfirst($packageB->category) }}</td>
                </tr>
                <tr>
                    <td class="p-3 text-gray-500">Maskapai</td>
                    <td class="p-3 font-medium">{{ $packageA->airline ?? '-' }}</td>
                    <td class="p-3 font-medium">{{ $packageB->airline ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="p-3 text-gray-500">Durasi</td>
                    <td class="p-3 font-medium">{{ $packageA->duration_days }} Hari</td>
                    <td class="p-3 font-medium">{{ $packageB->duration_days }} Hari</td>
                </tr>
                <tr>
                    <td class="p-3 text-gray-500">Keberangkatan</td>
                    <td class="p-3 font-medium">{{ $packageA->departure_date->format('d M Y') }}</td>
                    <td class="p-3 font-medium">{{ $packageB->departure_date->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td class="p-3 text-gray-500">Kepulangan</td>
                    <td class="p-3 font-medium">{{ $packageA->return_date->format('d M Y') }}</td>
                    <td class="p-3 font-medium">{{ $packageB->return_date->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td class="p-3 text-gray-500">Bandara</td>
                    <td class="p-3 font-medium">{{ $packageA->departure_airport ?? '-' }}</td>
                    <td class="p-3 font-medium">{{ $packageB->departure_airport ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="p-3 text-gray-500">Hotel Makkah</td>
                    <td class="p-3 font-medium">
                        @if($packageA->makkah_hotel_name)
                            {{ $packageA->makkah_hotel_name }} {{ $packageA->makkah_hotel_stars ? str_repeat('★', $packageA->makkah_hotel_stars) : '' }}
                            @if($packageA->makkah_hotel_distance_meters) ({{ $packageA->makkah_hotel_distance_meters }}m) @endif
                        @else - @endif
                    </td>
                    <td class="p-3 font-medium">
                        @if($packageB->makkah_hotel_name)
                            {{ $packageB->makkah_hotel_name }} {{ $packageB->makkah_hotel_stars ? str_repeat('★', $packageB->makkah_hotel_stars) : '' }}
                            @if($packageB->makkah_hotel_distance_meters) ({{ $packageB->makkah_hotel_distance_meters }}m) @endif
                        @else - @endif
                    </td>
                </tr>
                <tr>
                    <td class="p-3 text-gray-500">Hotel Madinah</td>
                    <td class="p-3 font-medium">
                        @if($packageA->madinah_hotel_name)
                            {{ $packageA->madinah_hotel_name }} {{ $packageA->madinah_hotel_stars ? str_repeat('★', $packageA->madinah_hotel_stars) : '' }}
                        @else - @endif
                    </td>
                    <td class="p-3 font-medium">
                        @if($packageB->madinah_hotel_name)
                            {{ $packageB->madinah_hotel_name }} {{ $packageB->madinah_hotel_stars ? str_repeat('★', $packageB->madinah_hotel_stars) : '' }}
                        @else - @endif
                    </td>
                </tr>
                @php
                    $minA = $packageA->price_quad > 0 ? $packageA->price_quad : ($packageA->price_triple > 0 ? $packageA->price_triple : $packageA->price_double);
                    $minB = $packageB->price_quad > 0 ? $packageB->price_quad : ($packageB->price_triple > 0 ? $packageB->price_triple : $packageB->price_double);
                @endphp
                <tr>
                    <td class="p-3 text-gray-500">Harga Mulai</td>
                    <td class="p-3 font-bold {{ $minA <= $minB ? 'text-green-600' : 'text-gray-900' }}">Rp {{ number_format($minA, 0, ',', '.') }}</td>
                    <td class="p-3 font-bold {{ $minB <= $minA ? 'text-green-600' : 'text-gray-900' }}">Rp {{ number_format($minB, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="p-3 text-gray-500">Kuota</td>
                    <td class="p-3 font-medium">{{ $packageA->quota }}/{{ $packageA->booked_count }} terisi</td>
                    <td class="p-3 font-medium">{{ $packageB->quota }}/{{ $packageB->booked_count }} terisi</td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3 class="text-lg font-bold text-gray-900 mt-8">Kesimpulan</h3>
    <p>Kedua paket umrah ini memiliki keunggulan masing-masing. Pilihan terbaik bergantung pada preferensi pribadi Anda, seperti budget, kenyamanan hotel, jarak hotel ke masjid, dan jadwal keberangkatan yang diinginkan.</p>
    <p>Kami menyarankan Anda untuk menghubungi tim kami untuk konsultasi lebih lanjut. Tim konsultan kami akan membantu Anda memilih paket yang paling sesuai dengan kebutuhan ibadah Anda.</p>

    <div class="mt-6 flex flex-col sm:flex-row gap-4">
        <a href="{{ route('package.detail', $packageA->slug) }}" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition text-sm">
            Lihat {{ $packageA->name }}
        </a>
        <a href="{{ route('package.detail', $packageB->slug) }}" class="inline-flex items-center justify-center px-6 py-3 bg-violet-600 text-white font-semibold rounded-xl hover:bg-violet-700 transition text-sm">
            Lihat {{ $packageB->name }}
        </a>
    </div>

    <h3 class="text-lg font-bold text-gray-900 mt-8">FAQ Perbandingan</h3>

    <div class="space-y-4">
        <div class="bg-gray-50 rounded-xl p-5">
            <h4 class="font-semibold text-gray-900 mb-2">Apa perbedaan utama {{ $packageA->name }} dan {{ $packageB->name }}?</h4>
            <p class="text-sm text-gray-600">
                {{ $packageA->name }} adalah paket dengan durasi {{ $packageA->duration_days }} hari,
                @if($packageA->airline) menggunakan maskapai {{ $packageA->airline }}, @endif
                dengan hotel Makkah {{ $packageA->makkah_hotel_name ?? 'pilihan' }}
                @if($packageA->makkah_hotel_distance_meters) berjarak {{ $packageA->makkah_hotel_distance_meters }}m dari Masjidil Haram @endif.
                Sementara {{ $packageB->name }} berdurasi {{ $packageB->duration_days }} hari,
                @if($packageB->airline) menggunakan {{ $packageB->airline }}, @endif
                dengan hotel Makkah {{ $packageB->makkah_hotel_name ?? 'pilihan' }}
                @if($packageB->makkah_hotel_distance_meters) berjarak {{ $packageB->makkah_hotel_distance_meters }}m @endif.
            </p>
        </div>
        <div class="bg-gray-50 rounded-xl p-5">
            <h4 class="font-semibold text-gray-900 mb-2">Mana yang lebih hemat?</h4>
            <p class="text-sm text-gray-600">
                @if($minA < $minB)
                    {{ $packageA->name }} lebih hemat dengan harga mulai Rp {{ number_format($minA, 0, ',', '.') }} dibanding {{ $packageB->name }} seharga Rp {{ number_format($minB, 0, ',', '.') }} (selisih Rp {{ number_format($minB - $minA, 0, ',', '.') }}).
                @elseif($minB < $minA)
                    {{ $packageB->name }} lebih hemat dengan harga mulai Rp {{ number_format($minB, 0, ',', '.') }} dibanding {{ $packageA->name }} seharga Rp {{ number_format($minA, 0, ',', '.') }} (selisih Rp {{ number_format($minA - $minB, 0, ',', '.') }}).
                @else
                    Kedua paket memiliki harga yang setara. Perbedaan ada pada fasilitas hotel, maskapai, dan durasi.
                @endif
            </p>
        </div>
    </div>
</div>
