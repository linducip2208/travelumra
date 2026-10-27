<?php

namespace App\Models;

use App\Models\Traits\HasGallery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cluster extends Model
{
    use HasFactory, HasGallery;

    protected $fillable = [
        'code',
        'name',
        'package_id',
        'muthawif_id',
        'capacity',
        'booked_count',
        'status',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function muthawif(): BelongsTo
    {
        return $this->belongsTo(User::class, 'muthawif_id');
    }

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'cluster_pilgrim')
            ->withPivot(['seat_number', 'room_number', 'catering_group'])
            ->withTimestamps();
    }

    public function pilgrims()
    {
        return Pilgrim::whereHas('bookings', function ($query) {
            $query->whereIn('id', $this->bookings()->pluck('bookings.id'));
        });
    }

    public function busAssignments(): HasMany
    {
        return $this->hasMany(BusAssignment::class);
    }

    public function itineraries(): HasMany
    {
        return $this->hasMany(Itinerary::class);
    }

    public function itineraryDays(): HasMany
    {
        return $this->hasMany(ItineraryDay::class);
    }

    public function flights(): HasMany
    {
        return $this->hasMany(Flight::class);
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    public function broadcasts(): HasMany
    {
        return $this->hasMany(Broadcast::class);
    }
}
