<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaDocument extends Model
{
    use HasFactory;

    protected $fillable = ['visa_id', 'document_type', 'file_path', 'status', 'notes', 'verified_by', 'verified_at'];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function visa() { return $this->belongsTo(Visa::class); }
    public function verifier() { return $this->belongsTo(User::class, 'verified_by'); }
}
