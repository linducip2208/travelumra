<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected ?string $apiUrl;
    protected ?string $apiKey;
    protected ?string $sender;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url');
        $this->apiKey = config('services.whatsapp.api_key');
        $this->sender = config('services.whatsapp.sender', '');
    }

    public function send(string $phone, string $message): bool
    {
        if (!$this->apiUrl || !$this->apiKey) {
            Log::info("WhatsApp: {$phone} - {$message}");
            return true;
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
                ->post($this->apiUrl, [
                    'to' => $this->formatPhone($phone),
                    'message' => $message,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("WhatsApp send failed: {$e->getMessage()}");
            return false;
        }
    }

    public function sendBookingConfirmation($phone, string $name, string $bookingNumber, string $packageName): bool
    {
        $message = "*Konfirmasi Booking Umrah*\n\n";
        $message .= "Assalamualaikum {$name},\n\n";
        $message .= "Booking Anda telah dikonfirmasi:\n";
        $message .= "📋 No. Booking: *{$bookingNumber}*\n";
        $message .= "🕋 Paket: *{$packageName}*\n\n";
        $message .= "Tim kami akan menghubungi untuk proses selanjutnya.\n";
        $message .= "Info: 021-xxxx-xxxx";

        return $this->send($phone, $message);
    }

    public function sendPaymentReminder($phone, string $name, string $invoiceNumber, float $amount): bool
    {
        $message = "*Pengingat Pembayaran*\n\n";
        $message .= "Yth. {$name},\n\n";
        $message .= "Pembayaran invoice *{$invoiceNumber}* sebesar *Rp " . number_format($amount, 0, ',', '.') . "* masih menunggu.\n\n";
        $message .= "Segera lakukan pembayaran sebelum jatuh tempo.";

        return $this->send($phone, $message);
    }

    public function sendDepartureReminder($phone, string $name, string $date, string $airport): bool
    {
        $message = "*Pengingat Keberangkatan*\n\n";
        $message .= "Yth. {$name},\n\n";
        $message .= "Keberangkatan Anda:\n";
        $message .= "📅 Tanggal: *{$date}*\n";
        $message .= "✈️ Bandara: *{$airport}*\n\n";
        $message .= "Mohon tiba 3 jam sebelum keberangkatan.\n";
        $message .= "Selamat menunaikan ibadah!";

        return $this->send($phone, $message);
    }

    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }
}
