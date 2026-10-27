<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'photo',
        'content',
        'rating',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_published' => 'boolean',
    ];
}
