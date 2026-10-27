<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visa extends Model
{
    use HasFactory;

    protected $fillable = [
        'pilgrim_id',
        'booking_id',
        'visa_number',
        'type',
        'status',
        'submission_date',
        'approval_date',
        'issue_date',
        'expiry_date',
        'embassy',
        'mofa_number',
        'rejection_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'submission_date' => 'date',
            'approval_date' => 'date',
            'issue_date' => 'date',
            'expiry_date' => 'date',
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

    public function documents(): HasMany
    {
        return $this->hasMany(VisaDocument::class);
    }
}
