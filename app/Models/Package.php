<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'slug',
        'type',
        'category',
        'airline',
        'departure_airport',
        'departure_date',
        'return_date',
        'duration_days',
        'makkah_hotel_name',
        'makkah_hotel_stars',
        'makkah_hotel_distance_meters',
        'makkah_hotel_room_type',
        'madinah_hotel_name',
        'madinah_hotel_stars',
        'madinah_hotel_distance_meters',
        'price_single',
        'price_double',
        'price_triple',
        'price_quad',
        'quota',
        'booked_count',
        'available_count',
        'itinerary',
        'includes',
        'excludes',
        'terms',
        'banner_image',
        'gallery_images',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'return_date' => 'date',
            'price_single' => 'decimal:2',
            'price_double' => 'decimal:2',
            'price_triple' => 'decimal:2',
            'price_quad' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'gallery_images' => 'array',
            'itinerary' => 'array',
            'includes' => 'array',
            'excludes' => 'array',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function clusters(): HasMany
    {
        return $this->hasMany(Cluster::class);
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    public function promoCodes(): HasMany
    {
        return $this->hasMany(PromoCode::class);
    }

    public function itineraries(): HasMany
    {
        return $this->hasMany(Itinerary::class);
    }
}
