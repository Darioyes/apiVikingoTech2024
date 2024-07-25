<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products extends Model
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
        'image1',
        'image2',
        'image3',
        'image4',
        'image5',
        'color',
        'category_id',
    ];

    //relacion de uno a muchos con la tabla sales
    public function sales()
    {
        return $this->hasMany(sales::class);
    }

  //relacion de muchos a uno con la tabla categories
    public function categories()
    {
        return $this->belongsTo(categoriesProducts::class);
    }
     //relacion de uno a muchos con la tabla carousels
    public function carousels()
    {
        return $this->hasMany(carousel::class);
    }

    //relacion de uno a muchos con la tabla purcharse_orders
    public function purcharseOrders()
    {
        return $this->hasMany(purchaseOrders::class);
    }
}
