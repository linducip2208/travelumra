<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'model',
        'capacity',
        'driver_name',
        'driver_phone',
        'company',
        'last_service_date',
        'next_service_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'last_service_date' => 'date',
            'next_service_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(BusAssignment::class);
    }
}
