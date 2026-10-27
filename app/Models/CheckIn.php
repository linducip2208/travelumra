<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckIn extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'cluster_id', 'checked_in_at', 'checked_out_at', 'checked_in_by', 'notes'];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
        ];
    }

    public function booking() { return $this->belongsTo(Booking::class); }
    public function cluster() { return $this->belongsTo(Cluster::class); }
    public function checkedBy() { return $this->belongsTo(User::class, 'checked_in_by'); }
}
