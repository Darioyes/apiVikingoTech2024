<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'reference',
        'barcode',
        'description',
        'stock',
        'sale_price',
        'cost_price',
        'visible',
        'image1',
        'image2',
        'image3',
        'image4',
        'image5',
        'color',
        'categories_products_id',
    ];

    //relacion de uno a muchos con la tabla sales
    public function sales()
    {
        return $this->hasMany(Sales::class);
    }

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

    //relacion de uno a muchos con la tabla purcharse_orders
    public function purcharseOrders()
    {
        return $this->hasMany(PurchaseOrders::class);
    }
}
