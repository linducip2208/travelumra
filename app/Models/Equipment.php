<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'name',
        'type',
        'stock',
        'allocated',
        'notes',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function pilgrims(): BelongsToMany
    {
        return $this->belongsToMany(Pilgrim::class, 'equipment_pilgrim')
            ->withPivot(['booking_id', 'quantity', 'status', 'pickup_date'])
            ->withTimestamps();
    }
}
