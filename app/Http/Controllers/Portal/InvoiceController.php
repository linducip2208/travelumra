<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $pilgrimId = $request->session()->get('portal_pilgrim_id');

        $invoices = Invoice::with('booking')
            ->where('pilgrim_id', $pilgrimId)
            ->latest()
            ->paginate(10);

        return view('portal.invoices', [
            'invoices' => $invoices,
        ]);
    }

    public function show(Request $request, Invoice $invoice)
    {
        $pilgrimId = $request->session()->get('portal_pilgrim_id');

        if ($invoice->pilgrim_id !== $pilgrimId) {
            abort(404);
        }
        \Illuminate\Support\Facades\Gate::forUser(
            \App\Models\Pilgrim::find($pilgrimId) ?? new \App\Models\Pilgrim()
        )->authorize('viewPilgrim', $invoice);

        $invoice->load(['booking.package', 'payments']);

        return view('portal.invoice-detail', [
            'invoice' => $invoice,
        ]);
    }

    public function uploadPayment(Request $request, Invoice $invoice)
    {
        $pilgrimId = $request->session()->get('portal_pilgrim_id');

        if ($invoice->pilgrim_id !== $pilgrimId) {
            abort(404);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $invoice->remaining_amount,
            'payment_method' => 'required|string',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'bank_holder' => 'nullable|string|max:255',
            'proof_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($invoice->status === 'paid') {
            return back()->with('error', 'Invoice sudah lunas.');
        }

        $proofPath = $request->file('proof_file')->store('payment-proofs', 'local');

        Payment::create([
            'payment_number' => Payment::generatePaymentNumberSafe(),
            'invoice_id' => $invoice->id,
            'booking_id' => $invoice->booking_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'bank_name' => $request->bank_name,
            'bank_account' => $request->bank_account,
            'bank_holder' => $request->bank_holder,
            'payment_date' => now(),
            'proof_file' => $proofPath,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload. Tim kami akan memverifikasi pembayaran Anda.');
    }
}
