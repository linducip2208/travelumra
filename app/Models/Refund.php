<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refund extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_id', 'refund_number', 'total_paid', 'refund_amount',
        'cancellation_fee', 'cancellation_fee_pct', 'reason', 'status',
        'requested_by', 'approved_by', 'approved_at', 'completed_at', 'notes',
    ];

    protected $casts = [
        'total_paid' => 'decimal:2', 'refund_amount' => 'decimal:2',
        'cancellation_fee' => 'decimal:2', 'approved_at' => 'datetime', 'completed_at' => 'datetime',
    ];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }

    public static function generateRefundNumber(): string
    {
        $prefix = 'REF-' . date('Ym');
        $last = static::where('refund_number', 'like', $prefix . '%')->latest('id')->first();
        $num = $last ? intval(substr($last->refund_number, -5)) + 1 : 1;
        return $prefix . '-' . str_pad($num, 5, '0', STR_PAD_LEFT);
    }
}
