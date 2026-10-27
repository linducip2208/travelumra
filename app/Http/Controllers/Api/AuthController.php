<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pilgrim;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'registration_number' => 'required|string',
            'passport_or_phone' => 'required|string',
        ]);

        $pilgrim = Pilgrim::where('registration_number', $validated['registration_number'])
            ->where('is_active', true)
            ->where(function ($q) use ($validated) {
                $q->where('passport_number', $validated['passport_or_phone'])
                    ->orWhere('phone', $validated['passport_or_phone']);
            })
            ->first();

        if (! $pilgrim) {
            return response()->json(['success' => false, 'message' => 'Kredensial tidak valid.'], 401);
        }

        $pilgrim->tokens()->where('name', 'pilgrim-app')->delete();
        $token = $pilgrim->createToken('pilgrim-app', ['pilgrim'])->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => ['token' => $token, 'pilgrim' => $pilgrim->fresh()],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json(['success' => true, 'message' => 'Logged out.']);
    }
}
