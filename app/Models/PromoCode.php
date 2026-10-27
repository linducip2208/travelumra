<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'type', 'value', 'min_booking_amount', 'max_discount', 'usage_limit', 'used_count', 'start_date', 'end_date', 'package_id', 'is_active'];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_booking_amount' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function package() { return $this->belongsTo(Package::class); }
    public function bookings() { return $this->belongsToMany(Booking::class, 'booking_promo')->withPivot('discount_amount'); }

    public function isValid(float $bookingAmount): bool
    {
        if (!$this->is_active) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        if ($this->start_date && now()->lt($this->start_date)) return false;
        if ($this->end_date && now()->gt($this->end_date)) return false;
        if ($this->min_booking_amount && $bookingAmount < $this->min_booking_amount) return false;
        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'percentage') {
            $discount = $amount * ($this->value / 100);
            return $this->max_discount ? min($discount, (float)$this->max_discount) : $discount;
        }
        return min((float)$this->value, $amount);
    }
}
