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
        'reference'
    ];
}
