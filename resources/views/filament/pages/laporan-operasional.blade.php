<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Form -->
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="px-6 py-4">
                <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">Filter Laporan</h2>
            </div>
            <div class="px-6 pb-6">
                <form wire:submit.prevent="applyFilters" class="flex flex-wrap items-end gap-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 flex-1">
                        {{ $this->form }}
                    </div>
                    <x-filament::button type="submit" color="primary" size="sm">
                        Terapkan Filter
                    </x-filament::button>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $colors = [
                    'indigo' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-950 dark:text-indigo-300',
                    'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-950 dark:text-emerald-300',
                    'amber' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-950 dark:text-amber-300',
                    'sky' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-950 dark:text-sky-300',
                ];
            @endphp
            @foreach ($this->summaryCards as $card)
                <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                            <p class="mt-1 text-2xl font-bold tracking-tight text-gray-950 dark:text-white">{{ $card['value'] }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg {{ $colors[$card['color']] ?? $colors['indigo'] }} ring-1">
                            <x-filament::icon icon="{{ $card['icon'] }}" class="h-6 w-6" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Booking Status Doughnut Chart -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Distribusi Status Booking</h3>
            <div class="relative h-80 w-full max-w-md mx-auto">
                <canvas id="statusChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Upcoming Departures Table -->
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">Keberangkatan Mendatang</h3>
                <x-filament::button tag="button" color="gray" size="sm" class="cursor-pointer">
                    <x-filament::icon icon="heroicon-o-document-arrow-down" class="h-4 w-4 mr-1" />
                    Export PDF
                </x-filament::button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Nama Paket</th>
                            <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Tanggal Berangkat</th>
                            <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 text-right">Jemaah</th>
                            <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 text-right">Kuota</th>
                            <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($this->upcomingDepartures as $dep)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-6 py-3.5 font-medium text-gray-900 dark:text-white">{{ $dep['package_name'] }}</td>
                                <td class="px-6 py-3.5 text-gray-700 dark:text-gray-300">{{ $dep['departure_date'] }}</td>
                                <td class="px-6 py-3.5 text-right text-gray-700 dark:text-gray-300">{{ number_format($dep['pilgrims_count'], 0, ',', '.') }}</td>
                                <td class="px-6 py-3.5 text-right text-gray-700 dark:text-gray-300">{{ number_format($dep['quota'], 0, ',', '.') }}</td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $dep['status'] === 'Penuh' ? 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20 dark:bg-rose-950 dark:text-rose-300' : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                        {{ $dep['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">Belum ada jadwal keberangkatan dalam periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            let chartInstance = null;
            const ctx = document.getElementById('statusChart');

            function renderChart() {
                if (!ctx) return;
                if (chartInstance) chartInstance.destroy();

                const chartData = @json($this->chartData);
                chartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 16,
                                    font: { size: 12 }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        const pct = total > 0 ? Math.round((ctx.raw / total) * 100) : 0;
                                        return ctx.label + ': ' + ctx.raw + ' (' + pct + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            renderChart();

            Livewire.hook('morph.updated', ({ component }) => {
                if (component.id === @js($this->getId())) {
                    setTimeout(renderChart, 100);
                }
            });
        });
    </script>
</x-filament-panels::page>
