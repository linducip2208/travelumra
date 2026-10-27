<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\Payment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class LaporanPenjualan extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.laporan-penjualan';

    protected static string | \UnitEnum | null $navigationGroup = 'Laporan';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'Laporan Penjualan';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'date_from' => request()->get('date_from', now()->startOfMonth()->toDateString()),
            'date_to' => request()->get('date_to', now()->endOfMonth()->toDateString()),
            'group_by' => request()->get('group_by', 'bulanan'),
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
                Select::make('group_by')
                    ->label('Kelompokkan')
                    ->options([
                        'harian' => 'Harian',
                        'bulanan' => 'Bulanan',
                        'tahunan' => 'Tahunan',
                    ])
                    ->default('bulanan')
                    ->native(false),
            ])
            ->columns(3)
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

        $bookingQuery = Booking::query();
        $paymentQuery = Payment::query()->where('status', 'confirmed');

        if ($dateFrom) {
            $bookingQuery->whereDate('created_at', '>=', $dateFrom);
            $paymentQuery->whereDate('payment_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $bookingQuery->whereDate('created_at', '<=', $dateTo);
            $paymentQuery->whereDate('payment_date', '<=', $dateTo);
        }

        $totalBooking = (clone $bookingQuery)->count();
        $totalRevenue = (float) (clone $paymentQuery)->sum('amount');
        $avgPerBooking = $totalBooking > 0 ? $totalRevenue / $totalBooking : 0;
        $completedBookings = (clone $bookingQuery)->where('status', 'returned')->count();

        return [
            [
                'label' => 'Total Booking',
                'value' => number_format($totalBooking, 0, ',', '.'),
                'icon' => 'heroicon-o-ticket',
                'color' => 'indigo',
            ],
            [
                'label' => 'Total Pendapatan',
                'value' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'),
                'icon' => 'heroicon-o-banknotes',
                'color' => 'emerald',
            ],
            [
                'label' => 'Rata-rata per Booking',
                'value' => 'Rp ' . number_format($avgPerBooking, 0, ',', '.'),
                'icon' => 'heroicon-o-calculator',
                'color' => 'amber',
            ],
            [
                'label' => 'Booking Selesai',
                'value' => number_format($completedBookings, 0, ',', '.'),
                'icon' => 'heroicon-o-check-badge',
                'color' => 'sky',
            ],
        ];
    }

    public function getChartDataProperty(): array
    {
        $state = $this->form->getState();
        $dateFrom = $state['date_from'] ?? now()->startOfMonth()->toDateString();
        $dateTo = $state['date_to'] ?? now()->endOfMonth()->toDateString();
        $groupBy = $state['group_by'] ?? 'bulanan';

        $labels = [];
        $data = [];

        $query = Payment::query()
            ->where('status', 'confirmed')
            ->whereDate('payment_date', '>=', $dateFrom)
            ->whereDate('payment_date', '<=', $dateTo);

        switch ($groupBy) {
            case 'harian':
                $results = (clone $query)
                    ->selectRaw('DATE(payment_date) as period, SUM(amount) as total')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                foreach ($results as $row) {
                    $labels[] = date('d/m', strtotime($row->period));
                    $data[] = (float) $row->total;
                }
                break;

            case 'tahunan':
                $results = (clone $query)
                    ->selectRaw('YEAR(payment_date) as period, SUM(amount) as total')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                foreach ($results as $row) {
                    $labels[] = (string) $row->period;
                    $data[] = (float) $row->total;
                }
                break;

            case 'bulanan':
            default:
                $results = (clone $query)
                    ->selectRaw("DATE_FORMAT(payment_date, '%Y-%m') as period, SUM(amount) as total")
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                foreach ($results as $row) {
                    $labels[] = date('M Y', strtotime($row->period . '-01'));
                    $data[] = (float) $row->total;
                }
                break;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $data,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.7)',
                    'borderColor' => '#4f46e5',
                    'borderWidth' => 1,
                    'borderRadius' => 6,
                ],
            ],
        ];
    }

    public function getTopPackagesProperty(): array
    {
        $state = $this->form->getState();
        $dateFrom = $state['date_from'] ?? now()->startOfMonth()->toDateString();
        $dateTo = $state['date_to'] ?? now()->endOfMonth()->toDateString();

        return Booking::query()
            ->whereDate('bookings.created_at', '>=', $dateFrom)
            ->whereDate('bookings.created_at', '<=', $dateTo)
            ->join('packages', 'bookings.package_id', '=', 'packages.id')
            ->select('packages.name as package_name', DB::raw('COUNT(*) as booking_count'), DB::raw('SUM(bookings.total_price) as total_revenue'))
            ->groupBy('packages.id', 'packages.name')
            ->orderByDesc('booking_count')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public static function getNavigationLabel(): string
    {
        return 'Laporan Penjualan';
    }
}
