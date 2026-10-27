<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    public function getHeading(): string|null { return 'Pendapatan 6 Bulan Terakhir'; }
    protected static ?int $sort = 2;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabel = $date->translatedFormat('M Y');
            $labels[] = $monthLabel;

            $total = Payment::where('status', 'confirmed')
                ->whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->sum('amount');

            $data[] = (float) $total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $data,
                    'backgroundColor' => '#6366f1',
                    'borderColor' => '#4f46e5',
                ],
            ],
            'labels' => $labels,
        ];
    }
}
