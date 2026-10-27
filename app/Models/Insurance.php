<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'policy_number', 'provider', 'coverage_type',
        'premium', 'coverage_amount', 'start_date', 'end_date', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'premium' => 'decimal:2',
            'coverage_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function booking() { return $this->belongsTo(Booking::class); }
    public function claims() { return $this->hasMany(InsuranceClaim::class); }
}
