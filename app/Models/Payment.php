<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    public static function generatePaymentNumber(): string
    {
        return static::generatePaymentNumberSafe();
    }

    public static function generatePaymentNumberSafe(): string
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $number = \Illuminate\Support\Facades\DB::transaction(function () {
                $year = date('Y');
                $month = date('m');
                $prefix = "PMT-{$year}{$month}-";

                $last = static::where('payment_number', 'like', "{$prefix}%")
                    ->lockForUpdate()
                    ->orderBy('payment_number', 'desc')
                    ->first();

                $next = $last ? ((int) substr($last->payment_number, -4)) + 1 : 1;

                return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
            });

            if (! static::where('payment_number', $number)->exists()) {
                return $number;
            }
            usleep(50000 * ($attempt + 1));
        }

        return 'PMT-' . date('Ym') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
    }

    protected $fillable = [
        'payment_number', 'invoice_id', 'booking_id',
        'amount', 'payment_method', 'bank_name', 'bank_account', 'bank_holder',
        'payment_date', 'proof_file', 'status', 'rejection_reason', 'notes',
        'confirmed_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
