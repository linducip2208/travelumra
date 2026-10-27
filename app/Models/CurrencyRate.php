<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    protected $fillable = ['currency', 'rate', 'effective_date'];

    protected $casts = [
        'rate' => 'decimal:4', 'effective_date' => 'date',
    ];

    public static function getRate(string $currency = 'USD'): float
    {
        return (float) static::where('currency', strtoupper($currency))
            ->where('effective_date', '<=', today())
            ->latest('effective_date')
            ->value('rate') ?: 0;
    }
}
