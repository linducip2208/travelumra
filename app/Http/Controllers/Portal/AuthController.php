<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Pilgrim;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('portal.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|string',
            'passport_or_phone' => 'required|string',
        ]);

        $pilgrim = Pilgrim::where('registration_number', $request->registration_number)
            ->where('is_active', true)
            ->where(function ($query) use ($request) {
                $query->where('passport_number', $request->passport_or_phone)
                    ->orWhere('phone', $request->passport_or_phone);
            })
            ->first();

        if (! $pilgrim) {
            return back()->withErrors([
                'registration_number' => 'Nomor registrasi atau passport/telepon tidak cocok.',
            ])->withInput();
        }

        // OTP 6 digit, berlaku 5 menit, maks 5x coba. Dikirim via WA (log bila tanpa API).
        $otp = (string) random_int(100000, 999999);
        $request->session()->put('portal_otp_pilgrim_id', $pilgrim->id);
        $request->session()->put('portal_otp_hash', hash('sha256', $otp . config('app.key')));
        $request->session()->put('portal_otp_expires', now()->addMinutes(5)->timestamp);
        $request->session()->put('portal_otp_attempts', 0);

        try {
            app(\App\Services\WhatsAppService::class)->send(
                $pilgrim->phone ?? '',
                "Kode OTP Portal Jamaah Anda: {$otp}. Berlaku 5 menit. Jangan bagikan ke siapapun."
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('OTP WA failed: ' . $e->getMessage());
        }
        // Dev/local: tampilkan di log agar bisa testing tanpa WA gateway
        if (app()->environment(['local', 'testing'])) {
            \Illuminate\Support\Facades\Log::info("Portal OTP for pilgrim {$pilgrim->id}: {$otp}");
        }

        return redirect()->route('portal.otp');
    }

    public function showOtp()
    {
        if (! session()->has('portal_otp_pilgrim_id')) {
            return redirect()->route('portal.login');
        }
        return view('portal.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $pilgrimId = $request->session()->get('portal_otp_pilgrim_id');
        $hash = $request->session()->get('portal_otp_hash');
        $expires = $request->session()->get('portal_otp_expires', 0);
        $attempts = (int) $request->session()->get('portal_otp_attempts', 0);

        if (! $pilgrimId || ! $hash) {
            return redirect()->route('portal.login');
        }
        if (now()->timestamp > (int) $expires) {
            $request->session()->forget(['portal_otp_pilgrim_id', 'portal_otp_hash', 'portal_otp_expires', 'portal_otp_attempts']);
            return redirect()->route('portal.login')->withErrors(['registration_number' => 'Kode OTP kedaluwarsa. Silakan login ulang.']);
        }
        if ($attempts >= 5) {
            $request->session()->forget(['portal_otp_pilgrim_id', 'portal_otp_hash', 'portal_otp_expires', 'portal_otp_attempts']);
            return redirect()->route('portal.login')->withErrors(['registration_number' => 'Terlalu banyak percobaan. Silakan login ulang.']);
        }

        $expected = hash('sha256', $request->otp . config('app.key'));
        if (! hash_equals((string) $hash, $expected)) {
            $request->session()->put('portal_otp_attempts', $attempts + 1);
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        $pilgrim = Pilgrim::where('is_active', true)->find($pilgrimId);
        if (! $pilgrim) {
            return redirect()->route('portal.login');
        }

        $request->session()->forget(['portal_otp_pilgrim_id', 'portal_otp_hash', 'portal_otp_expires', 'portal_otp_attempts']);
        $request->session()->regenerate();
        $request->session()->put('portal_pilgrim_id', $pilgrim->id);
        $request->session()->put('portal_pilgrim_name', $pilgrim->full_name);
        $request->session()->put('portal_login_at', now()->toDateTimeString());

        return redirect()->route('portal.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['portal_pilgrim_id', 'portal_pilgrim_name', 'portal_login_at']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('portal.login');
    }
}
