<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPaymentRequest;
use App\Models\Booking;
use App\Models\PilgrimDocument;
use App\Models\Itinerary;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PilgrimController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        $pilgrim = $request->user();
        if (! $pilgrim instanceof \App\Models\Pilgrim) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $pilgrim->load('branch', 'agent'),
        ]);
    }

    public function bookings(Request $request): JsonResponse
    {
        $pilgrim = $request->user();
        if (! $pilgrim instanceof \App\Models\Pilgrim) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $bookings = $pilgrim->bookings()
            ->with(['package', 'invoices', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }

    public function invoices(Request $request): JsonResponse
    {
        $pilgrim = $request->user();
        if (! $pilgrim instanceof \App\Models\Pilgrim) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $invoices = $pilgrim->invoices()
            ->with(['booking.package', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $invoices,
        ]);
    }

    public function itinerary(Request $request, Booking $booking): JsonResponse
    {
        $pilgrim = $request->user();
        if (! $pilgrim instanceof \App\Models\Pilgrim) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }
        \Illuminate\Support\Facades\Gate::forUser($pilgrim)->authorize('viewPilgrim', $booking);

        $itineraries = Itinerary::where('package_id', $booking->package_id)
            ->orderBy('day_number')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'booking' => $booking->load('package'),
                'itinerary' => $itineraries,
            ],
        ]);
    }

    public function uploadPayment(UploadPaymentRequest $request): JsonResponse
    {
        $pilgrim = $request->user();
        if (! $pilgrim instanceof \App\Models\Pilgrim) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $validated = $request->validated();

        // Ownership check: invoice & booking harus milik pilgrim + konsisten
        $invoice = \App\Models\Invoice::findOrFail($validated['invoice_id']);
        if ($invoice->pilgrim_id !== $pilgrim->id || $invoice->booking_id !== (int) $validated['booking_id']) {
            return response()->json(['success' => false, 'message' => 'Invoice tidak ditemukan.'], 404);
        }
        $booking = \App\Models\Booking::findOrFail($validated['booking_id']);
        if ($booking->pilgrim_id !== $pilgrim->id) {
            return response()->json(['success' => false, 'message' => 'Booking tidak ditemukan.'], 404);
        }
        if ($invoice->status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Invoice sudah lunas.'], 422);
        }
        if ((float) $validated['amount'] > (float) $invoice->remaining_amount) {
            return response()->json(['success' => false, 'message' => 'Nominal melebihi sisa tagihan.'], 422);
        }

        $paymentNumber = Payment::generatePaymentNumberSafe();

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('payments', 'local');
        }

        $payment = Payment::create([
            'payment_number' => $paymentNumber,
            'invoice_id' => $validated['invoice_id'],
            'booking_id' => $validated['booking_id'],
            'amount' => $validated['amount'],
            'payment_method' => 'transfer',
            'bank_name' => $validated['bank_name'],
            'bank_account' => $validated['bank_account'],
            'bank_holder' => $validated['bank_holder'],
            'payment_date' => $validated['payment_date'],
            'proof_file' => $proofPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil diupload. Menunggu verifikasi.',
            'data' => $payment,
        ], 201);
    }

    public function documents(Request $request): JsonResponse
    {
        $pilgrim = $request->user();
        if (! $pilgrim instanceof \App\Models\Pilgrim) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $documents = $pilgrim->documents()->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $documents,
        ]);
    }

    public function uploadDocument(Request $request): JsonResponse
    {
        $pilgrim = $request->user();
        if (! $pilgrim instanceof \App\Models\Pilgrim) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $validated = $request->validate([
            'type' => 'required|string|in:ktp,kk,paspor,foto,vaksin,medical,lainnya',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $filePath = $request->file('file')->store('documents/' . $pilgrim->id, 'local');

        $document = PilgrimDocument::create([
            'pilgrim_id' => $pilgrim->id,
            'type' => $validated['type'],
            'file_path' => $filePath,
            'status' => 'pending',
            'created_by' => $pilgrim->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil diupload.',
            'data' => $document,
        ], 201);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $pilgrim = $request->user();
        if (! $pilgrim instanceof \App\Models\Pilgrim) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:20',
            'full_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'emergency_name' => 'nullable|string|max:255',
            'emergency_relation' => 'nullable|string|max:50',
            'emergency_phone' => 'nullable|string|max:20',
            'allergies' => 'nullable|string|max:500',
            'medications' => 'nullable|string|max:500',
            'medical_notes' => 'nullable|string|max:500',
        ]);

        $pilgrim->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data' => $pilgrim->fresh(),
        ]);
    }
}
