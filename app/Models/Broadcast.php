<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Broadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'message', 'channel', 'recipient_ids',
        'cluster_id', 'sender_id', 'status',
        'scheduled_at', 'recipient_count', 'sent_count',
    ];

    protected function casts(): array
    {
        return [
            'recipient_ids' => 'array',
            'scheduled_at' => 'datetime',
        ];
    }

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
