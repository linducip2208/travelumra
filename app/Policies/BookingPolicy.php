<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\Pilgrim;
use App\Models\User;

class BookingPolicy
{
    public function viewPilgrim(Pilgrim $pilgrim, Booking $booking): bool
    {
        return $booking->pilgrim_id === $pilgrim->id;
    }

    public function viewUser(User $user, Booking $booking): bool
    {
        if (in_array($user->role, ['super_admin', 'admin', 'finance', 'branch_manager'], true)) {
            return true;
        }
        if ($user->branch_id && $booking->branch_id === $user->branch_id) {
            return true;
        }
        return $booking->agent_id === $user->id;
    }
}
