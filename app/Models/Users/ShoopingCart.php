<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoopingCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'user_id',
        'product_id',
    ];

    //relacion de muchos a uno con la tabla users
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //relacion de muchos a uno con la tabla products
    public function product()
    {
        return $this->belongsTo(Products::class);
    }

}
