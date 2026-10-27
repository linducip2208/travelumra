<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;

class BookingStatusChart extends ChartWidget
{
    public function getHeading(): string|null { return 'Status Booking'; }
    protected static ?int $sort = 3;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $statuses = [
            'pending' => 'Menunggu',
            'dp_paid' => 'DP Dibayar',
            'confirmed' => 'Dikonfirmasi',
            'documents_complete' => 'Dokumen Lengkap',
            'visa_processing' => 'Proses Visa',
            'visa_approved' => 'Visa Disetujui',
            'visa_rejected' => 'Visa Ditolak',
            'ready_to_depart' => 'Siap Berangkat',
            'departed' => 'Berangkat',
            'returned' => 'Kembali',
            'cancelled' => 'Dibatalkan',
            'refunded' => 'Refund',
        ];

        $labels = [];
        $data = [];
        $colors = [
            '#9ca3af', '#fbbf24', '#3b82f6', '#6366f1',
            '#f59e0b', '#10b981', '#ef4444', '#8b5cf6',
            '#06b6d4', '#22c55e', '#ef4444', '#f97316',
        ];

        $i = 0;
        foreach ($statuses as $key => $label) {
            $count = Booking::where('status', $key)->count();
            if ($count > 0) {
                $labels[] = $label;
                $data[] = $count;
            }
            $i++;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Booking',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }
}
