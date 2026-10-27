<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'city',
        'interested_package_id',
        'source',
        'status',
        'assigned_to',
        'follow_up_date',
        'notes',
        'pilgrim_id',
        'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_date' => 'date',
            'converted_at' => 'datetime',
        ];
    }

    public function pilgrim(): BelongsTo
    {
        return $this->belongsTo(Pilgrim::class);
    }

    public function interestedPackage(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'interested_package_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }
}
