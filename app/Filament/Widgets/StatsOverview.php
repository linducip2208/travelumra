<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Cluster;
use App\Models\Payment;
use App\Models\Pilgrim;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalJemaah = Pilgrim::count();
        $bookingAktif = Booking::whereNotIn('status', ['cancelled', 'refunded', 'returned'])->count();
        $pendapatanBulanIni = Payment::where('status', 'confirmed')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');
        $kloterBerangkat = Cluster::where('status', 'departed')->count();

        return [
            Stat::make('Total Jemaah', $totalJemaah)
                ->description('Total jemaah terdaftar')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary')
                ->icon('heroicon-o-users'),

            Stat::make('Booking Aktif', $bookingAktif)
                ->description('Booking yang sedang berjalan')
                ->descriptionIcon('heroicon-o-ticket')
                ->color('success')
                ->icon('heroicon-o-ticket'),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.'))
                ->description(now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('warning')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Kloter Berangkat', $kloterBerangkat)
                ->description('Kloter sedang berangkat')
                ->descriptionIcon('heroicon-o-globe-alt')
                ->color('info')
                ->icon('heroicon-o-globe-alt'),
        ];
    }
}
