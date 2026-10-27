<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    public static function generateInvoiceNumber(): string
    {
        return static::generateInvoiceNumberSafe();
    }

    public static function generateInvoiceNumberSafe(): string
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $number = \Illuminate\Support\Facades\DB::transaction(function () {
                $year = date('Y');
                $month = date('m');
                $prefix = "INV-{$year}{$month}-";

                $last = static::where('invoice_number', 'like', "{$prefix}%")
                    ->lockForUpdate()
                    ->orderBy('invoice_number', 'desc')
                    ->first();

                $next = $last ? ((int) substr($last->invoice_number, -4)) + 1 : 1;

                return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
            });

            if (! static::where('invoice_number', $number)->exists()) {
                return $number;
            }
            usleep(50000 * ($attempt + 1));
        }

        return 'INV-' . date('Ym') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
    }

    protected $fillable = [
        'invoice_number', 'booking_id', 'pilgrim_id',
        'total_amount', 'paid_amount', 'remaining_amount',
        'status', 'due_date', 'payment_link_url', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function pilgrim(): BelongsTo
    {
        return $this->belongsTo(Pilgrim::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
