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
