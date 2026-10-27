<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class EscalateOverdueBookings extends Command
{
    protected $signature = 'bookings:escalate-overdue';

    protected $description = 'Eskalasi booking yang overdue ke status expired';

    public function handle(): void
    {
        $pendingExpired = Booking::where('status', 'pending')
            ->where('created_at', '<', now()->subDays(7))
            ->update(['status' => 'expired']);

        $confirmedExpired = Booking::where('status', 'confirmed')
            ->whereHas('package', function ($q) {
                $q->where('departure_date', '<', now());
            })
            ->update(['status' => 'expired']);

        $total = $pendingExpired + $confirmedExpired;

        $this->info("Escalated {$total} bookings to expired.");
        $this->line("  - Pending > 7 days: {$pendingExpired}");
        $this->line("  - Confirmed past departure: {$confirmedExpired}");

        \Log::info("bookings:escalate-overdue", [
            'pending_expired' => $pendingExpired,
            'confirmed_expired' => $confirmedExpired,
            'total' => $total,
        ]);
    }
}
