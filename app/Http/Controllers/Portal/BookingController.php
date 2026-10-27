<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $pilgrimId = $request->session()->get('portal_pilgrim_id');

        $bookings = Booking::with('package')
            ->where('pilgrim_id', $pilgrimId)
            ->latest()
            ->paginate(10);

        return view('portal.bookings', [
            'bookings' => $bookings,
        ]);
    }

    public function show(Request $request, Booking $booking)
    {
        $pilgrimId = $request->session()->get('portal_pilgrim_id');

        if ($booking->pilgrim_id !== $pilgrimId) {
            abort(404);
        }

        $booking->load(['package', 'invoices.payments']);

        return view('portal.booking-detail', [
            'booking' => $booking,
        ]);
    }
}
