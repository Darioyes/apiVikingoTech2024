<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBold extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'currency',
        'status',
        'reference',
        'bold_response',
        'signature_valid'
    ];

    protected $casts = [
        'bold_response' => 'array',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];
}
