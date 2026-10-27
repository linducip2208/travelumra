<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class RefundCalculator
{
    public function calculate(Booking $booking): array
    {
        $totalPaid = (float) $booking->paid_amount;
        $departure = $booking->package?->departure_date;

        if (!$departure) {
            return $this->result($totalPaid, 0, 100);
        }

        $daysUntilDeparture = Carbon::now()->diffInDays(Carbon::parse($departure), false);

        return match (true) {
            $daysUntilDeparture >= 30 => $this->result($totalPaid, 25, 75),
            $daysUntilDeparture >= 14 => $this->result($totalPaid, 50, 50),
            $daysUntilDeparture >= 7  => $this->result($totalPaid, 75, 25),
            default                   => $this->result($totalPaid, 100, 0),
        };
    }

    private function result(float $totalPaid, int $feePct, int $refundPct): array
    {
        $fee = $totalPaid * ($feePct / 100);
        $refund = $totalPaid - $fee;

        return [
            'total_paid' => $totalPaid,
            'cancellation_fee_pct' => $feePct,
            'cancellation_fee' => $fee,
            'refund_amount' => $refund,
            'refund_pct' => $refundPct,
        ];
    }
}
