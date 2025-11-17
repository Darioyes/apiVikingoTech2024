<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'reference',
        'description',
        'stock',
        'sale_price',
        'visible',
        'image1',
        'image2',
        'image3',
        'image4',
        'image5',
        'color',
        'categories_products_id',
    ];

    //relacion de muchos a uno con la tabla categories
    public function categoriesProducts()
    {
        return $this->belongsTo(CategoriesProducts::class);
    }

    //relacion de uno a muchos con la tabla carousels
    public function carousels()
    {
        return $this->hasMany(Carousel::class);
    }
}
