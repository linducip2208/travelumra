<?php

namespace App\Filament\Pages;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class LaporanKeuangan extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.laporan-keuangan';

    protected static string | \UnitEnum | null $navigationGroup = 'Laporan';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?int $navigationSort = 2;
    protected static ?string $title = 'Laporan Keuangan';

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

        $invoiceQuery = Invoice::query();
        $paymentQuery = Payment::query()->where('status', 'confirmed');

        if ($dateFrom) {
            $invoiceQuery->whereDate('created_at', '>=', $dateFrom);
            $paymentQuery->whereDate('payment_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $invoiceQuery->whereDate('created_at', '<=', $dateTo);
            $paymentQuery->whereDate('payment_date', '<=', $dateTo);
        }

        $totalRevenue = (float) (clone $invoiceQuery)->sum('total_amount');
        $totalCollected = (float) (clone $paymentQuery)->sum('amount');
        $outstanding = (float) Invoice::query()
            ->where('status', '!=', 'paid')
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->sum('remaining_amount');
        $expenses = (float) Expense::query()
            ->where('status', 'approved')
            ->when($dateFrom, fn ($q) => $q->whereDate('expense_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('expense_date', '<=', $dateTo))
            ->sum('amount');

        return [
            [
                'label' => 'Total Pendapatan',
                'value' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'),
                'icon' => 'heroicon-o-banknotes',
                'color' => 'indigo',
            ],
            [
                'label' => 'Total Tertagih',
                'value' => 'Rp ' . number_format($totalCollected, 0, ',', '.'),
                'icon' => 'heroicon-o-check-circle',
                'color' => 'emerald',
            ],
            [
                'label' => 'Piutang Belum Lunas',
                'value' => 'Rp ' . number_format($outstanding, 0, ',', '.'),
                'icon' => 'heroicon-o-exclamation-triangle',
                'color' => 'amber',
            ],
            [
                'label' => 'Total Pengeluaran',
                'value' => 'Rp ' . number_format($expenses, 0, ',', '.'),
                'icon' => 'heroicon-o-arrow-trending-down',
                'color' => 'rose',
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
        $revenueData = [];
        $collectedData = [];

        switch ($groupBy) {
            case 'harian':
                $results = Payment::query()
                    ->where('status', 'confirmed')
                    ->whereDate('payment_date', '>=', $dateFrom)
                    ->whereDate('payment_date', '<=', $dateTo)
                    ->selectRaw('DATE(payment_date) as period, SUM(amount) as total')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                foreach ($results as $row) {
                    $labels[] = date('d/m', strtotime($row->period));
                    $collectedData[] = (float) $row->total;
                }

                $invResults = Invoice::query()
                    ->whereDate('created_at', '>=', $dateFrom)
                    ->whereDate('created_at', '<=', $dateTo)
                    ->selectRaw('DATE(created_at) as period, SUM(total_amount) as total')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();

                $invMap = $invResults->pluck('total', 'period');
                foreach ($results as $row) {
                    $revenueData[] = (float) ($invMap[$row->period] ?? 0);
                }
                break;

            case 'tahunan':
                $results = Payment::query()
                    ->where('status', 'confirmed')
                    ->whereDate('payment_date', '>=', $dateFrom)
                    ->whereDate('payment_date', '<=', $dateTo)
                    ->selectRaw('YEAR(payment_date) as period, SUM(amount) as total')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                foreach ($results as $row) {
                    $labels[] = (string) $row->period;
                    $collectedData[] = (float) $row->total;
                }

                $invResults = Invoice::query()
                    ->whereDate('created_at', '>=', $dateFrom)
                    ->whereDate('created_at', '<=', $dateTo)
                    ->selectRaw('YEAR(created_at) as period, SUM(total_amount) as total')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();

                $invMap = $invResults->pluck('total', 'period');
                foreach ($results as $row) {
                    $revenueData[] = (float) ($invMap[$row->period] ?? 0);
                }
                break;

            case 'bulanan':
            default:
                $results = Payment::query()
                    ->where('status', 'confirmed')
                    ->whereDate('payment_date', '>=', $dateFrom)
                    ->whereDate('payment_date', '<=', $dateTo)
                    ->selectRaw("DATE_FORMAT(payment_date, '%Y-%m') as period, SUM(amount) as total")
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                foreach ($results as $row) {
                    $labels[] = date('M Y', strtotime($row->period . '-01'));
                    $collectedData[] = (float) $row->total;
                }

                $invResults = Invoice::query()
                    ->whereDate('created_at', '>=', $dateFrom)
                    ->whereDate('created_at', '<=', $dateTo)
                    ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, SUM(total_amount) as total")
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();

                $invMap = $invResults->pluck('total', 'period');
                foreach ($results as $row) {
                    $revenueData[] = (float) ($invMap[$row->period] ?? 0);
                }
                break;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Pendapatan',
                    'data' => $revenueData,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                    'fill' => true,
                    'pointBackgroundColor' => '#6366f1',
                    'pointRadius' => 4,
                ],
                [
                    'label' => 'Total Tertagih',
                    'data' => $collectedData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                    'fill' => true,
                    'pointBackgroundColor' => '#10b981',
                    'pointRadius' => 4,
                ],
            ],
        ];
    }

    public function getPaymentMethodsProperty(): array
    {
        $state = $this->form->getState();
        $dateFrom = $state['date_from'] ?? now()->startOfMonth()->toDateString();
        $dateTo = $state['date_to'] ?? now()->endOfMonth()->toDateString();

        return Payment::query()
            ->where('status', 'confirmed')
            ->whereDate('payment_date', '>=', $dateFrom)
            ->whereDate('payment_date', '<=', $dateTo)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }

    public static function getNavigationLabel(): string
    {
        return 'Laporan Keuangan';
    }
}
