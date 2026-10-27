<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendInvoiceEmail(Invoice $invoice): void
    {
        $invoice->load('booking.pilgrim');
        $email = $invoice->booking->pilgrim->email ?? null;
        if (!$email) return;

        Log::info("Email notification: Invoice {$invoice->invoice_number} sent to {$email}");
    }

    public function sendVisaStatusUpdate($visa, string $status): void
    {
        $visa->load('pilgrim');
        $email = $visa->pilgrim->email ?? null;
        if (!$email) return;

        $statusLabels = [
            'submitted' => 'Diajukan ke Kedutaan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'issued' => 'Visa Diterbitkan',
        ];

        $label = $statusLabels[$status] ?? $status;
        Log::info("Visa notification: Status {$visa->visa_number} updated to '{$label}' for {$visa->pilgrim->full_name}");
    }

    public function sendDepartureReminder(Booking $booking): void
    {
        $booking->load(['pilgrim', 'package']);
        $email = $booking->pilgrim->email ?? null;
        if (!$email) return;

        $departure = $booking->package->departure_date?->format('d M Y') ?? '-';
        Log::info("Departure reminder: Booking {$booking->booking_number}, departure {$departure}, sent to {$booking->pilgrim->full_name}");
    }

    public function sendPaymentReminder(Invoice $invoice): void
    {
        $invoice->load('booking.pilgrim');
        $email = $invoice->booking->pilgrim->email ?? null;
        if (!$email) return;

        Log::info("Payment reminder: Invoice {$invoice->invoice_number}, remaining Rp " . number_format($invoice->remaining_amount, 0, ',', '.'));
    }
}
