<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'nullable|string|max:30',
            'category' => 'nullable|string|max:30',
            'departure_airport' => 'nullable|string|max:10',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2024|max:2035',
            'search' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $query = Package::where('is_active', true);

        if (! empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }
        if (! empty($validated['category'])) {
            $query->where('category', $validated['category']);
        }
        if (! empty($validated['departure_airport'])) {
            $query->where('departure_airport', $validated['departure_airport']);
        }
        if (! empty($validated['month'])) {
            $query->whereMonth('departure_date', $validated['month']);
        }
        if (! empty($validated['year'])) {
            $query->whereYear('departure_date', $validated['year']);
        }
        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('airline', 'like', "%{$search}%");
            });
        }

        $packages = $query->orderBy('departure_date')
            ->paginate($validated['per_page'] ?? 10);

        return response()->json([
            'success' => true,
            'data' => $packages,
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $package = Package::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $package,
        ]);
    }

    public function featured(): JsonResponse
    {
        $packages = Package::where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $packages,
        ]);
    }
}
