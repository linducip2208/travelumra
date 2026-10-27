<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeadStoreRequest;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;

class LeadController extends Controller
{
    public function store(LeadStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $lead = Lead::create(array_merge($validated, [
            'status' => 'new',
            'follow_up_date' => now()->addDays(1)->format('Y-m-d'),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih! Tim kami akan menghubungi Anda segera.',
            'data' => $lead,
        ], 201);
    }
}
