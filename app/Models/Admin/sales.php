<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sales extends Model
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
        'cost_total'
    ];

    //relacion muchos a uno con la tabla users
    public function users()
    {
        return $this->belongsTo(User::class);
    }

    //relacion muchos a uno con la tabla products
    public function products()
    {
        return $this->belongsTo(products::class);
    }
    //relacion de uno a muchos con la tabla transactions
    public function transactions()
    {
        return $this->hasMany(transactions::class);
    }
}
