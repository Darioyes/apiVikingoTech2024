<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class carousel extends Model
{
    use HasFactory;
    protected $fillable = [
        'carousel',
        'discount',
        'image',
        'product_id',
    ];

    //relacion de muchos a uno con la tabla products
    public function product()
    {
        return $this->belongsTo(products::class);
    }
}
