<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Invoice;
use Illuminate\Console\Command;

class SendReminderNotifications extends Command
{
    protected $signature = 'notifications:send-reminders';

    protected $description = 'Kirim pengingat untuk booking dan invoice yang mendekati jatuh tempo';

    public function handle(): void
    {
        $bookings = Booking::where('status', 'confirmed')
            ->whereHas('package', function ($q) {
                $q->whereBetween('departure_date', [now()->toDateString(), now()->addDays(7)->toDateString()]);
            })
            ->with('package')
            ->get();

        $bookingCount = 0;
        foreach ($bookings as $booking) {
            $date = $booking->package->departure_date->format('d M Y');
            $this->info("Reminder: Booking {$booking->booking_number} departs on {$date}");
            $bookingCount++;
        }

        $invoices = Invoice::where('status', '!=', 'paid')
            ->whereBetween('due_date', [now()->toDateString(), now()->addDays(3)->toDateString()])
            ->get();

        $invoiceCount = 0;
        foreach ($invoices as $invoice) {
            $date = $invoice->due_date->format('d M Y');
            $this->info("Reminder: Invoice {$invoice->invoice_number} due on {$date}");
            $invoiceCount++;
        }

        $this->info("Sent {$bookingCount} booking reminders and {$invoiceCount} invoice reminders.");

        \Log::info('notifications:send-reminders', [
            'booking_reminders' => $bookingCount,
            'invoice_reminders' => $invoiceCount,
        ]);
    }
}
