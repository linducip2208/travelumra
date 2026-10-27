<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'insurance_id', 'claim_number', 'incident_type', 'description',
        'incident_date', 'claim_amount', 'approved_amount', 'status', 'resolution', 'handled_by',
    ];

    protected function casts(): array
    {
        return [
            'incident_date' => 'date',
            'claim_amount' => 'decimal:2',
            'approved_amount' => 'decimal:2',
        ];
    }

    public function insurance() { return $this->belongsTo(Insurance::class); }
    public function handler() { return $this->belongsTo(User::class, 'handled_by'); }

    public static function generateClaimNumber(): string
    {
        $prefix = 'CLM-' . date('Ym');
        $last = static::where('claim_number', 'like', $prefix . '%')->latest('id')->first();
        $num = $last ? intval(substr($last->claim_number, -5)) + 1 : 1;
        return $prefix . '-' . str_pad($num, 5, '0', STR_PAD_LEFT);
    }
}
