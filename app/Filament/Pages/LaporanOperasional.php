<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\Cluster;
use App\Models\Equipment;
use App\Models\Package;
use App\Models\Visa;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class LaporanOperasional extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.laporan-operasional';

    protected static string | \UnitEnum | null $navigationGroup = 'Laporan';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'Laporan Operasional';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'date_from' => request()->get('date_from', now()->startOfMonth()->toDateString()),
            'date_to' => request()->get('date_to', now()->endOfMonth()->toDateString()),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                DatePicker::make('date_from')
                    ->label('Dari Tanggal')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->closeOnDateSelection(),
                DatePicker::make('date_to')
                    ->label('Sampai Tanggal')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->closeOnDateSelection(),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function applyFilters(): void
    {
        $this->validate();
    }

    public function getSummaryCardsProperty(): array
    {
        $state = $this->form->getState();
        $dateFrom = $state['date_from'] ?? null;
        $dateTo = $state['date_to'] ?? null;

        $totalDepartures = Booking::query()
            ->whereIn('status', ['departed', 'returned'])
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->count();

        $activeClusters = Cluster::query()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->count();

        $visaApproved = Visa::query()
            ->where('status', 'approved')
            ->when($dateFrom, fn ($q) => $q->whereDate('approval_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('approval_date', '<=', $dateTo))
            ->count();

        $equipmentDistributed = Equipment::query()
            ->where('allocated', '>', 0)
            ->sum('allocated');

        return [
            [
                'label' => 'Jemaah Berangkat',
                'value' => number_format($totalDepartures, 0, ',', '.'),
                'icon' => 'heroicon-o-globe-alt',
                'color' => 'indigo',
            ],
            [
                'label' => 'Kloter Aktif',
                'value' => number_format($activeClusters, 0, ',', '.'),
                'icon' => 'heroicon-o-user-group',
                'color' => 'emerald',
            ],
            [
                'label' => 'Visa Disetujui',
                'value' => number_format($visaApproved, 0, ',', '.'),
                'icon' => 'heroicon-o-document-check',
                'color' => 'amber',
            ],
            [
                'label' => 'Perlengkapan Dibagikan',
                'value' => number_format($equipmentDistributed, 0, ',', '.'),
                'icon' => 'heroicon-o-gift',
                'color' => 'sky',
            ],
        ];
    }

    public function getChartDataProperty(): array
    {
        $statuses = [
            'pending' => 'Menunggu',
            'dp_paid' => 'DP Dibayar',
            'confirmed' => 'Dikonfirmasi',
            'documents_complete' => 'Dokumen Lengkap',
            'visa_processing' => 'Proses Visa',
            'visa_approved' => 'Visa Disetujui',
            'ready_to_depart' => 'Siap Berangkat',
            'departed' => 'Berangkat',
            'returned' => 'Kembali',
            'cancelled' => 'Dibatalkan',
            'refunded' => 'Refund',
        ];

        $colors = [
            '#9ca3af', '#fbbf24', '#3b82f6', '#6366f1',
            '#f59e0b', '#10b981', '#8b5cf6', '#06b6d4',
            '#22c55e', '#ef4444', '#f97316',
        ];

        $labels = [];
        $data = [];
        $bgColors = [];
        $i = 0;

        foreach ($statuses as $key => $label) {
            $count = Booking::where('status', $key)->count();
            if ($count > 0) {
                $labels[] = $label;
                $data[] = $count;
                $bgColors[] = $colors[$i] ?? '#9ca3af';
            }
            $i++;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $bgColors,
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
        ];
    }

    public function getUpcomingDeparturesProperty(): array
    {
        $state = $this->form->getState();
        $dateFrom = $state['date_from'] ?? now()->toDateString();
        $dateTo = $state['date_to'] ?? now()->addMonths(3)->toDateString();

        return Package::query()
            ->where('is_active', true)
            ->whereDate('departure_date', '>=', $dateFrom)
            ->whereDate('departure_date', '<=', $dateTo)
            ->withCount('bookings')
            ->orderBy('departure_date')
            ->limit(10)
            ->get()
            ->map(fn ($p) => [
                'package_name' => $p->name,
                'departure_date' => $p->departure_date?->format('d/m/Y') ?? '-',
                'pilgrims_count' => $p->bookings_count,
                'quota' => $p->quota,
                'status' => $p->bookings_count >= $p->quota ? 'Penuh' : 'Tersedia',
            ])
            ->toArray();
    }

    public static function getNavigationLabel(): string
    {
        return 'Laporan Operasional';
    }
}
