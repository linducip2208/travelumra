<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Pilgrim;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $pilgrimId = $request->session()->get('portal_pilgrim_id');
        $pilgrim = Pilgrim::findOrFail($pilgrimId);

        $activeBookingsCount = Booking::where('pilgrim_id', $pilgrimId)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->count();

        $invoicesCount = Invoice::where('pilgrim_id', $pilgrimId)->count();

        $totalPaid = Payment::whereHas('invoice', function ($q) use ($pilgrimId) {
            $q->where('pilgrim_id', $pilgrimId);
        })->where('status', 'confirmed')->sum('amount');

        $bookings = Booking::with('package')
            ->where('pilgrim_id', $pilgrimId)
            ->latest()
            ->limit(5)
            ->get();

        $invoices = Invoice::with('booking')
            ->where('pilgrim_id', $pilgrimId)
            ->latest()
            ->limit(5)
            ->get();

        return view('portal.dashboard', [
            'pilgrim' => $pilgrim,
            'activeBookingsCount' => $activeBookingsCount,
            'invoicesCount' => $invoicesCount,
            'totalPaid' => $totalPaid,
            'bookings' => $bookings,
            'invoices' => $invoices,
        ]);
    }
}
