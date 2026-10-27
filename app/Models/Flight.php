<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'cluster_id', 'flight_number', 'airline', 'departure_airport',
        'arrival_airport', 'departure_time', 'arrival_time', 'terminal',
        'gate', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'departure_time' => 'datetime',
            'arrival_time' => 'datetime',
        ];
    }

    public function cluster() { return $this->belongsTo(Cluster::class); }

    public function getDurationAttribute(): string
    {
        if (!$this->departure_time || !$this->arrival_time) return '-';
        $hours = $this->departure_time->diffInHours($this->arrival_time);
        $mins = $this->departure_time->diffInMinutes($this->arrival_time) % 60;
        return "{$hours}j {$mins}m";
    }
}
