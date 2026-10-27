<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Models\Payment;
use App\Http\Requests\BookingStoreRequest;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function create(string $slug)
    {
        $package = Package::where('slug', $slug)->where('is_active', true)->firstOrFail();

        if ($package->available_count <= 0) {
            return back()->with('error', 'Maaf, kuota paket ini sudah penuh.');
        }

        $providers = app(PaymentGatewayService::class)->getAvailableProviders();

        return view('booking.create', compact('package', 'providers'));
    }

    public function store(BookingStoreRequest $request, string $slug)
    {
        $validated = $request->validated();

        try {
            /** @var array{booking: Booking, invoice: Invoice} $result */
            $result = DB::transaction(function () use ($validated, $slug) {
                $package = Package::where('slug', $slug)->where('is_active', true)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($package->available_count <= 0) {
                    abort(422, 'Maaf, kuota paket ini sudah penuh.');
                }

                // Create or find pilgrim
                $pilgrim = Pilgrim::where('nik', $validated['nik'])->first()
                    ?? Pilgrim::where('phone', $validated['phone'])
                        ->where('full_name', $validated['full_name'])
                        ->first();

                if (! $pilgrim) {
                    $pilgrim = Pilgrim::create([
                        'registration_number' => Pilgrim::generateRegistrationNumberSafe(),
                        'full_name' => $validated['full_name'],
                        'title' => $validated['title'] ?? null,
                        'gender' => $validated['gender'] ?? 'male',
                        'birth_place' => $validated['birth_place'] ?? null,
                        'birth_date' => $validated['birth_date'] ?? null,
                        'marital_status' => $validated['marital_status'] ?? null,
                        'phone' => $validated['phone'],
                        'email' => $validated['email'] ?? null,
                        'address' => $validated['address'] ?? null,
                        'city' => $validated['city'] ?? null,
                        'province' => $validated['province'] ?? null,
                        'postal_code' => $validated['postal_code'] ?? null,
                        'nik' => $validated['nik'],
                        'passport_number' => $validated['passport_number'] ?? null,
                        'source' => 'website',
                        'is_active' => true,
                    ]);
                }

                $roomType = $validated['room_type'];
                $priceField = 'price_' . $roomType;
                $packagePrice = $package->$priceField ?? $package->price_quad;

                $booking = Booking::create([
                    'booking_number' => Booking::generateBookingNumberSafe(),
                    'package_id' => $package->id,
                    'pilgrim_id' => $pilgrim->id,
                    'room_type' => $roomType,
                    'package_price' => $packagePrice,
                    'additional_cost' => 0,
                    'discount' => 0,
                    'total_price' => $packagePrice,
                    'paid_amount' => 0,
                    'remaining_amount' => $packagePrice,
                    'status' => 'pending',
                ]);

                $invoice = Invoice::create([
                    'invoice_number' => Invoice::generateInvoiceNumberSafe(),
                    'booking_id' => $booking->id,
                    'pilgrim_id' => $pilgrim->id,
                    'total_amount' => $packagePrice,
                    'paid_amount' => 0,
                    'remaining_amount' => $packagePrice,
                    'status' => 'unpaid',
                    'due_date' => now()->addDays(7),
                ]);

                // Decrement bersyarat: cegah oversell saat race
                $affected = Package::whereKey($package->id)
                    ->where('available_count', '>', 0)
                    ->decrement('available_count');
                if (! $affected) {
                    throw new \RuntimeException('Kuota paket habis.');
                }
                Package::whereKey($package->id)->increment('booked_count');

                return compact('booking', 'invoice', 'package');
            });
        } catch (\Throwable $e) {
            Log::warning('Booking store failed', ['slug' => $slug, 'error' => $e->getMessage()]);
            return back()->with('error', 'Maaf, kuota paket ini sudah penuh atau booking gagal. Silakan coba lagi.');
        }

        $booking = $result['booking'];
        $invoice = $result['invoice'];

        // Payment gateway redirect
        if ($validated['payment_method'] === 'gateway') {
            $provider = \App\Models\PaymentProvider::findOrFail($validated['payment_provider_id']);
            $gateway = app(PaymentGatewayService::class)->setProvider($provider);

            try {
                $result = $gateway->createTransaction($invoice, [
                    'payment_channel' => $validated['payment_channel'] ?? null,
                ]);

                if (!empty($result['error'])) {
                    return redirect()->route('booking.success', $booking->booking_number)
                        ->with('gateway_error', $result['message'] ?? 'Pembayaran gagal.');
                }

                if (! empty($result['redirect_url'])) {
                    return redirect()->away($result['redirect_url']);
                }
                if (! empty($result['token'])) {
                    return redirect()->route('booking.pay', $invoice->id)
                        ->with('snap_token', $result['token']);
                }

                return redirect()->route('booking.success', $booking->booking_number);
            } catch (\Exception $e) {
                Log::error('Payment gateway error', [
                    'provider_id' => $validated['payment_provider_id'] ?? null,
                ]);
                return redirect()->route('booking.success', $booking->booking_number)
                    ->with('gateway_error', 'Pembayaran gateway gagal: ' . $e->getMessage());
            }
        }

        return redirect()->route('booking.success', $booking->booking_number);
    }

    public function pay(Invoice $invoice)
    {
        $booking = $invoice->booking;
        $snapToken = session('snap_token');
        $provider = app(PaymentGatewayService::class)->getProvider();

        if (! $snapToken || ! $provider) {
        return redirect()->route('booking.success', $booking->booking_number);
    }

        $clientKey = $provider->getDecryptedClientKey();
        $isProduction = ! str_contains($provider->base_url ?? '', 'sandbox');

        return view('booking.pay', compact('booking', 'invoice', 'snapToken', 'clientKey', 'isProduction'));
    }

    public function finish(Request $request, Invoice $invoice)
    {
        // JANGAN pernah mark-paid dari query param. Verifikasi server-side ke gateway.
        $booking = $invoice->booking;
        $gateway = app(PaymentGatewayService::class);

        $ref = $request->query('order_id') ?? $request->query('merchant_ref') ?? $request->query('ref');
        $serverStatus = $gateway->verifyServerStatus($invoice, $ref ? (string) $ref : null);

        if ($serverStatus === 'confirmed' && $invoice->status !== 'paid') {
            $gateway->markInvoicePaid($invoice->fresh());
        }

        return redirect()->route('booking.success', $booking->booking_number);
    }

    public function callback(Request $request, Invoice $invoice)
    {
        $gateway = app(PaymentGatewayService::class);
        $payment = $gateway->handleCallback($request->all(), $request);

        if (! $payment) {
            return response()->json(['status' => 'invalid_signature_or_not_found'], 400);
        }

        return response()->json(['status' => 'ok']);
    }

    public function simulator(Request $request, Invoice $invoice)
    {
        if (! app()->environment(['local', 'testing'])) {
            abort(404);
        }
        $invoice->load(['booking.package', 'booking.pilgrim']);

        return view('booking.simulator', [
            'invoice' => $invoice,
            'ref' => $request->get('ref', ''),
        ]);
    }

    public function simulatorConfirm(Request $request, Invoice $invoice)
    {
        if (! app()->environment(['local', 'testing'])) {
            abort(404);
        }
        $invoice->load('booking');

        // Hanya untuk provider simulator & invoice belum lunas
        if ($invoice->status === 'paid') {
            return redirect()->route('booking.success', $invoice->booking->booking_number);
        }

        DB::transaction(function () use ($invoice) {
            $fresh = Invoice::whereKey($invoice->id)->lockForUpdate()->first();
            if (! $fresh || $fresh->status === 'paid') {
                return;
            }
            $fresh->update([
                'paid_amount' => $fresh->total_amount,
                'remaining_amount' => 0,
                'status' => 'paid',
            ]);

            $fresh->booking->update([
                'paid_amount' => $fresh->booking->total_price,
                'remaining_amount' => 0,
                'status' => 'dp_paid',
            ]);

            $pendingPayment = Payment::where('invoice_id', $fresh->id)
                ->where('status', 'pending')
                ->latest()
                ->lockForUpdate()
                ->first();
            if ($pendingPayment) {
                $pendingPayment->update(['status' => 'confirmed']);
            }
        });

        return redirect()->route('booking.success', $invoice->booking->booking_number)
            ->with('simulator', true);
    }

    public function success(string $bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['package', 'pilgrim', 'invoices'])
            ->firstOrFail();

        $invoice = $booking->invoices->first();

        return view('booking.success', compact('booking', 'invoice'));
    }
}
