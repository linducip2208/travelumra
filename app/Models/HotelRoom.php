<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_number',
        'type',
        'floor',
        'block',
        'is_available',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
        ];
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
