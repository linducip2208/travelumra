<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cluster;
use App\Models\Pilgrim;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MuthawifController extends Controller
{
    public function cluster(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user instanceof \App\Models\User || ! $user->is_muthawif) {
            return response()->json([
                'success' => false,
                'message' => 'Akses hanya untuk muthawif.',
            ], 403);
        }

        $clusters = Cluster::where('muthawif_id', $user->id)
            ->with(['package', 'pilgrims'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $clusters,
        ]);
    }

    public function pilgrims(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user instanceof \App\Models\User || ! $user->is_muthawif) {
            return response()->json([
                'success' => false,
                'message' => 'Akses hanya untuk muthawif.',
            ], 403);
        }

        $clusterIds = Cluster::where('muthawif_id', $user->id)->pluck('id');

        $bookingIds = \Illuminate\Support\Facades\DB::table('cluster_pilgrim')
            ->whereIn('cluster_id', $clusterIds)
            ->pluck('booking_id');
        $pilgrimIds = \App\Models\Booking::whereIn('id', $bookingIds)->pluck('pilgrim_id')->unique();

        $pilgrims = Pilgrim::whereIn('id', $pilgrimIds)->with(['bookings.package', 'documents'])->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $pilgrims,
        ]);
    }

    public function attendance(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user instanceof \App\Models\User || ! $user->is_muthawif) {
            return response()->json(['success' => false, 'message' => 'Akses hanya untuk muthawif.'], 403);
        }

        $validated = $request->validate([
            'cluster_id' => 'required|exists:clusters,id',
            'pilgrim_id' => 'required|exists:pilgrims,id',
            'date' => 'required|date',
            'status' => 'required|in:hadir,sakit,izin,alpha',
            'notes' => 'nullable|string|max:255',
        ]);

        $cluster = Cluster::where('id', $validated['cluster_id'])
            ->where('muthawif_id', $user->id)
            ->first();
        if (! $cluster) {
            return response()->json(['success' => false, 'message' => 'Cluster tidak ditemukan.'], 404);
        }

        // Pastikan pilgrim bagian dari cluster (via pivot cluster_pilgrim -> bookings)
        $bookingId = \Illuminate\Support\Facades\DB::table('cluster_pilgrim')
            ->join('bookings', 'bookings.id', '=', 'cluster_pilgrim.booking_id')
            ->where('cluster_pilgrim.cluster_id', $cluster->id)
            ->where('bookings.pilgrim_id', $validated['pilgrim_id'])
            ->value('cluster_pilgrim.booking_id');
        $booking = $bookingId ? \App\Models\Booking::find($bookingId) : null;
        if (! $booking) {
            // Fallback: booking paket sama dengan cluster
            $booking = \App\Models\Booking::where('pilgrim_id', $validated['pilgrim_id'])
                ->where('package_id', $cluster->package_id)
                ->first();
        }
        if (! $booking) {
            return response()->json(['success' => false, 'message' => 'Jemaah bukan bagian cluster ini.'], 422);
        }

        $checkIn = \App\Models\CheckIn::create([
            'booking_id' => $booking->id,
            'cluster_id' => $cluster->id,
            'checked_in_at' => $validated['date'],
            'checked_in_by' => $user->id,
            'notes' => "Status: {$validated['status']}" . ($validated['notes'] ? " | {$validated['notes']}" : ''),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat.',
            'data' => $checkIn,
        ], 201);
    }

    public function dailyReport(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user instanceof \App\Models\User || ! $user->is_muthawif) {
            return response()->json(['success' => false, 'message' => 'Akses hanya untuk muthawif.'], 403);
        }

        $validated = $request->validate([
            'cluster_id' => 'required|exists:clusters,id',
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'activity_type' => 'required|in:ibadah,perjalanan,kesehatan,insidental',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $cluster = Cluster::where('id', $validated['cluster_id'])
            ->where('muthawif_id', $user->id)
            ->first();
        if (! $cluster) {
            return response()->json(['success' => false, 'message' => 'Cluster tidak ditemukan.'], 404);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('reports', 'local');
        }

        // Persist sebagai broadcast internal cluster (audit trail)
        $report = \App\Models\Broadcast::create([
            'cluster_id' => $cluster->id,
            'sender_id' => $user->id,
            'channel' => 'internal',
            'title' => $validated['title'],
            'message' => "[DailyReport {$validated['date']} | {$validated['activity_type']}] {$validated['description']}" . ($photoPath ? " | Foto: {$photoPath}" : ''),
            'status' => 'sent',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan harian berhasil dicatat.',
            'data' => $report,
        ], 201);
    }

    public function emergency(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user instanceof \App\Models\User || ! $user->is_muthawif) {
            return response()->json(['success' => false, 'message' => 'Akses hanya untuk muthawif.'], 403);
        }

        $validated = $request->validate([
            'cluster_id' => 'required|exists:clusters,id',
            'pilgrim_id' => 'required|exists:pilgrims,id',
            'type' => 'required|string|in:medical,missing,accident,other',
            'description' => 'required|string|max:2000',
            'location' => 'nullable|string|max:255',
            'priority' => 'required|in:low,medium,high,critical',
        ]);

        $cluster = Cluster::where('id', $validated['cluster_id'])
            ->where('muthawif_id', $user->id)
            ->first();
        if (! $cluster) {
            return response()->json(['success' => false, 'message' => 'Cluster tidak ditemukan.'], 404);
        }

        $report = \App\Models\Broadcast::create([
            'cluster_id' => $cluster->id,
            'sender_id' => $user->id,
            'channel' => 'internal',
            'title' => "[EMERGENCY-{$validated['priority']}] {$validated['type']}",
            'message' => "Pilgrim #{$validated['pilgrim_id']} | Lokasi: " . ($validated['location'] ?? '-') . " | {$validated['description']}",
            'status' => 'sent',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan darurat telah dikirim. Tim akan segera merespon.',
            'data' => $report,
        ], 201);
    }
}
