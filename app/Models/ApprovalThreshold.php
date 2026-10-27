<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalThreshold extends Model
{
    protected $fillable = [
        'entity_type', 'min_amount', 'max_amount', 'approver_role', 'is_active',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
