<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Itinerary extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'cluster_id',
        'day_number',
        'date',
        'activity',
        'description',
        'location',
        'time',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class);
    }
}
