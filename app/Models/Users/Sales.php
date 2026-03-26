<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

        protected $fillable = [
        'description',
        'amount',
        'confirm_sale',
        'shopping_cart',
        'user_id',
        'product_id',
        'sale_total',
    ];

    //relacion muchos a uno con la tabla users
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //relacion muchos a uno con la tabla products
    public function product()
    {
        return $this->belongsTo(Products::class);
    }
}
