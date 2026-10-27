<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    protected ?PaymentProvider $provider = null;

    public function __construct()
    {
        $this->provider = PaymentProvider::active()->default()->first()
            ?? PaymentProvider::active()->first();
    }

    public function setProvider(PaymentProvider $provider): self
    {
        $this->provider = $provider;
        return $this;
    }

    public function getProvider(): ?PaymentProvider
    {
        return $this->provider;
    }

    public function getAvailableProviders(): array
    {
        return PaymentProvider::active()->orderBy('sort_order')->get()->all();
    }

    public function createTransaction(Invoice $invoice, array $customerData = []): array
    {
        if (! $this->provider) {
            throw new \RuntimeException('No active payment provider configured.');
        }

        return match ($this->provider->api_format) {
            'redirect_snap' => $this->createSnapRedirect($invoice, $customerData),
            'redirect_va' => $this->createVaRedirect($invoice, $customerData),
            'redirect_tripay' => $this->createTripayRedirect($invoice, $customerData),
            'simulator' => $this->createSimulatorRedirect($invoice, $customerData),
            default => throw new \RuntimeException("Unsupported API format: {$this->provider->api_format}"),
        };
    }

    protected function createSnapRedirect(Invoice $invoice, array $customerData): array
    {
        $serverKey = $this->provider->getDecryptedServerKey();
        $baseUrl = $this->provider->base_url ?: 'https://app.midtrans.com';

        $booking = $invoice->booking;
        $pilgrim = $invoice->pilgrim;
        $package = $booking->package;

        $orderId = $invoice->invoice_number . '-' . time();
        $grossAmount = (int) $invoice->remaining_amount;

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $pilgrim->full_name,
                'email' => $pilgrim->email ?? 'jemaah@travelumrah.com',
                'phone' => $pilgrim->phone ?? '08123456789',
                'billing_address' => [
                    'address' => $pilgrim->address ?? 'Indonesia',
                    'city' => $pilgrim->city ?? 'Jakarta',
                    'country_code' => 'IDN',
                ],
            ],
            'item_details' => [[
                'id' => (string) $package->id,
                'price' => $grossAmount,
                'quantity' => 1,
                'name' => $package->name . ' - ' . ucfirst($booking->room_type),
            ]],
            'callbacks' => [
                'finish' => route('payment.finish', $invoice->id),
                'notification' => route('payment.callback', $invoice->id),
            ],
        ];

        $response = Http::withBasicAuth($serverKey, '')
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$baseUrl}/snap/v1/transactions", $payload);

        if (! $response->successful()) {
            Log::error('Payment gateway error', [
                'provider' => $this->provider->code,
                'response' => $response->body(),
            ]);
            return ['error' => true, 'message' => 'Gagal membuat transaksi pembayaran.'];
        }

        $data = $response->json();

        // Save payment record (atomic with quota-safe number)
        $payment = DB::transaction(function () use ($invoice, $booking, $grossAmount) {
            return Payment::create([
                'payment_number' => Payment::generatePaymentNumberSafe(),
                'invoice_id' => $invoice->id,
                'booking_id' => $booking->id,
                'amount' => $grossAmount,
                'payment_method' => 'gateway',
                'payment_date' => now()->format('Y-m-d'),
                'status' => 'pending',
                'notes' => "Gateway: {$this->provider->name} | Order ID: {$orderId}",
            ]);
        });

        return [
            'success' => true,
            'redirect_url' => $data['redirect_url'] ?? null,
            'token' => $data['token'] ?? null,
            'order_id' => $orderId,
        ];
    }

    protected function createVaRedirect(Invoice $invoice, array $customerData): array
    {
        return $this->createSnapRedirect($invoice, $customerData);
    }

    protected function createTripayRedirect(Invoice $invoice, array $customerData): array
    {
        $apiKey = $this->provider->getDecryptedApiKey();
        $privateKey = $this->provider->getDecryptedPrivateKey();
        $merchantCode = $this->provider->getDecryptedMerchantCode();
        $baseUrl = $this->provider->base_url ?: 'https://tripay.co.id/api-sandbox';

        $booking = $invoice->booking;
        $pilgrim = $invoice->pilgrim;
        $package = $booking->package;

        $merchantRef = $invoice->invoice_number . '-' . time();
        $amount = (int) $invoice->remaining_amount;

        $payload = [
            'merchant_ref' => $merchantRef,
            'amount' => $amount,
            'customer_name' => $pilgrim->full_name,
            'customer_email' => $pilgrim->email ?? 'jemaah@travelumrah.com',
            'customer_phone' => $pilgrim->phone ?? '08123456789',
            'order_items' => [[
                'name' => $package->name . ' - ' . ucfirst($booking->room_type) . ' (' . $booking->booking_number . ')',
                'price' => $amount,
                'quantity' => 1,
            ]],
            'return_url' => route('payment.finish', $invoice->id),
        ];

        // Tripay requires a method + signature
        $payload['method'] = !empty($customerData['payment_channel'])
            ? $customerData['payment_channel']
            : 'BRIVA';

        $signature = hash_hmac('sha256', $merchantCode . $merchantRef . $amount, $privateKey);
        $payload['signature'] = $signature;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post(rtrim($baseUrl, '/') . '/transaction/create', $payload);

        if (!$response->successful()) {
            Log::error('Tripay payment error', [
                'provider' => $this->provider->code,
                'response' => $response->body(),
            ]);
            return ['error' => true, 'message' => 'Gagal membuat transaksi Tripay: ' . ($response->json('message') ?? 'Unknown error')];
        }

        $data = $response->json();

        if (!($data['success'] ?? false)) {
            return ['error' => true, 'message' => $data['message'] ?? 'Tripay transaction failed'];
        }

        $payment = DB::transaction(function () use ($invoice, $booking, $amount, $merchantRef) {
            return Payment::create([
                'payment_number' => Payment::generatePaymentNumberSafe(),
                'invoice_id' => $invoice->id,
                'booking_id' => $booking->id,
                'amount' => $amount,
                'payment_method' => 'gateway',
                'payment_date' => now()->format('Y-m-d'),
                'status' => 'pending',
                'notes' => "Gateway: {$this->provider->name} | Ref: {$merchantRef}",
            ]);
        });

        return [
            'success' => true,
            'redirect_url' => $data['data']['checkout_url'] ?? null,
            'reference' => $data['data']['reference'] ?? $merchantRef,
            'order_id' => $merchantRef,
        ];
    }

    protected function createSimulatorRedirect(Invoice $invoice, array $customerData): array
    {
        // Simulator hanya boleh di environment local/testing.
        if (! app()->environment(['local', 'testing'])) {
            Log::warning('Simulator payment blocked in non-local env', ['invoice_id' => $invoice->id]);
            return ['error' => true, 'message' => 'Metode simulator tidak tersedia di production.'];
        }

        $merchantRef = $invoice->invoice_number . '-' . time();
        $amount = (int) $invoice->remaining_amount;

        DB::transaction(function () use ($invoice, $merchantRef, $amount) {
            Payment::create([
                'payment_number' => Payment::generatePaymentNumberSafe(),
                'invoice_id' => $invoice->id,
                'booking_id' => $invoice->booking->id,
                'amount' => $amount,
                'payment_method' => 'simulator',
                'payment_date' => now()->format('Y-m-d'),
                'status' => 'pending',
                'notes' => "Simulator | Ref: {$merchantRef}",
            ]);
        });

        return [
            'success' => true,
            'redirect_url' => route('payment.simulator', ['invoice' => $invoice->id, 'ref' => $merchantRef]),
            'token' => null,
            'order_id' => $merchantRef,
        ];
    }

    /**
     * Verifikasi callback Midtrans: signature_key = SHA512(order_id+status_code+gross_amount+serverKey).
     */
    public function verifyMidtransSignature(array $data): bool
    {
        $serverKey = $this->provider?->getDecryptedServerKey();
        if (! $serverKey) {
            return false;
        }
        $orderId = $data['order_id'] ?? '';
        $statusCode = $data['status_code'] ?? '';
        $grossAmount = $data['gross_amount'] ?? '';
        $signature = $data['signature_key'] ?? '';
        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signature === '') {
            return false;
        }
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expected, (string) $signature);
    }

    /**
     * Verifikasi callback Tripay: X-Callback-Signature = HMAC SHA256(raw JSON, privateKey).
     */
    public function verifyTripaySignature(Request $request): bool
    {
        $privateKey = $this->provider?->getDecryptedPrivateKey();
        if (! $privateKey) {
            return false;
        }
        $signature = $request->header('X-Callback-Signature') ?? $request->header('X-Callback-Event') ?? '';
        // Tripay mengirim signature via header X-Callback-Signature
        $headerSig = $request->header('X-Callback-Signature', '');
        if ($headerSig === '') {
            return false;
        }
        $expected = hash_hmac('sha256', $request->getContent(), $privateKey);

        return hash_equals($expected, (string) $headerSig);
    }

    /**
     * Fetch status transaksi langsung dari gateway (server-side, anti-spoof).
     * Return: 'confirmed' | 'failed' | 'pending' | null (unknown/error).
     */
    public function verifyServerStatus(Invoice $invoice, ?string $orderRef = null): ?string
    {
        if (! $this->provider) {
            return null;
        }
        try {
            if ($this->provider->api_format === 'redirect_tripay') {
                $apiKey = $this->provider->getDecryptedApiKey();
                $ref = $orderRef ?? $invoice->payments()->latest()->first()?->notes;
                // Ambil merchant_ref dari notes "Ref: XXX" bila tidak diberikan
                if (! $orderRef && is_string($ref) && preg_match('/Ref:\s*(\S+)/', $ref, $m)) {
                    $orderRef = $m[1];
                }
                if (! $orderRef) {
                    return null;
                }
                $baseUrl = rtrim($this->provider->base_url ?: 'https://tripay.co.id/api-sandbox', '/');
                $resp = Http::withHeaders(['Authorization' => 'Bearer ' . $apiKey])
                    ->get($baseUrl . '/transaction/detail', ['merchant_ref' => $orderRef]);
                if (! $resp->successful()) {
                    return null;
                }
                $status = strtoupper($resp->json('data.status') ?? '');
                return match ($status) {
                    'PAID' => 'confirmed',
                    'EXPIRED', 'FAILED', 'REFUND' => 'failed',
                    'UNPAID' => 'pending',
                    default => null,
                };
            }

            // Midtrans: GET /v2/{order_id}/status
            $serverKey = $this->provider->getDecryptedServerKey();
            $baseUrl = $this->provider->base_url ?: 'https://api.midtrans.com';
            // Normalisasi base snap -> api
            $baseUrl = str_replace('app.midtrans.com/snap', 'api.midtrans.com', $baseUrl);
            $baseUrl = str_replace('app.sandbox.midtrans.com/snap', 'api.sandbox.midtrans.com', $baseUrl);
            if (! $orderRef) {
                $last = $invoice->payments()->latest()->first();
                if ($last && preg_match('/Order ID:\s*(\S+)/', (string) $last->notes, $m)) {
                    $orderRef = $m[1];
                }
            }
            if (! $orderRef) {
                return null;
            }
            $resp = Http::withBasicAuth($serverKey, '')->get(rtrim($baseUrl, '/') . "/v2/{$orderRef}/status");
            if (! $resp->successful()) {
                return null;
            }
            $tx = $resp->json('transaction_status');
            $fraud = $resp->json('fraud_status', 'accept');
            return match (true) {
                $tx === 'capture' && $fraud === 'accept' => 'confirmed',
                $tx === 'settlement' => 'confirmed',
                in_array($tx, ['deny', 'cancel', 'expire', 'failure']) => 'failed',
                $tx === 'pending' => 'pending',
                default => null,
            };
        } catch (\Throwable $e) {
            Log::warning('verifyServerStatus failed: ' . $e->getMessage(), ['invoice_id' => $invoice->id]);
            return null;
        }
    }

    public function markInvoicePaid(Invoice $invoice): void
    {
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
            $booking = $fresh->booking;
            if ($booking && $booking->status !== 'dp_paid' && $booking->status !== 'confirmed') {
                $booking->update([
                    'paid_amount' => $booking->total_price,
                    'remaining_amount' => 0,
                    'status' => 'dp_paid',
                ]);
            }
        });
    }

    public function handleCallback(array $data, ?Request $request = null): ?Payment
    {
        if (! $this->provider) {
            return null;
        }

        // Tripay callback format (wajib signature header)
        if ($this->provider->api_format === 'redirect_tripay') {
            if ($request && ! $this->verifyTripaySignature($request)) {
                Log::warning('Tripay callback invalid signature', ['ip' => $request?->ip()]);
                return null;
            }
            return $this->handleTripayCallback($data);
        }

        // Midtrans callback format (wajib signature_key)
        if ($request !== null || isset($data['signature_key'])) {
            if (! $this->verifyMidtransSignature($data)) {
                Log::warning('Midtrans callback invalid signature', ['order_id' => $data['order_id'] ?? null]);
                return null;
            }
        } else {
            // Tanpa signature sama sekali -> tolak
            Log::warning('Midtrans callback missing signature');
            return null;
        }

        // Midtrans callback format
        $orderId = $data['order_id'] ?? null;
        $transactionStatus = $data['transaction_status'] ?? null;
        $fraudStatus = $data['fraud_status'] ?? 'accept';

        // Find invoice by order_id prefix (invoice_number part)
        $orderIdParts = explode('-', $orderId);
        array_pop($orderIdParts); // remove timestamp
        $invoiceNumber = implode('-', $orderIdParts);

        $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();
        if (! $invoice) return null;

        // Find pending payment
        $payment = Payment::where('invoice_id', $invoice->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (! $payment) return null;

        // Determine payment status
        $status = match (true) {
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'confirmed',
            $transactionStatus === 'settlement' => 'confirmed',
            $transactionStatus === 'deny' => 'failed',
            $transactionStatus === 'cancel' => 'failed',
            $transactionStatus === 'expire' => 'failed',
            $transactionStatus === 'failure' => 'failed',
            default => null,
        };

        if ($status && $payment->status !== $status) {
            DB::transaction(function () use ($payment, $status, $invoice) {
                $freshPayment = Payment::whereKey($payment->id)->lockForUpdate()->first();
                if (! $freshPayment || $freshPayment->status !== 'pending') {
                    return;
                }
                $freshPayment->update(['status' => $status]);

                if ($status === 'confirmed') {
                    $freshInvoice = Invoice::whereKey($invoice->id)->lockForUpdate()->first();
                    if ($freshInvoice && $freshInvoice->status !== 'paid') {
                        $freshInvoice->update([
                            'paid_amount' => $freshInvoice->total_amount,
                            'remaining_amount' => 0,
                            'status' => 'paid',
                        ]);
                        $booking = $freshInvoice->booking;
                        if ($booking) {
                            $booking->update([
                                'paid_amount' => $booking->total_price,
                                'remaining_amount' => 0,
                                'status' => 'dp_paid',
                            ]);
                        }
                    }
                }
            });
        }

        return $payment;
    }

    protected function handleTripayCallback(array $data): ?Payment
    {
        $merchantRef = $data['merchant_ref'] ?? '';
        $reference = $data['reference'] ?? '';
        $status = $data['status'] ?? '';

        $orderIdParts = explode('-', $merchantRef);
        array_pop($orderIdParts);
        $invoiceNumber = implode('-', $orderIdParts);

        $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();
        if (!$invoice) return null;

        $payment = Payment::where('invoice_id', $invoice->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$payment) return null;

        $statusMap = [
            'PAID' => 'confirmed',
            'EXPIRED' => 'failed',
            'FAILED' => 'failed',
            'REFUND' => 'failed',
        ];

        $newStatus = $statusMap[$status] ?? null;

        if ($newStatus && $payment->status !== $newStatus) {
            DB::transaction(function () use ($payment, $newStatus, $invoice) {
                $freshPayment = Payment::whereKey($payment->id)->lockForUpdate()->first();
                if (! $freshPayment || $freshPayment->status !== 'pending') {
                    return;
                }
                $freshPayment->update(['status' => $newStatus]);

                if ($newStatus === 'confirmed') {
                    $freshInvoice = Invoice::whereKey($invoice->id)->lockForUpdate()->first();
                    if ($freshInvoice && $freshInvoice->status !== 'paid') {
                        $freshInvoice->update([
                            'paid_amount' => $freshInvoice->total_amount,
                            'remaining_amount' => 0,
                            'status' => 'paid',
                        ]);
                        $booking = $freshInvoice->booking;
                        if ($booking) {
                            $booking->update([
                                'paid_amount' => $booking->total_price,
                                'remaining_amount' => 0,
                                'status' => 'dp_paid',
                            ]);
                        }
                    }
                }
            });
        }

        return $payment;
    }
}
