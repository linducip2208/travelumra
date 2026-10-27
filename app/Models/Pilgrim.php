<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Pilgrim extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;

    public static function generateRegistrationNumber(): string
    {
        return static::generateRegistrationNumberSafe();
    }

    public static function generateRegistrationNumberSafe(): string
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $number = \Illuminate\Support\Facades\DB::transaction(function () {
                $year = date('Y');
                $month = date('m');
                $prefix = "JMA-{$year}{$month}-";

                $last = static::where('registration_number', 'like', "{$prefix}%")
                    ->lockForUpdate()
                    ->orderBy('registration_number', 'desc')
                    ->first();

                $next = $last ? ((int) substr($last->registration_number, -4)) + 1 : 1;

                return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
            });

            if (! static::where('registration_number', $number)->exists()) {
                return $number;
            }
            usleep(50000 * ($attempt + 1));
        }

        return 'JMA-' . date('Ym') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
    }

    protected $fillable = [
        'registration_number', 'title', 'full_name', 'father_name',
        'nik', 'kk_number', 'passport_number', 'passport_issued_date',
        'passport_expiry_date', 'passport_issuer', 'birth_place', 'birth_date',
        'gender', 'marital_status', 'blood_type',
        'phone', 'email', 'address', 'city', 'province', 'postal_code',
        'emergency_name', 'emergency_relation', 'emergency_phone',
        'mahram_id', 'has_meningitis_vaccine', 'meningitis_vaccine_date',
        'has_covid_vaccine', 'medical_notes', 'allergies', 'medications',
        'source', 'branch_id', 'agent_id', 'referral_pilgrim_id', 'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'passport_issued_date' => 'date',
            'passport_expiry_date' => 'date',
            'birth_date' => 'date',
            'meningitis_vaccine_date' => 'date',
            'has_meningitis_vaccine' => 'boolean',
            'has_covid_vaccine' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function mahram(): BelongsTo
    {
        return $this->belongsTo(Pilgrim::class, 'mahram_id');
    }

    public function referralPilgrim(): BelongsTo
    {
        return $this->belongsTo(Pilgrim::class, 'referral_pilgrim_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Pilgrim::class, 'referral_pilgrim_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PilgrimDocument::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function visas(): HasMany
    {
        return $this->hasMany(Visa::class);
    }

    public function loyaltyPoints(): HasMany
    {
        return $this->hasMany(LoyaltyPoint::class);
    }
}
