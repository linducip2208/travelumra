<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PilgrimDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SecureFileController extends Controller
{
    public function portalPayment(Request $request, Payment $payment)
    {
        $pilgrimId = $request->session()->get('portal_pilgrim_id');
        if (! $pilgrimId || $payment->invoice?->pilgrim_id !== $pilgrimId) {
            abort(404);
        }
        return $this->serve('local', $payment->proof_file);
    }

    public function apiDocument(Request $request, PilgrimDocument $document)
    {
        $user = $request->user();
        if (! $user instanceof \App\Models\Pilgrim || $document->pilgrim_id !== $user->id) {
            abort(404);
        }
        return $this->serve('local', $document->file_path);
    }

    public function apiPayment(Request $request, Payment $payment)
    {
        $user = $request->user();
        if (! $user instanceof \App\Models\Pilgrim || $payment->invoice?->pilgrim_id !== $user->id) {
            abort(404);
        }
        return $this->serve('local', $payment->proof_file);
    }

    protected function serve(string $disk, ?string $path)
    {
        if (! $path || ! Storage::disk($disk)->exists($path)) {
            abort(404);
        }
        // Cegah path traversal
        if (str_contains($path, '..')) {
            abort(404);
        }
        return Storage::disk($disk)->download($path);
    }
}
