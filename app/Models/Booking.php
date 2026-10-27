<?php

namespace App\Models;

use App\Models\Traits\HasGallery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes, HasGallery;

    public static function generateBookingNumber(): string
    {
        return static::generateBookingNumberSafe();
    }

    /**
     * Concurrency-safe: lockForUpdate + retry on unique violation.
     */
    public static function generateBookingNumberSafe(): string
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $number = \Illuminate\Support\Facades\DB::transaction(function () {
                $year = date('Y');
                $month = date('m');
                $prefix = "BKG-{$year}{$month}-";

                $last = static::where('booking_number', 'like', "{$prefix}%")
                    ->lockForUpdate()
                    ->orderBy('booking_number', 'desc')
                    ->first();

                $next = $last ? ((int) substr($last->booking_number, -4)) + 1 : 1;

                return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
            });

            // Cek duplikat sebelum return (race antar transaksi)
            if (! static::where('booking_number', $number)->exists()) {
                return $number;
            }
            usleep(50000 * ($attempt + 1));
        }

        // Fallback unik: tambah suffix random
        return 'BKG-' . date('Ym') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
    }

    protected $fillable = [
        'booking_number', 'package_id', 'pilgrim_id', 'room_type',
        'agent_id', 'branch_id',
        'package_price', 'additional_cost', 'discount', 'total_price', 'paid_amount', 'remaining_amount',
        'status', 'confirmed_date', 'cancelled_date', 'cancellation_reason',
        'referral_by', 'referral_commission', 'currency', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'package_price' => 'decimal:2',
            'additional_cost' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_price' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'referral_commission' => 'decimal:2',
            'confirmed_date' => 'date',
            'cancelled_date' => 'date',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function pilgrim(): BelongsTo
    {
        return $this->belongsTo(Pilgrim::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Pilgrim::class, 'referral_by');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function visas(): HasMany
    {
        return $this->hasMany(Visa::class);
    }

    public function insurance()
    {
        return $this->hasOne(Insurance::class);
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function loyaltyPoints(): HasMany
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    public function promoCodes()
    {
        return $this->belongsToMany(PromoCode::class, 'booking_promo')->withPivot('discount_amount');
    }
}
