<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItineraryDay extends Model
{
    use HasFactory;

    protected $fillable = ['cluster_id', 'day_number', 'date', 'title', 'description', 'location'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function cluster() { return $this->belongsTo(Cluster::class); }
    public function activities() { return $this->hasMany(ItineraryActivity::class)->orderBy('sort_order'); }
}
