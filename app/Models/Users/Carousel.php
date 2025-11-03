<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    use HasFactory;
    protected $fillable = [
        'carousel',
        'order',
        'image',
        'image2',
        'image3',
        'product_id',
    ];

    //relacion de muchos a uno con la tabla products
    public function product()
    {
        return $this->belongsTo(Products::class);
    }
}
