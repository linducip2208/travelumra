<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\LoyaltyPoint;
use App\Models\Setting;

class BookingObserver
{
    public function updated(Booking $booking): void
    {
        // Idempotency: jangan duplikat loyalty untuk booking yang sama
        if ($booking->wasChanged('status') && $booking->status === 'confirmed' && $booking->referral_by) {
            $exists = LoyaltyPoint::where('booking_id', $booking->id)
                ->where('pilgrim_id', $booking->referral_by)
                ->exists();
            if ($exists) {
                return;
            }
            $points = (int) Setting::getValue('referral_loyalty_points', 500);

            LoyaltyPoint::create([
                'pilgrim_id' => $booking->referral_by,
                'booking_id' => $booking->id,
                'points' => $points,
                'transaction_type' => 'earned',
                'description' => "Referral booking {$booking->booking_number}",
                'value_rupiah' => $booking->referral_commission,
            ]);
        }

        // Guard transisi status ilegal: paid booking tidak boleh kembali ke pending
        if ($booking->wasChanged('status')) {
            $old = $booking->getOriginal('status');
            $paidStates = ['dp_paid', 'confirmed', 'documents_complete', 'visa_processing', 'visa_approved', 'ready_to_depart', 'departed', 'returned'];
            if (in_array($old, $paidStates, true) && $booking->status === 'pending' && $booking->paid_amount > 0) {
                \Illuminate\Support\Facades\Log::warning('Illegal booking status regression', [
                    'booking_id' => $booking->id,
                    'from' => $old,
                    'to' => $booking->status,
                ]);
            }
        }
    }
}
