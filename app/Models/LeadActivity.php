<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'staff_id',
        'type',
        'content',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
