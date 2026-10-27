<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Cluster;
use App\Models\Expense;
use App\Models\Invoice;

class ReportPdfService
{
    public function generateSalesReport(array $filters): string
    {
        $dateFrom = $filters['date_from'] ?? now()->startOfMonth()->toDateString();
        $dateTo = $filters['date_to'] ?? now()->endOfMonth()->toDateString();

        $bookings = Booking::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['package', 'pilgrim'])->get();
        $totalBooking = $bookings->count();
        $totalRevenue = $bookings->sum('total_price');
        $payments = \App\Models\Payment::where('status', 'confirmed')
            ->whereBetween('payment_date', [$dateFrom, $dateTo])->sum('amount');
        $avgPerBooking = $totalBooking > 0 ? $totalRevenue / $totalBooking : 0;

        $html = view('pdf.laporan-penjualan', compact(
            'bookings', 'payments', 'totalBooking', 'totalRevenue', 'avgPerBooking', 'dateFrom', 'dateTo'
        ))->render();

        $filename = 'laporan-penjualan-' . date('Ymd-His') . '.html';
        $path = storage_path('app/public/reports/' . $filename);
        if (!is_dir(dirname($path))) mkdir(dirname($path), 0755, true);
        file_put_contents($path, $html);
        return $path;
    }

    public function generateInvoice(Invoice $invoice): string
    {
        $invoice->load(['booking.package', 'booking.pilgrim', 'payments']);
        $html = view('pdf.invoice', compact('invoice'))->render();

        $filename = 'invoice-' . $invoice->invoice_number . '.html';
        $path = storage_path('app/public/invoices/' . $filename);
        if (!is_dir(dirname($path))) mkdir(dirname($path), 0755, true);
        file_put_contents($path, $html);
        return $path;
    }

    public function generateManifest(Cluster $cluster): string
    {
        $cluster->load(['bookings.pilgrim', 'package']);
        $html = view('pdf.manifest', compact('cluster'))->render();

        $filename = 'manifest-' . $cluster->code . '.html';
        $path = storage_path('app/public/manifests/' . $filename);
        if (!is_dir(dirname($path))) mkdir(dirname($path), 0755, true);
        file_put_contents($path, $html);
        return $path;
    }

    public function generateFinanceReport(array $filters): string
    {
        $dateFrom = $filters['date_from'] ?? now()->startOfMonth()->toDateString();
        $dateTo = $filters['date_to'] ?? now()->endOfMonth()->toDateString();

        $payments = \App\Models\Payment::where('status', 'confirmed')
            ->whereBetween('payment_date', [$dateFrom, $dateTo])->sum('amount');
        $expenses = Expense::where('status', 'approved')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])->sum('amount');
        $outstanding = Invoice::where('status', '!=', 'paid')->sum('remaining_amount');

        $html = view('pdf.laporan-keuangan', compact(
            'payments', 'expenses', 'outstanding', 'dateFrom', 'dateTo'
        ))->render();

        $filename = 'laporan-keuangan-' . date('Ymd-His') . '.html';
        $path = storage_path('app/public/reports/' . $filename);
        if (!is_dir(dirname($path))) mkdir(dirname($path), 0755, true);
        file_put_contents($path, $html);
        return $path;
    }
}
