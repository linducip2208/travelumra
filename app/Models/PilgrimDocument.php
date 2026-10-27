<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilgrimDocument extends Model
{
    use HasFactory;
    protected $fillable = [
        'pilgrim_id', 'type', 'file_path', 'original_name', 'mime_type', 'size',
        'status', 'verification_notes', 'verified_by', 'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'size' => 'integer',
        ];
    }

    public function pilgrim(): BelongsTo
    {
        return $this->belongsTo(Pilgrim::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
