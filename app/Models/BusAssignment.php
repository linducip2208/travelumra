<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'cluster_id',
        'route',
        'start_date',
        'end_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class);
    }
}
