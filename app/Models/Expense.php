<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'expense_category_id', 'branch_id', 'booking_id',
        'expense_number', 'description', 'amount',
        'expense_date', 'payment_method', 'recipient',
        'notes', 'status', 'created_by', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'approved_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function generateExpenseNumber(): string
    {
        $prefix = 'EXP-' . date('Ym');
        $last = static::where('expense_number', 'like', $prefix . '%')->latest('id')->first();
        $num = $last ? intval(substr($last->expense_number, -5)) + 1 : 1;
        return $prefix . '-' . str_pad($num, 5, '0', STR_PAD_LEFT);
    }
}
