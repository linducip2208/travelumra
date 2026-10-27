<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItineraryActivity extends Model
{
    use HasFactory;
    protected $fillable = ['itinerary_day_id','time','title','description','location_type','sort_order'];

    public function day() { return $this->belongsTo(ItineraryDay::class, 'itinerary_day_id'); }
}
