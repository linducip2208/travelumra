<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceSummaryWidget extends BaseWidget
{
    protected static ?int $sort = 7;

    public static function canView(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin', 'finance']);
    }

    protected function getStats(): array
    {
        $totalUnpaid = Invoice::where('status', '!=', 'paid')->sum('remaining_amount');
        $totalRevenue = Payment::where('status', 'confirmed')->sum('amount');
        $totalExpenses = Expense::where('status', 'approved')->sum('amount');
        $profit = $totalRevenue - $totalExpenses;
        $pendingPayments = Payment::where('status', 'pending')->count();

        return [
            Stat::make('Piutang Belum Lunas', 'Rp ' . number_format($totalUnpaid, 0, ',', '.'))
                ->description($pendingPayments . ' pembayaran menunggu')
                ->color('warning')
                ->icon('heroicon-o-exclamation-triangle'),
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->color('success')
                ->icon('heroicon-o-banknotes'),
            Stat::make('Total Pengeluaran', 'Rp ' . number_format($totalExpenses, 0, ',', '.'))
                ->color('danger')
                ->icon('heroicon-o-arrow-trending-down'),
            Stat::make('Laba / Rugi', 'Rp ' . number_format($profit, 0, ',', '.'))
                ->color($profit >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-calculator'),
        ];
    }
}
