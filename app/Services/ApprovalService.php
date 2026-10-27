<?php

namespace App\Services;

use App\Models\ApprovalThreshold;
use App\Models\Booking;

class ApprovalService
{
    public function needsApproval(Booking $booking): bool
    {
        $threshold = ApprovalThreshold::where('entity_type', 'booking')
            ->where('is_active', true)
            ->where('min_amount', '<=', $booking->total_price)
            ->where(function ($q) use ($booking) {
                $q->whereNull('max_amount')->orWhere('max_amount', '>=', $booking->total_price);
            })
            ->first();

        return $threshold !== null;
    }

    public function getApproverRole(Booking $booking): ?string
    {
        $threshold = ApprovalThreshold::where('entity_type', 'booking')
            ->where('is_active', true)
            ->where('min_amount', '<=', $booking->total_price)
            ->where(function ($q) use ($booking) {
                $q->whereNull('max_amount')->orWhere('max_amount', '>=', $booking->total_price);
            })
            ->first();

        return $threshold?->approver_role;
    }

    public function autoFlagForApproval(Booking $booking): void
    {
        if ($this->needsApproval($booking)) {
            $booking->update(['status' => 'pending_approval']);
        }
    }

    public function approve(Booking $booking, $approvedBy, ?string $notes = null): void
    {
        $old = $booking->status;
        $booking->update([
            'status' => 'confirmed',
            'confirmed_date' => now(),
            'notes' => trim(($booking->notes ? $booking->notes . "\n" : '') . 'Approved by ' . (is_object($approvedBy) ? ($approvedBy->id ?? '') : $approvedBy) . ($notes ? ": {$notes}" : '')),
        ]);
        $this->audit($approvedBy, 'approve_booking', $booking, $old);
    }

    public function reject(Booking $booking, $rejectedBy, string $reason): void
    {
        $old = $booking->status;
        $booking->update([
            'status' => 'cancelled',
            'cancelled_date' => now(),
            'cancellation_reason' => 'Ditolak: ' . $reason,
        ]);
        $this->audit($rejectedBy, 'reject_booking', $booking, $old);
    }

    public function getPendingApprovals()
    {
        return Booking::where('status', 'pending_approval')
            ->with(['package', 'pilgrim'])
            ->latest()
            ->paginate(20);
    }

    protected function audit($actor, string $action, Booking $booking, string $oldStatus): void
    {
        try {
            \App\Models\AuditLog::create([
                'user_id' => is_object($actor) ? ($actor->id ?? null) : (is_numeric($actor) ? (int) $actor : null),
                'action' => $action,
                'entity_type' => 'booking',
                'entity_id' => $booking->id,
                'old_values' => ['status' => $oldStatus],
                'new_values' => ['status' => $booking->status],
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 500),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Audit log failed: ' . $e->getMessage());
        }
    }
}
