<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'pilgrim_id',
        'booking_id',
        'points',
        'transaction_type',
        'description',
        'value_rupiah',
    ];

    protected function casts(): array
    {
        return [
            'value_rupiah' => 'decimal:2',
        ];
    }

    public function pilgrim(): BelongsTo
    {
        return $this->belongsTo(Pilgrim::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
