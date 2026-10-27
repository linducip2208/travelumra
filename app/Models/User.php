<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use \App\Traits\HasRoles;
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password',
        'branch_id', 'role', 'phone', 'photo',
        'is_muthawif', 'muthawif_certification', 'muthawif_experience_years',
        'is_active',
    ];

    protected $attributes = [
        'muthawif_experience_years' => 0,
        'is_muthawif' => false,
        'is_active' => true,
        'role' => 'agent',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'nik',
        'kk_number',
        'passport_number',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_muthawif' => 'boolean',
            'is_active' => 'boolean',
            'muthawif_experience_years' => 'integer',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function pilgrims(): HasMany
    {
        return $this->hasMany(Pilgrim::class, 'agent_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'agent_id');
    }

    public function clusters(): HasMany
    {
        return $this->hasMany(Cluster::class, 'muthawif_id');
    }

    public function confirmedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'confirmed_by');
    }

    public function verifiedDocuments(): HasMany
    {
        return $this->hasMany(PilgrimDocument::class, 'verified_by');
    }

    public function broadcastMessages(): HasMany
    {
        return $this->hasMany(Broadcast::class, 'sender_id');
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function leadActivities(): HasMany
    {
        return $this->hasMany(LeadActivity::class, 'staff_id');
    }

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    public function canAccessFilament(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        // Hanya staf internal, bukan agent/muthawif.
        return in_array($this->role, [
            'super_admin',
            'admin',
            'finance',
            'branch_manager',
        ], true);
    }
}
