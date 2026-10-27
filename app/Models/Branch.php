<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'code', 'type', 'parent_id', 'address', 'city', 'province',
        'phone', 'email', 'bank_name', 'bank_account', 'bank_holder',
        'is_active', 'quota_haji', 'quota_umrah', 'quota_used',
        'referral_commission_pct',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'quota_haji' => 'integer',
            'quota_umrah' => 'integer',
            'quota_used' => 'integer',
            'referral_commission_pct' => 'decimal:2',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Branch::class, 'parent_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function pilgrims(): HasMany
    {
        return $this->hasMany(Pilgrim::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
