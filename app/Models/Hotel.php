<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'stars',
        'distance_to_haram',
        'distance_to_nabawi',
        'distance_to_airport',
        'address',
        'phone',
        'email',
        'website',
        'amenities',
        'images',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'images' => 'array',
            'amenities' => 'array',
        ];
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(HotelRoom::class);
    }
}
