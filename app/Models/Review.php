<?php

namespace App\Models;

use Database\Factories\ReviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<ReviewFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'rating',
        'comment',
        'is_approved',
        'locale',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'rating' => 'float',
    ];
}
